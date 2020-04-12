<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

if(!isset($sRayHeaderPath)) $sRayHeaderPath = "modules/global/inc/header.inc.php";
if(!file_exists($sRayHeaderPath)) {
    header("Location:install/index.php");
    exit;
}

$sModule = isset($sModule) ? $sModule : $_REQUEST['module'];
$sApp = isset($sApp) ? $sApp : $_REQUEST['app'];

require_once('../inc/header.inc.php');
require_once($sIncPath . 'functions.inc.php');

if(secureCheckWidgetName($sModule) && file_exists($sRayHeaderPath) && !empty($sModule) && !empty($sApp) && secureCheckWidgetName($sApp)) {
    require_once(CH_DIRECTORY_PATH_INC . "db.inc.php");
    require_once(CH_DIRECTORY_PATH_INC . "utils.inc.php");
    require_once($sRayHeaderPath);
    require_once($sIncPath . "content.inc.php");
    require_once($sModulesPath . $sModule . "/inc/header.inc.php");
    require_once($sModulesPath . $sModule . "/inc/constants.inc.php");
} else exit;

$aParameters = Array();
foreach($aModules[$sApp]['parameters'] as $sParameter)
    $aParameters[$sParameter] = isset($$sParameter) ? $$sParameter : $_REQUEST[$sParameter];

echo getApplicationContent($sModule, $sApp, $aParameters);
