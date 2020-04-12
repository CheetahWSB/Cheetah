<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModuleTemplate');

class ChWsbMobileTemplate extends ChWsbModuleTemplate
{
    var $_aMobileJs = array ('jquery.js');
    var $_aMobileCss = array ('default.css', 'mobile.css');

    /*
     * Constructor.
     */
    function __construct(&$oConfig, &$oDb, $sRootPath = CH_DIRECTORY_PATH_ROOT, $sRootUrl = CH_WSB_URL_ROOT)
    {
        parent::__construct($oConfig, $oDb, $sRootPath, $sRootUrl);
    }

    function addMobileCss($mixedFiles)
    {
        if (is_array($mixedFiles))
            $this->_aMobileJs = array_merge($this->_aMobileJs, $mixedFiles);
        else
            $this->_aMobileJs[] = $mixedFiles;
    }

    function addMobileJs($mixedFiles)
    {
        if (is_array($mixedFiles))
            $this->_aMobileCss = array_merge($this->_aMobileCss, $mixedFiles);
        else
            $this->_aMobileCss[] = $mixedFiles;
    }

    function pageCode ($sTitle, $isDesignBox = true, $isWrap = true)
    {
        global $_page;
        global $_page_cont;

        $GLOBALS['ChWsbTemplateJs'] = array ();
        $GLOBALS['ChWsbTemplateCss'] = array ();
        $this->addCss($this->_aMobileCss);
        $this->addJs($this->_aMobileJs);

        $sOutput = $this->pageEnd();

        if ($isDesignBox) {
            $aVars = array ('content' => $sOutput);
            $sOutput = $this->parseHtmlByName('mobile_box.html', $aVars);
        }

        if ($isWrap) {
            $aVars = array ('content' => $sOutput);
            $sOutput = $this->parseHtmlByName('mobile_page_padding.html', $aVars);
        }

        $iNameIndex = 11;
        $_page['name_index'] = $iNameIndex;
        $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
        $_page_cont[$iNameIndex]['page_main_code'] = $sOutput;

        PageCode($this);
    }

    function displayNoData($sCaption = false)
    {
        $this->displayMsg(_t('_Empty'), false, $sCaption);
    }

    function displayAccessDenied($sCaption = false)
    {
        $this->displayMsg(_t('_Access denied'), false, $sCaption);
    }

    function displayPageNotFound()
    {
        header("HTTP/1.0 404 Not Found");
        $this->displayMsg(_t('_sys_request_page_not_found_cpt'));
    }

    function displayMsg($sMsg, $bTranslateMsg = false, $sTitle = false)
    {
        $sMsg = $bTranslateMsg ? _t($sMsg) : $sMsg;
        $sTitle = $bTranslateMsg ? _t($sTitle) : $sTitle;
        echo $sMsg;
        $this->pageCode($sTitle ? $sTitle : $sMsg);
    }
}
