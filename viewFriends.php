<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbPageView.php');

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbFriendsPageView.php');
require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbPaginate.php');

ch_import('ChTemplProfileView');
ch_import('ChTemplSearchProfile');

$_page['name_index'] = 7;
$_page['css_name']   = array('browse.css');
$_page['js_name']    = 'browse_members.js';

$iProfileId = isset($_GET['iUser']) ? (int)$_GET['iUser'] : getLoggedId();
if (!$iProfileId) {
    $_page['header']                 = _t('_View friends');
    $_page['header_text']            = _t('_View friends');
    $_page['name_index']             = 0;
    $_page_cont[0]['page_main_code'] = MsgBox(_t('_Profile NA'));
    PageCode();
    exit;
}

$sPageCaption = _t('_Friends of', getNickName($iProfileId));

$_page['header']      = $sPageCaption;
$_page['header_text'] = $sPageCaption;
$_ni                  = $_page['name_index'];

// check profile membership, status, privacy and if it is exists
ch_check_profile_visibility($iProfileId, getLoggedId());

// generate page
if (isset($_GET['per_page'])) {
    $iPerPage = (int)$_GET['per_page'];
} else {
    if (isset($_GET['mode']) && $_GET['mode'] == 'extended') {
        $iPerPage = 5;
    } else {
        $iPerPage = 32;
    }
}

if ($iPerPage <= 0) {
    $iPerPage = 32;
}

if ($iPerPage > 100) {
    $iPerPage = 100;
}

$iPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($iPage <= 0) {
    $iPage = 1;
}

$aDisplayParameters = array(
    'per_page' => $iPerPage,
    'page'     => $iPage,
    'mode'     => isset($_GET['mode']) ? $_GET['mode'] : null,
    'photos'   => isset($_GET['photos_only']) ? true : false,
    'online'   => isset($_GET['online_only']) ? true : false,
    'sort'     => isset($_GET['sort']) ? $_GET['sort'] : null,
);

$oFriendsPage                       = new ChWsbFriendsPageView('friends', $aDisplayParameters, $iProfileId);
$_page_cont[$_ni]['page_main_code'] = $oFriendsPage->getCode();

PageCode();
