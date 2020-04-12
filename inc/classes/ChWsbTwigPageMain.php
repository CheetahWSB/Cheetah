<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

/**
 * Base module homepage class for modules like events/groups/store
 */
class ChWsbTwigPageMain extends ChWsbPageView
{
    var $oMain;
    var $oTemplate;
    var $oConfig;
    var $oDb;
    var $sUrlStart;
    var $sSearchResultClassName;
    var $sFilterName;

    function __construct($sName, &$oMain)
    {
        $this->oMain = &$oMain;
        $this->oTemplate = $oMain->_oTemplate;
        $this->oConfig = $oMain->_oConfig;
        $this->oDb = $oMain->_oDb;
        $this->sUrlStart = CH_WSB_URL_ROOT . $this->oMain->_oConfig->getBaseUri();
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');
        parent::__construct($sName);
    }

    function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true)
    {
        ch_import ('SearchResult', $this->oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue);
        $o->aCurrent['paginate']['perPage'] = $iPerPage;
        $o->setPublicUnitsOnly($isPublicOnly);

        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('_RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => 'rss')));

        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);

        if (!($s = $o->displayResultBlock()))
            return $isPublicOnly ? array(MsgBox(_t('_Empty')), $aMenu) : '';

        $sFilter = (false !== ch_get($this->sFilterName)) ? $this->sFilterName . '=' . rawurlencode(ch_get($this->sFilterName)) . '&' : '';
        $oPaginate = new ChWsbPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->oConfig->getBaseUri() . $o->sBrowseUrl);

        return array(
            $s,
            $aMenu,
            $sAjaxPaginate,
            '');
    }

    function getBlockCode_Calendar($iBlockID, $sContent)
    {
        $aDateParams = array(0, 0);
        $sDate = ch_get('date');
        if ($sDate)
            $aDateParams = explode('/', $sDate);

        ch_import ('Calendar', $this->oMain->_aModule);
        $oCalendar = ch_instance ($this->oMain->_aModule['class_prefix'] . 'Calendar', array ((int)$aDateParams[0], (int)$aDateParams[1], $this->oDb, $this->oConfig, $this->oTemplate));

        $oCalendar->setBlockId($iBlockID);
        $oCalendar->setDynamicUrl($this->oConfig->getBaseUri() . 'home/');

        return $oCalendar->display(true);
    }

    function getBlockCode_Categories($iBlockID, $sContent)
    {
        ch_import('ChTemplCategoriesModule');
        $aParam = array('type' => $this->oMain->_sPrefix);
        $oCateg = new ChTemplCategoriesModule($aParam, _t('_categ_users'), CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'categories');
        return $oCateg->getBlockCode_Common($iBlockId, true);
    }

    function getBlockCode_Tags($iBlockID, $sContent)
    {
        ch_import('ChTemplTagsModule');
        $aParam = array('type' => $this->oMain->_sPrefix, 'orderby' => 'popular');
        $oTags = new ChTemplTagsModule($aParam, '', CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'tags');
        $aResult = $oTags->getBlockCode_All($iBlockId);
        return $aResult[0];
    }

}
