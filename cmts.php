<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( 'inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'utils.inc.php' );

check_logged();

$sSys = isset($_REQUEST['sys']) ? $_REQUEST['sys'] : '';
$sAction = isset($_REQUEST['action']) && preg_match ('/^[A-Za-z_-]+$/', $_REQUEST['action']) ? $_REQUEST['action'] : '';
$iId = (int)$_REQUEST['id'];

ch_import ('ChWsbCmts');
$aSystems = ChWsbCmts::getSystems ();

if ($sSys && $sAction && $iId && ($oCmts = ChWsbCmts::getObjectInstance($sSys, $iId, true))) {
    header('Content-Type: text/html; charset=utf-8');
    $sMethod = 'action' . $sAction;
    echo $oCmts->$sMethod();
}
