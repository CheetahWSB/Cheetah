<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once("ChPmtProvider.php");

define('TUCO_MODE_LIVE', 1);
define('TUCO_MODE_TEST', 2);

define('TUCO_PAYMENT_METHOD_CC', 'CC');
define('TUCO_PAYMENT_METHOD_CK', 'CK');
define('TUCO_PAYMENT_METHOD_AL', 'AL');
define('TUCO_PAYMENT_METHOD_PPI', 'PPI');

class ChPmt2Checkout extends ChPmtProvider
{
    var $_sDataReturnUrl;

    /**
     * Constructor
     */
    function __construct($oDb, $oConfig, $aConfig)
    {
        parent::__construct($oDb, $oConfig, $aConfig);
        $this->_bRedirectOnResult = true;

        $this->_sDataReturnUrl = $this->_oConfig->getDataReturnUrl() . $this->_sName . '/';
    }
    function initializeCheckout($iPendingId, $aCartInfo, $bRecurring = false, $iRecurringDays = 0)
    {
    	$bTest = (int)$this->getOption('mode') == TUCO_MODE_TEST;

        $aFormData = array(
            'sid' => $this->getOption('account_id'),
        	'mode' => '2CO',
        	'demo' => $bTest ? 'Y' : '',
        	'merchant_order_id' => $iPendingId,
            'total' => sprintf("%.2f", (float)$aCartInfo['items_price']),
        	'currency_code' => $aCartInfo['vendor_currency_code'],
            'pay_method' => $this->getOption('payment_method'),
            'x_receipt_link_url' => $this->_sDataReturnUrl . $aCartInfo['vendor_id']
        );

        $iIndex = 0;
        foreach($aCartInfo['items'] as $aItem) {
        	$aFormData['li_' . $iIndex . '_type'] = 'product';
            $aFormData['li_' . $iIndex . '_name'] = $aItem['title'];
            $aFormData['li_' . $iIndex . '_price'] = $aItem['price'];
            $aFormData['li_' . $iIndex . '_quantity'] = $aItem['quantity'];
            $aFormData['li_' . $iIndex . '_tangible'] = 'N';

            $iIndex++;
        }

        $sActionURL = 'https://' . ($bTest ? 'sandbox' : 'www') . '.2checkout.com/checkout/purchase';
        Redirect($sActionURL, $aFormData, 'post', $this->_sCaption);
        exit();
    }
    function finalizeCheckout(&$aData)
    {
        return $this->_registerCheckout($aData);
    }

    /**
     *
     * @param $aData - data from payment provider.
     * @param $bSubscription - Is not needed. May be used in the future for subscriptions.
     * @param $iPendingId - Is not needed. May be used in the future for subscriptions.
     * @return array with results.
     */
    function _registerCheckout(&$aData, $bSubscription = false, $iPendingId = 0)
    {
        if(empty($this->_aOptions) && isset($aData['merchant_order_id']))
            $this->_aOptions = $this->getOptionsByPending($aData['merchant_order_id']);

        if(empty($this->_aOptions))
            return array('code' => 2, 'message' => _t('_payment_2co_err_no_vendor_given'));

        $aResult = $this->_validateCheckout($aData);

        if(empty($aResult['pending_id']))
            return $aResult;

        $aPending = $this->_oDb->getPending(array('type' => 'id', 'id' => (int)$aResult['pending_id']));
        if(!empty($aPending['order']) || !empty($aPending['error_code']) || !empty($aPending['error_msg']) || (int)$aPending['processed'] != 0)
            return array('code' => 6, 'message' => _t('_payment_2co_err_already_processed'));

        $this->_oDb->updatePending((int)$aResult['pending_id'], array(
            'order' => $aData['order_number'],
            'error_code' => $aResult['code'],
            'error_msg' => $aResult['message']
        ));
        return $aResult;
    }
    function _validateCheckout(&$aData)
    {
        if(empty($aData['order_number']) || empty($aData['total']) || empty($aData['key']) || empty($aData['merchant_order_id']))
            return array('code' => 3, 'message' => _t('_payment_2co_err_no_data_given'));

        $sOrder = process_db_input($aData['order_number'], CH_TAGS_STRIP);
        $sAmount = process_db_input($aData['total'], CH_TAGS_STRIP);
        $iPendingId = (int)$aData['merchant_order_id'];

        if($aData['key'] != $this->_generateKey($sOrder, $sAmount))
            return array('code' => 4, 'message' => _t('_payment_2co_err_wrong_key'), 'pending_id' => $iPendingId);

        $aPending = $this->_oDb->getPending(array('type' => 'id', 'id' => $iPendingId));
        if((float)$sAmount != (float)$aPending['amount'])
            return array('code' => 5, 'message' => _t('_payment_2co_err_wrong_payment'), 'pending_id' => $iPendingId);

		$sBuyerFirstName = process_db_input($aData['first_name'], CH_TAGS_STRIP);
        $sBuyerLastName = process_db_input($aData['last_name'], CH_TAGS_STRIP);
        $sBuyerEmail = process_db_input($aData['email'], CH_TAGS_STRIP);

        return array(
        	'code' => 1,
        	'message' => _t('_payment_2co_msg_verified'),
        	'pending_id' => $iPendingId,
        	'payer_name' => _t('_payment_txt_buyer_name_mask', $sBuyerFirstName, $sBuyerLastName),
        	'payer_email' => $sBuyerEmail
        );
    }
    function _generateKey($sOrder, $sAmount)
    {
    	if((int)$this->getOption('mode') == TUCO_MODE_TEST)
    		$sOrder = '1';

        $sKey = $this->getOption('secret_word') . $this->getOption('account_id') . $sOrder . $sAmount;
        return strtoupper(md5($sKey));
    }
}
