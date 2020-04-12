<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( './inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin.inc.php' );

require_once( CH_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbPageView.php' );

check_logged();

$_page['name_index'] 	= 81;

$sPageName = process_pass_data( $_GET['ID'] );

$oIPV = new ChWsbPageView($sPageName);
if ($oIPV->isLoaded()) {
    $sPageTitle = htmlspecialchars($oIPV->getPageTitle());
    $_page['header'] 		= $sPageTitle;
    $_page['header_text'] 	= $sPageTitle;

    $_ni = $_page['name_index'];
    $_page_cont[$_ni]['page_main_code'] = $oIPV -> getCode();

    PageCode();
} else {
    $oSysTemplate->displayPageNotFound();
}
