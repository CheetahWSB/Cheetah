<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModule');
ch_import('ChWsbPaginate');

define('CH_WMAP_ZOOM_DEFAULT_ENTRY', 10);
define('CH_WMAP_ZOOM_DEFAULT_EDIT', 5);
define('CH_WMAP_PRIVACY_DEFAULT', 3);

class ChWmapModule extends ChWsbModule
{
    var $_iProfileId;
    var $_aParts;
    var $_sProto = 'http';
    var $aIconsSizes = array(
        'group.png' => array('w' => 24, 'h' => 24, 'url' => ''),
        'default'   => array('w' => 24, 'h' => 24, 'url' => ''),
    );

    function __construct(&$aModule)
    {
        parent::__construct($aModule);
        $this->_iProfileId   = getLoggedId();
        $this->_aParts       = $this->_oDb->getParts();
        $this->_oDb->_aParts = &$this->_aParts;
        $this->_sProto       = ch_proto();
    }

    function actionHome()
    {
        $this->_oTemplate->pageStart();

        ch_import('PageMain', $this->_aModule);
        $oPage = new ChWmapPageMain ($this);
        echo $oPage->getCode();

        $this->_oTemplate->addJs($this->_sProto . '://www.google.com/jsapi?key=' . getParam('ch_wmap_key'));
        $this->_oTemplate->addJs('ChWmap.js');
        $this->_oTemplate->addCss('main.css');
        $this->_oTemplate->pageCode(_t('_ch_wmap_block_title_block_map'), false, false);
    }

    function actionEdit($iEntryId, $sPart)
    {
        if (!isset($this->_aParts[$sPart])) {
            $this->_oTemplate->displayPageNotFound();

            return;
        }

        $iEntryId  = (int)$iEntryId;
        $aLocation = $this->_iProfileId ? $this->_oDb->getDirectLocation($iEntryId, $this->_aParts[$sPart]) : false;

        if (!$aLocation || !$this->isAllowedEditOwnLocation($aLocation)) {
            $this->_oTemplate->displayAccessDenied();

            return;
        }

        if ('profiles' == $sPart) {
            $aLocation['title'] = getNickName($aLocation['id']);
        }

        $this->_oTemplate->pageStart();

        ch_import('PageEdit', $this->_aModule);
        $oPage = new ChWmapPageEdit ($this, $aLocation);
        echo $oPage->getCode();

        $this->_oTemplate->addJs($this->_sProto . '://www.google.com/jsapi?key=' . getParam('ch_wmap_key'));
        $this->_oTemplate->addJs('ChWmap.js');
        $this->_oTemplate->addCss('main.css');
        $this->_oTemplate->pageCode(sprintf(_t('_ch_wmap_edit'), $aLocation['title'],
            _t($this->_aParts[$sPart]['title_singular'])), false, false);
    }

    function actionSaveData(
        $iEntryId,
        $sPart,
        $iZoom,
        $sMapType,
        $fLat,
        $fLng,
        $sMapClassInstanceName,
        $sAddress,
        $sCountry
    ) {
        $iRet = $this->_saveData($iEntryId, $sPart, $iZoom, $sMapType, $fLat, $fLng, $sMapClassInstanceName, $sAddress,
            $sCountry);

        switch ((int)$iRet) {
            case 404:
                echo 404;
                break;
            case 403:
                $this->_oTemplate->displayAccessDenied();
                break;
            case 1:
                echo 1;
                break;
        }
    }

    function actionSaveLocationPartHome($sPart, $iZoom, $sMapType, $fLat, $fLng)
    {
        $this->_saveLocationByPrefix('ch_wmap_home_' . $sPart, $iZoom, $sMapType, $fLat, $fLng);
    }

    function actionSaveLocationHomepage($iZoom, $sMapType, $fLat, $fLng)
    {
        $this->_saveLocationByPrefix('ch_wmap_homepage', $iZoom, $sMapType, $fLat, $fLng);
    }

    function actionSaveLocationSeparatePage($iZoom, $sMapType, $fLat, $fLng)
    {
        $this->_saveLocationByPrefix('ch_wmap_separate', $iZoom, $sMapType, $fLat, $fLng);
    }

    function actionGetDataLocation($iId, $sPart, $sMapClassInstanceName)
    {
        if (!isset($this->_aParts[$sPart])) {
            return;
        }

        $iEntryId = (int)$iId;
        $r        = $this->_oDb->getDirectLocation($iEntryId, $this->_aParts[$sPart]);
        if (!$r || empty($r['lat'])) {
            return;
        }

        if ('profiles' == $sPart) {
            $r['title'] = getNickName($r['id']);
        }

        $oPermalinks = new ChWsbPermalinks();
        $sIcon       = $this->_aParts[$r['part']]['icon_site'];
        $sIcon       = $GLOBALS['oFunctions']->sysImage(false === strpos($sIcon,
            '.') ? $sIcon : $this->_oTemplate->getIconUrl($sIcon));
        $aVars       = array(
            'icon'  => $sIcon,
            'title' => $r['title'],
            'link'  => CH_WSB_URL_ROOT . $oPermalinks->permalink($this->_aParts[$r['part']]['permalink'] . $r['uri'])
        );
        $sHtml       = $this->_oTemplate->parseHtmlByName('popup_location', $aVars);

        $aIconJSON = $this->_getIconArray($sMapClassInstanceName == 'glChWmapEdit' ? '' : $this->_aParts[$r['part']]['icon']);

        $aRet   = array();
        $aRet[] = array(
            'lat'  => $r['lat'],
            'lng'  => $r['lng'],
            'data' => $sHtml,
            'icon' => $aIconJSON,
        );

        echo json_encode($aRet);
    }

    function actionGetData(
        $iZoom,
        $fLatMin,
        $fLatMax,
        $fLngMin,
        $fLngMax,
        $sMapClassInstanceName,
        $sCustomParts = '',
        $sCustom = ''
    ) {
        $fLatMin = (float)$fLatMin;
        $fLatMax = (float)$fLatMax;
        $fLngMin = (float)$fLngMin;
        $fLngMax = (float)$fLngMax;
        $iZoom   = (int)$iZoom;

        echo $this->_getLocationsData($fLatMin, $fLatMax, $fLngMin, $fLngMax, $sCustomParts, $sCustom);
    }

    function _getLocationsData($fLatMin, $fLatMax, $fLngMin, $fLngMax, $sCustomParts = '', $sCustom = '')
    {
        ch_import('ChWsbPrivacy');

        $oPermalinks = new ChWsbPermalinks();

        $aCustomParts = $this->_validateParts($sCustomParts);
        $a            = $this->_oDb->getLocationsByBounds('', (float)$fLatMin, (float)$fLatMax, (float)$fLngMin,
            (float)$fLngMax, $aCustomParts, getLoggedId() ? array(CH_WSB_PG_ALL, CH_WSB_PG_MEMBERS) : CH_WSB_PG_ALL);

        $aa = array();
        foreach ($a as $r) {
            if (!$this->_oDb->getDirectLocation($r['id'], $this->_aParts[$r['part']], true))
                continue;

            if ('profiles' == $r['part']) {
                $r['title'] = getNickName($r['id']);
            }

            $sKey = $r['lat'] . 'x' . $r['lng'];

            $sIcon = $this->_aParts[$r['part']]['icon_site'];
            $sIcon = $GLOBALS['oFunctions']->sysImage(false === strpos($sIcon,
                '.') ? $sIcon : $this->_oTemplate->getIconUrl($sIcon));

            $aVars = array(
                'icon'  => $sIcon,
                'title' => $r['title'],
                'link'  => CH_WSB_URL_ROOT . $oPermalinks->permalink($this->_aParts[$r['part']]['permalink'] . $r['uri'])
            );

            $aa[$sKey][] = array(
                'lat'   => $r['lat'],
                'lng'   => $r['lng'],
                'title' => $r['title'],
                'icon'  => $this->_aParts[$r['part']]['icon'],
                'html'  => $this->_oTemplate->parseHtmlByName('popup_location', $aVars),
            );
        }

        $aRet = array();
        foreach ($aa as $k => $a) {
            $sHtml   = '';
            $aTitles = array();
            $sIcon   = '';
            foreach ($a as $r) {
                $sHtml .= $r['html'];
                $aTitles[] = $r['title'];
                $sIcon     = $r['icon'];
            }
            $aVars  = array('content' => $sHtml);
            $aRet[] = array(
                'lat'    => $r['lat'],
                'lng'    => $r['lng'],
                'titles' => $aTitles,
                'data'   => $this->_oTemplate->parseHtmlByName('popup_locations', $aVars),
                'icon'   => $this->_getIconArray((count($a) > 1 ? 'group.png' : $sIcon)),
            );
        }

        return json_encode($aRet);
    }

    // ================================== admin actions

    function actionUpdateLocations($iLimit = 4, $iDelay = 6)
    {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied();

            return;
        }

        $iLimit = (int)$iLimit;
        $iDelay = (int)$iDelay;

        $a = $this->_oDb->getUndefinedLocations($iLimit);
        if ($a) {
            foreach ($a as $r) {
                $this->_updateLocation($iDelay, $r);
            }

            $aVars = array(
                'refresh' => 1,
                'msg'     => 'Entries update is in progress, please wait...',
            );
            echo $this->_oTemplate->parseHtmlByName('updating', $aVars);
        } else {
            $this->_oTemplate->displayMsg('Entries locations update has been completed');
        }
    }

    function actionAdministrationParts($sPart)
    {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied();

            return;
        }

        if (!isset($this->_aParts[$sPart])) {
            $this->_oTemplate->displayPageNotFound();

            return;
        }

        $this->_oTemplate->pageStart();

        ch_import('ChWsbAdminSettings');

        $mixedResult = '';
        if (isset($_POST['save']) && isset($_POST['cat']) && (int)$_POST['cat']) {
            $oSettings   = new ChWsbAdminSettings((int)$_POST['cat']);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        foreach ($_POST as $k => $v) {
            unset ($_POST[$k]);
        }

        $aCats = array(
            array(
                'cat'   => 'World Map Home: ' . ucfirst($sPart),
                'title' => _t('_ch_wmap_admin_settings_part_home', _t($this->_aParts[$sPart]['title'])),
                'extra' => 'return $this->_saveLocationForm ("PartHome", $this->serviceHomepagePartBlock ("' . $sPart . '"));',
            ),
            array(
                'cat'   => 'World Map Entry: ' . ucfirst($sPart),
                'title' => _t('_ch_wmap_admin_settings_part_entry', _t($this->_aParts[$sPart]['title'])),
                'extra' => '',
            ),
            array(
                'cat'   => 'World Map Edit Location: ' . ucfirst($sPart),
                'title' => _t('_ch_wmap_admin_settings_edit_location', _t($this->_aParts[$sPart]['title'])),
                'extra' => '',
            ),
        );

        foreach ($aCats as $a) {
            $iId     = $this->_oDb->getSettingsCategory($a['cat']);
            $sResult = '';
            if ($iId) {
                $oSettings = new ChWsbAdminSettings($iId);
                $sResult   = $oSettings->getForm();
                if ($mixedResult !== true && !empty($mixedResult) && $_POST['cat'] == $iId) {
                    $sResult = $mixedResult . $sResult;
                }
            }
            $sExtra = '';
            if ($a['extra']) {
                $aVars  = array('content' => eval($a['extra']));
                $sExtra = $this->_oTemplate->parseHtmlByName('extra_wrapper', $aVars);
            }
            $aVars = array('content' => $sResult . $sExtra);
            echo $this->_oTemplate->adminBlock($this->_oTemplate->parseHtmlByName('default_padding', $aVars),
                $a['title']);
        }

        $this->_oTemplate->addJsAdmin($this->_sProto . '://www.google.com/jsapi?key=' . getParam('ch_wmap_key'));
        $this->_oTemplate->addJsAdmin('modules/cheetah/world_map/js/|ChWmap.js');
        $this->_oTemplate->addCssAdmin('main.css');
        $this->_oTemplate->addCssAdmin('forms_adv.css');
        $this->_oTemplate->pageCodeAdmin(_t('_ch_wmap_administration') . ' ' . _t($this->_aParts[$sPart]['title']));
    }

    function actionAdministration()
    {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied();

            return;
        }

        $this->_oTemplate->pageStart();

        $aPartsForVars = array();
        foreach ($this->_aParts as $k => $r) {
            $aPartsForVars[] = array(
                'part'      => $k,
                'title'     => _t($r['title']),
                'icon'      => $GLOBALS['oFunctions']->sysImage(false === strpos($r['icon_site'],
                    '.') ? $r['icon_site'] : $this->_oTemplate->getIconUrl($r['icon_site'])),
                'link_base' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration_parts/',
            );
        }

        $aVars    = array(
            'module_url'      => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri(),
            'ch_repeat:parts' => $aPartsForVars,
        );
        $sContent = $this->_oTemplate->parseHtmlByName('admin_links', $aVars);
        echo $this->_oTemplate->adminBlock($sContent, _t('_ch_wmap_admin_links'), false, false, 11);

        ch_import('ChWsbAdminSettings');

        $mixedResult = '';
        if (isset($_POST['save']) && isset($_POST['cat']) && (int)$_POST['cat']) {
            $oSettings   = new ChWsbAdminSettings((int)$_POST['cat']);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        foreach ($_POST as $k => $v) {
            unset ($_POST[$k]);
        }

        $aCats = array(
            array(
                'cat'   => 'World Map General',
                'title' => _t('_ch_wmap_admin_settings_general'),
                'extra' => '',
            ),
            array(
                'cat'   => 'World Map Homepage',
                'title' => _t('_ch_wmap_admin_settings_homepage'),
                'extra' => 'return $this->_saveLocationForm ("Home", $this->serviceHomepageBlock ());',
            ),
            array(
                'cat'   => 'World Map Separate',
                'title' => _t('_ch_wmap_admin_settings_separate'),
                'extra' => 'return $this->_saveLocationForm ("Page", $this->serviceSeparatePageBlock ());',
            ),
        );

        foreach ($aCats as $a) {
            $iId     = $this->_oDb->getSettingsCategory($a['cat']);
            $sResult = '';
            if ($iId) {
                $oSettings = new ChWsbAdminSettings($iId);
                $sResult   = $oSettings->getForm();
                if ($mixedResult !== true && !empty($mixedResult) && $_POST['cat'] == $iId) {
                    $sResult = $mixedResult . $sResult;
                }
            }
            $sExtra = '';
            if ($a['extra']) {
                $aVars  = array('content' => eval($a['extra']));
                $sExtra = $this->_oTemplate->parseHtmlByName('extra_wrapper', $aVars);
            }
            $aVars = array('content' => $sResult . $sExtra);
            echo $this->_oTemplate->adminBlock($this->_oTemplate->parseHtmlByName('default_padding', $aVars),
                $a['title']);
        }

        $this->_oTemplate->addJsAdmin($this->_sProto . '://www.google.com/jsapi?key=' . getParam('ch_wmap_key'));
        $this->_oTemplate->addJsAdmin(CH_WSB_URL_MODULES . $this->_aModule['path'] . 'js/ChWmap.js');
        $this->_oTemplate->addCssAdmin('main.css');
        $this->_oTemplate->addCssAdmin('forms_adv.css');
        $this->_oTemplate->pageCodeAdmin(_t('_ch_wmap_administration'));
    }

    // ================================== service actions

    /**
     * Get location array
     *
     * @param $sPart    module/part name
     * @param $iEntryId entry's id which location is edited
     * @param $iViewer  viewer profile id
     * @return false - location undefined, -1 - access denied, array - all good
     */
    function serviceGetLocation($sPart, $iEntryId, $iViewer = false)
    {
        if (false === $iViewer) {
            $iViewer = getLoggedId();
        }

        if ('profiles' == $sPart) {
            if (!ch_check_profile_visibility($iEntryId, $iViewer, true)) {
                return -1;
            }
        } else {
            ch_import('ChWsbPrivacy');
            $oPrivacy = new ChWsbPrivacy($this->_aParts[$sPart]['join_table'], $this->_aParts[$sPart]['join_field_id'],
                $this->_aParts[$sPart]['join_field_author']);
            if (!$oPrivacy->check('view', $iEntryId, $iViewer)) {
                return -1;
            }
        }

        $aLocation = $this->_oDb->getDirectLocation($iEntryId, $this->_aParts[$sPart]);
        if (!$aLocation || (!$aLocation['lat'] && $aLocation['lng'])) {
            return false;
        }

        if (false === $aLocation['zoom'] || -1 == $aLocation['zoom']) {
            $aLocation['zoom'] = getParam("ch_wmap_edit_{$sPart}_zoom");
        }

        if (!$aLocation['type']) {
            $aLocation['type'] = getParam("ch_wmap_edit_{$sPart}_map_type");
        }

        return $aLocation;
    }

    /**
     * Update location
     *
     * @param $sPart    module/part name
     * @param $iEntryId entry's id which location is edited
     * @param $fLat     latitude
     * @param $fLng     longitude
     * @param $iZoom    zoom level
     * @param $sMapType map type: normal, satellite, hybrid, terrain
     * @param $sCountry
     * @param $sState
     * @param $sCity
     * @param $sAddress
     * @return false - location undefined, -1 - access denied, array - all good
     */
    function serviceUpdateLocationManually(
        $sPart,
        $iEntryId,
        $fLat,
        $fLng,
        $iZoom,
        $sMapType,
        $sCountry = '',
        $sState = '',
        $sCity = '',
        $sAddress = ''
    ) {
        $a = array('fLat', 'fLng', 'iZoom', 'sMapType', 'sCountry', 'sState', 'sCity', 'sAddress');
        foreach ($a as $sVar) {
            if ('' === $$sVar || false === $$sVar) {
                $$sVar = 'null';
            }
        }

        return $this->_saveData($iEntryId, $sPart, $iZoom, $sMapType, $fLat, $fLng, '', $sAddress, $sCountry);
    }

    /**
     * Edit location block
     *
     * @param $sPart    module/part name
     * @param $iEntryId entry's id which location is edited
     * @return html with clickable map
     */
    function serviceEditLocation($sPart, $iEntryId)
    {
        if (!isset($this->_aParts[$sPart])) {
            return false;
        }

        $iEntryId  = (int)$iEntryId;
        $aLocation = $this->_oDb->getDirectLocation($iEntryId, $this->_aParts[$sPart]);
        if ('profiles' == $sPart) {
            $aLocation['title'] = getNickName($aLocation['id']);
        }

        if (!$aLocation) {
            return false;
        }

        $fLat     = false;
        $fLng     = false;
        $iZoom    = false;
        $sMapType = false;

        if ($aLocation && !empty($aLocation['lat'])) {
            $fLat     = $aLocation['lat'];
            $fLng     = $aLocation['lng'];
            $iZoom    = $aLocation['zoom'];
            $sMapType = $aLocation['type'];
        }

        if (false === $fLat || false === $fLng) {
            $aLocationCountry = $this->_geocode($aLocation['country'], $aLocation['country']);
            $fLat             = isset($aLocationCountry[0]) ? $aLocationCountry[0] : 0;
            $fLng             = isset($aLocationCountry[1]) ? $aLocationCountry[1] : 0;
            $iZoom            = CH_WMAP_ZOOM_DEFAULT_EDIT;
        }

        if (false === $iZoom || -1 == $iZoom) {
            $iZoom = getParam("ch_wmap_edit_{$sPart}_zoom");
        }

        if (!$sMapType) {
            $sMapType = getParam("ch_wmap_edit_{$sPart}_map_type");
        }

        $aVars = array(
            'msg_incorrect_google_key' => trim(_t('_ch_wmap_msg_incorrect_google_key')),
            'loading'                  => _t('_loading ...'),
            'map_control'              => getParam("ch_wmap_edit_{$sPart}_control_type"),
            'map_is_type_control'      => getParam("ch_wmap_edit_{$sPart}_is_type_control") == 'on' ? 1 : 0,
            'map_is_scale_control'     => getParam("ch_wmap_edit_{$sPart}_is_scale_control") == 'on' ? 1 : 0,
            'map_is_overview_control'  => getParam("ch_wmap_edit_{$sPart}_is_overview_control") == 'on' ? 1 : 0,
            'map_is_dragable'          => getParam("ch_wmap_edit_{$sPart}_is_map_dragable") == 'on' ? 1 : 0,
            'map_type'                 => $sMapType,
            'map_lat'                  => $fLat,
            'map_lng'                  => $fLng,
            'map_zoom'                 => $iZoom,
            'parts'                    => $sPart,
            'custom'                   => '',
            'suffix'                   => 'Edit',
            'subclass'                 => 'ch_wmap_edit',
            'data_url'                 => CH_WSB_URL_MODULES . "?r=wmap/get_data_location/$iEntryId/{parts}/{instance}/{ts}",
            'save_data_url'            => CH_WSB_URL_MODULES . "?r=wmap/save_data/$iEntryId/{parts}/{zoom}/{map_type}/{lat}/{lng}/{instance}/{address}/{country}/{ts}",
            'save_location_url'        => '',
            'shadow_url'               => '',
            'key'                      => getParam('ch_wmap_key'),
        );
        $sMap = $this->_oTemplate->parseHtmlByName('map', $aVars);

        $oPermalinks = new ChWsbPermalinks();
        $sBackLink   = CH_WSB_URL_ROOT . $oPermalinks->permalink($this->_aParts[$aLocation['part']]['permalink'] . $aLocation['uri']);
        $aVars       = array(
            'info' => sprintf(_t('_ch_wmap_edit'), "<a href=\"{$sBackLink}\">{$aLocation['title']}</a>",
                _t($this->_aParts[$sPart]['title_singular'])),
            'map'  => $sMap,
        );

        return array($this->_oTemplate->parseHtmlByName('map_edit', $aVars));
    }

    /**
     * Homepage block with world map
     *
     * @return html with world map
     */
    function serviceHomepageBlock()
    {
        $this->_oTemplate->addJs($this->_sProto . '://www.google.com/jsapi?key=' . getParam('ch_wmap_key'));
        $this->_oTemplate->addJs('ChWmap.js');
        $this->_oTemplate->addCss('main.css');

        return $this->serviceSeparatePageBlock(false, false, false, '', '', 'ch_wmap_homepage', 'ch_wmap_homepage',
            'Home', 'homepage');
    }

    /**
     * Module Homepage block with world map
     *
     * @return html with world map
     */
    function serviceHomepagePartBlock($sPart)
    {
        if (!isset($this->_aParts[$sPart])) {
            return '';
        }
        $this->_oTemplate->addJs($this->_sProto . '://www.google.com/jsapi?key=' . getParam('ch_wmap_key'));
        $this->_oTemplate->addJs('ChWmap.js');
        $this->_oTemplate->addCss('main.css');

        return $this->serviceSeparatePageBlock(false, false, false, $sPart, '', 'ch_wmap_homepage_part',
            'ch_wmap_home_' . $sPart, 'PartHome', 'part_home/' . $sPart, false);
    }

    /**
     * Separate page block with world map
     *
     * @return html with world map
     */
    function serviceSeparatePageBlock(
        $fLat = false,
        $fLng = false,
        $iZoom = false,
        $sPartsCustom = '',
        $sCustom = '',
        $sSubclass = 'ch_wmap_separate',
        $sParamPrefix = 'ch_wmap_separate',
        $sSuffix = 'Page',
        $sSaveLocationSuffix = 'separate_page',
        $isPartsSelector = true
    ) {
        if (false === $fLat) {
            $fLat = getParam($sParamPrefix . '_lat');
        }
        if (false === $fLng) {
            $fLng = getParam($sParamPrefix . '_lng');
        }
        if (false === $iZoom) {
            $iZoom = getParam($sParamPrefix . '_zoom');
        }

        $aVars = array(
            'msg_incorrect_google_key' => trim(_t('_ch_wmap_msg_incorrect_google_key')),
            'loading'                  => _t('_loading ...'),
            'map_control'              => getParam($sParamPrefix . '_control_type'),
            'map_is_type_control'      => getParam($sParamPrefix . '_is_type_control') == 'on' ? 1 : 0,
            'map_is_scale_control'     => getParam($sParamPrefix . '_is_scale_control') == 'on' ? 1 : 0,
            'map_is_overview_control'  => getParam($sParamPrefix . '_is_overview_control') == 'on' ? 1 : 0,
            'map_is_dragable'          => getParam($sParamPrefix . '_is_map_dragable') == 'on' ? 1 : 0,
            'map_type'                 => getParam($sParamPrefix . '_map_type'),
            'map_lat'                  => $fLat,
            'map_lng'                  => $fLng,
            'map_zoom'                 => $iZoom,
            'parts'                    => $sPartsCustom,
            'custom'                   => $sCustom,
            'suffix'                   => $sSuffix,
            'subclass'                 => $sSubclass,
            'data_url'                 => CH_WSB_URL_MODULES . "?r=wmap/get_data/{zoom}/{lat_min}/{lat_max}/{lng_min}/{lng_max}/{instance}/{parts}/{custom}",
            'save_data_url'            => '',
            'save_location_url'        => $this->isAdmin() ? CH_WSB_URL_MODULES . "?r=wmap/save_location_{$sSaveLocationSuffix}/{zoom}/{map_type}/{lat}/{lng}" : '',
            'shadow_url'               => $this->_oTemplate->getIconUrl('flag_icon_shadow.png'),
            'lang'                     => ch_lang_name(),
            'key'                      => getParam('ch_wmap_key'),
        );
        $sMap = $this->_oTemplate->parseHtmlByName('map', $aVars);

        if (!$isPartsSelector) {
            return array($sMap);
        }

        $aVarsParts     = array(
            'suffix'          => $aVars['suffix'],
            'subclass'        => $aVars['subclass'],
            'ch_repeat:parts' => array(),
        );
        $aPartsSelected = $this->_validateParts($sPartsCustom);
        foreach ($this->_aParts AS $k => $r) {
            $aVarsParts['ch_repeat:parts'][] = array(
                'part'    => $k,
                'title'   => _t($r['title']),
                'icon'    => $GLOBALS['oFunctions']->sysImage(false === strpos($r['icon_site'],
                    '.') ? $r['icon_site'] : $this->_oTemplate->getIconUrl($r['icon_site'])),
                'suffix'  => $aVars['suffix'],
                'checked' => isset($aPartsSelected[$k]) ? 'checked' : '',
            );
        }
        $sMapParts = $this->_oTemplate->parseHtmlByName('map_parts', $aVarsParts);

        return array($sMapParts . $sMap);
    }

    /**
     * Block with entry's location map
     *
     * @param $sPart    module/part name
     * @param $iEntryId entry's id which location is shown on the map
     * @return html with entry's location map
     */
    function serviceLocationBlock($sPart, $iEntryId)
    {
        if (!isset($this->_aParts[$sPart])) {
            return '';
        }

        $sParamPrefix = 'ch_wmap_entry_' . $sPart;
        $iEntryId     = (int)$iEntryId;
        $r            = $this->_oDb->getDirectLocation($iEntryId, $this->_aParts[$sPart]);

        $sBoxContent = '';
        if ($r && !empty($r['lat'])) {

            $aVars = array(
                'msg_incorrect_google_key' => _t('_ch_wmap_msg_incorrect_google_key'),
                'loading'                  => _t('_loading ...'),
                'map_control'              => getParam($sParamPrefix . '_control_type'),
                'map_is_type_control'      => getParam($sParamPrefix . '_is_type_control') == 'on' ? 1 : 0,
                'map_is_scale_control'     => getParam($sParamPrefix . '_is_scale_control') == 'on' ? 1 : 0,
                'map_is_overview_control'  => getParam($sParamPrefix . '_is_overview_control') == 'on' ? 1 : 0,
                'map_is_dragable'          => getParam($sParamPrefix . '_is_map_dragable') == 'on' ? 1 : 0,
                'map_lat'                  => $r['lat'],
                'map_lng'                  => $r['lng'],
                'map_zoom'                 => -1 != $r['zoom'] ? $r['zoom'] : (getParam($sParamPrefix . '_zoom') ? getParam($sParamPrefix . '_zoom') : CH_WMAP_ZOOM_DEFAULT_ENTRY),
                'map_type'                 => $r['type'] ? $r['type'] : (getParam($sParamPrefix . '_map_type') ? getParam($sParamPrefix . '_map_type') : 'normal'),
                'parts'                    => $sPart,
                'custom'                   => '',
                'suffix'                   => 'Location',
                'subclass'                 => 'ch_wmap_location_box',
                'data_url'                 => CH_WSB_URL_MODULES . "' + '?r=wmap/get_data_location/" . $iEntryId . "/" . $sPart . "/{instance}",
                'save_data_url'            => '',
                'save_location_url'        => '',
                'shadow_url'               => '',
                'lang'                     => ch_lang_name(),
                'key'                      => getParam('ch_wmap_key'),
            );
            $this->_oTemplate->addJs($this->_sProto . '://www.google.com/jsapi?key=' . getParam('ch_wmap_key'));
            $this->_oTemplate->addJs('ChWmap.js');
            $this->_oTemplate->addCss('main.css');

            $aVars2 = array(
                'map' => $this->_oTemplate->parseHtmlByName('map', $aVars),
            );
            $sBoxContent = $this->_oTemplate->parseHtmlByName('entry_location', $aVars2);
        }

        $sBoxFooter = '';
        if ($r['author_id'] == $this->_iProfileId || $this->isAdmin()) {
            $aVars      = array(
                'icon'  => $this->_oTemplate->getIconUrl('more.png'),
                'url'   => $this->_oConfig->getBaseUri() . "edit/$iEntryId/$sPart",
                'title' => _t('_ch_wmap_box_footer_edit'),
            );
            $sBoxFooter = $this->_oTemplate->parseHtmlByName('box_footer', $aVars);
            if (!$sBoxContent) {
                $sBoxContent = MsgBox(_t('_ch_wmap_msg_locations_is_not_defined'));
            }
        }

        if ($sBoxContent || $sBoxFooter) {
            return array($sBoxContent, array(), $sBoxFooter);
        }

        return '';
    }

    function serviceResponseEntryDelete($sPart, $iEntryId)
    {
        if (!isset($this->_aParts[$sPart])) {
            return false;
        }

        $aPart = $this->_aParts[$sPart];

        return $this->_oDb->deleteLocation((int)$iEntryId, $sPart);
    }

    function serviceResponseEntryAdd($sPart, $iEntryId)
    {
        return $this->serviceResponseEntryChange($sPart, $iEntryId);
    }

    function serviceResponseEntryChange($sPart, $iEntryId)
    {
        if (!isset($this->_aParts[$sPart])) {
            return false;
        }

        $aPart = $this->_aParts[$sPart];

        $a = $this->_oDb->getDirectLocation($iEntryId, $aPart);
        if (!$a) {
            return false;
        }

        if ($a['lat'] && $a['lng'] && $a['type']) {
            // Don't update location (just update privacy)
            // if it is already geocoded automatically.
            // The manual update will not be erased (detected by 'type')
            $this->_oDb->updateLocationPrivacy((int)$a['id'],
                !empty($a['privacy']) ? $a['privacy'] : CH_WMAP_PRIVACY_DEFAULT);

            return true;
        }

        return $this->_updateLocation(0, $a);
    }

    function servicePartEnable($sPart, $isEnable, $isClearPartLocations = false)
    {
        if (!$this->_oDb->enablePart($sPart, (int)$isEnable)) {
            return false;
        }

        if ($isClearPartLocations) {
            $this->_oDb->clearLocations($sPart, false);
        }

        return true;
    }

    function servicePartUpdate($sPart, $a)
    {
        if (!$this->_oDb->updatePart($sPart, $a))
            return false;

        return true;
    }

    function servicePartInstall($sPart, $a)
    {
        $aDefaults = array(
            'part'                 => $sPart,
            'title'                => '',
            'title_singular'       => '',
            'icon'                 => '',
            'icon_site'            => '',
            'join_table'           => '',
            'join_where'           => '',
            'join_field_id'        => 'id',
            'join_field_country'   => '',
            'join_field_city'      => '',
            'join_field_state'     => '',
            'join_field_zip'       => '',
            'join_field_address'   => '',
            'join_field_latitude'  => '',
            'join_field_longitude' => '',
            'join_field_title'     => '',
            'join_field_uri'       => '',
            'join_field_author'    => '',
            'join_field_privacy'   => '',
            'permalink'            => '',
            'enabled'              => 1
        );

        $aOptions = array_merge($aDefaults, $a);

        if (!$this->_oDb->addPart($aOptions)) {
            return false;
        }

        return true;
    }

    function servicePartUninstall($sPart)
    {
        $this->_oDb->clearLocations($sPart, false);

        return $this->_oDb->removePart($sPart);
    }

    // ================================== events

    function onEventGeolocateProfile($iProfileId, $aLocation)
    {
        ch_import('ChWsbAlerts');
        $oAlert = new ChWsbAlerts('ch_wmap', 'geolocate_profile', $iProfileId, $this->_iProfileId,
            array('location' => $aLocation));
        $oAlert->alert();
    }

    function onEventLocationManuallyUpdated($sPart, $iEntryId, $aLocation)
    {
        ch_import('ChWsbAlerts');
        $oAlert = new ChWsbAlerts('ch_wmap', 'location_manually_updated', $iEntryId, $this->_iProfileId,
            array('location' => $aLocation, 'part' => $sPart));
        $oAlert->alert();
    }

    // ================================== permissions

    function isAllowedEditOwnLocation(&$aLocation)
    {
        if (!$this->_iProfileId) {
            return false;
        }
        if ($this->isAdmin()) {
            return true;
        }
        if ($aLocation && $aLocation['author_id'] == $this->_iProfileId) {
            return true;
        }

        return false;
    }

    function isAdmin()
    {
        return $GLOBALS['logged']['admin'] || $GLOBALS['logged']['moderator'];
    }

    // ================================== other

    function _geocode($sAddress, $sCountryCode = '')
    {
        $sStatus = false;

        $sAddress = rawurlencode($sAddress);

        $sUrl = ch_proto() . "://maps.googleapis.com/maps/api/geocode/json";

        $s = ch_file_get_contents($sUrl, array(
            'address' => $sAddress,
            'sensor'  => 'false'
        ));

        $oData = json_decode($s);
        if (null == $oData) {
            return false;
        }

        if ('OK' != $oData->status) {
            return false;
        }

        foreach ($oData->results as $oResult) {
            $sShortNameCountry = '';
            foreach ($oResult->address_components as $oAddressComponent) {
                if (in_array('country', $oAddressComponent->types)) {
                    $sShortNameCountry = $oAddressComponent->short_name;
                }
            }

            if (!$sCountryCode || ($sShortNameCountry && $sCountryCode == $sShortNameCountry)) {
                $oLocation = $oResult->geometry->location;

                return array($oLocation->lat, $oLocation->lng, $sShortNameCountry);
            }
        }

        return false;
    }

    function _updateLocation($iDelay, &$r)
    {
        $iDelay = (int)$iDelay;

        $iId = (int)$r['id'];
        $a   = false;

        if (isset($r['latitude']) && isset($r['longitude'])) {
            $r['latitude']  = floatval($r['latitude']);
            $r['longitude'] = floatval($r['longitude']);
            if (is_float($r['latitude']) && is_float($r['longitude'])) {
                if ($iDelay) {
                    sleep($iDelay);
                }
                $a = $this->_geocode($r['latitude'] . ', ' . $r['longitude']);
            }
        } else {
            $sState = '';
            if (isset($r['state']) && trim($r['state'])) {
                $sState = ' ' . $r['state'];
            }

            if (isset($r['address']) && trim($r['address'])) {
                if ($iDelay) {
                    sleep($iDelay);
                }
                $a = $this->_geocode($r['address'] . ' ' . $r['city'] . ' ' . $r['state'] . $r['country'],
                    $r['country']);
            }

            if (!$a && isset($r['zip']) && trim($r['zip'])) {
                if ($iDelay) {
                    sleep($iDelay);
                }
                $a = $this->_geocode($r['zip'] . ' ' . $r['country'], $r['country']);
            }

            if (!$a) {
                if ($iDelay) {
                    sleep($iDelay);
                }
                $a = $this->_geocode($r['city'] . ' ' . $r['state'] . $r['country'], $r['country']);
            }
        }

        $sTitle = process_db_input($r['title'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION);

        $mixedPrivacy = !empty($r['privacy']) ? $r['privacy'] : CH_WMAP_PRIVACY_DEFAULT;

        if ($a) {
            $this->_oDb->insertLocation($iId, $r['part'], $sTitle, $r['uri'], $a[0], $a[1], -1, '',
                process_db_input($r['city'] . ', ' . $r['country'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION),
                process_db_input($r['city'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION),
                process_db_input($r['country'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION), $mixedPrivacy, 0);
            $bRet = true;
        } else {
            $this->_oDb->insertLocation($iId, $r['part'], $sTitle, $r['uri'], 0, 0, -1, '', '', '', '', $mixedPrivacy,
                1);
            $bRet = false;
        }

        $this->onEventGeolocateProfile($iId, array(
            'lat'     => (isset($a[0]) ? $a[0] : false),
            'lng'     => (isset($a[1]) ? $a[1] : false),
            'country' => $sCountryCode
        ));

        return $bRet;
    }

    function _saveLocationByPrefix($sPrefix, $iZoom, $sMapType, $fLat, $fLng)
    {
        if (!$this->isAdmin()) {
            echo 'Access denied';

            return;
        }

        if ($iZoom = (int)$iZoom) {
            setParam($sPrefix . '_zoom', $iZoom);
        }

        switch ($sMapType) {
            case 'normal':
            case 'satellite':
            case 'hybrid':
                setParam($sPrefix . '_map_type', $sMapType);
        }

        if ($fLat = (float)$fLat) {
            setParam($sPrefix . '_lat', $fLat);
        }

        if ($fLng = (float)$fLng) {
            setParam($sPrefix . '_lng', $fLng);
        }

        echo 'ok';
    }

    function _saveLocationForm($sSuffix, $sMap)
    {
        if (is_array($sMap)) {
            $sMap = $sMap[0];
        }

        if (!preg_match('/^[A-Za-z0-9]+$/', $sSuffix)) {
            return '';
        }

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => "ch_wmap_save_location_{$sSuffix}",
                'onsubmit' => "return glChWmap{$sSuffix}.saveLocation();",
                'method'   => 'post',
            ),

            'inputs' => array(

                'Map' => array(
                    'type'    => 'custom',
                    'content' => "<div class=\"ch_wmap_form_map\">$sMap</div>",
                    'name'    => 'Map',
                    'caption' => _t('_ch_wmap_admin_map'),
                ),

                'Submit' => array(
                    'type'  => 'submit',
                    'name'  => 'submit_form',
                    'value' => _t('_ch_wmap_admin_save_location'),
                ),
            ),
        );

        $f = new ChTemplFormView ($aCustomForm);

        return $f->getCode();
    }

    /**
     * @return 404 - not found, 403 - access denied, false - error occured, 1 - succesfully saved
     */
    function _saveData(
        $iEntryId,
        $sPart,
        $iZoom,
        $sMapType,
        $fLat,
        $fLng,
        $sMapClassInstanceName = '',
        $sAddress = 'null',
        $sCountry = 'null'
    ) {
        if (!isset($this->_aParts[$sPart])) {
            return 404;
        }

        $iEntryId  = (int)$iEntryId;
        $aLocation = $this->_iProfileId ? $this->_oDb->getDirectLocation($iEntryId, $this->_aParts[$sPart]) : false;

        if (!$aLocation || !$this->isAllowedEditOwnLocation($aLocation)) {
            return 403;
        }

        if (!$aLocation && ('null' == $fLat || 'null' == $fLng)) {
            return false;
        }

        $fLat     = 'null' != $fLat ? (float)$fLat : $aLocation['lat'];
        $fLng     = 'null' != $fLng ? (float)$fLng : $aLocation['lng'];
        $iZoom    = 'null' != $iZoom ? (int)$iZoom : ($aLocation ? $aLocation['zoom'] : -1);
        $sMapType = $sMapType && 'null' != $sMapType ? $sMapType : ($aLocation ? $aLocation['type'] : '');
        $sAddress = $sAddress && 'null' != $sAddress ? process_db_input($sAddress,
            CH_TAGS_STRIP) : ($aLocation ? process_db_input($aLocation['address'], CH_TAGS_NO_ACTION,
            CH_SLASHES_NO_ACTION) : '');
        $sCountry = $sCountry && 'null' != $sCountry ? process_db_input($sCountry,
            CH_TAGS_STRIP) : ($aLocation ? process_db_input($aLocation['country'], CH_TAGS_NO_ACTION,
            CH_SLASHES_NO_ACTION) : '');

        switch ($sMapType) {
            case 'normal':
            case 'satellite':
            case 'hybrid':
                break;
            default:
                $sMapType = 'normal';
        }

        $aLocation['city']  = process_db_input($aLocation['city'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION);
        $aLocation['title'] = process_db_input($aLocation['title'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION);
        $mixedPrivacy       = !empty($aLocation['privacy']) ? $aLocation['privacy'] : CH_WMAP_PRIVACY_DEFAULT;

        if (!$this->_oDb->insertLocation($iEntryId, $sPart, $aLocation['title'], $aLocation['uri'], $fLat, $fLng,
            $iZoom, $sMapType, $sAddress, $aLocation['city'], $sCountry, $mixedPrivacy)
        ) {
            return false;
        }

        $this->onEventLocationManuallyUpdated($sPart, $iEntryId, array(
            'lat'      => $fLat,
            'lng'      => $fLng,
            'zoom'     => $iZoom,
            'map_type' => $sMapType,
            'address'  => $sAddress,
            'country'  => $sCountry
        ));

        return true;
    }

    function _validateParts($sParts)
    {
        $aPartsRet = array();
        $aPartsTmp = explode(',', $sParts);
        foreach ($aPartsTmp as $sPart) {
            if (isset($this->_aParts[$sPart])) {
                $aPartsRet[$sPart] = $sPart;
            }
        }
        if (!$aPartsRet) {
            foreach ($this->_aParts as $sPart => $r) {
                $aPartsRet[$sPart] = $sPart;
            }
        }

        return $aPartsRet;
    }

    function _getIconArray($sBaseFilename = '', $isCountryFlag = false)
    {
        if ($isCountryFlag) {
            $this->aIconsSizes['country_flag']['url'] = $sBaseFilename;

            return $this->aIconsSizes['country_flag'];
        }

        if (!$sBaseFilename) {
            return $this->aIconsSizes['default'];
        }

        if (empty($this->aIconsSizes[$sBaseFilename])) {
            $this->aIconsSizes[$sBaseFilename] = $this->aIconsSizes['default'];
        }

        if (empty($this->aIconsSizes[$sBaseFilename]['url'])) {
            $this->aIconsSizes[$sBaseFilename]['url'] = $this->_oTemplate->getIconUrl($sBaseFilename);
        }

        return $this->aIconsSizes[$sBaseFilename];
    }

}
