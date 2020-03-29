<?php

/**
 * This work, "Cheetah - https://cheetah.deanbassett.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChPfwPageHistory extends ChWsbPageView
{
	protected $_oMain;
    protected $_iVendorId;

    function __construct($sType, &$oMain)
    {
        parent::__construct('ch_pfw_history');

        $this->_iVendorId = $sType == 'site' ? CH_PMT_ADMINISTRATOR_ID : CH_PMT_EMPTY_ID;
        $this->_oMain = $oMain;

        $GLOBALS['oTopMenu']->setCurrentProfileID($this->_oMain->getUserId());
        $GLOBALS['oTopMenu']->setCustomVar('sys_payment_module_uri', $this->_oMain->_oConfig->getUri());
    }

	function getBlockCode_History()
    {
        return $this->_oMain->getCartHistory($this->_iVendorId);
    }
}
