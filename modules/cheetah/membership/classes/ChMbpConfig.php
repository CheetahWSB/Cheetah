<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbConfig');

class ChMbpConfig extends ChWsbConfig
{
    var $_oDb;
    var $_bDisableFreeJoin;
    var $_bEnableStandardOnPaidJoin;
    var $_bEnableCaptchaOnPaidJoin;
    var $_sCurrencySign;
    var $_sCurrencyCode;
    var $_sIconsFolder;

	var $_aJsClasses;
    var $_aJsObjects;
    var $_sAnimationEffect;
    var $_iAnimationSpeed;

    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);

        $this->_oDb = null;
        $this->_bDisableFreeJoin = false;
        $this->_bEnableStandardOnPaidJoin = true;
        $this->_bEnableCaptchaOnPaidJoin = true;
        $this->_sIconsFolder = 'media/images/membership/';

        $this->_aJsClasses = array(
        	'join' => 'ChMbpJoin',
        );
        $this->_aJsObjects = array(
        	'join' => 'oMbpJoin',
        );

		$this->_sAnimationEffect = 'fade';
	    $this->_iAnimationSpeed = 'slow';
    }
    function init(&$oDb)
    {
        $this->_oDb = &$oDb;

        $this->_bDisableFreeJoin = $this->_oDb->getParam('mbp_disable_free_join') == 'on';
        $this->_bEnableStandardOnPaidJoin = $this->_oDb->getParam('mbp_enable_standard_for_paid_join') == 'on';
        $this->_bEnableCaptchaOnPaidJoin = $this->_oDb->getParam('mbp_enable_captcha_for_paid_join') == 'on';

        ch_import('ChWsbPayments');
		$oPayment = ChWsbPayments::getInstance();

        $this->_sCurrencySign = $oPayment->getOption('default_currency_sign');
        $this->_sCurrencyCode = $oPayment->getOption('default_currency_code');
    }

    function isDisableFreeJoin()
    {
    	return $this->_bDisableFreeJoin;
    }
	function isStandardOnPaidJoin()
    {
    	return $this->_bEnableStandardOnPaidJoin;
    }
	function isCaptchaOnPaidJoin()
    {
    	return $this->_bEnableCaptchaOnPaidJoin;
    }
    function getCurrencySign()
    {
        return $this->_sCurrencySign;
    }
    function getCurrencyCode()
    {
        return $this->_sCurrencyCode;
    }
    function getIconsUrl()
    {
        return CH_WSB_URL_ROOT . $this->_sIconsFolder;
    }
    function getIconsPath()
    {
        return CH_DIRECTORY_PATH_ROOT . $this->_sIconsFolder;
    }
	function getJsClass($sType = '')
    {
        if(empty($sType))
            return $this->_aJsClasses;

        return isset($this->_aJsClasses[$sType]) ? $this->_aJsClasses[$sType] : '';
    }
    function getJsObject($sType = '')
    {
    	if(empty($sType))
            return $this->_aJsClasses;

        return isset($this->_aJsObjects[$sType]) ? $this->_aJsObjects[$sType] : '';
    }
	function getAnimationEffect()
	{
	    return $this->_sAnimationEffect;
	}
	function getAnimationSpeed()
	{
	    return $this->_iAnimationSpeed;
	}
	function getStandardDescriptor()
	{
		return MEMBERSHIP_ID_STANDARD;
	}
}
