<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    require_once("../inc/header.inc.php");

    $GLOBALS['aRequest'] = explode('/', $_GET['r']);

    if ($GLOBALS['aRequest'][1] == 'admin' || $GLOBALS['aRequest'][1] == 'administration')
        $GLOBALS['iAdminPage'] = 1;

    require_once(CH_DIRECTORY_PATH_INC . "design.inc.php");
    require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbModuleDb.php');

    $sName = process_db_input(array_shift($GLOBALS['aRequest']), CH_TAGS_STRIP);

    $oDb = new ChWsbModuleDb();
    $GLOBALS['aModule'] = $oDb->getModuleByUri($sName);

    if(empty($GLOBALS['aModule']))
        ChWsbRequest::moduleNotFound($sName);
    include(CH_DIRECTORY_PATH_MODULES . $GLOBALS['aModule']['path'] . 'request.php');
