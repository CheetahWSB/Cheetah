<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin.inc.php' );

ch_import('ChWsbPaginate');
ch_import('ChWsbAdminIpBlockList');

$logged['admin'] = member_auth( 1, true, true );

$oChWsbAdminIpBlockList = new ChWsbAdminIpBlockList();

$sResult = '';
switch(ch_get('action')) {
    case 'apply_delete':
        $oChWsbAdminIpBlockList->ActionApplyDelete();
        break;
}

$iNameIndex = 3;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('ip_blacklist.css'),
    'js_name' => array(),
    'header' => _t('_adm_ipbl_title'),
    'header_text' => _t('_adm_ipbl_title')
);

$aPages = array (
    'manage' => array (
        'title' => _t('_adm_txt_manage'),
        'url' => CH_WSB_URL_ADMIN . 'ip_blacklist.php?mode=manage',
        'func' => 'PageCodeManage',
        'func_params' => array(),
    ),
    'list' => array (
        'title' => _t('_adm_txt_list'),
        'url' => CH_WSB_URL_ADMIN . 'ip_blacklist.php?mode=list',
        'func' => 'PageCodeIpMembers',
        'func_params' => array(),
    ),
    'settings' => array (
        'title' => _t('_Settings'),
        'url' => CH_WSB_URL_ADMIN . 'ip_blacklist.php?mode=settings',
        'func' => 'PageCodeSettings',
        'func_params' => array(),
    ),
);

if (!isset($_GET['mode']) || !isset($aPages[$_GET['mode']]))
    $sMode = 'manage';
else
    $sMode = $_GET['mode'];

$aTopItems = array();
foreach ($aPages as $k => $r)
    $aTopItems['dbmenu_' . $k] = array(
        'href' => $r['url'],
        'title' => $r['title'],
        'active' => $k == $sMode ? 1 : 0
    );

$_page_cont[$iNameIndex]['page_main_code'] = call_user_func_array($aPages[$sMode]['func'], $aPages[$sMode]['func_params']);

PageCodeAdmin();

function PageCodeManage ()
{
    global $oChWsbAdminIpBlockList;

    $s = DesignBoxAdmin(_t('_adm_ipbl_manage'), $oChWsbAdminIpBlockList->getManagingForm(), $GLOBALS['aTopItems'], '', 11);

    $s .= DesignBoxAdmin(_t('_adm_ipbl_Type' . (int)getParam('ipListGlobalType') . '_desc'), $oChWsbAdminIpBlockList->GenIPBlackListTable(), '', '', 11);

    return $s;
}

function PageCodeIpMembers ()
{
    global $oChWsbAdminIpBlockList;

    $s = getParam('enable_member_store_ip') ? $oChWsbAdminIpBlockList->GenStoredMemIPs() : MsgBox(_t('_Empty'));

    return DesignBoxAdmin(_t('_adm_ipbl_Stored_members_caption'), $s, $GLOBALS['aTopItems'], '', 11);
}

function PageCodeSettings ()
{
    ch_import('ChWsbAdminSettings');
    $oSettings = new ChWsbAdminSettings(22);

    $sResults = false;
    if (isset($_POST['save']) && isset($_POST['cat']))
        $sResult = $oSettings->saveChanges($_POST);

    $s = $oSettings->getForm();
    if ($sResult)
        $s = $sResult . $s;

    return DesignBoxAdmin(_t('_Settings'), $s, $GLOBALS['aTopItems'], '', 11);
}
