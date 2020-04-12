<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once("ChPmtProvider.php");

define('BP_STATUS_NEW', 'new');
define('BP_STATUS_PAID', 'paid');
define('BP_STATUS_CONFIRMED', 'confirmed');
define('BP_STATUS_COMPLETE', 'complete');
define('BP_STATUS_EXPIRED', 'expired');
define('BP_STATUS_INVALID', 'invalid');

define('BP_SPEED_HIGH', 'high');
define('BP_SPEED_MEDIUM', 'medium');
define('BP_SPEED_LOW', 'low');

class ChPmtBitPay extends ChPmtProvider
{
    /**
     * Constructor
     */
    function __construct($oDb, $oConfig, $aConfig)
    {
        parent::__construct($oDb, $oConfig, $aConfig);
        $this->_bRedirectOnResult = false;

        $this->_initializeOptions();
    }

    function initializeCheckout($iPendingId, $aCartInfo, $bRecurring = false, $iRecurringDays = 0)
    {
    	$this->aBpOptions['redirectURL'] .= $aCartInfo['vendor_id'];

    	$this->aBpOptions['notificationURL'] .= $aCartInfo['vendor_id'];
    	$this->aBpOptions['notificationURL'] = ch_append_url_params($this->aBpOptions['notificationURL'], array('bxssl' => 1));

	    switch ($aCartInfo['vendor_currency_code']) {
			case 'USD':
            case 'EUR':
            case 'CAD':
            case 'AUD':
            case 'GBP':
            case 'MXN':
            case 'NZD':
			case 'ZAR':
			case 'BTC':
				$this->aBpOptions['currency'] = $aCartInfo['vendor_currency_code'];
		}

		$aPosData = array(
			'vnd' => (string)$aCartInfo['vendor_id'],
			'clt' => (string)$aCartInfo['client_id'],
			'pnd' => (string)$iPendingId
		);
		$aOptions = array(
			'itemDesc' => 'Payment to ' . $aCartInfo['vendor_profile_name']
		);
		$aResponse = $this->createInvoice($iPendingId, (float)$aCartInfo['items_price'], $aPosData, $aOptions);
		if(!empty($aResponse['error']))
			return _t(is_array($aResponse['error']) ? $aResponse['error']['message'] : $aResponse['error']);

		header('Location: ' . $aResponse['url']);
		exit;
    }

    function finalizeCheckout(&$aData)
    {
    	$aData = $this->_verifyNotification();
    	if($aData === false)
    		return array('code' => 2, 'message' => _t('_payment_bp_err_no_data_given'));

		if(empty($this->_aOptions) && isset($aData['posData']['d']['pnd'])) {
            $this->_aOptions = $this->getOptionsByPending($aData['posData']['d']['pnd']);
			if(empty($this->_aOptions))
            	return array('code' => 3, 'message' => _t('_payment_bp_err_no_vendor_given'));

            $this->_initializeOptions();
		}

		$aPosData = $this->_verifyPosData($aData['posData']);
		if($aPosData === false)
			return array('code' => 4, 'message' => _t('_payment_bp_err_incorrect_data'));

		//--- Update pending transaction ---//
		$sStatus = $aData['status'];
		$sMessage = '';

		$sResult = $this->_verifyAmount($aPosData, $aData['price']);
		if($sResult === false) {
			$sStatus = BP_STATUS_INVALID;
			$sMessage = _t('_payment_bp_err_wrong_amount');
		}

		$iPendingId = (int)$aPosData['pnd'];
		$sOrderId = process_db_input($aData['id']);
        $this->_oDb->updatePending($iPendingId, array(
            'order' => $sOrderId,
            'error_code' => $sStatus,
            'error_msg' => $sMessage
        ));

        $aPending = $this->_oDb->getPending(array('type' => 'id', 'id' => $iPendingId));
        if((int)$aPending['processed'] != 0)
            return array('code' => 6, 'message' => _t('_payment_bp_err_already_processed'));

        //--- Process purchased items in the database if STATUS became CONFIRMED (HIGH and MEDIUM speed), COMPLETE (LOW speed)
		$sSpeed = $this->aBpOptions['transactionSpeed'];
		if((in_array($sSpeed, array(BP_SPEED_HIGH, BP_SPEED_MEDIUM)) && $sStatus == BP_STATUS_CONFIRMED) || ($sSpeed == BP_SPEED_LOW && $sStatus == BP_STATUS_COMPLETE))
	        return array('code' => 1, 'message' => '', 'pending_id' => $iPendingId);

		return array('code' => 7, 'message' => _t('_payment_bp_err_no_confirmation_given'));
    }

    function checkoutFinished()
    {
    	return array(
    		'message' => _t('_payment_bp_msg_checkout_finished')
    	);
    }

	/**
	 *
	 * Creates BitPay invoice via Bitpay::curl.
	 *
	 * @param string $orderId, string $price, string $posData, array $options
	 * @return array $response
	 * @throws Exception $e
	 *
	 */
	public function createInvoice($orderId, $price, $posData, $options = array()) {
		// $orderId: Used to display an orderID to the buyer. In the account summary view, this value is used to
		// identify a ledger entry if present. Maximum length is 100 characters.
		//
		// $price: by default, $price is expressed in the currency you set in Bitpay::aOptions['currency'].
		//
		// $posData: this field is included in status updates or requests to get an invoice.  It is intended to be used by
		// the merchant to uniquely identify an order associated with an invoice in their system.  Aside from that, Bit-Pay does
		// not use the data in this field.  The data in this field can be anything that is meaningful to the merchant.
		// Maximum length is 100 characters.
		//
		// Note:  Using the posData hash option will APPEND the hash to the posData field and could push you over the 100
		//        character limit.
		//
		// $options keys can include any of:
		//	'itemDesc', 'itemCode', 'notificationEmail', 'notificationURL', 'redirectURL', 'apiKey'
		//	'currency', 'physical', 'fullNotifications', 'transactionSpeed', 'buyerName',
		//	'buyerAddress1', 'buyerAddress2', 'buyerCity', 'buyerState', 'buyerZip', 'buyerEmail', 'buyerPhone'
		//
		// If a given option is not provided here, the value of that option will default to what is found in Bitpay::aOptions

		try {
			$options = array_merge($this->aBpOptions, $options);  // $options override any options found in Bitpay::aOptions
			$pos = array('d' => $posData);

			if ($this->aBpOptions['verifyPos'])
				$pos['h'] = $this->_hash(serialize($posData), $options['apiKey']);

			$options['posData'] = json_encode($pos);
			if(strlen($options['posData']) > 100)
				return array('error' => '_payment_bp_err_posdata_exceed_limit');

			$options['orderID'] = $orderId;
			$options['price'] = $price;

			$postOptions = array('orderID', 'itemDesc', 'itemCode', 'notificationEmail', 'notificationURL', 'redirectURL',
	        	'posData', 'price', 'currency', 'physical', 'fullNotifications', 'transactionSpeed', 'buyerName',
	            'buyerAddress1', 'buyerAddress2', 'buyerCity', 'buyerState', 'buyerZip', 'buyerEmail', 'buyerPhone');

			foreach($postOptions as $o) {
				if (array_key_exists($o, $options))
					$post[$o] = $options[$o];
			}
			$post = json_encode($post);

			$response = $this->_curl('https://bitpay.com/api/invoice/', $options['apiKey'], $post);

			if($this->aBpOptions['useLogging']) {
				$this->_log('Create Invoice: ');
				$this->_log('-- Data: ' . $post);
				$this->_log('Response: ');
				$this->_log($response);
			}

			return $response;

		}
		catch (Exception $e) {
			if($this->aBpOptions['useLogging'])
				$this->_log('Error: ' . $e->getMessage());

			return array('error' => $e->getMessage());
		}
	}

	/**
	 *
	 * Retrieves an invoice from BitPay.  $options can include 'apiKey'
	 *
	 * @param string $invoiceId, boolean $apiKey
	 * @return mixed $json
	 * @throws Exception $e
	 *
	 */
	public function getInvoice($invoiceId, $apiKey=false) {
		try {
			if (!$apiKey)
				$apiKey = $this->aBpOptions['apiKey'];

			$response = $this->_curl('https://bitpay.com/api/invoice/'.$invoiceId, $apiKey);

			if (is_string($response))
				return $response; // error

			$response['posData'] = json_decode($response['posData'], true);
			$response['posData'] = $response['posData']['d'];

			return $response;
		}
		catch (Exception $e) {
			if($this->aBpOptions['useLogging'])
				$this->_log('Error: ' . $e->getMessage());

			return 'Error: ' . $e->getMessage();
		}
	}

	/**
	 *
	 * Retrieves a list of all supported currencies
	 * and returns associative array.
	 *
	 * @param none
	 * @return array $currencies
	 * @throws Exception $e
	 *
	 */
	function getCurrencyList() {
		$currencies = array();
		$rate_url = 'https://bitpay.com/api/rates';

		try {
			$clist = json_decode(file_get_contents($rate_url),true);

			foreach($clist as $key => $value)
				$currencies[$value['code']] = $value['name'];

			return $currencies;
		}
		catch (Exception $e) {
			if($this->aBpOptions['useLogging'])
				$this->_log('Error: ' . $e->getMessage());

			return 'Error: ' . $e->getMessage();
		}
	}

	/**
	 *
	 * Retrieves the current rate based on $code.
	 * The default code us USD, so calling the
	 * function without a parameter will return
	 * the current BTC/USD price.
	 *
	 * @param string $code
	 * @return string $rate
	 * @throws Exception $e
	 *
	 */
	public function getRate($code = 'USD') {
		$rate_url = 'https://bitpay.com/api/rates';

		try {
			$clist = json_decode(file_get_contents($rate_url),true);

			foreach($clist as $key => $value) {
				if($value['code'] == $code)
					$rate = number_format($value['rate'], 2, '.', '');
			}

			return $rate;
		}
		catch (Exception $e) {
			if($this->aBpOptions['useLogging'])
				$this->_log('Error: ' . $e->getMessage());
			return 'Error: ' . $e->getMessage();
		}
	}

	protected function _initializeOptions() {
		// REQUIRED Api key you created at bitpay.com
		$this->aBpOptions['apiKey'] = $this->getOption('api_key');

		// whether to verify POS data by hashing above api key.  If set to false, you should
		// have some way of verifying that callback data comes from bitpay.com
		// note: this option can only be changed here.  It cannot be set dynamically.
		$this->aBpOptions['verifyPos'] = true;

		// email where invoice update notifications should be sent
		$this->aBpOptions['notificationEmail'] = $this->getOption('notification_email');

		// url where bit-pay server should send update notifications.  See API doc for more details.
		// example: $bpNotificationUrl = 'http://www.example.com/callback.php';
		$this->aBpOptions['notificationURL'] = $this->_oConfig->getDataReturnUrl(true) . $this->_sName . '/';

		// url where the customer should be directed to after paying for the order
		// example: $bpNotificationUrl = 'http://www.example.com/confirmation.php';
		$this->aBpOptions['redirectURL'] = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'act_checkout_finished/' . $this->_sName . '/';

		// This is the currency used for the price setting.  A list of other pricing
		// currencies supported is found at bitpay.com
		$this->aBpOptions['currency'] = 'BTC';

		// Indicates whether anything is to be shipped with the order
		// (if false, the buyer will be informed that nothing is to be shipped)
		$this->aBpOptions['physical'] = false;

		// If set to false, then notificaitions are only
		// sent when an invoice is confirmed (according the the
		// transactionSpeed setting). If set to true, then a notification
		// will be sent on every status change
		$this->aBpOptions['fullNotifications'] = $this->getOption('full_notifications') == 'on';

		// transaction speed: low/medium/high.   See API docs for more details.
		$this->aBpOptions['transactionSpeed'] = $this->getOption('transaction_speed');

		// Logfile for use by the bpLog function.  Note: ensure the web server process has write access
		// to this file and/or directory!
		$this->aBpOptions['logFile'] = $GLOBALS['dir']['tmp'] . 'ch_payment_bp.log';

		// Change to 'true' if you would like automatic logging of invoices and errors.
		// Otherwise you will have to call the bpLog function manually to log any information.
		$this->aBpOptions['useLogging'] = true;
	}

	/**
	 *
	 * Call from your notification handler to convert $_POST data to an object containing invoice data
	 *
	 * @param boolean $apiKey
	 * @return mixed $json
	 * @throws Exception $e
	 *
	 */
	protected function _verifyNotification() {
		try {
			$this->_log('Notification received: ' . date("m.d.y H:i:s"));

			$post = file_get_contents("php://input");
			if(!$post) {
				$this->_log('Error: No post data');
				return false;
			}

			$json = json_decode($post, true);
			$this->_log('-- Data: ' . $post);
			$this->_log($json);

			if(is_string($json))
				return false;

			if(!array_key_exists('posData', $json)) {
				$this->_log('Error: No posData');
				return false;
			}

			$json['posData'] = json_decode($json['posData'], true);
			if(empty($json['posData']) || !is_array($json['posData'])) {
				$this->_log('Error: Empty posData');
				return false;
			}

			return $json;
		}
		catch (Exception $e) {
			if($this->aBpOptions['useLogging'])
				$this->_log('Error: ' . $e->getMessage());

			return false;
		}
	}

	/**
	 *
	 * Call from your notification handler to verify posData
	 *
	 * @param array $aPosData
	 * @return boolean
	 *
	 */
	protected function _verifyPosData($aPosData) {
		if(!$this->aBpOptions['verifyPos']) {
			if(empty($aPosData['d']) || !is_array($aPosData['d'])) {
				$this->_log('Error: Payment data cannot be found.');
				return false;
			}

			return $aPosData['d'];
		}

		if($this->_hash(serialize($aPosData['d']), $this->aBpOptions['apiKey']) != $aPosData['h']) {
			$this->_log('Error: Authentication failed (bad posData hash).');
			return false;
		}

		return $aPosData['d'];
	}

	protected function _verifyAmount($aPosData, $fAmount) {
    	$iPendingId = (int)$aPosData['pnd'];

        $aPending = $this->_oDb->getPending(array('type' => 'id', 'id' => $iPendingId));
        if(empty($aPending) || !is_array($aPending) || $fAmount != (float)$aPending['amount'])
            return false;

        return true;
	}

	/**
	 *
	 * Decodes JSON response and returns
	 * associative array.
	 *
	 * @param string $response
	 * @return array $arrResponse
	 * @throws Exception $e
	 *
	 */
	protected function _decodeResponse($response) {
		try {
			if (empty($response) || !(is_string($response)))
				return array('error' => 'ChPmtBitPay::_decodeResponse expects a string parameter.');

			return json_decode($response, true);
		}
		catch (Exception $e) {
			if($this->aBpOptions['useLogging'])
				$this->_log('Error: ' . $e->getMessage());

			return array('error' => $e->getMessage());
		}
	}

	/**
	 *
	 * Generates a base64 encoded keyed hash.
	 *
	 * @param string $data, string $key
	 * @return string $hmac
	 * @throws Exception $e
	 *
	 */
	protected function _hash($data, $key) {
		try {
			$hmac = base64_encode(hash_hmac('sha256', $data, $key, TRUE));
			return strtr($hmac, array('+' => '-', '/' => '_', '=' => ''));
		}
		catch (Exception $e) {
			if($this->aBpOptions['useLogging'])
				$this->_log('Error: ' . $e->getMessage());

			return 'Error: ' . $e->getMessage();
		}
	}

	/**
	 *
	 * Handles post/get to BitPay via curl.
	 *
	 * @param string $url, string $apiKey, boolean $post
	 * @return mixed $response
	 * @throws Exception $e
	 *
	 */
	protected function _curl($url, $apiKey, $post = false) {
		if(!isset($url) || trim($url) == '' || !isset($apiKey) || trim($apiKey) == '') {
			// Invalid parameter specified
			if($this->aBpOptions['useLogging'])
				$this->_log('Error: You must supply non-empty url and apiKey parameters.');

	    	return array('error' => 'You must supply non-empty url and apiKey parameters.');
		}

		try {
			$curl = curl_init();
			$length = 0;

			if ($post) {
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
				$length = strlen($post);
			}

			$uname = base64_encode($apiKey);

			if($uname) {
				$header = array(
					'Content-Type: application/json',
					'Content-Length: ' . $length,
					'Authorization: Basic ' . $uname,
				);

				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_PORT, 443);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); // verify certificate
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // check existence of CN and verify that it matches hostname
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);

				$responseString = curl_exec($curl);

				if($responseString == false) {
					$response = array('error' => curl_error($curl));
					if($this->aBpOptions['useLogging'])
						$this->_log('Error: ' . curl_error($curl));
				}
				else {
					$response = json_decode($responseString, true);
					if (!$response) {
						$response = array('error' => 'invalid json: '.$responseString);
						if($this->aBpOptions['useLogging'])
							$this->_log('Error - Invalid JSON: ' . $responseString);
					}
				}

				curl_close($curl);
				return $response;
			}
			else {
				curl_close($curl);

				if($this->aBpOptions['useLogging'])
					$this->_log('Invalid data found in apiKey value passed to Bitpay::curl method. (Failed: base64_encode(apikey))');

				return array('error' => 'Invalid data found in apiKey value passed to Bitpay::curl method. (Failed: base64_encode(apikey))');
			}
		}
		catch (Exception $e) {
			@curl_close($curl);
			if($this->aBpOptions['useLogging'])
			$this->_log('Error: ' . $e->getMessage());
			return array('error' => $e->getMessage());
		}
	}

	/**
	 *
	 * Writes $contents to a log file specified in the bp_options file or, if missing,
	 * defaults to a standard filename of 'bplog.txt'.
	 *
	 * @param mixed $contents
	 * @return
	 * @throws Exception $e
	 *
	 */
	protected function _log($contents) {
		try {
			if(isset($this->aBpOptions['logFile']) && $this->aBpOptions['logFile'] != '') {
				$file = $this->aBpOptions['logFile'];
	    	}
	    	else {
				// Fallback to using a default logfile name in case the variable is
				// missing or not set.
				$file = dirname(__FILE__) . '/bplog.txt';
			}

			file_put_contents($file, date('m-d H:i:s').": ", FILE_APPEND);

			if (is_array($contents))
				$contents = var_export($contents, true);
			else if (is_object($contents))
				$contents = json_encode($contents);

			file_put_contents($file, $contents."\n", FILE_APPEND);
	  	}
	  	catch (Exception $e) {
			echo 'Error: ' . $e->getMessage();
	  	}
	}
}
