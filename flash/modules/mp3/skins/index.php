<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

setlocale(LC_ALL, 'EN_US');
header('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
header("Content-Type: application/x-shockwave-flash");

require_once("../../../../inc/header.inc.php");
require_once($sIncPath . "functions.inc.php");
require_once($sIncPath . "apiFunctions.inc.php");
require_once($sIncPath . "xml.inc.php");

$aPathParts = explode("/", $_SERVER['PHP_SELF']);
$iPartsCount = count($aPathParts);
$aResult = getExtraFiles($aPathParts[$iPartsCount-3], $aPathParts[$iPartsCount-2]);
$sFile = $aResult['current'] . "." . $aResult['extension'];

readfile($sFile);
