<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import ('ChWsbModuleTemplate');

/**
 * Base template class for modules like events/groups/store
 */
class ChWsbTwigTemplate extends ChWsbModuleTemplate
{
    var $_iPageIndex = 13;
    var $_oMain = null;

    function __construct(&$oConfig, &$oDb, $sRootPath = CH_DIRECTORY_PATH_ROOT, $sRootUrl = CH_WSB_URL_ROOT)
    {
        parent::__construct($oConfig, $oDb, $sRootPath, $sRootUrl);

        if (isset($GLOBALS['oAdmTemplate']))
            $GLOBALS['oAdmTemplate']->addDynamicLocation($this->_oConfig->getHomePath(), $this->_oConfig->getHomeUrl());
    }

    // ======================= common functions

    function addCssAdmin ($sName)
    {
        if (empty($GLOBALS['oAdmTemplate']))
            return;
        $GLOBALS['oAdmTemplate']->addCss ($sName);
    }

    function addJsAdmin ($sName)
    {
        if (empty($GLOBALS['oAdmTemplate']))
            return;
        $GLOBALS['oAdmTemplate']->addJs ($sName);
    }

    function parseHtmlByName ($sName, $aVariables, $mixedKeyWrapperHtml = null, $sCheckIn = CH_WSB_TEMPLATE_CHECK_IN_BOTH)
    {
        return parent::parseHtmlByName ($sName . (strlen($sName) < 6 || substr_compare($sName, '.html', -5, 5) !== 0 ? '.html' : ''), $aVariables);
    }

    // ======================= page generation functions

    function pageCode ($sTitle, $isDesignBox = true, $isWrap = true)
    {
        global $_page;
        global $_page_cont;

        $_page['name_index'] = $isDesignBox ? 0 : $this->_iPageIndex;

        $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
        $_page['header_text'] = $sTitle;

        $_page_cont[$_page['name_index']]['page_main_code'] = $this->pageEnd();
        if ($isWrap) {
            $aVars = array (
                'content' => $_page_cont[$_page['name_index']]['page_main_code'],
            );
            $_page_cont[$_page['name_index']]['page_main_code'] = $this->parseHtmlByName('default_padding', $aVars);
        }

        $GLOBALS['oSysTemplate']->addDynamicLocation($this->_oConfig->getHomePath(), $this->_oConfig->getHomeUrl());
        PageCode($GLOBALS['oSysTemplate']);
    }

	// New function to allow title and header to be set separately
    function pageCodeWithHeader ($sTitle, $sHeader = '', $isDesignBox = true, $isWrap = true)
    {
        global $_page;
        global $_page_cont;

        $_page['name_index'] = $isDesignBox ? 0 : $this->_iPageIndex;

		if($sHeader == '') $sHeader = $sTitle;

        $_page['header'] = $sHeader ? $sHeader : $GLOBALS['site']['title'];
        $_page['header_text'] = $sTitle;

        $_page_cont[$_page['name_index']]['page_main_code'] = $this->pageEnd();
        if ($isWrap) {
            $aVars = array (
                'content' => $_page_cont[$_page['name_index']]['page_main_code'],
            );
            $_page_cont[$_page['name_index']]['page_main_code'] = $this->parseHtmlByName('default_padding', $aVars);
        }

        $GLOBALS['oSysTemplate']->addDynamicLocation($this->_oConfig->getHomePath(), $this->_oConfig->getHomeUrl());
        PageCode($GLOBALS['oSysTemplate']);
    }


    function adminBlock ($sContent, $sTitle, $aMenu = array(), $sBottomItems = '', $iIndex = 1)
    {
        return DesignBoxAdmin($sTitle, $sContent, $aMenu, $sBottomItems, $iIndex);
    }

    function pageCodeAdmin ($sTitle)
    {
        global $_page;
        global $_page_cont;

        $_page['name_index'] = 9;

        $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
        $_page['header_text'] = $sTitle;

        $_page_cont[$_page['name_index']]['page_main_code'] = $this->pageEnd();

        PageCodeAdmin();
    }

    // ======================= tags/cat parsing functions

    function parseTags ($s)
    {
        return $this->_parseAnything ($s, ',', CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/tag/');
    }

    function parseCategories ($s)
    {
        ch_import ('ChWsbCategories');
        return $this->_parseAnything ($s, CATEGORIES_DIVIDER, CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/category/');
    }

    // ======================= display standard pages functions

    function displayAccessDenied ()
    {
        $this->pageStart();
        echo MsgBox(_t('_Access denied'));
        $this->pageCode (_t('_Access denied'), true, false);
    }

    function displayNoData ()
    {
        $this->pageStart();
        echo MsgBox(_t('_Empty'));
        $this->pageCode (_t('_Empty'), true, false);
    }

    function displayErrorOccured ()
    {
        $this->pageStart();
        echo MsgBox(_t('_Error Occured'));
        $this->pageCode (_t('_Error Occured'), true, false);
    }

    function displayPageNotFound ()
    {
        header("HTTP/1.0 404 Not Found");
        $this->pageStart();
        echo MsgBox(_t('_sys_request_page_not_found_cpt'));
        $this->pageCode (_t('_sys_request_page_not_found_cpt'), true, false);
    }

    function displayMsg ($s, $isTranslate = false)
    {
        $this->pageStart();
        echo MsgBox($isTranslate ? _t($s) : $s);
        $this->pageCode ($isTranslate ? _t($s) : $s, true);
    }

}
