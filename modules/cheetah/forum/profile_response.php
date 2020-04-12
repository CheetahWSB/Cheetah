<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

if ($_GET['orca_integration'] && preg_match('/^[0-9a-z]+$/', $_GET['orca_integration'])) {
    define('CH_ORCA_INTEGRATION', $_GET['orca_integration']);
} else {
    define('CH_ORCA_INTEGRATION', 'cheetah');
}

$aPathInfo = pathinfo(__FILE__);
require_once( $aPathInfo['dirname'] . '/inc/header.inc.php' );
if (!class_exists('Thing'))
    require_once( $GLOBALS['gConf']['dir']['classes'] . 'Thing.php' );
if (!class_exists('ThingPage'))
    require_once( $GLOBALS['gConf']['dir']['classes'] . 'ThingPage.php' );
if (!class_exists('Mistake'))
    require_once( $GLOBALS['gConf']['dir']['classes'] . 'Mistake.php' );
if (!class_exists('ChXslTransform'))
    require_once( $GLOBALS['gConf']['dir']['classes'] . 'ChXslTransform.php' );
if (!class_exists('ChDb'))
    require_once( $GLOBALS['gConf']['dir']['classes'] . 'ChDb.php' );
if (!class_exists('DbForum'))
    require_once( $GLOBALS['gConf']['dir']['classes'] . 'DbForum.php' );

class ChForumProfileResponse extends ChWsbAlertsResponse
{
    function response($oAlert)
    {
        global $gConf;

        $iProfileId = $oAlert->iObject;

        if (!$iProfileId || $oAlert->sUnit != 'profile' || ('delete' != $oAlert->sAction && 'edit' != $oAlert->sAction))
            return;

        $sUsername = ('delete' == $oAlert->sAction ? $oAlert->aExtras['profile_info']['NickName'] : getUsername($iProfileId));

        if ('edit' == $oAlert->sAction && $oAlert->aExtras['OldProfileInfo']['NickName'] == $sUsername)
            return;

        $oDb = new DbForum ();

        if (isset($oAlert->aExtras['delete_spammer']) && $oAlert->aExtras['delete_spammer']) {
            $oDb->deleteUser($sUsername);
        } else {
            $sOldUsername = ('delete' == $oAlert->sAction ? $sUsername : $oAlert->aExtras['OldProfileInfo']['NickName']);
            $sNewUsername = ('delete' == $oAlert->sAction ? $gConf['anonymous'] : $sUsername);
            $oDb->renameUser($sOldUsername, $sNewUsername);
        }
    }

}
