<?php

/**
 * This work, "Cheetah - https://cheetah.deanbassett.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . "ChWsbInstaller.php");

class ChDskInstaller extends ChWsbInstaller
{
    var $sGetDesktopUrl = "http://air.cheetah.com/desktop/";
    var $sDesktopFile = "file/desktop.air";

    function __construct($aConfig)
    {
        parent::__construct($aConfig);
        $this->_aActions['get_desktop'] = array('title' => 'Getting Desktop downloadable from cheetah.com:');
        $this->_aActions['remove_desktop'] = array('title' => 'Removing Desktop downloadable:');
    }

    function actionGetDesktop($bInstall = true)
    {
        global $sHomeUrl;

        $sTempFile = CH_DIRECTORY_PATH_MODULES . $this->_aConfig['home_dir'] . $this->sDesktopFile;

        $sData = $this->readUrl($this->sGetDesktopUrl . "index.php", array('url' => $sHomeUrl . 'XML.php'));
        if(empty($sData)) return CH_WSB_INSTALLER_FAILED;

        $fp = @fopen($sTempFile, "w");
        @fwrite($fp, $this->readUrl($this->sGetDesktopUrl . $sData));
        @fclose($fp);

        $this->readUrl($this->sGetDesktopUrl . "index.php", array("delete" => $sData));

        if(!file_exists($sTempFile) || filesize($sTempFile) == 0) return CH_WSB_INSTALLER_FAILED;
        return CH_WSB_INSTALLER_SUCCESS;
    }

    function actionRemoveDesktop($bInstall = true)
    {
        @unlink(CH_DIRECTORY_PATH_MODULES . $this->_aConfig['home_dir'] . $this->sDesktopFile);
        return CH_WSB_INSTALLER_SUCCESS;
    }

    function readUrl($sUrl, $aParams = array())
    {
        return ch_file_get_contents($sUrl, $aParams);
    }
}
