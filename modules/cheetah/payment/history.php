<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('Module', $aModule);
ch_import('ChWsbPageView');

class ChPmtHistoryPage extends ChWsbPageView
{
    var $_iVendorId;
    var $_oPayments;

    function __construct($sType, &$oPayments)
    {
        parent::__construct('ch_pmt_history');

        $this->_iVendorId = $sType == 'site' ? CH_PMT_ADMINISTRATOR_ID : CH_PMT_EMPTY_ID;
        $this->_oPayments = &$oPayments;

        $GLOBALS['oTopMenu']->setCurrentProfileID($this->_oPayments->_iUserId);
        $GLOBALS['oTopMenu']->setCustomVar('sys_payment_module_uri', $this->_oPayments->_oConfig->getUri());
    }
    function getBlockCode_History()
    {
        return $this->_oPayments->getCartHistory($this->_iVendorId);
    }
}

global $_page;
global $_page_cont;
global $logged;

$iIndex = 2;
$_page['name_index'] = $iIndex;
$_page['js_name'] = 'orders.js';
$_page['css_name'] = 'orders.css';

check_logged();

$sType = '';
if(isset($aRequest))
    $sType = process_db_input(array_shift($aRequest), CH_TAGS_STRIP);

$oPayments = new ChPmtModule($aModule);
$oHistoryPage = new ChPmtHistoryPage($sType, $oPayments);
$_page_cont[$iIndex]['page_main_code'] = $oHistoryPage->getCode();
$_page_cont[$iIndex]['more_code'] = $oPayments->getMoreWindow();
$_page_cont[$iIndex]['js_code'] = $oPayments->getExtraJs('orders');

$oPayments->_oTemplate->setPageTitle(_t('_payment_pcpt_cart_history'));
PageCode($oPayments->_oTemplate);
