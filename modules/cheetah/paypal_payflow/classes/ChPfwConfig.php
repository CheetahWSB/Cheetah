<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_MODULES . 'cheetah/payment/classes/ChPmtConfig.php');

class ChPfwConfig extends ChPmtConfig
{
	protected $_sParentHomePath;
	protected $_sParentHomeUrl;
	protected $_sParentClassPrefix;

	protected $_sProvider;
	protected $_sMode;
	protected $_iTimeout;
	protected $_sCancelUrl;
	protected $_sResponseUrl;

	protected $_sPfwEndpointCall;
	protected $_sPfwEndpointHosted;
	protected $_sPpEndpointHosted;

	protected $_sLogPath;
	protected $_aLogFiles;
	protected $_aLogEnabled;

    function __construct($aModule)
    {
        parent::__construct($aModule);

        $sParentDirectory = 'cheetah/payment/';
        $this->_sParentHomePath = CH_DIRECTORY_PATH_MODULES . $sParentDirectory;
        $this->_sParentHomeUrl = CH_WSB_URL_MODULES . $sParentDirectory;
        $this->_sParentClassPrefix = 'ChPmt';

        $this->_sProvider = '';
        $this->_sMode = CH_PFW_MODE_LIVE;
        $this->_iTimeout = 90;
		$this->_sReturnUrl = $this->getBaseUri();
        $this->_sCancelUrl = $this->getBaseUri() . 'cart/';
        $this->_sResponseUrl = $this->getBaseUri() . 'response/';

        $this->_aPrefixes = array(
        	'general' => 'ch_pfw_',
        	'langs' => '_ch_pfw_',
        	'options' => 'ch_pfw_',
        );
        $this->_aJsClasses = array(
        	'cart' => 'ChPfwCart',
        	'cart_parent' => 'ChPmtCart',
        	'orders' => 'ChPfwOrders',
        	'orders_parent' => 'ChPmtOrders'
        );
        $this->_aJsObjects = array(
        	'cart' => 'oPfwCart',
        	'orders' => 'oPfwOrders'
        );

        $this->_sOptionsCategory = 'PayPal PayFlow Pro';

        $this->_sLogPath = $this->getHomePath() . 'log/';
        $this->_aLogFiles = array(
        	'info' => 'pp.info.log',
	        'error' => 'pp.error.log',
        );
        $this->_aLogEnabled = array(
        	'info' => 1,
	        'error' => 1,
        );
    }

	function init(&$oDb)
    {
        parent::init($oDb);

        $sOptionPrefix = $this->getOptionsPrefix();
        //TODO: init necessary settings here.
    }

	function setProvider($sProvider)
    {
    	$this->_sProvider = $sProvider;
    }
	function getProvider()
    {
    	return $this->_sProvider;
    }
    function setMode($sMode)
    {
    	$this->_sMode = $sMode;
    }
	function getMode()
    {
    	return $this->_sMode;
    }
	function getParentClassPrefix()
    {
        return $this->_sParentClassPrefix;
    }
	function getParentHomePath()
    {
        return $this->_sParentHomePath;
    }
    function getParentHomeUrl()
    {
        return $this->_sParentHomeUrl;
    }

    function getTimeout()
    {
    	return $this->_iTimeout;
    }
	function getReturnUrl($bSsl = false)
    {
    	$sResult = '';

    	switch($this->getProvider()) {
    		case CH_PFW_PROVIDER_HOSTED:
    			$sResult = $this->_sReturnUrl . 'finalize_checkout/';
    	 		break;

    		case CH_PFW_PROVIDER_EXPRESS:
    	 		$sResult = $this->_sReturnUrl . 'confirm/';
    	 		break;

    	 	case CH_PFW_PROVIDER_RECURRING:
    	 		$sResult = $this->_sReturnUrl . 'confirm/';
    	 		break;
    	}

    	$sResult = CH_WSB_URL_ROOT . $sResult;
    	if($bSsl && strpos($sResult, 'https://') === false)
    		$sResult = 'https://' . ch_ltrim_str($sResult, 'http://');

        return $sResult;
    }
	function getCancelUrl($bSsl = false)
    {
    	$sResult = CH_WSB_URL_ROOT . $this->_sCancelUrl;
    	if($bSsl && strpos($sResult, 'https://') === false)
    		$sResult = 'https://' . ch_ltrim_str($sResult, 'http://');

        return $sResult;
    }
	function getResponseUrl($bSsl = false)
    {
    	$sResult = CH_WSB_URL_ROOT . $this->_sResponseUrl;
    	if($bSsl && strpos($sResult, 'https://') === false)
    		$sResult = 'https://' . ch_ltrim_str($sResult, 'http://');

        return $sResult;
    }
    function getPfwEndpoint($sType = CH_PFW_ENDPOINT_TYPE_CALL)
    {
    	switch($this->_sMode) {
    		case CH_PFW_MODE_LIVE:
    			$sPfwEndpointCall = 'https://payflowpro.paypal.com';
				$sPfwEndpointHosted = 'https://payflowlink.paypal.com';
    			break;

    		case CH_PFW_MODE_TEST:
    			$sPfwEndpointCall = 'https://pilot-payflowpro.paypal.com';
				$sPfwEndpointHosted = 'https://pilot-payflowlink.paypal.com';
    			break;
    	}

    	$sResult = '';
    	switch($sType) {
    		case CH_PFW_ENDPOINT_TYPE_CALL;
    			$sResult = $sPfwEndpointCall;
    			break;

    		case CH_PFW_ENDPOINT_TYPE_HOSTED:
    			$sResult = $sPfwEndpointHosted;
    			break;
    	}

    	return $sResult;
    }
    function getPpEndpoint($sType = CH_PFW_ENDPOINT_TYPE_HOSTED)
    {
    	switch($this->_sMode) {
    		case CH_PFW_MODE_LIVE:
    			$sPpEndpointHosted = 'https://www.paypal.com/cgi-bin/webscr';
    			break;

    		case CH_PFW_MODE_TEST:
    			$sPpEndpointHosted = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    			break;
    	}

    	$sResult = '';
    	switch($sType) {
    		case CH_PFW_ENDPOINT_TYPE_HOSTED:
    			$sResult = $sPpEndpointHosted;
    			break;
    	}

    	return $sResult;
    }
    function isLog($sType)
    {
    	return isset($this->_aLogEnabled[$sType]) && (int)$this->_aLogEnabled[$sType] == 1;
    }
	function getLogPath()
    {
        return $this->_sLogPath;
    }
    function getLogFile($sType)
    {
    	return isset($this->_aLogFiles[$sType]) ? $this->_aLogFiles[$sType] : $this->_aLogFiles['error'];
    }
}
