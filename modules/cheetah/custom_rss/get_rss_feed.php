<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../../../inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'db.inc.php' );

$sMemberRSSSQL = "SELECT `RSSUrl` FROM `ch_crss_main` WHERE `ID`='". (int)ch_get('ID') ."' AND `Status`='active'";
$sCont = db_value( $sMemberRSSSQL );

if( !$sCont )
    exit;

$sUrl = $sCont;

header( 'Content-Type: text/xml' );
readfile( $sUrl );
