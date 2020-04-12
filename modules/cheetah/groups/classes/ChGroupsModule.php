<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ch_groups_import ($sClassPostfix, $aModuleOverwright = array())
{
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'groups') {
        $oMain = ChWsbModule::getInstance('ChGroupsModule');
        $a = $oMain->_aModule;
    }
    ch_import ($sClassPostfix, $a) ;
}

ch_import('ChWsbPaginate');
ch_import('ChWsbAlerts');
ch_import('ChWsbTwigModule');

define ('CH_GROUPS_PHOTOS_CAT', 'Groups');
define ('CH_GROUPS_PHOTOS_TAG', 'groups');

define ('CH_GROUPS_VIDEOS_CAT', 'Groups');
define ('CH_GROUPS_VIDEOS_TAG', 'groups');

define ('CH_GROUPS_SOUNDS_CAT', 'Groups');
define ('CH_GROUPS_SOUNDS_TAG', 'groups');

define ('CH_GROUPS_FILES_CAT', 'Groups');
define ('CH_GROUPS_FILES_TAG', 'groups');

define ('CH_GROUPS_MAX_FANS', 1000);

/**
 * Groups module
 *
 * This module allow users to create user's groups,
 * users can rate, comment and discuss group.
 * Group can have photos, videos, sounds and files, uploaded
 * by group's fans and/or admins.
 *
 *
 *
 * Profile's Wall:
 * 'add group' event is displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following activity is displayed for content_activity:
 * add - new group was created
 * change - group was changed
 * join - somebody joined group
 * rate - somebody rated group
 * commentPost - somebody posted comment in group
 *
 *
 *
 * Memberships/ACL:
 * groups view group - CH_GROUPS_VIEW_GROUP
 * groups browse - CH_GROUPS_BROWSE
 * groups search - CH_GROUPS_SEARCH
 * groups add group - CH_GROUPS_ADD_GROUP
 * groups comments delete and edit - CH_GROUPS_COMMENTS_DELETE_AND_EDIT
 * groups edit any group - CH_GROUPS_EDIT_ANY_GROUP
 * groups delete any group - CH_GROUPS_DELETE_ANY_GROUP
 * groups mark as featured - CH_GROUPS_MARK_AS_FEATURED
 * groups approve groups - CH_GROUPS_APPROVE_GROUPS
 * groups broadcast message - CH_GROUPS_BROADCAST_MESSAGE
 *
 *
 *
 * Service methods:
 *
 * Homepage block with different groups
 * @see ChGroupsModule::serviceHomepageBlock
 * ChWsbService::call('groups', 'homepage_block', array());
 *
 * Profile block with user's groups
 * @see ChGroupsModule::serviceProfileBlock
 * ChWsbService::call('groups', 'profile_block', array($iProfileId));
 *
 * Group's forum permissions (for internal usage only)
 * @see ChGroupsModule::serviceGetForumPermission
 * ChWsbService::call('groups', 'get_forum_permission', array($iMemberId, $iForumId));
 *
 * Member menu item for my groups (for internal usage only)
 * @see ChGroupsModule::serviceGetMemberMenuItem
 * ChWsbService::call('groups', 'get_member_menu_item');
 *
 * Member menu item for group adding (for internal usage only)
 * @see ChGroupsModule::serviceGetMemberMenuItemAddContent
 * ChWsbService::call('groups', 'get_member_menu_item_add_content');
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'ch_groups'
 * The following alerts are rised
 *
 *  join - user joined a group
 *      $iObjectId - group id
 *      $iSenderId - joined user
 *
 *  join_request - user want to join a group
 *      $iObjectId - group id
 *      $iSenderId - user id which want to join a group
 *
 *  join_reject - user was rejected to join a group
 *      $iObjectId - group id
 *      $iSenderId - regected user id
 *
 *  fan_remove - fan was removed from a group
 *      $iObjectId - group id
 *      $iSenderId - fan user if which was removed from admins
 *
 *  fan_become_admin - fan become group's admin
 *      $iObjectId - group id
 *      $iSenderId - nerw group's fan user id
 *
 *  admin_become_fan - group's admin become regular fan
 *      $iObjectId - group id
 *      $iSenderId - group's admin user id which become regular fan
 *
 *  join_confirm - group's admin confirmed join request
 *      $iObjectId - group id
 *      $iSenderId - condirmed user id
 *
 *  add - new group was added
 *      $iObjectId - group id
 *      $iSenderId - creator of a group
 *      $aExtras['Status'] - status of added group
 *
 *  change - group's info was changed
 *      $iObjectId - group id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed group
 *
 *  delete - group was deleted
 *      $iObjectId - group id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - group was marked/unmarked as featured
 *      $iObjectId - group id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if group was marked as featured and 0 - if group was removed from featured
 *
 */
class ChGroupsModule extends ChWsbTwigModule
{
    var $_oPrivacy;
    var $_aQuickCache = array ();

    function __construct(&$aModule)
    {
        parent::__construct($aModule);
        $this->_sFilterName = 'ch_groups_filter';
        $this->_sPrefix = 'ch_groups';

        ch_import ('Privacy', $aModule);
        $this->_oPrivacy = new ChGroupsPrivacy($this);

        $GLOBALS['oChGroupsModule'] = &$this;
    }

    function actionHome ()
    {
        parent::_actionHome(_t('_ch_groups_page_title_home'));
    }

    function actionFiles ($sUri)
    {
        parent::_actionFiles ($sUri, _t('_ch_groups_page_title_files'));
    }

    function actionSounds ($sUri)
    {
        parent::_actionSounds ($sUri, _t('_ch_groups_page_title_sounds'));
    }

    function actionVideos ($sUri)
    {
        parent::_actionVideos ($sUri, _t('_ch_groups_page_title_videos'));
    }

    function actionPhotos ($sUri)
    {
        parent::_actionPhotos ($sUri, _t('_ch_groups_page_title_photos'));
    }

    function actionComments ($sUri)
    {
        parent::_actionComments ($sUri, _t('_ch_groups_page_title_comments'));
    }

    function actionBrowseFans ($sUri)
    {
        parent::_actionBrowseFans ($sUri, 'isAllowedViewFans', 'getFansBrowse', $this->_oDb->getParam('ch_groups_perpage_browse_fans'), 'browse_fans/', _t('_ch_groups_page_title_fans'));
    }

    function actionView ($sUri)
    {
        parent::_actionView ($sUri, _t('_ch_groups_msg_pending_approval'));
    }

    function actionUploadPhotos ($sUri)
    {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadPhotos', 'images', array ('images_choice', 'images_upload'), _t('_ch_groups_page_title_upload_photos'));
    }

    function actionUploadVideos ($sUri)
    {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadVideos', 'videos', array ('videos_choice', 'videos_upload'), _t('_ch_groups_page_title_upload_videos'));
    }

    function actionUploadSounds ($sUri)
    {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadSounds', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_ch_groups_page_title_upload_sounds'));
    }

    function actionUploadFiles ($sUri)
    {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadFiles', 'files', array ('files_choice', 'files_upload'), _t('_ch_groups_page_title_upload_files'));
    }

    function actionBroadcast ($iEntryId)
    {
        parent::_actionBroadcast ($iEntryId, _t('_ch_groups_page_title_broadcast'), _t('_ch_groups_msg_broadcast_no_recipients'), _t('_ch_groups_msg_broadcast_message_sent'));
    }

    function actionInvite ($iEntryId)
    {
        parent::_actionInvite ($iEntryId, 'ch_groups_invitation', $this->_oDb->getParam('ch_groups_max_email_invitations'), _t('_ch_groups_msg_invitation_sent'), _t('_ch_groups_msg_no_users_to_invite'), _t('_ch_groups_page_title_invite'));
    }

    function _getInviteParams ($aDataEntry, $aInviter)
    {
        return array (
                'GroupName' => $aDataEntry['title'],
                'GroupLocation' => _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']) . (trim($aDataEntry['city']) ? ', '.$aDataEntry['city'] : '') . ', ' . $aDataEntry['zip'],
                'GroupUrl' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'InviterUrl' => $aInviter ? getProfileLink($aInviter['ID']) : 'javascript:void(0);',
                'InviterNickName' => $aInviter ? getNickName($aInviter['ID']) : _t('_ch_groups_user_unknown'),
                'InvitationText' => nl2br(process_pass_data(strip_tags($_POST['inviter_text']))),
            );
    }

    function actionCalendar ($iYear = '', $iMonth = '')
    {
        parent::_actionCalendar ($iYear, $iMonth, _t('_ch_groups_page_title_calendar'));
    }

    function actionSearch ($sKeyword = '', $sCategory = '')
    {
        parent::_actionSearch ($sKeyword, $sCategory, _t('_ch_groups_page_title_search'));
    }

    function actionAdd ()
    {
        parent::_actionAdd (_t('_ch_groups_page_title_add'));
    }

    function actionEdit ($iEntryId)
    {
        parent::_actionEdit ($iEntryId, _t('_ch_groups_page_title_edit'));
    }

    function actionDelete ($iEntryId)
    {
        parent::_actionDelete ($iEntryId, _t('_ch_groups_msg_group_was_deleted'));
    }

    function actionMarkFeatured ($iEntryId)
    {
        parent::_actionMarkFeatured ($iEntryId, _t('_ch_groups_msg_added_to_featured'), _t('_ch_groups_msg_removed_from_featured'));
    }

    function actionJoin ($iEntryId, $iProfileId)
    {
        parent::_actionJoin ($iEntryId, $iProfileId, _t('_ch_groups_msg_joined_already'), _t('_ch_groups_msg_joined_request_pending'), _t('_ch_groups_msg_join_success'), _t('_ch_groups_msg_join_success_pending'), _t('_ch_groups_msg_leave_success'));
    }

    function actionSharePopup ($iEntryId)
    {
        parent::_actionSharePopup ($iEntryId, _t('_ch_groups_caption_share_group'));
    }

    function actionManageFansPopup ($iEntryId)
    {
        parent::_actionManageFansPopup ($iEntryId, _t('_ch_groups_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', CH_GROUPS_MAX_FANS);
    }

    function actionTags()
    {
        parent::_actionTags (_t('_ch_groups_page_title_tags'));
    }

    function actionCategories()
    {
        parent::_actionCategories (_t('_ch_groups_page_title_categories'));
    }

    function actionDownload ($iEntryId, $iMediaId)
    {
        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'files');

        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getEntryByIdAndOwner((int)$iEntryId, 0, true))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }

        if (!$this->isAllowedView ($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

    // ================================== external actions

    /**
     * Homepage block with different groups
     * @return html to display on homepage in a block
     */
    function serviceHomepageBlock ()
    {
        if (!$this->_oDb->isAnyPublicContent())
            return '';

        ch_import ('PageMain', $this->_aModule);
        $o = new ChGroupsPageMain ($this);
        $o->sUrlStart = CH_WSB_URL_ROOT . '?';

        $sDefaultHomepageTab = $this->_oDb->getParam('ch_groups_homepage_default_tab');
        $sBrowseMode = $sDefaultHomepageTab;
        switch ($_GET['ch_groups_filter']) {
            case 'featured':
            case 'recent':
            case 'top':
            case 'popular':
            case $sDefaultHomepageTab:
                $sBrowseMode = $_GET['ch_groups_filter'];
                break;
        }

        return $o->ajaxBrowse(
            $sBrowseMode,
            $this->_oDb->getParam('ch_groups_perpage_homepage'),
            array(
                _t('_ch_groups_tab_featured') => array('href' => CH_WSB_URL_ROOT . '?ch_groups_filter=featured', 'active' => 'featured' == $sBrowseMode, 'dynamic' => true),
                _t('_ch_groups_tab_recent') => array('href' => CH_WSB_URL_ROOT . '?ch_groups_filter=recent', 'active' => 'recent' == $sBrowseMode, 'dynamic' => true),
                _t('_ch_groups_tab_top') => array('href' => CH_WSB_URL_ROOT . '?ch_groups_filter=top', 'active' => 'top' == $sBrowseMode, 'dynamic' => true),
                _t('_ch_groups_tab_popular') => array('href' => CH_WSB_URL_ROOT . '?ch_groups_filter=popular', 'active' => 'popular' == $sBrowseMode, 'dynamic' => true),
            )
        );
    }

    /**
     * Profile block with user's groups
     * @param $iProfileId profile id
     * @return html to display on homepage in a block
     */
    function serviceProfileBlock ($iProfileId)
    {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        ch_import ('PageMain', $this->_aModule);
        $o = new ChGroupsPageMain ($this);
        $o->sUrlStart = getProfileLink($aProfile['ID']) . '?';

        return $o->ajaxBrowse(
            'user',
            $this->_oDb->getParam('ch_groups_perpage_profile'),
            array(),
            process_db_input ($aProfile['NickName'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION),
            true,
            false
        );
    }

    /**
     * Profile block with groups user joined
     * @param $iProfileId profile id
     * @return html to display on homepage in a block
     */
    function serviceProfileBlockJoined ($iProfileId)
    {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        ch_import ('PageMain', $this->_aModule);
        $o = new ChGroupsPageMain ($this);
        $o->sUrlStart = $_SERVER['PHP_SELF'] . '?' . ch_encode_url_params($_GET, array('page'));
        return $o->ajaxBrowse(
            'joined',
            $this->_oDb->getParam('ch_groups_perpage_profile'),
            array(),
            process_db_input ($aProfile['NickName'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION),
            true,
            false
        );
    }

    function serviceGetMemberMenuItem ()
    {
        return parent::_serviceGetMemberMenuItem (_t('_ch_groups'), _t('_ch_groups'), 'users');
    }

    function serviceGetMemberMenuItemAddContent ()
    {
        if (!$this->isAllowedAdd())
            return '';
        return parent::_serviceGetMemberMenuItem (_t('_ch_groups_group_single'), _t('_ch_groups_group_single'), 'users', false, '&ch_groups_filter=add_group');
    }

    function serviceGetWallPost ($aEvent)
    {
        $aParams = array(
        	'icon' => 'users',
            'txt_object' => '_ch_groups_wall_object',
            'txt_added_new_single' => '_ch_groups_wall_added_new',
        	'txt_added_new_title_single' => '_ch_groups_wall_added_new_title',
            'txt_added_new_plural' => '_ch_groups_wall_added_new_items',
        	'txt_added_new_title_plural' => '_ch_groups_wall_added_new_title_items',
            'txt_privacy_view_event' => 'view_group',
            'obj_privacy' => $this->_oPrivacy,
        	'fields' => array(
                'owner' => 'author_id',
                'date' => 'created'
            )
        );
        return parent::_serviceGetWallPost ($aEvent, $aParams);
    }

	function serviceGetWallAddComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_group',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallAddComment($aEvent, $aParams);
    }

    /**
     * DEPRICATED, saved for backward compatibility
     */
    function serviceGetWallPostComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_group',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallPostComment($aEvent, $aParams);
    }

    function serviceGetWallPostOutline($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_group',
            'obj_privacy' => $this->_oPrivacy,
            'templates' => array(
                'grouped' => 'wall_outline_grouped'
            )
        );
        return parent::_serviceGetWallPostOutline($aEvent, 'users', $aParams);
    }

    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array())
    {
        return parent::_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_ch_groups_spy_post',
            'change' => '_ch_groups_spy_post_change',
            'join' => '_ch_groups_spy_join',
            'rate' => '_ch_groups_spy_rate',
            'commentPost' => '_ch_groups_spy_comment',
        ));
    }

    function serviceGetSubscriptionParams ($sAction, $iEntryId)
    {
        $a = array (
            'change' => _t('_ch_groups_sbs_change'),
            'commentPost' => _t('_ch_groups_sbs_comment'),
            'rate' => _t('_ch_groups_sbs_rate'),
            'join' => _t('_ch_groups_sbs_join'),
        );

        return parent::_serviceGetSubscriptionParams ($sAction, $iEntryId, $a);
    }

    /**
     * Install map support
     */
    function serviceMapInstall()
    {
        if (!ChWsbModule::getInstance('ChWmapModule'))
            return false;

        return ChWsbService::call('wmap', 'part_install', array('groups', array(
            'part' => 'groups',
            'title' => '_ch_groups',
            'title_singular' => '_ch_groups_group_single',
            'icon' => 'modules/cheetah/groups/|map_marker.png',
            'icon_site' => 'users',
            'join_table' => 'ch_groups_main',
            'join_where' => "AND `p`.`status` = 'approved'",
            'join_field_id' => 'id',
            'join_field_country' => 'country',
            'join_field_city' => 'city',
            'join_field_state' => '',
            'join_field_zip' => 'zip',
            'join_field_address' => '',
            'join_field_title' => 'title',
            'join_field_uri' => 'uri',
            'join_field_author' => 'author_id',
            'join_field_privacy' => 'allow_view_group_to',
            'permalink' => 'modules/?r=groups/view/',
        )));
    }

    // ================================== admin actions

    function actionAdministration ($sUrl = '')
    {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $aMenu = array(
            'pending_approval' => array(
                'title' => _t('_ch_groups_menu_admin_pending_approval'),
                'href' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_approval',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false, 'administration/pending_approval')),
            ),
            'admin_entries' => array(
                'title' => _t('_ch_groups_menu_admin_entries'),
                'href' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true, 'administration/admin_entries')),
            ),
            'create' => array(
                'title' => _t('_ch_groups_menu_admin_add_entry'),
                'href' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
            'settings' => array(
                'title' => _t('_ch_groups_menu_admin_settings'),
                'href' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'pending_approval';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_ch_groups_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css'));
        $this->_oTemplate->pageCodeAdmin (_t('_ch_groups_page_title_administration'));
    }

    function actionAdministrationSettings ()
    {
        return parent::_actionAdministrationSettings ('Groups');
    }

    function actionAdministrationManage ($isAdminEntries = false, $sUrl = '')
    {
        return parent::_actionAdministrationManage ($isAdminEntries, '_ch_groups_admin_delete', '_ch_groups_admin_activate', $sUrl);
    }

    // ================================== events


    function onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry)
    {
        parent::_onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry, 'ch_groups_join_request', CH_GROUPS_MAX_FANS);
    }

    function onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry)
    {
        parent::_onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry, 'ch_groups_join_reject');
    }

    function onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry)
    {
        parent::_onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry, 'ch_groups_fan_remove');
    }

    function onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry)
    {
        parent::_onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry, 'ch_groups_fan_become_admin');
    }

    function onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry)
    {
        parent::_onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry, 'ch_groups_admin_become_fan');
    }

    function onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry)
    {
        parent::_onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry, 'ch_groups_join_confirm');
    }

    // ================================== permissions

    function isAllowedView ($aDataEntry, $isPerformAction = false)
    {
        // admin and owner always have access
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId)
            return true;

        // check admin acl
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_GROUPS_VIEW_GROUP, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;

        // check user group
        return $this->_oPrivacy->check('view_group', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedBrowse ($isPerformAction = false)
    {
        if ($this->isAdmin())
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_GROUPS_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch ($isPerformAction = false)
    {
        if ($this->isAdmin())
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_GROUPS_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd ($isPerformAction = false)
    {
        if ($this->isAdmin())
            return true;
        if (!$GLOBALS['logged']['member'])
            return false;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_GROUPS_ADD_GROUP, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedEdit ($aDataEntry, $isPerformAction = false)
    {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)))
            return true;

        // check acl
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_GROUPS_EDIT_ANY_GROUP, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedMarkAsFeatured ($aDataEntry, $isPerformAction = false)
    {
        if ($this->isAdmin())
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_GROUPS_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedBroadcast ($aDataEntry, $isPerformAction = false)
    {
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_GROUPS_BROADCAST_MESSAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedDelete (&$aDataEntry, $isPerformAction = false)
    {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)))
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_GROUPS_DELETE_ANY_GROUP, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedActivate (&$aDataEntry, $isPerformAction = false)
    {
        if ($aDataEntry['status'] != 'pending')
            return false;
        if ($this->isAdmin())
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_GROUPS_APPROVE_GROUPS, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedJoin (&$aDataEntry)
    {
        if (!$this->_iProfileId)
            return false;
        return $this->_oPrivacy->check('join', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedSendInvitation (&$aDataEntry)
    {
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) ? true : false;
    }

    function isAllowedShare (&$aDataEntry)
    {
    	if($aDataEntry['allow_view_group_to'] != CH_WSB_PG_ALL)
    		return false;

        return true;
    }

    function isAllowedPostInForum(&$aDataEntry, $iProfileId = -1)
    {
        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) || $this->_oPrivacy->check('post_in_forum', $aDataEntry['id'], $iProfileId);
    }

    function isAllowedReadForum(&$aDataEntry, $iProfileId = -1)
    {
        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) || $this->_oPrivacy->check('view_forum', $aDataEntry['id'], $iProfileId);
    }

    function isAllowedRate(&$aDataEntry)
    {
        if ($this->isAdmin())
            return true;
        return $this->_oPrivacy->check('rate', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedComments(&$aDataEntry)
    {
        if ($this->isAdmin())
            return true;
        return $this->_oPrivacy->check('comment', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedViewFans(&$aDataEntry)
    {
        if ($this->isAdmin())
            return true;
        return $this->_oPrivacy->check('view_fans', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadPhotos(&$aDataEntry)
    {
        if (!ChWsbRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            return false;
        if (!$this->_iProfileId)
            return false;
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        return $this->_oPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadVideos(&$aDataEntry)
    {
        if (!ChWsbRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            return false;
        if (!$this->_iProfileId)
            return false;
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForVideos())
            return false;
        return $this->_oPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadSounds(&$aDataEntry)
    {
        if (!ChWsbRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            return false;
        if (!$this->_iProfileId)
            return false;
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForSounds())
            return false;
        return $this->_oPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFiles(&$aDataEntry)
    {
        if (!ChWsbRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            return false;
        if (!$this->_iProfileId)
            return false;
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForFiles())
            return false;
        return $this->_oPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedCreatorCommentsDeleteAndEdit (&$aDataEntry, $isPerformAction = false)
    {
        if ($this->isAdmin())
            return true;
        if (getParam('ch_groups_author_comments_admin') && $this->isEntryAdmin($aDataEntry))
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_GROUPS_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedManageAdmins($aDataEntry)
    {
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))
            return true;
        return false;
    }

    function isAllowedManageFans($aDataEntry)
    {
        return $this->isEntryAdmin($aDataEntry);
    }

    function isFan($aDataEntry, $iProfileId = 0, $isConfirmed = true)
    {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isFan ($aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }

    function isEntryAdmin($aDataEntry, $iProfileId = 0)
    {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;
        return $this->_oDb->isGroupAdmin ($aDataEntry['id'], $iProfileId) && isProfileActive($iProfileId);
    }

    function _defineActions ()
    {
        defineMembershipActions(array('groups view group', 'groups browse', 'groups search', 'groups add group', 'groups comments delete and edit', 'groups edit any group', 'groups delete any group', 'groups mark as featured', 'groups approve groups', 'groups broadcast message'));
    }

    function _browseMy (&$aProfile, $sTitle = null)
    {
        parent::_browseMy ($aProfile, _t('_ch_groups_page_title_my_groups'));
    }

    function _formatLocation (&$aDataEntry, $isCountryLink = false, $isFlag = false)
    {
        $sFlag = $isFlag ? ' ' . genFlag($aDataEntry['country']) : '';
        $sCountry = _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']);
        if ($isCountryLink)
            $sCountry = '<a href="' . $this->_oConfig->getBaseUri() . 'browse/country/' . strtolower($country['Country']) . '">' . $sCountry . '</a>';
        return (trim($aDataEntry['city']) ? $aDataEntry['city'] . ', ' : '') . $sCountry . $sFlag;
    }

    function _formatSnippetTextForOutline($aEntryData)
    {
        return $this->_oTemplate->parseHtmlByName('wall_outline_extra_info', array(
            'desc' => $this->_formatSnippetText($aEntryData, 200),
            'location' => $this->_formatLocation($aEntryData, false, false),
            'fans_count' => $aEntryData['fans_count'],
        ));
    }
}
