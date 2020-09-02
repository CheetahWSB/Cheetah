<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('../inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'admin_design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'utils.inc.php');

$logged['admin'] = member_auth(1, true, true);
$iId = getLoggedId();

// Store this websites IP address.
setParam('site_ip', $_SERVER["SERVER_ADDR"]);

// This is used to manually force a licence check.
// Here for testing of licensing system. Will be removed from production.
if (isset($_GET['checklic'])) {
    if ($_GET['checklic'] == 'true') {
        echo 'Check License<br><br>';
        chCheckLicense(true, true, false);
    }
    exit;
}

if (isset($_POST['license_code']) && isset($_POST['register'])) {
    $sLicense = $_POST['license_code'];
    if ($sLicense != '') {
        setParam('license_code', $sLicense);
        setParam('license_keydata', '');
        chCheckLicense(false, true, false);
        header('Location: ' . $_SERVER['PHP_SELF']);
        die;
    }
}

if (isset($_POST['recheck'])) {
    // Recheck server and update stored license_keydata.
    chCheckLicense(false, true, false);
    header('Location: ' . $_SERVER['PHP_SELF']);
    die;
}

if (isset($_POST['reset'])) {
    // Reset license.
    setParam('license_code', '');
    setParam('license_keydata', '');
    setcookie('aun', time(), time() + 3600, "/"); // Expires in 1 hour.
    header('Location: ' . $_SERVER['PHP_SELF']);
    die;
}

$sLicense = getParam('license_code');
$sLicenseData = getParam('license_keydata');
if ($sLicenseData != '') {
    $aLicenseData = chJsonDecode($sLicenseData);
} else {
    $sLicense = '';
}

$chLicenseStatus = '<span style="color: #008000">Valid</span>';
if ((int)$aLicenseData['subscription_canceled'] == 1) {
    $chLicenseStatus = '<span style="color: #800000">Subscription Canceled</span>';
}
if ((int)$aLicenseData['suspended'] == 1) {
    $chLicenseStatus = '<span style="color: #800000">Suspended</span>';
}
if ((int)$aLicenseData['revoked'] == 1) {
    $chLicenseStatus = '<span style="color: #800000">Revoked</span>';
}
if ($aLicenseData['status'] == 'Invalid') {
    $chLicenseStatus = '<span style="color: #800000">Invalid</span>';
    $aLicenseData['site_url'] = '';
    $aLicenseData['server_ip'] = '';
    $aLicenseData['key_type'] = '';
}
if ($sLicense == '') {
    $chLicenseStatus = '<span style="color: #000080">Unregistered</span>';
}


$iIssueDate = chDateToTimestamp($aLicenseData['issue_date']);
$iExpiresDate = chDateToTimestamp($aLicenseData['expire_date']);
if ($aLicenseData['site_url'] == '') {
    $aLicenseData['site_url'] = 'Unregistered';
}
if ($aLicenseData['server_ip'] == '') {
    $aLicenseData['server_ip'] = 'Unregistered';
}
$aLicenseData['issue_date'] = date("F d, Y", $iIssueDate);
if ($aLicenseData['key_type'] == 'Permanent' || $aLicenseData['key_type'] == 'Free') {
    $aLicenseData['expire_date'] = 'Never';
} else {
    $aLicenseData['expire_date'] = date("F d, Y", $iExpiresDate);
}

$aVars = array(
    'license_status' => $chLicenseStatus,
    'license_key' => $aLicenseData['license_key'],
    'site_url' => $aLicenseData['site_url'],
    'server_ip' => $aLicenseData['server_ip'],
    'key_type' => $aLicenseData['key_type'],
    'warning' => ch_js_string(_t('_adm_license_warning')),
    'reset_warning' => ch_js_string(_t('_adm_license_reset_warning')),

    'ch_if:suspended' => array(
        'condition' => $aLicenseData['subscription_canceled'],
        'content' => array(
            'suspended_reason' => $aLicenseData['suspended_reason']
        )
    ),
    'ch_if:prepaid' => array(
        'condition' => $aLicenseData['key_type'] == 'Prepaid',
        'content' => array()
    ),

    'license' => $sLicense,
    'license_status' => $chLicenseStatus,

    'ch_if:show_unregistered' => array(
        'condition' => $sLicense == '' || (int)$aLicenseData['active'] == 0,
        'content' => array()
    ),
    'ch_if:show_permanent' => array(
        'condition' => $aLicenseData['key_type'] == 'Permanent',
        'content' => array(
            'license' => $sLicense
        )
    ),
    'ch_if:show_free' => array(
        'condition' => $aLicenseData['key_type'] == 'Free',
        'content' => array(
            'license' => $sLicense
        )
    ),
    'ch_if:show_monthly' => array(
        'condition' => $aLicenseData['key_type'] == 'Monthly',
        'content' => array(
            'license' => $sLicense
        )
    ),
    'ch_if:show_yearly' => array(
        'condition' => $aLicenseData['key_type'] == 'Yearly',
        'content' => array(
            'license' => $sLicense
        )
    ),
    'ch_if:show_auto_renew' => array(
        'condition' => $aLicenseData['key_type'] == 'Auto Renew',
        'content' => array(
            'license' => $sLicense
        )
    ),
);

if ($sLicense == '' || $aLicenseData['status'] == 'Invalid') {
    $aVars['site_url'] = 'Not available until valid key registered to site.';
    $aVars['server_ip'] = 'Not available until valid key registered to site.';
    $aVars['key_type'] = 'Not available until valid key has been purchased.';
}

$sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('license.html', $aVars);

$iNameIndex = 0;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('license.css'),
    'header' => _t('_adm_page_cpt_license'),
    'header_text' => _t('_adm_box_cpt_license')
);

$_page_cont[$iNameIndex]['page_main_code'] = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array(
    'content' => $sContent
));

PageCodeAdmin();

function chDateToTimestamp($sDate)
{
    $aDate = explode('-', $sDate);
    $iDate = mktime(0, 0, 0, $aDate[0], $aDate[1], $aDate[2]);
    return $iDate;
}
