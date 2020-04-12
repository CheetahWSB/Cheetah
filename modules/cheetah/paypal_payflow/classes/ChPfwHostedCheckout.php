<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once("ChPfwPayPal.php");
require_once("ChPfwSecureToken.php");

class ChPfwHostedCheckout extends ChPfwPayPal
{
	function __construct($oDb, $oConfig, $aConfig)
	{
		$this->_sTender = 'C';

		parent::__construct($oDb, $oConfig, $aConfig);
	}

	function initializeCheckout($iPendingId, $aCartInfo)
	{
		$oSecureToken = new ChPfwSecureToken($this->_oDb, $this->_oConfig, $this->_aConfig);
		$aSecureToken = $oSecureToken->getSecureToken($iPendingId, $aCartInfo);
		if($aSecureToken === false)
			return false;

		$sRequestUrl = $this->_oConfig->getPfwEndpoint(CH_PFW_ENDPOINT_TYPE_HOSTED);
		$sRequestData = array(
			'MODE' => $this->_oConfig->getMode() == CH_PFW_MODE_LIVE ? 'LIVE' : 'TEST',
    		'SECURETOKEN' => $aSecureToken['token'],
    		'SECURETOKENID' => $aSecureToken['token_id']
    	);

		Redirect($sRequestUrl, $sRequestData, 'post');
		exit;
	}

	function finalizeCheckout(&$aData)
	{
		$this->_logInfo(__METHOD__, $aData);

		$iPending = (int)$aData['INVNUM'];
		$aPending = $this->_oDb->getPending(array('type' => 'id', 'id' => $iPending));
        if(!empty($aPending['order']) || !empty($aPending['error_code']) || !empty($aPending['error_msg']) || (int)$aPending['processed'] != 0)
            return array('code' => 0, 'message' => _t($this->_sLangsPrefix . 'err_already_processed'));

		$iResponseCode = (int)$aData['RESULT'];
		$sResponseMessage = process_db_input($aData['RESPMSG'], CH_TAGS_STRIP);

		$aResult = array(
			'code' => $iResponseCode == 0 ? 1 : 0,
			'message' => $iResponseCode == 0 ? _t($this->_sLangsPrefix . 'msg_accepted') : $sResponseMessage,
			'pending_id' => $iPending
		);

        //--- Update pending transaction ---//
        $this->_oDb->updatePending($iPending, array(
            'order' => process_db_input($aData['PPREF'], CH_TAGS_STRIP),
        	'order_ref' => process_db_input($aData['PNREF'], CH_TAGS_STRIP),
            'error_code' => $aResult['code'],
            'error_msg' => $sResponseMessage
        ));

		return $aResult;
	}
}
