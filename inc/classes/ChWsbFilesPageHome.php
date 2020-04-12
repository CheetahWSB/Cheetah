<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');
ch_import('ChWsbPrivacy');

class ChWsbFilesPageHome extends ChWsbPageView
{
    var $oModule;
    var $oDb;
    var $oConfig;
    var $oTemplate;
    var $oSearch;

    var $aVisible = array();

    function __construct (&$oShared)
    {
        parent::__construct($oShared->_oConfig->getMainPrefix() . '_home');
        $this->oModule = $oShared;
        $this->oDb = $oShared->_oDb;
        $this->oConfig = $oShared->_oConfig;
        $this->oTemplate = $oShared->_oTemplate;
        ch_import('Search', $this->oModule->_aModule);
        $sClassSearch = $this->oConfig->getClassPrefix() . 'Search';
        $this->oSearch = new $sClassSearch();
        $this->aVisible[] = CH_WSB_PG_ALL;
        if ($this->iMemberID)
            $this->aVisible[] = CH_WSB_PG_MEMBERS;
        $this->oSearch->aCurrent['restriction']['allow_view']['value'] = $this->aVisible;
        $this->oSearch->aCurrent['restriction']['activeStatus']['value'] = 'approved';
        $this->oSearch->aCurrent['restriction']['album_status']['value'] = 'active';
    }

    function getBlockCode_All ($id)
    {
        $this->oSearch->clearFilters(array('activeStatus', 'allow_view', 'album_status', 'albumType', 'ownerStatus'), array('albumsObjects', 'albums'));
        $this->oSearch->aCurrent['paginate']['perPage'] = (int)$this->oConfig->getGlParam('number_home');
        $this->oSearch->aCurrent['view'] = 'full';
        if (isset($this->oSearch->aCurrent['rss']))
            $this->oSearch->aCurrent['rss']['link'] = $this->oSearch->getCurrentUrl('browseAll', 0, '');

        $sCode = $this->oSearch->displayResultBlock();
        if ($this->oSearch->aCurrent['paginate']['totalNum'] > 0) {
            $sCode = $this->wrapUnits($sCode);

            $aExclude = array('r');
            $sMode = isset($_GET[$this->oConfig->getMainPrefix() . '_mode']) ? '&_' . $this->oConfig->getMainPrefix() . '_mode=' . rawurlencode($_GET['ch_' . $this->oConfig->getUri() . '_mode']) : '';
            $sLink  = CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'home/';
            $aLinkAddon = $this->oSearch->getLinkAddByPrams($aExclude);
            $oPaginate = new ChWsbPaginate(array(
                'page_url' => $sLink,
                'count' => $this->oSearch->aCurrent['paginate']['totalNum'],
                'per_page' => $this->oSearch->aCurrent['paginate']['perPage'],
                'page' => $this->oSearch->aCurrent['paginate']['page'],
                'on_change_page' => 'return !loadDynamicBlock(' . $id . ', \'' . $sLink . $sMode . $aLinkAddon['params'] . '&page={page}&per_page={per_page}\');',
                'on_change_per_page' => 'return !loadDynamicBlock(' . $id . ', \'' . $sLink . $sMode . $aLinkAddon['params'] . '&page=1&per_page=\' + this.value);'
            ));
            $aTopMenu = $this->oSearch->getTopMenu(array($this->oConfig->getMainPrefix() . '_mode'));
            $sPaginate = $oPaginate->getPaginate();
        } else {
            $sCode = MsgBox(_t("_Empty"));
            $aTopMenu = array();
            $sPaginate = '';
        }
        return array($sCode, $aTopMenu, $sPaginate, true);
    }

    function getBlockCode_Albums ()
    {
        $this->oSearch->clearFilters(array('activeStatus', 'allow_view', 'album_status', 'albumType', 'ownerStatus'), array('albumsObjects', 'albums'));
        $aAlbumParams = array(
            'allow_view' => $this->aVisible,
            'obj_count' => array('min' => (int)$this->oConfig->getGlParam('number_albums_public_objects') - 1)
        );
        $aCustom = array(
            'paginate_url' => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'home',
            'simple_paginate_url' => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'albums/browse/all',
        );
        $aCode = $this->oSearch->getAlbumsBlock(array(), $aAlbumParams, $aCustom);
        if ($this->oSearch->aCurrent['paginate']['totalAlbumNum'] > 0)
            return $aCode;
        else
            return MsgBox(_t('_Empty'));
    }

    function getBlockCode_Special ()
    {
        $this->oSearch->aCurrent['restriction']['featured'] = array(
            'field' => 'Featured',
            'value' => '',
            'operator' => '=',
            'paramName' => 'featured'
        );
        $this->oSearch->aConstants['linksTempl']['featured'] = 'browse/featured';
        $aCustom = array(
            'per_page' => (int)$this->oConfig->getGlParam('number_top'),
            'menu_bottom_type' => 'featured',
            'wrapper_class' => 'result_block'
        );
        $aCode = $this->oSearch->getBrowseBlock(array('featured' => 1, 'allow_view' => $this->aVisible), $aCustom, CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'home');
        if ($this->oSearch->aCurrent['paginate']['totalNum'] > 0)
            return array($aCode['code'], $aCode['menu_top'], $aCode['menu_bottom'], '');
    }

    function getBlockCode_LatestFile ()
    {
        $this->oSearch->clearFilters(array('activeStatus', 'allow_view', 'album_status', 'albumType', 'ownerStatus'), array('albumsObjects', 'albums'));
        $this->oSearch->aCurrent['restriction']['featured'] = array(
            'field' => 'Featured',
            'value' => '1',
            'operator' => '=',
            'param' => 'featured'
        );
        $sContent = $this->oSearch->getLatestFile();
        return !empty($sContent) ? $sContent : '';
    }

    function getBlockCode_Tags($iBlockId)
    {
        ch_import('ChTemplTags');
        $oTags = new ChTemplTags();
        $oTags->getTagObjectConfig();

        return $oTags->display(
            array(
                'type' => $this->oConfig->getMainPrefix(),
                'orderby' => 'popular',
                'limit' => getParam('tags_show_limit'),
            ),
            $iBlockId, '', CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'tags'
        );
    }

    function getBlockCode_Calendar($iBlockId)
    {
        $sClassName = $this->oConfig->getClassPrefix() . 'Calendar';
        ch_import('Calendar', $this->oSearch->oModule->_aModule);
        $sDate = ch_get('date');
        if (!$sDate)
            $sDate = date("Y-m");

        list($iYear, $iMonth) = explode('/', $sDate);

        $oCalendar = new $sClassName($iYear, $iMonth, $this->oDb, $this->oTemplate, $this->oConfig);
        $oCalendar->setBlockId($iBlockId);
        $oCalendar->setDynamicUrl(CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'home/');
        return $oCalendar->display(true);
    }

    function wrapUnits($sCode, $bCenter = true, $bIndent = true, $sIndent = 'default_padding_thd.html')
    {
        if($bCenter)
            $sCode = $GLOBALS['oFunctions']->centerContent($sCode, '.sys_file_search_unit');

        if($bIndent && $sIndent != '')
            $sCode = $this->oTemplate->parseHtmlByName($sIndent, array('content' => $sCode));

        return $sCode;
    }
}
