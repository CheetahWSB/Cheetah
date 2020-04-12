<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once("ChPfwProvider.php");

class ChPfwPayPal extends ChPfwProvider
{
	protected $_aValidationParameters;

	protected $_sTender = '';
	protected $_aCallParameters;
	protected $_aCallCredentials;

	protected $_sLangsPrefix;

	function __construct($oDb, $oConfig, $aConfig)
	{
		parent::__construct($oDb, $oConfig, $aConfig);

		$this->_sLangsPrefix = $this->_oConfig->getLangsPrefix();

		$this->_oConfig->setProvider($this->_sName);
		$this->_oConfig->setMode($this->getOption('mode'));

		$this->_initCallCredentials();
		$this->_initCallParameters();
	}

	function processResponse(&$aData)
	{
		if(empty($aData) || !is_array($aData)) {
			$this->_logError("--- An unknown error occured.");

			return $aResult = array(
				'code' => -1,
				'message' => $this->_sLangsPrefix . 'err_unknown'
			);
		}

		if((int)$aData['RESULT'] != 0) {
			$sLog .= "--- An error occured.";
			$sLog .= "\n--- Response code: " . $aData['RESULT'] . ",";
			$sLog .= "\n--- Response message: " . $aData['RESPMSG'];
			$this->_logError($sLog);

			return array(
				'code' => $aData['RESULT'],
				'message' => $aData['RESPMSG']
			);
		}

		return array(
			'code' => 0,
			'message' => $this->_sLangsPrefix . 'msg_successfully_done'
		);
	}

	protected function _initCallCredentials()
	{
	    $this->_aCallCredentials = array(
			'PARTNER' => $this->getOption('partner'),
			'VENDOR' => $this->getOption('vendor'),
			'USER' => $this->getOption('user'),
			'PWD' => $this->getOption('password'),
		);
	}

    protected function _initCallParameters($sTender = '')
	{
	    $this->_aCallParameters = array(
			'TENDER' => !empty($sTender) ? $sTender : $this->_sTender,
			'TRXTYPE' => 'S',
			'ACTION' => '',
			'VERBOSITY' => 'HIGH',
		);
	}

	protected function _executeCall()
	{
		if(!$this->_validateCallParameters())
			return false;

		$sRequestUrl = $this->_oConfig->getPfwEndpoint(CH_PFW_ENDPOINT_TYPE_CALL);
		$sRequestParams = $this->_getRequestParams();

		$rCurl = curl_init ();
		curl_setopt($rCurl, CURLOPT_URL,  $sRequestUrl);
		curl_setopt($rCurl, CURLOPT_VERBOSE, 1);
		curl_setopt($rCurl, CURLOPT_POST, true);
		curl_setopt($rCurl, CURLOPT_POSTFIELDS, $sRequestParams);
		curl_setopt($rCurl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($rCurl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($rCurl, CURLOPT_CONNECTTIMEOUT ,0);
		curl_setopt($rCurl, CURLOPT_TIMEOUT, $this->_oConfig->getTimeout());

		$sResponse = curl_exec($rCurl);
		$aResponse = $this->_decodeRequestResponse($sResponse);

		if(empty($aResponse) || !is_array($aResponse)) {
			$sLog .= "Empty response for API call(" . $this->_aCallParameters['ACTION'] . "). ";
			$this->_logError(__METHOD__, $sLog);

			return false;
		}

		if((int)$aResponse['RESULT'] != 0) {
			$sLog .= "API call(" . $this->_aCallParameters['ACTION'] . ") error.";
			$sLog .= "\nResponse code: " . $aResponse['RESULT'] . ",";
			$sLog .= "\nResponse message: " . $aResponse['RESPMSG'];
			$this->_logError(__METHOD__, $sLog);
		}

		return $aResponse;
	}

	protected function _validateCallParameters()
	{
		if(!is_array($this->_aValidationParameters) || empty($this->_aValidationParameters))
			return true;

		foreach($this->_aValidationParameters as $sName)
			if(!array_key_exists($sName, $this->_aCallParameters)) {
				$sLog .= "--- " . __METHOD__ . ": " . $sName . " is listed as a required variable and not present in the call variables.'";
				$this->_logError($sLog);

				return false;
			}

		return true;
	}

	protected function _getRequestParams()
	{
		$s = '';

		foreach($this->_aCallCredentials as $sKey => $sValue)
			$s .= $sKey . '['.strlen($sValue).']=' . $sValue . '&';

		foreach($this->_aCallParameters as $sKey => $sValue)
			$s .= $sKey . '['.strlen($sValue).']=' . $sValue . '&';

		return $s;
	}

	protected function _decodeRequestResponse($sResponse)
	{
		$aResponse = array();

		$aKeys = explode('&', $sResponse);
		foreach($aKeys as $sKeyValue) {
			$aKeyValue = explode('=', $sKeyValue);
			if(isset($aKeyValue[1]))
				$aResponse[$aKeyValue[0]] = $aKeyValue[1];
		}

		return $aResponse;
	}

	protected function _logInfo()
	{
		$aArgs = func_get_args();

		$oLog = ChPfwLog::getInstance();
		call_user_func_array(array($oLog, 'logInfo'), $aArgs);
	}

	protected function _logError($mixedValue)
	{
		$aArgs = func_get_args();

		$oLog = ChPfwLog::getInstance();
		call_user_func_array(array($oLog, 'logError'), $aArgs);
	}
}
