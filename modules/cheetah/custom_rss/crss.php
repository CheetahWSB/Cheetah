<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../../../inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );

//require_once( CH_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $aModule['class_prefix'] . 'Module.php');
require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbModuleDb.php');
require_once( CH_DIRECTORY_PATH_MODULES . 'cheetah/custom_rss/classes/ChCRSSModule.php');

check_logged();

$oModuleDb = new ChWsbModuleDb();
$aModule = $oModuleDb->getModuleByUri('custom_rss');

$oChCRSSModule = new ChCRSSModule($aModule);

$sAction = ch_get('action');
$sCodeResult = '';

switch ($sAction) {
    case 'a':
    default:
        $sCodeResult = $oChCRSSModule->GenCustomRssBlock((int)ch_get('ID'));
        break;
}

echo $sCodeResult;
