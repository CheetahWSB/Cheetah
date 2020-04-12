<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once("ChPfwPayPal.php");

class ChPfwSecureToken extends ChPfwPayPal
{
	function __construct($oDb, $oConfig, $aConfig)
	{
		parent::__construct($oDb, $oConfig, $aConfig);

		unset($this->_aCallParameters['TENDER']);
		unset($this->_aCallParameters['ACTION']);
		unset($this->_aCallParameters['VERBOSITY']);
	}

	function getSecureToken($iPendingId, $aCartInfo)
	{
		$sTokenId = md5(uniqid(rand(), true));
		$this->_getSecureToken($iPendingId, $aCartInfo, $sTokenId);

		$aResponse = $this->_executeCall();
		if($aResponse === false || strcmp($sTokenId, $aResponse['SECURETOKENID']) !== 0)
			return false;

		$this->_logInfo(__METHOD__, $aResponse);
		return array(
			'token' => $aResponse['SECURETOKEN'],
			'token_id' => $aResponse['SECURETOKENID'],
		);
	}

	protected function _getSecureToken($iPendingId, $aCartInfo, $sTokenId)
	{
		$this->_aValidationParameters = array('CREATESECURETOKEN', 'SECURETOKENID', 'ERRORURL', 'CANCELURL', 'AMT');

		$this->_aCallParameters['CREATESECURETOKEN'] = 'Y';
		$this->_aCallParameters['SECURETOKENID'] = $sTokenId;

		$sUrlAddon = $this->_sName . '/' . $aCartInfo['vendor_id'] . '/';
		$this->_aCallParameters['RETURNURL'] = $this->_oConfig->getReturnUrl() . $sUrlAddon;
		$this->_aCallParameters['CANCELURL'] = $this->_oConfig->getCancelUrl();
		$this->_aCallParameters['ERRORURL'] = $this->_oConfig->getResponseUrl() . $sUrlAddon;

		$this->_aCallParameters['INVNUM'] = $iPendingId;

		$this->_aCallParameters['AMT'] = sprintf( "%.2f", (float)$aCartInfo['items_price']);
		$this->_aCallParameters['CURRENCY'] = $aCartInfo['vendor_currency_code'];
	}
}
