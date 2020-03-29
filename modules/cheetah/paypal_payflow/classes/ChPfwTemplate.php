<?php

/**
 * This work, "Cheetah - https://cheetah.deanbassett.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_MODULES . 'cheetah/payment/classes/ChPmtTemplate.php');

class ChPfwTemplate extends ChPmtTemplate
{
    var $_oModule;

    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);

        $this->_removeLocations($this);
        $this->_addLocations($this);

        global $oAdmTemplate;
        $this->_removeLocations($oAdmTemplate);
        $this->_addLocations($oAdmTemplate);

        $this->_oModule = null;
    }

    function setModule(&$oModule)
    {
        $this->_oModule = $oModule;
    }

    function addAdminParentJs($mixedFiles, $bDynamic = false)
    {
        global $oAdmTemplate;
        return $oAdmTemplate->addJs($mixedFiles, $bDynamic);
    }

    function addAdminParentCss($mixedFiles, $bDynamic = false)
    {
        global $oAdmTemplate;
        return $oAdmTemplate->addCss($mixedFiles, $bDynamic);
    }

	function getJsCode($sType, $bWrap = false)
    {
    	$sJsObject = $this->_oConfig->getJsObject($sType);
        $sJsClass = $this->_oConfig->getJsClass($sType);

        $aOptions = array(
        	'sActionUrl' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri(),
        	'sObjName' => $sJsObject,
        	'sAnimationEffect' => $this->_oConfig->getAnimationEffect(),
        	'iAnimationSpeed' => $this->_oConfig->getAnimationSpeed()
        );
        $sContent .= 'var ' . $sJsObject . ' = new ' . $sJsClass . '(' . json_encode($aOptions) . ');';

        return $bWrap ? $this->_wrapInTagJsCode($sContent) : $sContent;
    }

    function displayOrder($sType, $iId)
    {
    	$sOrder = parent::displayOrder(($sType == CH_PMT_ORDERS_TYPE_SUBSCRIPTION ? CH_PMT_ORDERS_TYPE_PROCESSED : $sType), $iId);
    	if(!in_array($sType, array(CH_PMT_ORDERS_TYPE_SUBSCRIPTION, CH_PMT_ORDERS_TYPE_HISTORY)))
    		return $sOrder;

    	$sMethodName = 'get' . ucfirst($sType);
        $aOrder = $this->_oDb->$sMethodName(array('type' => 'id', 'id' => $iId));
        if(empty($aOrder['order_profile']) || (!isAdmin() && (int)$aOrder['client_id'] != getLoggedId()))
        	return $sOrder;

    	return $this->parseHtmlByName('rb_processed_order.html', array(
    		'js_object'  => $this->_oConfig->getJsObject('orders'),
    		'type' => $sType,
    		'id' => $iId,
    		'order' => $sOrder,
    		'order_profile' => $aOrder['order_profile'],
    		'loading' => LoadingBox('pfw-order-loading-' . $iId)
    	));
    }

    function displayCartContent($aCartInfo, $iVendorId = CH_PMT_EMPTY_ID)
    {
    	$sResult = parent::displayCartContent($aCartInfo, $iVendorId);

    	$this->addJs(array('_cart.js'));
    	return $sResult;
    }

    function displayCartJs($bWrapped = true)
    {
    	$sResult = parent::displayCartJs($bWrapped);

    	$this->addJs(array('_cart.js'));
    	return $sResult;
    }

    function displayAddToCartJs($iVendorId, $iModuleId, $iItemId, $iItemCount, $bNeedRedirect = false, $bWrapped = true)
    {
    	$aResult = parent::displayAddToCartJs($iVendorId, $iModuleId, $iItemId, $iItemCount, $bNeedRedirect, $bWrapped);

    	$this->addJs(array('_cart.js'));
    	return $aResult;
    }

    function displayAddToCartLink($iVendorId, $iModuleId, $iItemId, $iItemCount, $bNeedRedirect = false)
    {
    	$sResult = parent::displayAddToCartLink($iVendorId, $iModuleId, $iItemId, $iItemCount, $bNeedRedirect);

		$this->addJs(array('_cart.js'));
    	return $sResult;
    }

    function displayConfirmPage($sProvider, $iVendorId, &$aOrderInfo, &$aCartInfo)
    {
    	$bSubscription = isset($aOrderInfo['SUBSCRIPTION']) && (int)$aOrderInfo['SUBSCRIPTION'] == 1;

    	$aTmplItems = array();
        foreach($aCartInfo['items'] as $aItem) {
        	$aTmplItems[] = array(
	        	'url' => $aItem['url'],
	        	'title' => $aItem['title'],
        		'quantity' => $aItem['quantity'],
        		'price' => $aItem['price'],
        		'currency' => $aCartInfo['vendor_currency_code'],
        		'ch_if:show_duration' => array(
        			'condition' => $bSubscription,
        			'content' => array(
        				'duration' => _t($this->_sLangsPrefix . 'txt_subscription_duration_mask', $aItem['duration'])
        			)
        		)
        	);
        }


    	$sDetails = $this->parseHtmlByName('ec_confirm.html', array(
    		'buyer_name' => _t($this->_sLangsPrefix . 'txt_buyer_name_mask', $aOrderInfo['FIRSTNAME'], $aOrderInfo['LASTNAME']),
    		'buyer_email' =>  _t($this->_sLangsPrefix . 'txt_buyer_email_mask', $aOrderInfo['EMAIL'], $aOrderInfo['PAYERSTATUS']),
    		'txt_items_info' => _t($bSubscription ? '_ch_pfw_txt_subscription_info' : '_ch_pfw_txt_products_info'),
    		'ch_repeat:items' => $aTmplItems,
    		'total_count' => $aCartInfo['items_count'],
    		'total_price' => $aCartInfo['items_price'],
    		'currency' => $aCartInfo['vendor_currency_code']
    	));

    	$aForm = array(
            'form_attrs' => array(
                'id' => 'pfw_ec_confirm',
                'name' => 'pfw_ec_confirm',
                'action' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'finalize_checkout/' . $sProvider . '/' . $iVendorId,
                'method' => 'post'
            ),
            'params' => array(
                'db' => array(
                    'table' => '',
                    'key' => 'id',
                    'uri' => '',
                    'uri_title' => '',
                    'submit_name' => 'submit'
                ),
            ),
            'inputs' => array (
            	'token' => array(
            		'type' => 'hidden',
            		'name' => 'token',
            		'value' => $aOrderInfo['TOKEN']
            	),
            	'payerid' => array(
            		'type' => 'hidden',
            		'name' => 'payerid',
            		'value' => $aOrderInfo['PAYERID']
            	),
            	'payername' => array(
            		'type' => 'hidden',
            		'name' => 'payername',
            		'value' => _t($this->_sLangsPrefix . 'txt_buyer_name_mask', $aOrderInfo['FIRSTNAME'], $aOrderInfo['LASTNAME'])
            	),
            	'payeremail' => array(
            		'type' => 'hidden',
            		'name' => 'payeremail',
            		'value' => $aOrderInfo['EMAIL']
            	),
            	'amt' => array(
            		'type' => 'hidden',
            		'name' => 'amt',
            		'value' => $aOrderInfo['AMT']
            	),
            	'pendingid' => array(
            		'type' => 'hidden',
            		'name' => 'pendingid',
            		'value' => (int)$aOrderInfo['CUSTOM']
            	),
            	'details' => array(
		            'type' => 'custom',
		            'name' => 'details',
		            'content' => $sDetails,
            		'colspan' => 2,
		        ),
				'submit' => array(
		            'type' => 'submit',
		            'name' => 'submit',
		            'value' => _t($this->_sLangsPrefix . 'btn_' . ($bSubscription ? 'subscribe' : 'pay')),
		        	'colspan' => 2,
		        )
            )
        );

        ch_import('ChTemplFormView');
        $oForm = new ChTemplFormView($aForm);

    	$aParams = array(
    		'index' => 1,
    		'css' => array('_cart.css'),
            'title' => array(
                'page' => _t($this->_sLangsPrefix . 'pcpt_receipt'),
            ),
            'content' => array(
                'page_main_code' => DesignBoxContent(_t($this->_sLangsPrefix . 'bcpt_receipt'), $oForm->getCode(), 11)
            )
        );
        $this->getPageCode($aParams);
    }

    function _getJsContentCart($bWrapped = true)
    {
    	$sJsObject = $this->_oConfig->getJsObject('cart');
    	$sJsContent = $this->getJsCode('cart', $bWrapped);

    	return array('js_object' => $sJsObject, 'js_content' => $sJsContent);
    }

	function _isSubscription($aOrder)
    {
    	return _t('_ch_pfw_txt_' . (!empty($aOrder['order_profile']) ? 'yes' : 'no'));
    }

    protected function _addLocations($oTemplate)
    {
    	//--- Add Parent module locations
        $sClassPrefix = $this->_oConfig->getParentClassPrefix();
        $sHomePath = $this->_oConfig->getParentHomePath();
        $sHomeUrl = $this->_oConfig->getParentHomeUrl();

        $oTemplate->addLocation($sClassPrefix, $sHomePath, $sHomeUrl);
        $oTemplate->addLocationJs($sClassPrefix, $sHomePath . 'js/', $sHomeUrl . 'js/');

        //--- Add current module locations
		$sClassPrefix = $this->_oConfig->getClassPrefix();
        $sHomePath = $this->_oConfig->getHomePath();
        $sHomeUrl = $this->_oConfig->getHomeUrl();

        $oTemplate->addLocation($sClassPrefix, $sHomePath, $sHomeUrl);
        $oTemplate->addLocationJs($sClassPrefix, $sHomePath . 'js/', $sHomeUrl . 'js/');
    }

    protected function _removeLocations($oTemplate)
    {
    	//--- Remove Parent module locations
		$sClassPrefix = $this->_oConfig->getParentClassPrefix();
		$oTemplate->removeLocation($sClassPrefix);
        $oTemplate->removeLocationJs($sClassPrefix);

    	//--- Remove current module locations
    	$sClassPrefix = $this->_oConfig->getClassPrefix();
        $oTemplate->removeLocation($sClassPrefix);
        $oTemplate->removeLocationJs($sClassPrefix);
    }
}
