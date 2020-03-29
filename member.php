<?php

/**
 * This work, "Cheetah - https://cheetah.deanbassett.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

define('CH_MEMBER_PAGE', 1);

require_once('inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'utils.inc.php');

ch_import('ChTemplAccountView');

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

        $member['ID'] = getID($member['ID']);

        // Ajaxy check
        if ($bAjxMode) {
            echo check_password($member['ID'], $member['Password'], CH_WSB_ROLE_MEMBER, false) ? 'OK' : 'Fail';
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
