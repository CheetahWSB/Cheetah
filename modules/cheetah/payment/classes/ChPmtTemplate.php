<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPaginate');
ch_import('ChWsbModuleTemplate');
ch_import('ChTemplFormView');
ch_import('ChTemplSearchResult');

class ChPmtTemplate extends ChWsbModuleTemplate
{
	var $_sLangsPrefix;

    /**
     * Constructor
     */
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);

        $this->_sLangsPrefix = $this->_oConfig->getLangsPrefix();
    }
    function loadTemplates()
    {
        parent::loadTemplates();

        $this->_aTemplates['script'] = '<script language="javascript" type="text/javascript">__content__</script>';
        $this->_aTemplates['on_result'] = '<script language="javascript" type="text/javascript">alert(\'__message__\')</script>';
        $this->_aTemplates['on_result_inline'] = '<script language="javascript" type="text/javascript">parent.__js_object__.showResultInline(__params__);</script>';
    }
    function displayMoreWindow()
    {
        $sContent = $this->parseHtmlByName('more.html', array());
        return PopupBox('pmt-orders-more', _t($this->_sLangsPrefix . 'wcpt_order_info'), $sContent);
    }
    function displayItems($aItemsInfo)
    {
        $aItems = array();
        foreach($aItemsInfo as $aItem) {
            $aItems[] = array(
                'id' => $aItem['id'],
                'price' => $aItem['price'],
                'ch_if:link' => array(
                    'condition' => !empty($aItem['url']),
                    'content' => array(
                        'url' => $aItem['url'],
                        'title' => $aItem['title']
                    )
                ),
                'ch_if:text' => array(
                    'condition' => empty($aItem['url']),
                    'content' => array(
                        'title' => $aItem['title']
                    )
                ),
            );
        }

        return $this->parseHtmlByName('items.html', array('ch_repeat:items' => $aItems));
    }
    function displayManualOrderWindow($aForm)
    {
        $oForm = new ChTemplFormView($aForm);
        $sContent = $this->parseHtmlByName('manual_order_form.html', array(
            'form' => $oForm->getCode(),
            'loading' => LoadingBox('pmt-order-manual-loading')
        ));

        return PopupBox('pmt-manual-order', _t($this->_sLangsPrefix . 'wcpt_manual_order'), $sContent);
    }
    function displayOrder($sType, $iId)
    {
        $sMethodName = 'get' . ucfirst($sType);
        $aOrder = $this->_oDb->$sMethodName(array('type' => 'id', 'id' => $iId));
        $aSeller = $this->_oDb->getVendorInfoProfile((int)$aOrder['seller_id']);

        $aResult = array(
        	'txt_client' => _t($this->_sLangsPrefix . 'txt_client'),
        	'txt_seller' => _t($this->_sLangsPrefix . 'txt_seller'),
        	'txt_order' => _t($this->_sLangsPrefix . 'txt_order'),
        	'txt_processed_with' => _t($this->_sLangsPrefix . 'txt_processed_with'),
        	'txt_message' => _t($this->_sLangsPrefix . 'txt_message'),
        	'txt_date' => _t($this->_sLangsPrefix . 'txt_date'),
        	'txt_products' => _t($this->_sLangsPrefix . 'txt_products'),
            'client_name' => getNickName($aOrder['client_id']),
            'client_url' => getProfileLink($aOrder['client_id']),
            'ch_if:show_link' => array(
                'condition' => !empty($aSeller['profile_url']),
                'content' => array(
                    'seller_name' => $aSeller['profile_name'],
                    'seller_url' => $aSeller['profile_url'],
                )
            ),
            'ch_if:show_text' => array(
                'condition' => empty($aSeller['profile_url']),
                'content' => array(
                    'seller_name' => $aSeller['profile_name']
                )
            ),
            'order' => $aOrder['order'],
            'provider' => $aOrder['provider'],
            'error' => $aOrder['error_msg'],
            'date' => $aOrder['date_uf'],
            'ch_repeat:items' => array()
        );

        if($sType == CH_PMT_ORDERS_TYPE_PENDING)
            $aItems = ChPmtCart::items2array($aOrder['items']);
        else
            $aItems = ChPmtCart::items2array($aOrder['seller_id'] . '_' . $aOrder['module_id'] . '_' . $aOrder['item_id'] . '_' . $aOrder['item_count']);

        foreach($aItems as $aItem) {
            $aInfo = ChWsbService::call((int)$aItem['module_id'], 'get_cart_item', array($aOrder['client_id'], $aItem['item_id']));
            if(!empty($aInfo) && is_array($aInfo))
	            $aResult['ch_repeat:items'][] = array(
	                'ch_if:link' => array(
	                    'condition' => !empty($aInfo['url']),
	                    'content' => array(
	                        'title' => $aInfo['title'],
	                        'url' => $aInfo['url']
	                    )
	                ),
	                'ch_if:text' => array(
	                    'condition' => empty($aInfo['url']),
	                    'content' => array(
	                        'title' => $aInfo['title'],
	                    )
	                ),
	                'quantity' => $aItem['item_count'],
	                'price' => $aInfo['price'],
	                'currency_code' => $aSeller['currency_code']
	            );
        }

        return $this->parseHtmlByName($sType . '_order.html', $aResult);
    }
    function displayOrders($sType, $aParams)
    {
        if(empty($aParams['per_page']))
            $aParams['per_page'] = $this->_oConfig->getPerPage('orders');
        $sJsObject = $this->_oConfig->getJsObject('orders');

        $sMethodNameInfo = 'get' . ucfirst($sType) . 'Orders';
        $aOrders = $this->_oDb->$sMethodNameInfo($aParams);
        if(empty($aOrders))
           return MsgBox(_t($this->_sLangsPrefix . 'msg_no_results'));

        $aAdministrator = $this->_oDb->getVendorInfoProfile(CH_PMT_ADMINISTRATOR_ID);

        $sTxtMoreProducts = _t($this->_sLangsPrefix . 'txt_more_products');
        $sTxtMoreItems = _t($this->_sLangsPrefix . 'txt_more_items');
        $sTxtActionMore = _t($this->_sLangsPrefix . 'txt_action_more');

        //--- Get Orders ---//
        $aResultOrders = array();
        foreach($aOrders as $aOrder) {
            if(empty($aOrder['user_id'])) {
                $aOrder['user_name'] = $aAdministrator['profile_name'];
                $aOrder['user_url'] = $aAdministrator['profile_url'];
            }
            else {
                $aOrder['user_name'] = getNickName($aOrder['user_id']);
                $aOrder['user_url'] = getProfileLink($aOrder['user_id']);
                if(!$aOrder['user_name']) {
                	$aOrder['user_name'] = _t('_undefined');
                	$aOrder['user_url'] = '';
                }
            }

            $aResultOrders[] = array_merge($aOrder, array(
            	'js_object' => $sJsObject,
            	'type' => $sType,
            	'txt_more_products' => $sTxtMoreProducts,
            	'txt_more_items' => $sTxtMoreItems,
            	'txt_action_more' => $sTxtActionMore,
                'ch_if:show_link' => array(
                    'condition' => !empty($aOrder['user_url']),
                    'content' => array(
                        'user_name' => $aOrder['user_name'],
                        'user_url' => $aOrder['user_url']
                    )
                ),
                'ch_if:show_text' => array(
                    'condition' => empty($aOrder['user_url']),
                    'content' => array(
                        'user_name' => $aOrder['user_name']
                    )
                ),
                'ch_if:pending' => array(
                    'condition' => $sType == CH_PMT_ORDERS_TYPE_PENDING,
                    'content' => array(
                        'id' => $aOrder['id'],
                        'order' => $aOrder['order']
                    )
                ),
                'ch_if:processed' => array(
                    'condition' => $sType == CH_PMT_ORDERS_TYPE_PROCESSED || CH_PMT_ORDERS_TYPE_SUBSCRIPTION || $sType == CH_PMT_ORDERS_TYPE_HISTORY,
                    'content' => array(
                        'order' => $aOrder['order']
                    )
                ),
                'products' => $aOrder['products'],
                'items' => $aOrder['items'],
                'subscription' => $this->_isSubscription($aOrder)
            ));
        }

        //--- Get Paginate Panel ---//
        $sPaginatePanel = "";
        $sMethodNameCount = 'get' . ucfirst($sType) . 'OrdersCount';
        if(($iCount = $this->_oDb->$sMethodNameCount($aParams)) > $aParams['per_page']) {
            $oPaginate = new ChWsbPaginate(array(
                'start' => $aParams['start'],
                'count' => $iCount,
                'per_page' => $aParams['per_page'],
                'per_page_step' => 2,
                'per_page_interval' => 3,
                'page_url' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . ($sType == CH_PMT_ORDERS_TYPE_HISTORY ? 'history' : 'orders') . '/',
                'on_change_page' => $sJsObject . ".changePage('" . $sType . "', {start}, {per_page}, " . $aParams['seller_id'] . ")"
            ));
            $sPaginatePanel = $oPaginate->getPaginate();
        }

        return $this->parseHtmlByName($sType . '_orders.html', array(
            'ch_repeat:orders' => $aResultOrders,
            'paginate_panel' => $sPaginatePanel
        ));
    }
    function displayOrdersBlock($sType, $iVendorId)
    {
        $sJsObject = $this->_oConfig->getJsObject('orders');

        //--- Get Filter Panel ---//
        $sFilterPanel = ChTemplSearchResult::showAdminFilterPanel('', 'pmt-filter-text-' . $sType, 'pmt-filter-enable-' . $sType, 'filter', $sJsObject . ".applyFilter('" . $sType . "', this)");

        //--- Get Control Panel ---//
        $aButtons = array();
        if($sType == CH_PMT_ORDERS_TYPE_PENDING)
            $aButtons['pmt-process'] = _t($this->_sLangsPrefix . 'btn_process');
        $aButtons['pmt-cancel'] = _t($this->_sLangsPrefix . 'btn_cancel');
        $aButtons['pmt-report'] = _t($this->_sLangsPrefix . 'btn_report');
        if($sType == CH_PMT_ORDERS_TYPE_PROCESSED)
            $aButtons['pmt-manual'] = array('type' => 'button', 'name' => 'pmt-manual', 'value' => _t($this->_sLangsPrefix . 'btn_manual_order'), 'onclick' => 'onclick="javascript:' . $sJsObject . '.addManually(this);"');

        $sControlPanel = ChTemplSearchResult::showAdminActionsPanel('pmt-form-' . $sType, $aButtons, 'orders');

        return $this->parseHtmlByName($sType . '_orders_block.html', array(
        	'txt_date' => _t($this->_sLangsPrefix . 'txt_date'),
        	'txt_client' => _t($this->_sLangsPrefix . 'txt_client'),
	        'txt_order' => _t($this->_sLangsPrefix . 'txt_order'),
	        'txt_amount' => _t($this->_sLangsPrefix . 'txt_amount'),
	        'txt_license' => _t($this->_sLangsPrefix . 'txt_license'),
	        'txt_action' => _t($this->_sLangsPrefix . 'txt_action'),
            'type' => $sType,
            'action' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'act_orders_submit/' . $sType,
            'orders' => $this->displayOrders($sType, array('seller_id' => $iVendorId, 'start' => 0)),
            'filter_panel' => $sFilterPanel,
            'control_panel' => $sControlPanel,
            'loading' => LoadingBox('pmt-orders-' . $sType . '-loading')
        ));
    }
    function displayHistoryBlock($iUserId, $iVendorId)
    {
        $sJsObject = $this->_oConfig->getJsObject('orders');

        //--- Get Filter Panel ---//
        $sFilterPanel = ChTemplSearchResult::showAdminFilterPanel('', 'pmt-filter-text-history', 'pmt-filter-enable-history', 'filter', $sJsObject . ".applyFilter('history', this)");

        return $this->parseHtmlByName('history_orders_block.html', array(
        	'txt_date' => _t($this->_sLangsPrefix . 'txt_date'),
        	'txt_seller' => _t($this->_sLangsPrefix . 'txt_seller'),
	        'txt_order' => _t($this->_sLangsPrefix . 'txt_order'),
	        'txt_amount' => _t($this->_sLangsPrefix . 'txt_amount'),
	        'txt_license' => _t($this->_sLangsPrefix . 'txt_license'),
	        'txt_action' => _t($this->_sLangsPrefix . 'txt_action'),
            'orders' => $this->displayOrders('history', array('user_id' => $iUserId, 'seller_id' => $iVendorId, 'start' => 0)),
            'filter_panel' => $sFilterPanel,
            'loading' => LoadingBox('pmt-orders-history-loading')
        ));
    }
    function displayToolbarSubmenu($aInfo)
    {
        $aCarts = array();
        foreach($aInfo as $iVendorId => $aVendorCart) {
            //--- Get Items ---//
            $aItems = array();
            foreach($aVendorCart['items'] as $aItem)
                $aItems[] = array(
                    'vendor_id' => $aVendorCart['vendor_id'],
                    'vendor_currency_code' => $aVendorCart['vendor_currency_code'],
                    'item_id' => $aItem['id'],
                    'item_title' => $aItem['title'],
                    'item_url' => $aItem['url'],
                    'item_quantity' => $aItem['quantity'],
                    'item_price' => $aItem['quantity'] * $aItem['price'],
                );

            //--- Get General Info ---//
            $aCarts[] = array(
                'vendor_id' => $aVendorCart['vendor_id'],
                'ch_if:show_link' => array(
                    'condition' => !empty($aVendorCart['vendor_profile_url']),
                    'content' => array(
                        'vendor_username' => $aVendorCart['vendor_profile_name'],
                        'vendor_url' => $aVendorCart['vendor_profile_url'],
                        'vendor_currency_code' => $aVendorCart['vendor_currency_code'],
                        'items_count' => $aVendorCart['items_count'],
                        'items_price' => $aVendorCart['items_price']
                    )
                ),
                'ch_if:show_text' => array(
                    'condition' => empty($aVendorCart['vendor_profile_url']),
                    'content' => array(
                        'vendor_username' => $aVendorCart['vendor_profile_name'],
                        'vendor_currency_code' => $aVendorCart['vendor_currency_code'],
                        'items_count' => $aVendorCart['items_count'],
                        'items_price' => $aVendorCart['items_price']
                    )
                ),
                'vendor_icon' => $aVendorCart['vendor_profile_icon'],
                'ch_repeat:items' => $aItems
            );
        }
        return $this->addCss('toolbar.css', true) . $this->parseHtmlByName('toolbar_submenu.html', array('ch_repeat:carts' => $aCarts));
    }
    function displayCartContent($aCartInfo, $iVendorId = CH_PMT_EMPTY_ID)
    {
        $iAdminId = $this->_oConfig->getAdminId();
        $sJsObject = $this->_oConfig->getJsObject('cart');

        if($iVendorId != CH_PMT_EMPTY_ID)
            $aCartInfo = array($aCartInfo);

        $aVendors = array();
        foreach($aCartInfo as $aVendor) {
            //--- Get Providers ---//
            $aProviders = array();
            $aVendorProviders = $this->_oDb->getVendorInfoProviders($aVendor['vendor_id']);
            foreach($aVendorProviders as $aProvider)
                $aProviders[] = array(
                    'name' => $aProvider['name'],
                    'caption' => _t($this->_sLangsPrefix . 'txt_cart_' . $aProvider['name']),
                    'checked' => empty($aProviders) ? 'checked="checked"' : ''
                );

            //--- Get Items ---//
            $aItems = array();
            foreach($aVendor['items'] as $aItem)
                $aItems[] = array(
                    'vendor_id' => $aVendor['vendor_id'],
                    'vendor_currency_code' => $aVendor['vendor_currency_code'],
                    'module_id' => $aItem['module_id'],
                    'item_id' => $aItem['id'],
                    'item_title' => $aItem['title'],
                    'item_url' => $aItem['url'],
                    'item_quantity' => $aItem['quantity'],
                	'ch_if:show_price_paid' => array(
                		'condition' => (float)$aItem['price'] != 0,
                		'content' => array(
                			'item_price' => $aItem['quantity'] * $aItem['price'],
                			'vendor_currency_code' => $aVendor['vendor_currency_code'],
                		)
                	),
                	'ch_if:show_price_free' => array(
                		'condition' => (int)$aItem['price'] == 0,
                		'content' => array()
                	),
                    'js_object' => $sJsObject
                );

            //--- Get Control Panel ---//
            $aButtons = array(
                'pmt-checkout' => _t($this->_sLangsPrefix . 'btn_checkout'),
                'pmt-delete' => _t($this->_sLangsPrefix . 'btn_delete')
            );
            $sControlPanel = ChTemplSearchResult::showAdminActionsPanel('items_from_' . $aVendor['vendor_id'], $aButtons, 'items', true, true);

            //--- Get General ---//
            $aVendors[] = array(
                'vendor_id' => $aVendor['vendor_id'],
                'ch_if:show_link' => array(
                    'condition' => !empty($aVendor['vendor_profile_url']),
                    'content' => array(
                        'txt_shopping_cart' => _t($this->_sLangsPrefix . 'txt_shopping_cart', $this->parseHtmlByName('vendor_link.html', array(
                            'vendor_username' => $aVendor['vendor_profile_name'],
                            'vendor_url' => $aVendor['vendor_profile_url'],
                        ))),
                        'txt_shopping_cart_summary' => _t('_payment_txt_shopping_cart_summary', $aVendor['items_count'], $aVendor['items_price'], $aVendor['vendor_currency_code']),
                        'vendor_currency_code' => $aVendor['vendor_currency_code'],
                        'items_count' => $aVendor['items_count'],
                        'items_price' => $aVendor['items_price']
                    )
                ),
                'ch_if:show_text' => array(
                    'condition' => empty($aVendor['vendor_profile_url']),
                    'content' => array(
                        'txt_shopping_cart' => _t($this->_sLangsPrefix . 'txt_shopping_cart', $aVendor['vendor_profile_name']),
                        'txt_shopping_cart_summary' => _t('_payment_txt_shopping_cart_summary', $aVendor['items_count'], $aVendor['items_price'], $aVendor['vendor_currency_code']),
                        'vendor_currency_code' => $aVendor['vendor_currency_code'],
                        'items_count' => $aVendor['items_count'],
                        'items_price' => $aVendor['items_price']
                    )
                ),
                'vendor_icon' => $aVendor['vendor_profile_icon'],
                'ch_repeat:providers' => $aProviders,
                'ch_repeat:items' => $aItems,
                'js_object' => $sJsObject,
                'process_url' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'act_cart_submit/',
                'control_panel' => $sControlPanel
            );
        }

        $this->addCss('cart.css');
        $this->addJs('cart.js');
        return $this->parseHtmlByName('cart.html', array_merge($this->_getJsContentCart(), array('ch_repeat:vendors' => $aVendors)));
    }
    function displayCartJs($bWrapped = true)
    {
        $this->addJs('cart.js');

        $aJs = $this->_getJsContentCart($bWrapped);
        return $aJs['js_content'];
    }
    function displayAddToCartJs($iVendorId, $iModuleId, $iItemId, $iItemCount, $bNeedRedirect = false, $bWrapped = true)
    {
        $aJs = $this->_getJsContentCart();

        $sJsCode = $this->displayCartJs($bWrapped);
        $sJsMethod = $this->parseHtmlByName('add_to_cart_js.html', array(
            'js_object' => $aJs['js_object'],
            'vendor_id' => $iVendorId,
            'module_id' => $iModuleId,
            'item_id' => $iItemId,
            'item_count' => $iItemCount,
            'need_redirect' => (int)$bNeedRedirect
        ));

        return array($sJsCode, $sJsMethod);
    }
    function displayAddToCartLink($iVendorId, $iModuleId, $iItemId, $iItemCount, $bNeedRedirect = false)
    {
        $this->addJs('cart.js');
        return $this->parseHtmlByName('add_to_cart.html', array_merge($this->_getJsContentCart(), array(
        	'txt_add_to_cart' => _t($this->_sLangsPrefix . 'txt_add_to_cart'),
            'vendor_id' => $iVendorId,
            'module_id' => $iModuleId,
            'item_id' => $iItemId,
            'item_count' => $iItemCount,
            'need_redirect' => (int)$bNeedRedirect
        )));
    }

	function getPageCode(&$aParams)
    {
        global $_page;
        global $_page_cont;

        $iIndex = isset($aParams['index']) ? (int)$aParams['index'] : 0;
        $_page['name_index'] = $iIndex;
        $_page['js_name'] = isset($aParams['js']) ? $aParams['js'] : '';
        $_page['css_name'] = isset($aParams['css']) ? $aParams['css'] : '';
        $_page['extra_js'] = isset($aParams['extra_js']) ? $aParams['extra_js'] : '';

        check_logged();

        if(isset($aParams['content']))
            foreach($aParams['content'] as $sKey => $sValue)
                $_page_cont[$iIndex][$sKey] = $sValue;

        if(isset($aParams['title']['page']))
            $this->setPageTitle($aParams['title']['page']);
        if(isset($aParams['title']['block']))
            $this->setPageMainBoxTitle($aParams['title']['block']);

        if(isset($aParams['breadcrumb']))
            $GLOBALS['oTopMenu']->setCustomBreadcrumbs($aParams['breadcrumb']);

        PageCode($this);
    }

	function getPageCodeResponse($sMessage)
    {
		$aParams = array(
            'title' => array(
                'page' => _t($this->_sLangsPrefix . 'pcpt_response')
            ),
            'content' => array(
                'page_main_code' => MsgBox(_t($sMessage))
            )
        );
        $this->getPageCode($aParams);
    }

    function getPageCodeError($sMessage, $bWrap = true)
    {
		$aParams = array(
            'title' => array(
                'page' => _t($this->_sLangsPrefix . 'pcpt_error')
            ),
            'content' => array(
                'page_main_code' => $bWrap ? MsgBox(_t($sMessage)) : $sMessage
            )
        );
        $this->getPageCode($aParams);
    }

    function getPageCodeAdmin(&$aParams)
    {
        global $_page;
        global $_page_cont;

        $iIndex = isset($aParams['index']) ? (int)$aParams['index'] : 9;
        $_page['name_index'] = $iIndex;
        $_page['js_name'] = isset($aParams['js']) ? $aParams['js'] : '';
        $_page['css_name'] = isset($aParams['css']) ? $aParams['css'] : '';
        $_page['header'] = isset($aParams['title']['page']) ? $aParams['title']['page'] : '';

        if(isset($aParams['content']))
            foreach($aParams['content'] as $sKey => $sValue)
                $_page_cont[$iIndex][$sKey] = $sValue;

        PageCodeAdmin();
    }

    function _getJsContentCart($bWrapped = true)
    {
    	$sJsClass = $this->_oConfig->getJsClass('cart');
        $sJsObject = $this->_oConfig->getJsObject('cart');

        $aOptions = array(
        	'sActionUrl' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri(),
        	'sObjName' => $sJsObject,
        	'sAnimationEffect' => $this->_oConfig->getAnimationEffect(),
        	'iAnimationSpeed' => $this->_oConfig->getAnimationSpeed()
        );

        $sJsContent = 'var ' . $sJsObject . ' = new ' . $sJsClass . '(' . json_encode($aOptions) . ');';
        if($bWrapped)
			$sJsContent = $this->_wrapInTagJsCode($sJsContent);

        return array('js_object' => $sJsObject, 'js_content' => $sJsContent);
    }

    function _isSubscription($aOrder)
    {
    	return '';
    }

}
