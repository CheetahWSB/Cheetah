<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ch_store_import($sClassPostfix, $aModuleOverwright = array())
{
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'store') {
        $oMain = ChWsbModule::getInstance('ChStoreModule');
        $a     = $oMain->_aModule;
    }
    ch_import($sClassPostfix, $a);
}

ch_import('ChWsbPaginate');
ch_import('ChWsbAlerts');
ch_import('ChWsbTwigModule');

define('CH_STORE_PHOTOS_CAT', 'Store');
define('CH_STORE_PHOTOS_TAG', 'store');

define('CH_STORE_VIDEOS_CAT', 'Store');
define('CH_STORE_VIDEOS_TAG', 'store');

define('CH_STORE_FILES_CAT', 'Store');
define('CH_STORE_FILES_TAG', 'store');

/**
 * Store module
 *
 * This module allow users to post products,
 * other members can downoad them for free or some price.
 * Later customers can rate, comment and discuss products.
 * Product can have photos, videos and files.
 *
 *
 *
 * Profile's Wall:
 * 'add product' event are displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new product was created
 * change - product was chaned
 * rate - somebody rated product
 * commentPost - somebody posted review in product
 *
 *
 *
 * Memberships/ACL:
 * store view product - CH_STORE_VIEW_PRODUCT
 * store browse - CH_STORE_BROWSE
 * store search - CH_STORE_SEARCH
 * store add product - CH_STORE_ADD_PRODUCT
 * store edit any product - CH_STORE_EDIT_ANY_PRODUCT
 * store delete any product - CH_STORE_DELETE_ANY_PRODUCT
 * store mark as featured - CH_STORE_MARK_AS_FEATURED
 * store approve aproduct - CH_STORE_APPROVE_PRODUCT
 * store broadcast message - CH_STORE_BROADCAST_MESSAGE
 *
 *
 *
 * Service methods:
 *
 * Homepage block with different products
 *
 * @see ChStoreModule::serviceHomepageBlock
 *      ChWsbService::call('store', 'homepage_block', array());
 *
 * Profile block with user's products
 * @see ChStoreModule::serviceProfileBlock
 * ChWsbService::call('store', 'profile_block', array($iProfileId));
 *
 * Product's forum permissions (for internal usage only)
 * @see ChStoreModule::serviceGetForumPermission
 * ChWsbService::call('store', 'get_forum_permission', array($iMemberId, $iForumId));
 *
 * Member menu item for my products (for internal usage only)
 * @see ChStoreModule::serviceGetMemberMenuItem
 * ChWsbService::call('store', 'get_member_menu_item');
 *
 * Member menu item for add product (for internal usage only)
 * @see ChStoreModule::serviceGetMemberMenuItemAddContent
 * ChWsbService::call('store', 'get_member_menu_item_add_content');
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'ch_store'
 * The following alerts are rised
 *
 *  add - new product was added
 *      $iObjectId - product id
 *      $iSenderId - creator of product
 *      $aExtras['Status'] - status of added product
 *
 *  change - product's info was changed
 *      $iObjectId - product  id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed product
 *
 *  delete - product was deleted
 *      $iObjectId - product id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - product was marked/unmarked as featured
 *      $iObjectId - product id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if product was marked as featured and 0 - if product was removed from featured
 *
 */
class ChStoreModule extends ChWsbTwigModule
{
    var $_oPrivacyProduct;
    var $_oPrivacyFile;

    var $_aQuickCache = array();

    function __construct(&$aModule)
    {
        parent::__construct($aModule);
        $this->_sFilterName = 'ch_store_filter';
        $this->_sPrefix     = 'ch_store';

        ch_import('PrivacyProduct', $aModule);
        ch_import('PrivacyFile', $aModule);
        $this->_oPrivacyProduct = new ChStorePrivacyProduct($this);
        $this->_oPrivacyFile    = new ChStorePrivacyFile($this);

        $GLOBALS['oChStoreModule'] = &$this;
    }

    function actionHome()
    {
        parent::_actionHome(_t('_ch_store_page_title_home'));
    }

    function actionVideos($sUri)
    {
        parent::_actionVideos($sUri, _t('_ch_store_page_title_videos'));
    }

    function actionPhotos($sUri)
    {
        parent::_actionPhotos($sUri, _t('_ch_store_page_title_photos'));
    }

    function actionComments($sUri)
    {
        parent::_actionComments($sUri, _t('_ch_store_page_title_comments'));
    }

    function actionView($sUri)
    {
        parent::_actionView($sUri, _t('_ch_store_msg_pending_approval'));
    }

    function actionBroadcast($iEntryId)
    {
        parent::_actionBroadcast($iEntryId, _t('_ch_store_page_title_broadcast'),
            _t('_ch_store_msg_broadcast_no_recipients'), _t('_ch_store_msg_broadcast_message_sent'));
    }

    function actionCalendar($iYear = '', $iMonth = '')
    {
        parent::_actionCalendar($iYear, $iMonth, _t('_ch_store_page_title_calendar'));
    }

    function actionSearch($sKeyword = '', $sCategory = '')
    {
        parent::_actionSearch($sKeyword, $sCategory, _t('_ch_store_page_title_search'));
    }

    function actionAdd()
    {
        parent::_actionAdd(_t('_ch_store_page_title_add'));
    }

    function actionEdit($iEntryId)
    {
        $this->_oTemplate->addCss('form_field_product_files_choice.css');
        parent::_actionEdit($iEntryId, _t('_ch_store_page_title_edit'));
    }

    function actionDelete($iEntryId)
    {
        parent::_actionDelete($iEntryId, _t('_ch_store_msg_product_was_deleted'));
    }

    function actionMarkFeatured($iEntryId)
    {
        parent::_actionMarkFeatured($iEntryId, _t('_ch_store_msg_added_to_featured'),
            _t('_ch_store_msg_removed_from_featured'));
    }

    function actionSharePopup($iEntryId)
    {
        parent::_actionSharePopup($iEntryId, _t('_ch_store_cpation_share_product'));
    }

    function actionTags()
    {
        parent::_actionTags(_t('_ch_store_page_title_tags'));
    }

    function actionCategories()
    {
        parent::_actionCategories(_t('_ch_store_page_title_categories'));
    }

    function actionToggleProductFileVisibility($iFileId)
    {
        header('Content-type:text/html;charset=utf-8');

        $aFileInfo = $this->_oDb->getFileInfoByFileId((int)$iFileId);
        if (!$aFileInfo) {
            echo _t('_sys_request_page_not_found_cpt');
            exit;
        }

        if (!$this->_iProfileId || $aFileInfo['author_id'] != $this->_iProfileId) {
            echo _t('_Access denied');
            exit;
        }

        if (false === ($iHidden = $this->_oDb->toggleProductFileVisibility($aFileInfo['id']))) {
            echo _t('_Error Occured');
            exit;
        }

        echo $iHidden ? _t('_ch_store_product_file_hidden') : _t('_ch_store_product_file_visible');
        exit;
    }

    function actionDownload($iFileId)
    {
        $aFileInfo = $this->_oDb->getFileInfoByFileId((int)$iFileId);

        if (!$aFileInfo) {
            $this->_oTemplate->displayPageNotFound();
            exit;
        }

        if (!$this->isAllowedDownload($aFileInfo)) {
            $this->_oTemplate->displayAccessDenied();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

    // ================================== external actions

    function serviceHomepageBlock()
    {
        if (!$this->_oDb->isAnyPublicContent()) {
            return '';
        }

        ch_import('PageMain', $this->_aModule);
        $o            = new ChStorePageMain ($this);
        $o->sUrlStart = CH_WSB_URL_ROOT . '?';

        $sDefaultHomepageTab = $this->_oDb->getParam('ch_store_homepage_default_tab');

        $sBrowseMode = $sDefaultHomepageTab;
        switch ($_GET['ch_store_filter']) {
            case 'featured':
            case 'recent':
            case 'top':
            case 'popular':
            case 'free':
            case $sDefaultHomepageTab:
                $sBrowseMode = $_GET['ch_store_filter'];
                break;
        }

        return $o->ajaxBrowse(
            $sBrowseMode,
            $this->_oDb->getParam('ch_store_perpage_homepage'),
            array(
                _t('_ch_store_tab_featured') => array(
                    'href'    => CH_WSB_URL_ROOT . '?ch_store_filter=featured',
                    'active'  => 'featured' == $sBrowseMode,
                    'dynamic' => true
                ),
                _t('_ch_store_tab_recent')   => array(
                    'href'    => CH_WSB_URL_ROOT . '?ch_store_filter=recent',
                    'active'  => 'recent' == $sBrowseMode,
                    'dynamic' => true
                ),
                _t('_ch_store_tab_top')      => array(
                    'href'    => CH_WSB_URL_ROOT . '?ch_store_filter=top',
                    'active'  => 'top' == $sBrowseMode,
                    'dynamic' => true
                ),
                _t('_ch_store_tab_popular')  => array(
                    'href'    => CH_WSB_URL_ROOT . '?ch_store_filter=popular',
                    'active'  => 'popular' == $sBrowseMode,
                    'dynamic' => true
                ),
                _t('_ch_store_tab_free')     => array(
                    'href'    => CH_WSB_URL_ROOT . '?ch_store_filter=free',
                    'active'  => 'free' == $sBrowseMode,
                    'dynamic' => true
                ),
            )
        );
    }

    function serviceProfileBlock($iProfileId)
    {
        $aProfile = getProfileInfo($iProfileId);
        ch_import('PageMain', $this->_aModule);
        $o            = new ChStorePageMain ($this);
        $o->sUrlStart = getProfileLink($aProfile['ID']) . '?';

        return $o->ajaxBrowse(
            'user',
            $this->_oDb->getParam('ch_store_perpage_profile'),
            array(),
            process_db_input($aProfile['NickName'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION),
            true,
            false
        );
    }

    function serviceGetMemberMenuItem()
    {
        parent::_serviceGetMemberMenuItem(_t('_ch_store'), _t('_ch_store'), 'shopping-cart');
    }

    function serviceGetMemberMenuItemAddContent()
    {
        if (!$this->isAllowedAdd()) {
            return '';
        }

        return parent::_serviceGetMemberMenuItem(_t('_ch_store_products_single'), _t('_ch_store_products_single'),
            'shopping-cart', false, '&ch_store_filter=add_product');
    }

    function serviceGetWallPost($aEvent)
    {
        $aParams = array(
        	'icon' => 'shopping-cart',
            'txt_object' => '_ch_store_wall_object',
            'txt_added_new_single' => '_ch_store_wall_added_new',
        	'txt_added_new_title_single' => '_ch_store_wall_added_new_title',
            'txt_added_new_plural' => '_ch_store_wall_added_new_items',
        	'txt_added_new_title_plural' => '_ch_store_wall_added_new_title_items',
            'txt_privacy_view_event' => 'view_product',
            'obj_privacy' => $this->_oPrivacyProduct,
            'fields' => array(
                'owner' => 'author_id',
                'date' => 'created'
            )
        );

        return parent::_serviceGetWallPost($aEvent, $aParams);
    }

    function serviceGetWallAddComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_product',
            'obj_privacy'            => $this->_oPrivacyProduct
        );

        return parent::_serviceGetWallAddComment($aEvent, $aParams);
    }

    /**
     * DEPRICATED, saved for backward compatibility
     */
    function serviceGetWallPostComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_product',
            'obj_privacy'            => $this->_oPrivacyProduct
        );

        return parent::_serviceGetWallPostComment($aEvent, $aParams);
    }

    function serviceGetWallPostOutline($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_product',
            'obj_privacy'            => $this->_oPrivacyProduct,
            'templates'              => array(
                'grouped' => 'wall_outline_grouped'
            )
        );

        return parent::_serviceGetWallPostOutline($aEvent, 'shopping-cart', $aParams);
    }

    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array())
    {
        return parent::_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add'         => '_ch_store_spy_post',
            'change'      => '_ch_store_spy_post_change',
            'rate'        => '_ch_store_spy_rate',
            'commentPost' => '_ch_store_spy_comment',
        ));
    }

    function serviceGetSpyData()
    {
        $aOld = parent::serviceGetSpyData();
        $aNew = array(
            'handlers' => array(),
            'alerts'   => array(),
        );
        foreach ($aOld['handlers'] as $a) {
            if ('join' == $a['alert_action']) {
                continue;
            }
            $aNew['handlers'][] = $a;
        }
        foreach ($aOld['alerts'] as $a) {
            if ('join' == $a['action']) {
                continue;
            }
            $aNew['alerts'][] = $a;
        }

        return $aNew;
    }

    function serviceGetSubscriptionParams($sAction, $iEntryId)
    {
        $a = array(
            'change'      => _t('_ch_store_sbs_change'),
            'commentPost' => _t('_ch_store_sbs_comment'),
            'rate'        => _t('_ch_store_sbs_rate'),
        );

        return parent::_serviceGetSubscriptionParams($sAction, $iEntryId, $a);
    }

    function serviceGetItems($iVendorId)
    {
        $iVendorId = (int)$iVendorId;
        if ($iVendorId < 0) {
            return array();
        }

        $aItems = $this->_oDb->getFilesByAuthor($iVendorId);

        $aResult = array();
        foreach ($aItems as $aItem) {
            $aFile = ChWsbService::call('files', 'get_file_array', array($aItem['media_id']), 'Search');
            if (!$aFile['date']) {
                continue;
            }
            $aResult[] = array(
                'id'          => $aItem['id'],
                'title'       => $aItem['title'] . ' - ' . $aFile['title'] . ' / ' . $aItem['price'],
                'description' => $aItem['title'] . ' - ' . $aFile['title'],
                'url'         => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aItem['uri'],
                'price'       => $aItem['price'],
            );
        }

        return $aResult;
    }

    function serviceGetPaymentData()
    {
        return $this->_aModule;
    }

    function serviceGetCartItem($iClientId, $iItemId)
    {
        if (!$iItemId || !$iClientId) {
            return array();
        }

        $aItem = $this->_oDb->getFileInfoByFileId($iItemId);
        $aFile = ChWsbService::call('files', 'get_file_array', array($aItem['media_id']), 'Search');
        if (!$aFile['date']) {
            return array();
        }

        return array(
            'id'          => $aItem['id'],
            'title'       => $aItem['title'] . ' - ' . $aFile['title'],
            'description' => $aItem['title'] . ' - ' . $aFile['title'],
            'url'         => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aItem['uri'],
            'price'       => $aItem['price'],
        );
    }

    function serviceRegisterCartItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrderId)
    {
        $aItem = $this->_oDb->getFileInfoByFileId($iItemId);
        $aFile = ChWsbService::call('files', 'get_file_array', array($aItem['media_id']), 'Search');
        if (!$aFile['date']) {
            return array();
        }

        if (!$this->_oDb->registerCustomer($iClientId, $iItemId, $sOrderId, $iItemCount, time())) {
            return array();
        }

        return array(
            'id'          => $aItem['id'],
            'title'       => $aItem['title'] . ' - ' . $aFile['title'],
            'description' => $aItem['title'] . ' - ' . $aFile['title'],
            'url'         => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aItem['uri'],
            'price'       => $aItem['price'],
        );
    }

    function serviceUnregisterCartItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrderId)
    {
        return $this->_oDb->unregisterCustomer($iClientId, $iItemId, $sOrderId);
    }

    function serviceDeleteProfileData($iProfileId)
    {
        parent::serviceDeleteProfileData($iProfileId);

        // delete from list of customers
        $this->_oDb->removeCustomersFromAllEntries($iProfileId);
    }

    // ================================== admin actions

    function actionAdministration($sUrl = '')
    {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied();

            return;
        }

        $this->_oTemplate->pageStart();

        $aMenu = array(
            'pending_approval' => array(
                'title' => _t('_ch_store_menu_admin_pending_approval'),
                'href'  => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_approval',
                '_func' => array(
                    'name'   => 'actionAdministrationManage',
                    'params' => array(false, 'administration/pending_approval')
                ),
            ),
            'admin_entries'    => array(
                'title' => _t('_ch_store_menu_admin_entries'),
                'href'  => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array(
                    'name'   => 'actionAdministrationManage',
                    'params' => array(true, 'administration/admin_entries')
                ),
            ),
            'create'           => array(
                'title' => _t('_ch_store_menu_admin_add_entry'),
                'href'  => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
            'settings'         => array(
                'title' => _t('_ch_store_menu_admin_settings'),
                'href'  => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl])) {
            $sUrl = 'pending_approval';
        }

        $aMenu[$sUrl]['active'] = 1;
        $sContent               = call_user_func_array(array($this, $aMenu[$sUrl]['_func']['name']),
            $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock($sContent, _t('_ch_store_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin(array(
            'admin.css',
            'unit.css',
            'twig.css',
            'main.css',
            'forms_extra.css',
            'forms_adv.css'
        ));
        $this->_oTemplate->pageCodeAdmin(_t('_ch_store_page_title_administration'));
    }

    function actionAdministrationSettings()
    {
        return parent::_actionAdministrationSettings('Store');
    }

    function actionAdministrationManage($isAdminEntries = false, $sUrl = '')
    {
        return parent::_actionAdministrationManage($isAdminEntries, '_ch_store_admin_delete',
            '_ch_store_admin_activate', $sUrl);
    }

    // ================================== events


    // ================================== permissions

    function isEntryAdmin($aDataEntry, $iIdProfile = 0)
    {
        if (!$iIdProfile) {
            $iIdProfile = $this->_iProfileId;
        }

        return ($this->isAdmin() || $aDataEntry['author_id'] == $iIdProfile);
    }

    function isAllowedView($aDataEntry, $isPerformAction = false)
    {
        // admin and owner always have access
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId) {
            return true;
        }

        // check admin acl
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_STORE_VIEW_PRODUCT, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) {
            return false;
        }

        // check user group
        return $this->_oPrivacyProduct->check('view_product', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedBrowse($isPerformAction = false)
    {
        if ($this->isAdmin()) {
            return true;
        }
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_STORE_BROWSE, $isPerformAction);

        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch($isPerformAction = false)
    {
        if ($this->isAdmin()) {
            return true;
        }
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_STORE_SEARCH, $isPerformAction);

        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd($isPerformAction = false)
    {
        if ($this->isAdmin()) {
            return true;
        }
        if (!$GLOBALS['logged']['member']) {
            return false;
        }
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_STORE_ADD_PRODUCT, $isPerformAction);

        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedEdit($aDataEntry, $isPerformAction = false)
    {
        // admin and owner can always edit product
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) {
            return true;
        }

        // check acl
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_STORE_EDIT_ANY_PRODUCT, $isPerformAction);

        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedMarkAsFeatured($aDataEntry, $isPerformAction = false)
    {
        if ($this->isAdmin()) {
            return true;
        }
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_STORE_MARK_AS_FEATURED, $isPerformAction);

        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedBroadcast($aDataEntry, $isPerformAction = false)
    {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) {
            return true;
        }
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_STORE_BROADCAST_MESSAGE, $isPerformAction);

        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedDelete(&$aDataEntry, $isPerformAction = false)
    {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) {
            return true;
        }
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_STORE_DELETE_ANY_PRODUCT, $isPerformAction);

        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedActivate(&$aDataEntry, $isPerformAction = false)
    {
        if ($aDataEntry['status'] != 'pending') {
            return false;
        }
        if ($this->isAdmin()) {
            return true;
        }
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_STORE_APPROVE_PRODUCT, $isPerformAction);

        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedShare(&$aDataEntry)
    {
        if ($aDataEntry['allow_view_product_to'] != CH_WSB_PG_ALL) {
            return false;
        }

        return true;
    }

    function isAllowedPurchase(&$aItem)
    {
        if (!$this->_iProfileId || 0 == $aItem['price']) {
            return false;
        }

        return $this->isAdmin() || $this->_oPrivacyFile->check('purchase', $aItem['id'], $this->_iProfileId);
    }

    function isAllowedPostInForum(&$aDataEntry, $iProfileId = -1)
    {
        if (-1 == $iProfileId) {
            $iProfileId = $this->_iProfileId;
        }

        return $this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) || $this->_oPrivacyProduct->check('post_in_forum',
            $aDataEntry['id'], $iProfileId);
    }

    function isAllowedReadForum(&$aDataEntry, $iProfileId = -1)
    {
        if (-1 == $iProfileId) {
            $iProfileId = $this->_iProfileId;
        }

        return $this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) || $this->_oPrivacyProduct->check('view_forum',
            $aDataEntry['id'], $iProfileId);
    }

    function isAllowedDownload(&$aItem)
    {
        if ($this->isAdmin()) {
            return true;
        }
        if (0 == $aItem['price'] && $this->_oPrivacyFile->check('purchase', $aItem['id'], $this->_iProfileId)) {
            return true;
        }
        if ($this->_oDb->isPurchasedItem($this->_iProfileId, $aItem['id'])) {
            return true;
        }

        return false;
    }

    function isAllowedRate(&$aDataEntry)
    {
        return $this->isAdmin() || $this->isCustomer($aDataEntry);
    }

    function isAllowedComments(&$aDataEntry)
    {
        return $this->isAdmin() || $this->isCustomer($aDataEntry);
    }

    function isAllowedCreatorCommentsDeleteAndEdit(&$aEvent, $isPerformAction = false)
    {
        if ($this->isAdmin()) {
            return true;
        }
        if (!$GLOBALS['logged']['member'] || $aEvent['author_id'] != $this->_iProfileId) {
            return false;
        }
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_STORE_PRODUCT_COMMENTS_DELETE_AND_EDIT, $isPerformAction);

        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isCustomer($aItem)
    {
    	if($aItem['price_range'] == 'Free')
    		return true;

        return $this->_oDb->isCustomer($this->_iProfileId, $aItem['id']);
    }

    function _defineActions()
    {
        defineMembershipActions(array(
            'store view product',
            'store browse',
            'store search',
            'store add product',
            'store edit any product',
            'store delete any product',
            'store mark as featured',
            'store approve product',
            'store broadcast message'
        ));
    }

    // ================================== other function

    function getGroupName($mixedId)
    {
        if ('m' == $mixedId[0]) {
            require_once(CH_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
            $a = getMembershipInfo(substr($mixedId, 1));

            return $a && isset($a['Name']) ? $a['Name'] : 'undefined';
        } else {
            ch_import('ChWsbPrivacyQuery');
            $oPrivacyQuery = new ChWsbPrivacyQuery();
            $a             = $oPrivacyQuery->getGroupsBy(array('type' => 'id', 'id' => $mixedId));

            return $a && (int)$a['owner_id'] == 0 ? _t('_ps_group_' . $a['id'] . '_title') : $a['title'];
        }
    }

    function _browseMy(&$aProfile, $sTitle = null)
    {
        parent::_browseMy($aProfile, _t('_ch_store_page_title_my_store'));
    }

    function _formatPriceRange($aData)
    {
        $sPrice = '';
        if ('Free' == $aData['price_range']) {
            $sPrice = _t('_ch_store_free_product');
        } else {
            $sPrice = str_replace('.00', '', sprintf($aData['price_range'], getParam('pmt_default_currency_sign'),
                getParam('pmt_default_currency_sign')));
        }

        return $sPrice;
    }

    function _formatSnippetTextForOutline($aEntryData)
    {
        return $this->_oTemplate->parseHtmlByName('wall_outline_extra_info', array(
            'desc'        => $this->_formatSnippetText($aEntryData, 200),
            'price_range' => $this->_formatPriceRange($aEntryData),
        ));
    }
}
