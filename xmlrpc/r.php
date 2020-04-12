<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( CH_DIRECTORY_PATH_ROOT . 'xmlrpc/ChWsbXMLRPCUtil.php' );

$sUser = ch_get('user');
$sPwd = ch_get('pwd');
$sUrl = rawurldecode(ch_get('url'));
$iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd);

if ($iId) {
    ch_login($iId);
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . CH_WSB_URL_ROOT . $sUrl);
    exit;
} else {
    $GLOBALS['oSysTemplate']->addCss('mobile.css');
    $aVars = array ('content' => $_page_cont[$_ni]['page_main_code']);
    $sOutput = $GLOBALS['oSysTemplate']->parseHtmlByName('mobile_box.html', $aVars);
    $iNameIndex = 11;
    $_page['name_index'] = $iNameIndex;
    $_page_cont[$iNameIndex]['page_main_code'] = '<div style="text-align:center;" class="ch-sys-mobile-padding">Access Denied</div>';
}

PageCode();

