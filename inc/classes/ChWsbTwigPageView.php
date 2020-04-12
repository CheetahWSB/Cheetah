<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import ('ChWsbPageView');

/**
 * Base entry view class for modules like events/groups/store
 */
class ChWsbTwigPageView extends ChWsbPageView
{
    var $_oTemplate;
    var $_oMain;
    var $_oDb;
    var $_oConfig;
    var $aDataEntry;

    function __construct($sName, &$oMain, &$aDataEntry)
    {
        parent::__construct($sName);
        $this->_oMain = $oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->aDataEntry = &$aDataEntry;
    }

    function getBlockCode_SocialSharing()
    {
    	if(!$this->_oMain->isAllowedShare($this->aDataEntry))
    		return '';

        $sUrl = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $this->aDataEntry[$this->_oDb->_sFieldUri];
        $sTitle = $this->aDataEntry[$this->_oDb->_sFieldTitle];

        $aCustomParams = false;
        if ($this->aDataEntry[$this->_oDb->_sFieldThumb]) {
            $a = array('ID' => $this->aDataEntry[$this->_oDb->_sFieldAuthorId], 'Avatar' => $this->aDataEntry[$this->_oDb->_sFieldThumb]);
            $aImage = ChWsbService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImgUrl = $aImage['no_image'] ? '' : $aImage['file'];
            if ($sImgUrl) {
                $aCustomParams = array (
                    'img_url' => $sImgUrl,
                    'img_url_encoded' => rawurlencode($sImgUrl),
                );
            }
        }

        ch_import('ChTemplSocialSharing');
        $sCode = ChTemplSocialSharing::getInstance()->getCode($sUrl, $sTitle, $aCustomParams);
        return array($sCode, array(), array(), false);
    }

    function getBlockCode_ForumFeed()
    {
    	if (!$this->_oMain->isAllowedReadForum($this->aDataEntry))
            return '';

        $oModuleDb = new ChWsbModuleDb();
        if (!$oModuleDb->getModuleByUri('forum'))
            return '';

        $sRssId = 'forum|' . $this->_oConfig->getUri() . '|' . rawurlencode($this->aDataEntry[$this->_oDb->_sFieldUri]);
        return '<div class="RSSAggrCont" rssid="' . $sRssId . '" rssnum="8" member="' . getLoggedId() . '">' . $GLOBALS['oFunctions']->loadingBoxInline() . '</div>';
    }

    function _blockInfo ($aData, $sFields = '', $sLocation = '')
    {
        $aAuthor = getProfileInfo($aData['author_id']);

        $aVars = array (
            'date' => getLocaleDate($aData['created'], CH_WSB_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created'], false),
            'cats' => $this->_oTemplate->parseCategories($aData['categories']),
            'tags' => $this->_oTemplate->parseTags($aData['tags']),
            'fields' => $sFields,
            'author_unit' => $GLOBALS['oFunctions']->getMemberThumbnail($aAuthor['ID'], 'none', true),
            'location' => $sLocation,
        );
        return $this->_oTemplate->parseHtmlByName('entry_view_block_info', $aVars);
    }

    function _blockPhoto (&$aReadyMedia, $iAuthorId, $sPrefix = false)
    {
        if (!$aReadyMedia)
            return '';

        $aImages = array ();

        foreach ($aReadyMedia as $iMediaId) {

            $a = array ('ID' => $iAuthorId, 'Avatar' => $iMediaId);

            $aImageFile = ChWsbService::call('photos', 'get_image', array($a, 'file'), 'Search');
            if ($aImageFile['no_image'])
                continue;

            $aImageIcon = ChWsbService::call('photos', 'get_image', array($a, 'icon'), 'Search');
            if ($aImageIcon['no_image'])
                continue;

            $aImages[] = array (
                'icon_url' => $aImageIcon['file'],
                'image_url' => $aImageFile['file'],
                'title' => $aImageIcon['title'],
            );
        }

        if (!$aImages)
            return '';

        return $GLOBALS['oFunctions']->genGalleryImages($aImages);
    }

    function _blockVideo ($aReadyMedia, $iAuthorId, $sPrefix = false)
    {
        if (!$aReadyMedia)
            return '';

        $aVars = array (
            'title' => false,
            'prefix' => $sPrefix ? $sPrefix : 'id'.time().'_'.rand(1, 999999),
            'default_height' => getSettingValue('video', 'player_height'),
            'ch_repeat:videos' => array (),
            'ch_repeat:icons' => array (),
        );

        foreach ($aReadyMedia as $iMediaId) {

            $a = ChWsbService::call('videos', 'get_video_array', array($iMediaId), 'Search');
            $a['ID'] = $iMediaId;

            $aVars['ch_repeat:videos'][] = array (
                'style' => false === $aVars['title'] ? '' : 'display:none;',
                'id' => $iMediaId,
                'video' => ChWsbService::call('videos', 'get_video_concept', array($a), 'Search'),
            );
            $aVars['ch_repeat:icons'][] = array (
                'id' => $iMediaId,
                'icon_url' => $a['file'],
                'title' => $a['title'],
            );
            if (false === $aVars['title'])
                $aVars['title'] = $a['title'];
        }

        if (!$aVars['ch_repeat:icons'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_videos', $aVars);
    }

    function _blockFiles ($aReadyMedia, $iAuthorId = 0)
    {
        if (!$aReadyMedia)
            return '';

        $aVars = array (
            'ch_repeat:files' => array (),
        );

        foreach ($aReadyMedia as $iMediaId) {

            $a = ChWsbService::call('files', 'get_file_array', array($iMediaId), 'Search');
            if (!$a['date'])
                continue;

            ch_import('ChTemplFormView');
            $oForm = new ChTemplFormView(array());

            $aInputBtnDownload = array (
                'type' => 'submit',
                'name' => 'download',
                'value' => _t ('_download'),
                'attrs' => array(
                    'class' => 'ch-btn-small',
                    'onclick' => "window.open ('" . CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . "download/".$this->aDataEntry[$this->_oDb->_sFieldId]."/{$iMediaId}','_self');",
                ),
            );

            $aVars['ch_repeat:files'][] = array (
                'id' => $iMediaId,
                'title' => $a['title'],
                'icon' => $a['file'],
                'date' => defineTimeInterval($a['date']),
                'btn_download' => $oForm->genInputButton ($aInputBtnDownload),
            );
        }

        if (!$aVars['ch_repeat:files'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_files', $aVars);
    }

    function _blockSound ($aReadyMedia, $iAuthorId, $sPrefix = false)
    {
        if (!$aReadyMedia)
            return '';

        $aVars = array (
            'title' => false,
            'prefix' => $sPrefix ? $sPrefix : 'id'.time().'_'.rand(1, 999999),
            'default_height' => 350,
            'ch_repeat:sounds' => array (),
            'ch_repeat:icons' => array (),
        );

        foreach ($aReadyMedia as $iMediaId) {

            $a = ChWsbService::call('sounds', 'get_music_array', array($iMediaId, 'browse'), 'Search');
            $a['ID'] = $iMediaId;

            $aVars['ch_repeat:sounds'][] = array (
                'style' => false === $aVars['title'] ? '' : 'display:none;',
                'id' => $iMediaId,
                'sound' => ChWsbService::call('sounds', 'get_sound_concept', array($a), 'Search'),
            );
            $aVars['ch_repeat:icons'][] = array (
                'id' => $iMediaId,
                'icon_url' => $a['file'],
                'title' => $a['title'],
            );
            if (false === $aVars['title'])
                $aVars['title'] = $a['title'];
        }

        if (!$aVars['ch_repeat:icons'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_sounds', $aVars);
    }

    function _blockFans($iPerPage, $sFuncIsAllowed = 'isAllowedViewFans', $sFuncGetFans = 'getFans')
    {
        if (!$this->_oMain->$sFuncIsAllowed($this->aDataEntry))
            return '';

        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->$sFuncGetFans($aProfiles, $this->aDataEntry[$this->_oDb->_sFieldId], true, $iStart, $iPerPage);
        if (!$iNum || !$aProfiles)
            return MsgBox(_t("_Empty"));

        ch_import('ChTemplSearchProfile');
        $oChTemplSearchProfile = new ChTemplSearchProfile();
        $sMainContent = '';
        foreach ($aProfiles as $aProfile) {
            $sMainContent .= $oChTemplSearchProfile->displaySearchUnit($aProfile, array ('ext_css_class' => 'ch-def-margin-sec-top-auto'));
        }
        $ret .= $sMainContent;
        $ret .= '<div class="clear_both"></div>';

        $oPaginate = new ChWsbPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $iNum,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . ch_append_url_params(CH_WSB_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . "view/" . $this->aDataEntry[$this->_oDb->_sFieldUri], 'page={page}&per_page={per_page}') . '\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate('', -1, -1, false);

        return array($ret, array(), $sAjaxPaginate);
    }

    function _blockFansUnconfirmed($iFansLimit = 1000)
    {
        if (!$this->_oMain->isEntryAdmin($this->aDataEntry))
            return '';

        $aProfiles = array ();
        $iNum = $this->_oDb->getFans($aProfiles, $this->aDataEntry[$this->_oDb->_sFieldId], false, 0, $iFansLimit);
        if (!$iNum)
            return MsgBox(_t('_Empty'));

        $sActionsUrl = ch_append_url_params(CH_WSB_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . "view/" . $this->aDataEntry[$this->_oDb->_sFieldUri], array('ajax_action' => ''));
        $aButtons = array (
            array (
                'type' => 'submit',
                'name' => 'fans_reject',
                'value' => _t('_sys_btn_fans_reject'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_unconfirmed_fans_content', '{$sActionsUrl}reject&ids=' + sys_manage_items_get_unconfirmed_fans_ids(), false, 'post'); return false;\"",
            ),
            array (
                'type' => 'submit',
                'name' => 'fans_confirm',
                'value' => _t('_sys_btn_fans_confirm'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_unconfirmed_fans_content', '{$sActionsUrl}confirm&ids=' + sys_manage_items_get_unconfirmed_fans_ids(), false, 'post'); return false;\"",
            ),
        );
        ch_import ('ChTemplSearchResult');
        $sControl = ChTemplSearchResult::showAdminActionsPanel('sys_manage_items_unconfirmed_fans', $aButtons, 'sys_fan_unit');
        $aVars = array(
            'suffix' => 'unconfirmed_fans',
            'content' => $this->_oMain->_profilesEdit($aProfiles),
            'control' => $sControl,
        );
        return $this->_oMain->_oTemplate->parseHtmlByName('manage_items_form', $aVars);
    }
}
