<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbCron.php');
require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbAlerts.php');
require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbCategories.php');
require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbAlbums.php');

global $sModule;
$sModule = "video";

global $sIncPath;
global $sModulesPath;

require_once($sIncPath . "constants.inc.php");
require_once($sIncPath . "db.inc.php");
require_once($sIncPath . "xml.inc.php");
require_once($sIncPath . "functions.inc.php");
require_once($sIncPath . "apiFunctions.inc.php");
require_once($sIncPath . "customFunctions.inc.php");

global $sFilesPath;
$sModuleIncPath = $sModulesPath . $sModule . "/inc/";
require_once($sModuleIncPath . "header.inc.php");
require_once($sModuleIncPath . "constants.inc.php");
require_once($sModuleIncPath . "functions.inc.php");
require_once($sModuleIncPath . "customFunctions.inc.php");

class ChWsbCronVideo extends ChWsbCron
{
    function processing()
    {
        global $sModule;
        global $sFfmpegPath;
        global $sModulesPath;
        global $sFilesPath;

        //$iFilesCount = getSettingValue($sModule, "processCount");
        $iFilesCount = getParam('processCount');
        if(!is_numeric($iFilesCount)) $iFilesCount = 2;
        //$iFailedTimeout = getSettingValue($sModule, "failedTimeout");
        $iFailedTimeout = getParam('failedTimeout');
        if(!is_numeric($iFailedTimeout)) $iFailedTimeout = 1;
        $iFailedTimeout *= 86400;
        $sDbPrefix = DB_PREFIX . ucfirst($sModule);

        $iCurrentTime = time();

        do {
            //remove all tokens older than 10 minutes
            if (!getResult("DELETE FROM `" . $sDbPrefix . "Tokens` WHERE `Date`<'" . ($iCurrentTime - 600). "'"))
                break;

            if (!getResult("UPDATE `" . $sDbPrefix . "Files` SET `Date`='" . $iCurrentTime . "', `Status`='" . STATUS_FAILED . "' WHERE `Status`='" . STATUS_PROCESSING . "' AND `Date`<'" . ($iCurrentTime - $iFailedTimeout) . "'"))
                break;
            $rResult = getResult("SELECT * FROM `" . $sDbPrefix . "Files` WHERE `Status`='" . STATUS_PENDING . "' ORDER BY `ID` LIMIT " . $iFilesCount);
            if (!$rResult)
                break;
            for($i=0; $i<$rResult->rowCount(); $i++) {
                $aFile = $rResult->fetch();
                if(convertVideo($aFile['ID'])) {
                    $sType = 'ch_videos';
                    //album counter & cover update
                    //if(getSettingValue($sModule, "autoApprove") == TRUE_VAL) {
                    if(getParam('videoAutoApprove') == 'on') {
                        $oAlbum = new ChWsbAlbums($sType);
                        $oAlbum->updateObjCounterById($aFile['ID']);
                        if (getParam($oAlbum->sAlbumCoverParam) == 'on')
                            $oAlbum->updateLastObjById($aFile['ID']);
                    }
                    //tags & categories parsing
                    $oTag = new ChWsbTags();
                    $oTag->reparseObjTags($sType, $aFile['ID']);

                    $oCateg = new ChWsbCategories($aFile['Owner']);
                    $oCateg->reparseObjTags($sType, $aFile['ID']);
                } else
                    if(!getResult("UPDATE `" . $sDbPrefix . "Files` SET `Status`='" . STATUS_FAILED . "' WHERE `ID`='" . $aFile['ID'] . "'"))
                        break;
            }
        } while(false);
    }
}
