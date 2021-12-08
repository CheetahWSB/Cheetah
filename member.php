<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

define('CH_MEMBER_PAGE', 1);

define('CH_LOGIN_BY_ID', true);
define('CH_LOGIN_BY_NICK', true);
define('CH_LOGIN_BY_EMAIL', true);

require_once('inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'utils.inc.php');

ch_import('ChTemplAccountView');

// See if administrator is logging in as another member.
if(isset($_GET['loginas']) && $_GET['loginas'] == 'true' && isAdmin()) {
    $iMemberId = (int)$_GET['id'];
    $iCurMemberId = getLoggedId();
    if($iMemberId) {
        $aLoginData = $GLOBALS['MySQL']->getRow("SELECT `ID`, `NickName`, `Password` FROM `Profiles` WHERE `ID`= '$iMemberId' LIMIT 1");
        if($aLoginData) {
            // Logs admin in as the member id requested.
            $satoken = md5(microtime());
            $sPassword = $aLoginData['Password'];
            $aUrl = parse_url($GLOBALS['site']['url']);
            $sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';
            setcookie("memberID", $iMemberId, 0, $sPath, '');
            $_COOKIE['memberID'] = $iMemberId;
            setcookie("memberPassword", $sPassword, 0, $sPath, '', false, true);
            $_COOKIE['memberPassword'] = $sPassword;
            setcookie("satoken", $satoken, 0, $sPath, '');
            $_COOKIE['satoken'] = $satoken;
            $GLOBALS['MySQL']->query("INSERT INTO `sys_sa_tokens` SET `token`='$satoken', `memid`='$iCurMemberId'");
            // Admin now set as specified member. Reload the profile page.
            echo '<script>window.location.replace("' . CH_WSB_URL_ROOT . $aLoginData['NickName'] . '");</script>';
        }
    }
    exit;
}

// See if administrator is switching back to admin account.
if(isset($_GET['loginas']) && $_GET['loginas'] == 'admin' && isset($_COOKIE['satoken'])) {
    $satoken = $_COOKIE['satoken'];
    $iMemberId = $GLOBALS['MySQL']->getOne("SELECT `memid` FROM `sys_sa_tokens` WHERE `token`= '$satoken' LIMIT 1");
    $aLoginData = $GLOBALS['MySQL']->getRow("SELECT `ID`, `NickName`, `Password` FROM `Profiles` WHERE `ID`= '$iMemberId' LIMIT 1");
    if($aLoginData) {
        // Switch the account back to admin.
        $aUrl = parse_url($GLOBALS['site']['url']);
        $sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';
        setcookie("memberID", $iMemberId, 0, $sPath, '');
        $_COOKIE['memberID'] = $iMemberId;
        setcookie("memberPassword", $aLoginData['Password'], 0, $sPath, '', false, true);
        $_COOKIE['memberPassword'] = $aLoginData['Password'];
        setcookie("satoken", '', time() - 1000);
        unset($_COOKIE['satoken']);
        $GLOBALS['MySQL']->query("DELETE FROM `sys_sa_tokens` WHERE `token` = '$satoken'");
        // Switched back to admin. Load member.php for admin.
        echo '<script>window.location.replace("' . CH_WSB_URL_ROOT . 'member.php");</script>';
    } else {
        echo 'Could not switch back to admin.';
    }
    exit;
}

// --------------- page variables and login
$_page['name_index'] = 81;
$_page['css_name'] = array(
    'member_panel.css',
    'categories.css',
    'explanation.css'
);

$_page['header'] = _t("_My Account");

// --------------- GET/POST actions

$member['ID']	    = process_pass_data(empty($_POST['ID']) ? '' : $_POST['ID']);
$member['Password'] = process_pass_data(empty($_POST['Password']) ? '' : $_POST['Password']);

$bAjxMode = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? true : false;

if (!(isset($_POST['ID']) && $_POST['ID'] && isset($_POST['Password']) && $_POST['Password'])
    && ((!empty($_COOKIE['memberID']) &&  $_COOKIE['memberID']) && $_COOKIE['memberPassword'])) {
    if (!($logged['member'] = member_auth(0, false))) {
        login_form(_t("_LOGIN_OBSOLETE"), 0, $bAjxMode);
    }
} else {
    if (!isset($_POST['ID']) && !isset($_POST['Password'])) {

        // this is dynamic page -  send headers to not cache this page
        send_headers_page_changed();

        login_form('', 0, $bAjxMode);
    } else {
        require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbAlerts.php');
        $oZ = new ChWsbAlerts('profile', 'before_login', 0, 0, array('login' => $member['ID'], 'password' => $member['Password'], 'ip' => getVisitorIP()));
        $oZ->alert();

        if(!CH_LOGIN_BY_ID) {
            // Do not allow logins by ID.
            if(ctype_digit($member['ID'])) {
                echo 'NoLoginById';
                exit;
            }
        }

        if(!CH_LOGIN_BY_NICK) {
            // Do not allow logins by nickname.
            $sNickName = $GLOBALS['MySQL']->getOne("SELECT `NickName` FROM `Profiles` WHERE `NickName`= ? LIMIT 1", [$member['ID']]);
            if($sNickName == $member['ID']) {
                echo 'NoLoginByNick';
                exit;
            }
        }

        if(!CH_LOGIN_BY_EMAIL) {
            // Do not allow logins by email.
            if(filter_var($member['ID'], FILTER_VALIDATE_EMAIL)) {
                echo 'NoLoginByEmail';
                exit;
            }
        }

        $member['ID'] = getID($member['ID']);

        // Ajaxy check
        if ($bAjxMode) {
            $r = check_password($member['ID'], $member['Password'], CH_WSB_ROLE_MEMBER, false) ? 'OK' : 'Fail';
            $e = 'Unknown Error';
            if($r == 'Fail') {
                $aProfile = getProfileInfo($member['ID']);
                if(!$aProfile) {
                    $e = 'Invalid Username';
                } else {
                    if (strcmp($aProfile['Password'], $member['Password']) !== 0) {
                        $e = 'Invalid Password';
                    }
                }
            } else {
                $e = 'OK';
            }
            echo $e;
            exit;
        }

        // Check if ID and Password are correct (addslashes already inside)
        if (check_password($member['ID'], $member['Password'])) {
            $p_arr = ch_login($member['ID'], (bool)$_POST['rememberMe']);

            ch_member_ip_store($p_arr['ID']);

            if (isAdmin($p_arr['ID'])) {
                $iId = (int)$p_arr['ID'];
            }
            $sRelocate = ch_get('relocate');
            if (!$sUrlRelocate = $sRelocate or $sRelocate == $site['url'] or basename($sRelocate) == 'join.php' or 0 !== mb_stripos($sRelocate, CH_WSB_URL_ROOT)) {
                $sUrlRelocate = CH_WSB_URL_ROOT . 'member.php';
            }

            $_page['name_index'] = 150;
            $_page['css_name'] = '';

            $_ni = $_page['name_index'];
            $_page_cont[$_ni]['page_main_code'] = MsgBox(_t('_Please Wait'));
            $_page_cont[$_ni]['url_relocate'] = ch_js_string($sUrlRelocate);

            if (isAdmin($p_arr['ID']) && !in_array($iCode, array(0, -1))) {
                Redirect($site['url_admin'], array('ID' => $member['ID'], 'Password' => $member['Password'], 'rememberMe' => $_POST['rememberMe'], 'relocate' => $sUrlRelocate), 'post');
            }
            PageCode();
        }
        exit;
    }
}
/* ------------------ */

$member['ID'] = getLoggedId();
$member['Password'] = getLoggedPassword();

$_ni = $_page['name_index'];

// --------------- [END] page components

// --------------- page components functions

// this is dynamic page -  send headers to do not cache this page
send_headers_page_changed();
$oAccountView = new ChTemplAccountView($member['ID'], $site, $dir);
$_page_cont[$_ni]['page_main_code'] = $oAccountView->getCode();

// Submenu actions
$aVars = array(
    'ID' => $member['ID'],
    'BaseUri' => CH_WSB_URL_ROOT,
    'cpt_am_account_profile_page' => _t('_sys_am_account_profile_page')
);

$GLOBALS['oTopMenu']->setCustomSubActions($aVars, 'AccountTitle', false);

PageCode();
