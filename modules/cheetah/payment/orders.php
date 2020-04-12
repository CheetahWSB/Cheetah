<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('Module', $aModule);
ch_import('ChWsbPageView');

class ChPmtOrdersPage extends ChWsbPageView
{
    var $_oPayments;
    var $_sType;

    function __construct($sType, &$oPayments)
    {
        parent::__construct('ch_pmt_orders');

        $this->_sType = $sType;
        $this->_oPayments = &$oPayments;

        $GLOBALS['oTopMenu']->setCurrentProfileID($this->_oPayments->_iUserId);
        $GLOBALS['oTopMenu']->setCustomVar('sys_payment_module_uri', $this->_oPayments->_oConfig->getUri());
    }
    function getBlockCode_Orders()
    {
        if(empty($this->_sType))
            $this->_sType = CH_PMT_ORDERS_TYPE_PROCESSED;

        return $this->_oPayments->getOrdersBlock($this->_sType);
    }
}

global $_page;
global $_page_cont;
global $logged;

$iIndex = 3;
$_page['name_index'] = $iIndex;
$_page['css_name'] = 'orders.css';
$_page['js_name'] = 'orders.js';

check_logged();

$sType = '';
if(!empty($aRequest))
    $sType = process_db_input(array_shift($aRequest), CH_TAGS_STRIP);

$oPayments = new ChPmtModule($aModule);
$oOrdersPage = new ChPmtOrdersPage($sType, $oPayments);
$_page_cont[$iIndex]['page_main_code'] = $oOrdersPage->getCode();
$_page_cont[$iIndex]['more_code'] = $oPayments->getMoreWindow();
$_page_cont[$iIndex]['manual_order_code'] = $oPayments->getManualOrderWindow();
$_page_cont[$iIndex]['js_code'] = $oPayments->getExtraJs('orders');

$oPayments->_oTemplate->setPageTitle(_t('_payment_pcpt_view_orders'));
PageCode($oPayments->_oTemplate);
