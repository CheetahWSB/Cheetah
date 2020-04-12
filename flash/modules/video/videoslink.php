<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$sGlobalHeader = "../global/inc/header.inc.php";
require_once("../../../inc/header.inc.php");
require_once(CH_DIRECTORY_PATH_CLASSES . "ChWsbPermalinks.php");
require_once($sGlobalHeader);
require_once($sIncPath . "db.inc.php");
require_once($sIncPath . "customFunctions.inc.php");

$sId = (int)$_GET["id"];
$oDolPermalinks = new ChWsbPermalinks();
$sNick = getValue("SELECT `NickName` FROM `Profiles` WHERE `ID`=" . $sId);
header("Location: " . $sRootURL . $oDolPermalinks->permalink("modules?r=videos/") . "albums/browse/owner/" . $sNick);
