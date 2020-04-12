<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('Module', $aModule);
ch_import('ChWsbPageView');

class ChPmtDetailsPage extends ChWsbPageView
{
    var $_oPayments;

    function __construct(&$oPayments)
    {
        parent::__construct('ch_pmt_details');

        $this->_oPayments = &$oPayments;

        $GLOBALS['oTopMenu']->setCurrentProfileID($this->_oPayments->_iUserId);
        $GLOBALS['oTopMenu']->setCustomVar('sys_payment_module_uri', $this->_oPayments->_oConfig->getUri());
    }
    function getBlockCode_Details()
    {
        return $this->_oPayments->getDetailsForm();
    }
}

global $_page;
global $_page_cont;
global $logged;

$iIndex = 4;
$_page['name_index']	= $iIndex;
$_page['css_name']		= array();

check_logged();

$oPayments = new ChPmtModule($aModule);
$oDetailsPage = new ChPmtDetailsPage($oPayments);
$_page_cont[$iIndex]['page_main_code'] = $oDetailsPage->getCode();

$oPayments->_oTemplate->setPageTitle(_t('_payment_pcpt_details'));
PageCode($oPayments->_oTemplate);
