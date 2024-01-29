<?php

/**
 * Cheetah - Social Network Software Platform. Copyright (c) Dean J. Bassett Jr. - https://www.cheetahwsb.com
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

if ($_GET['orca_integration'] && preg_match('/^[0-9a-z]+$/', $_GET['orca_integration'])) {
    define('CH_ORCA_INTEGRATION', $_GET['orca_integration']);
} else {
    define('CH_ORCA_INTEGRATION', 'cheetah');
}

$aPathInfo = pathinfo(__FILE__);
require_once($aPathInfo['dirname'] . '/inc/header.inc.php');
if (!class_exists('Thing'))
    require_once($GLOBALS['gConf']['dir']['classes'] . 'Thing.php');
if (!class_exists('ThingPage'))
    require_once($GLOBALS['gConf']['dir']['classes'] . 'ThingPage.php');
if (!class_exists('Mistake'))
    require_once($GLOBALS['gConf']['dir']['classes'] . 'Mistake.php');
if (!class_exists('ChXslTransform'))
    require_once($GLOBALS['gConf']['dir']['classes'] . 'ChXslTransform.php');
if (!class_exists('ChDb'))
    require_once($GLOBALS['gConf']['dir']['classes'] . 'ChDb.php');
if (!class_exists('DbForum'))
    require_once($GLOBALS['gConf']['dir']['classes'] . 'DbForum.php');

class ChForumAlertResponse extends ChWsbAlertsResponse
{
    function response($oAlert)
    {
        global $gConf;

        $iProfileId = $oAlert->iSender;
        $iTopicId = $oAlert->iObject;

        $oDb = new DbForum();

        if ($oAlert->sAction == 'new_topic') {
            if (getParam('auto_subscribe_forum')) {
                $oDb->flag($iTopicId, getUsername($iProfileId));
            }
        }
    }
}

