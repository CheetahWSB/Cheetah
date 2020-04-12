<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$GLOBALS['iAdminPage'] = 1;

require_once(CH_DIRECTORY_PATH_INC . 'admin_design.inc.php');

ch_import('Module', $aModule);

global $_page;
global $_page_cont;
global $logged;

check_logged();

$iIndex = 9;
$_page['name_index'] = $iIndex;
$_page['header'] = _t('_payment_pcpt_admin');
$_page['css_name'] = '';

if(!@isAdmin()) {
    send_headers_page_changed();
    login_form("", 1);
    exit;
}

$oPayments = new ChPmtModule($aModule);
$aDetailsBox = $oPayments->getDetailsForm(CH_PMT_ADMINISTRATOR_ID);
$aPendingOrdersBox = $oPayments->getOrdersBlock('pending', CH_PMT_ADMINISTRATOR_ID);
$aProcessedOrdersBox = $oPayments->getOrdersBlock('processed', CH_PMT_ADMINISTRATOR_ID);

$mixedResultSettings = '';
if(isset($_POST['save']) && isset($_POST['cat'])) {
    $mixedResultSettings = $oPayments->setSettings($_POST);
}

$oPayments->_oTemplate->addAdminJs(array('orders.js'));
$oPayments->_oTemplate->addAdminCss(array('orders.css'));
$_page_cont[$iIndex]['page_main_code'] = $oPayments->getExtraJs('orders');
$_page_cont[$iIndex]['page_main_code'] .= DesignBoxAdmin(_t('_payment_bcpt_settings'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oPayments->getSettingsForm($mixedResultSettings))));
$_page_cont[$iIndex]['page_main_code'] .= DesignBoxAdmin(_t('_payment_bcpt_details'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $aDetailsBox[0])));
$_page_cont[$iIndex]['page_main_code'] .= DesignBoxAdmin(_t('_payment_bcpt_pending_orders'), $aPendingOrdersBox[0]);
$_page_cont[$iIndex]['page_main_code'] .= DesignBoxAdmin(_t('_payment_bcpt_processed_orders'), $aProcessedOrdersBox[0]);
$_page_cont[$iIndex]['page_main_code'] .= $oPayments->getMoreWindow();
$_page_cont[$iIndex]['page_main_code'] .= $oPayments->getManualOrderWindow();
PageCodeAdmin();
