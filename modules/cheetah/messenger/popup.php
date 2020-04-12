<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../../../inc/header.inc.php');
require_once( CH_DIRECTORY_PATH_INC . 'admin.inc.php');
require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbModuleDb.php');
require_once( CH_DIRECTORY_PATH_MODULES . 'cheetah/messenger/classes/ChMsgModule.php');

$iSndId = isset($_COOKIE['memberID']) ? (int)$_COOKIE['memberID'] : 0;
$sSndPassword = isset($_COOKIE['memberPassword']) ? $_COOKIE['memberPassword'] : '';
$iRspId = isset($_GET['rspId']) ? (int)$_GET['rspId'] : 0;

$oModuleDb = new ChWsbModuleDb();
$aModule = $oModuleDb->getModuleByUri('messenger');

$oMessenger = new ChMsgModule($aModule);
echo $oMessenger->getMessenger($iSndId, $sSndPassword, $iRspId);
