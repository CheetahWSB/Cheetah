<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin_design.inc.php' );

if ( false != ch_get('get_phpinfo') ) {
    phpInfo();
    exit;
}

ch_import('ChWsbAdminTools');

$logged['admin'] = member_auth( 1, true, true );

$oAdmTools = new ChWsbAdminTools();
$sResult = $oAdmTools->GenCommonCode();

switch(ch_get('action')) {
    case 'audit_send_test_email':
        header('Content-type: text/html; charset=utf-8');
        echo $oAdmTools->sendTestEmail();
        exit;
    case 'perm_table':
        $sResult .= $oAdmTools->GenPermTable(true);
        break;
    case 'main_page':
        $sResult .= $oAdmTools->GenTabbedPage(true);
        break;
    default:
        $sResult .= $oAdmTools->GenTabbedPage(true);
        break;
}

//'_adm_at_title' => 'Admin Tools',
ch_import('ChTemplFormView');
$oForm = new ChTemplFormView($_page);
$iNameIndex = 9;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('common.css'),
    'header' => _t('_adm_at_title'),
    'header_text' => _t('_adm_at_title')
);

$_page_cont[$iNameIndex]['page_main_code'] = $sResult . $oForm->getCode() . adm_hosting_promo();

PageCodeAdmin();
