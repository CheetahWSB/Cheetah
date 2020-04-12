<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import("ChWsbInstaller");

class ChPmtInstaller extends ChWsbInstaller
{
	protected $_sParamDefaultPayment;

    function __construct($aConfig)
    {
        parent::__construct($aConfig);

        $this->_sParamDefaultPayment = 'sys_default_payment';
    }

	function install($aParams)
    {
        $aResult = parent::install($aParams);

        if($aResult['result'] && getParam($this->_sParamDefaultPayment) == '')
        	setParam($this->_sParamDefaultPayment, $this->_aConfig['home_uri']);

        if($aResult['result'])
            ChWsbService::call($this->_aConfig['home_uri'], 'update_dependent_modules');

        return $aResult;
    }

	function uninstall($aParams)
    {
        $aResult = parent::uninstall($aParams);

        if($aResult['result'] && getParam($this->_sParamDefaultPayment) == $this->_aConfig['home_uri'])
        	setParam($this->_sParamDefaultPayment, '');

        return $aResult;
    }
}
