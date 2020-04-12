<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( 'inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'utils.inc.php' );

check_logged();

ch_import('ChWsbPayments');
$sUrl = $oPayment = ChWsbPayments::getInstance()->getOrdersUrl();

if(empty($sUrl))
	$oSysTemplate->displayPageNotFound();

header('Location: ' . $sUrl);
exit;
