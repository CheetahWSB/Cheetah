<?php

/**
 * This work, "Cheetah - https://cheetah.deanbassett.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChPfwPageOrders extends ChWsbPageView
{
	protected $_oMain;
    protected $_sType;

    function __construct($sType, &$oMain)
    {
        parent::__construct('ch_pfw_orders');

        $this->_sType = $sType;
        $this->_oMain = $oMain;

        $GLOBALS['oTopMenu']->setCurrentProfileID($this->_oMain->getUserId());
        $GLOBALS['oTopMenu']->setCustomVar('sys_payment_module_uri', $this->_oMain->_oConfig->getUri());
    }

	function getBlockCode_Orders()
    {
        if(empty($this->_sType))
            $this->_sType = CH_PMT_ORDERS_TYPE_PROCESSED;

        return $this->_oMain->getOrdersBlock($this->_sType);
    }
}
