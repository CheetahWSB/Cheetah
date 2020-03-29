<?php

/**
 * This work, "Cheetah - https://cheetah.deanbassett.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( CH_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $aModule['class_prefix'] . 'Module.php');

$iSndId = ( isset($_COOKIE['memberID']) && ($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) ) ? (int) $_COOKIE['memberID'] : 0;
$sSndPassword = isset($_COOKIE['memberPassword']) ? $_COOKIE['memberPassword'] : '';
$iRspId = count($aRequest) >= 1 ? array_shift($aRequest) : 0;

$oMessenger = new ChMsgModule($aModule);
echo $oMessenger->getMessenger($iSndId, $sSndPassword, $iRspId);
