<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$bResult = false;
$sId = (int)$_GET["id"];
$sFile = "files/" . $sId . ".m4v";

require_once("../../../inc/header.inc.php");

if(!empty($sId) && file_exists($sFile)) {
    require_once($sIncPath . "constants.inc.php");
    require_once($sIncPath . "xml.inc.php");
    require_once($sIncPath . "functions.inc.php");
    require_once($sIncPath . "apiFunctions.inc.php");
    //$bResult = getSettingValue("video_comments", "saveMobile") == TRUE_VAL;
    $bResult = 'on' == getParam('saveMobile') ? true : false;
}

if($bResult) {
    require_once($sIncPath . "functions.inc.php");
    smartReadFile($sFile, $sFile, "video/mp4");
} else
    readfile($sFileErrorPath);
