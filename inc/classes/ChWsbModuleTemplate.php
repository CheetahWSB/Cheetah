<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('ChWsbTemplate.php');

class ChWsbModuleTemplate extends ChWsbTemplate
{
    var $_oDb;
    var $_oConfig;
    var $_bObStarted = 0;

    /*
     * Constructor.
     */
    function __construct(&$oConfig, &$oDb, $sRootPath = CH_DIRECTORY_PATH_ROOT, $sRootUrl = CH_WSB_URL_ROOT)
    {
        parent::__construct($sRootPath, $sRootUrl);

        $this->_oDb = &$oDb;
        $this->_oConfig = &$oConfig;

        $sClassPrefix = $oConfig->getClassPrefix();
        $sHomePath = $oConfig->getHomePath();
        $sHomeUrl = $oConfig->getHomeUrl();

        $this->addLocation($sClassPrefix, $sHomePath, $sHomeUrl);
        $this->addLocationJs($sClassPrefix, $sHomePath . 'js/', $sHomeUrl . 'js/');
    }
    function addAdminCss($mixedFiles, $bDynamic = false)
    {
        global $oAdmTemplate;

        $sLocationKey = $oAdmTemplate->addDynamicLocation($this->_oConfig->getHomePath(), $this->_oConfig->getHomeUrl());
        $mixedResult = $oAdmTemplate->addCss($mixedFiles, $bDynamic);
        $oAdmTemplate->removeLocation($sLocationKey);

        return $mixedResult;
    }
    function addAdminJs($mixedFiles, $bDynamic = false)
    {
        global $oAdmTemplate;

        $sLocationKey = $oAdmTemplate->addDynamicLocationJs($this->_oConfig->getHomePath() . 'js/', $this->_oConfig->getHomeUrl() . 'js/');
        $mixedResult = $oAdmTemplate->addJs($mixedFiles, $bDynamic);
        $oAdmTemplate->removeLocationJs($sLocationKey);

        return $mixedResult;
    }
    function pageCodeAdminStart()
    {
        ob_start();
    }

    function pageCodeAdmin ($sTitle)
    {
        global $_page;
        global $_page_cont;

        $_page['name_index'] = 9;

        $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
        $_page['header_text'] = $sTitle;

        $_page_cont[$_page['name_index']]['page_main_code'] = ob_get_clean();

        PageCodeAdmin();
    }
    function pageStart ()
    {
        if (0 == $this->_bObStarted)  {
            ob_start ();
            $this->_bObStarted = 1;
        }
    }
    function pageEnd ($isGetContent = true)
    {
        if (1 == $this->_bObStarted)  {
            $sRet = '';
            if ($isGetContent)
                $sRet = ob_get_clean();
            else
                ob_end_clean();
            $this->_bObStarted = 0;
            return $sRet;
        }
    }

    // ======================= tags/cat parsing functions

    function _parseAnything ($s, $sDiv, $sLinkStart, $sClassName = '')
    {
        $sRet = '';
        $a = explode ($sDiv, $s);
        $sClass = $sClassName ? 'class="'.$sClassName.'"' : '';
        foreach ($a as $sName)
            $sRet .= '<a '.$sClass.' href="' . $sLinkStart . title2uri($sName) . '">'.$sName.'</a> ';
        return $sRet;
    }
}
