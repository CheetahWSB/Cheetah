<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

define('CH_INDEX_PAGE', 1);

if (!file_exists("inc/header.inc.php")) {
    $now = gmdate('D, d M Y H:i:s') . ' GMT';
    header("Expires: $now");
    header("Last-Modified: $now");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");

    echo "It looks like Cheetah is <b>not</b> installed.<br />\n";
    if ( file_exists( "install/index.php" ) ) {
        echo "Please, wait. Redirecting you to installation form...<br />\n";
        echo "<script language=\"Javascript\">location.href = 'install/index.php';</script>\n";
    }
    exit;
}

require_once( 'inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'prof.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'membership_levels.inc.php' );

ch_import('ChWsbPageView');
ch_import('ChWsbProfileFields');
ch_import('ChTemplFormView');
ch_import('ChTemplVotingView');
ch_import("ChTemplIndexPageView");

//-- registration by invitation only --//;
if (!empty($_GET['idFriend']) && (int)$_GET['idFriend'] && getParam('reg_by_inv_only') == 'on') {
    setcookie('idFriend', (int)$_GET['idFriend'], 0, '/');
}

check_logged();

$_page['name_index'] = 1;

$oSysTemplate->setPageTitle($site['title']);
$oSysTemplate->setPageDescription(getParam("MetaDescription"));
$oSysTemplate->setPageMainBoxTitle($site['title']);
$oSysTemplate->addPageKeywords(getParam("MetaKeyWords"));
$oSysTemplate->addCss(array('index.css'));

$oIPV = new ChTemplIndexPageView();

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = $oIPV -> getCode();

PageCode();
