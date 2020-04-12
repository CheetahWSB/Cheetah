<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('Module', $aModule);
ch_import('ChWsbPageView');

class ChPmtCartPage extends ChWsbPageView
{
    var $_oPayments;

    function __construct(&$oPayments)
    {
        parent::__construct('ch_pmt_cart');

        $this->_oPayments = &$oPayments;

        $GLOBALS['oTopMenu']->setCurrentProfileID($this->_oPayments->_iUserId);
        $GLOBALS['oTopMenu']->setCustomVar('sys_payment_module_uri', $this->_oPayments->_oConfig->getUri());
    }
    function getBlockCode_Featured()
    {
        return $this->_oPayments->getCartContent(CH_PMT_ADMINISTRATOR_ID);
    }
    function getBlockCode_Common()
    {
        return $this->_oPayments->getCartContent(CH_PMT_EMPTY_ID);
    }
}

global $_page;
global $_page_cont;
global $logged;

$iIndex = 1;
$_page['name_index']	= $iIndex;
$_page['css_name']		= array();

check_logged();

$oPayments = new ChPmtModule($aModule);
$oCartPage = new ChPmtCartPage($oPayments);
$_page_cont[$iIndex]['page_main_code'] = $oCartPage->getCode();

$oPayments->_oTemplate->addJsTranslation(array(
    '_payment_err_nothing_selected'
));
$oPayments->_oTemplate->setPageTitle(_t('_payment_pcpt_view_cart'));
PageCode($oPayments->_oTemplate);
