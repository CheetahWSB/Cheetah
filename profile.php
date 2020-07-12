<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

define('CH_PROFILE_PAGE', 1);

require_once( 'inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'utils.inc.php' );

ch_import('ChTemplProfileView');
ch_import('ChTemplProfileGenerator');
ch_import('ChWsbInstallerUtils');

$profileID = getID( $_GET['ID'] );
$memberID = getLoggedId();

if(isBlocked($profileID, $memberID)) {
    $_page['name_index'] = 0;
    $_page_cont[0]['page_main_code'] = MsgBox(_t('_sys_txt_error_you_are_blocked'));
    PageCode();
    exit;
}

$sCodeLang = 'lang';
$sCodeTempl = $GLOBALS['oSysTemplate']->getCodeKey();
if(isset($_GET[$sCodeLang]) || isset($_GET[$sCodeTempl])) {
    $sCurrentUrl = $_SERVER['PHP_SELF'] . '?' . ch_encode_url_params($_GET, array($sCodeLang, $sCodeTempl));

    $aMatch = array();
    if(preg_match('/profile.php\?ID=([a-zA-Z0-9_-]+)(.*)/', $sCurrentUrl, $aMatch)) {
        header("HTTP/1.1 301 Moved Permanently");
        header ('Location:' . getProfileLink($profileID));
        send_headers_page_changed();
    }
}

// check profile membership, status, privacy and if it is exists
ch_check_profile_visibility($profileID, $memberID);

// make profile view alert and record profile view event
if ($profileID != $memberID) {
    require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbAlerts.php');
    $oAlert = new ChWsbAlerts('profile', 'view', $profileID, $memberID);
    $oAlert->alert();

    ch_import ('ChWsbViews');
    new ChWsbViews('profiles', $profileID);
}

$oProfile = new ChTemplProfileGenerator( $profileID );

$oProfile->oCmtsView->getExtraCss();
$oProfile->oCmtsView->getExtraJs();
$oProfile->oVotingView->getExtraJs();

$oSysTemplate->addJs('view_edit.js');

$_ni = 5;

$_page['name_index'] = $_ni;
$_page['css_name'] = array('profile_view.css', 'profile_view_tablet.css', 'profile_view_phone.css');

$p_arr  = $oProfile -> _aProfile;

$sUserInfo = $oFunctions->getUserInfo($p_arr['ID']);
if(!empty($sUserInfo))
	$sUserInfo = ': ' . htmlspecialchars_adv($sUserInfo);

$_page['header'] = process_line_output(getNickName($p_arr['ID'])) . $sUserInfo;

$oPPV = new ChTemplProfileView($oProfile, $site, $dir);
$_page_cont[$_ni]['page_main_code'] = $oPPV->getCode();
$_page_cont[$_ni]['custom_block'] = '';
$_page_cont[$_ni]['page_main_css'] = '';

// add profile customizer
if (ChWsbInstallerUtils::isModuleInstalled("profile_customize")) {
    $_page_cont[$_ni]['custom_block'] = '<div id="profile_customize_page" style="display: none;">' .
        ChWsbService::call('profile_customize', 'get_customize_block', array()) . '</div>';
    $_page_cont[$_ni]['page_main_css'] = '<style type="text/css">' .
        ChWsbService::call('profile_customize', 'get_profile_style', array($profileID)) . '</style>';
}

// Submenu actions
$iId = $profileID;
$iMemberId = $memberID;

$sTxtProfileAccountPage = _t('_sys_am_profile_account_page');
$sTxtProfileMessage = _t('_sys_am_profile_message');
$sTxtFriendAdd = _t('_sys_am_profile_friend_add');
$sTxtFriendAccept = _t('_sys_am_profile_friend_accept');
$sTxtFriendCancel = _t('_sys_am_profile_friend_cancel');

$aVars = array(
    'ID' => $iId,
    'member_id' => $iMemberId,
    'BaseUri' => CH_WSB_URL_ROOT,
    'cpt_am_profile_account_page' => $sTxtProfileAccountPage
);

if(isFriendRequest($iMemberId, $iId)) {
    $aVars['cpt_am_friend_add'] = '';
    $aVars['cpt_am_profile_message'] = $sTxtProfileMessage;
} else if(isFriendRequest($iId, $iMemberId)) {
    $aVars['cpt_am_friend_add'] = '';
    $aVars['cpt_am_friend_accept'] = $sTxtFriendAccept;
    $aVars['cpt_am_profile_message'] = '';
} else {
    $aVars['cpt_am_friend_add'] = $sTxtFriendAdd;
    $aVars['cpt_am_friend_cancel'] = $sTxtFriendCancel;
    $aVars['cpt_am_profile_message'] = $sTxtProfileMessage;
}

$GLOBALS['oTopMenu']->setCustomSubActions($aVars, 'ProfileTitle', false);

PageCode();
