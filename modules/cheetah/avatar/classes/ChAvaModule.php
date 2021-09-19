<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ch_avatar_import ($sClassPostfix, $aModuleOverwright = array())
{
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'avatar') {
        $oMain = ChWsbModule::getInstance('ChAvaModule');
        $a = $oMain->_aModule;
    }
    ch_import ($sClassPostfix, $a) ;
}

ch_import('ChWsbModule');
ch_import('ChWsbAlerts');

$aPathInfo = pathinfo(__FILE__);
require_once ($aPathInfo['dirname'] . '/../include.php');

define ('CH_AVA_DIR_SITE_AVATARS', CH_DIRECTORY_PATH_MODULES . 'cheetah/avatar/data/ready/');
define ('CH_AVA_URL_SITE_AVATARS', CH_WSB_URL_MODULES . 'cheetah/avatar/data/ready/');

define ('CH_AVA_DIR_TMP', CH_DIRECTORY_PATH_MODULES . 'cheetah/avatar/data/tmp/');
define ('CH_AVA_URL_TMP', CH_WSB_URL_MODULES . 'cheetah/avatar/data/tmp/');

define ('CH_AVA_PRE_RESIZE_W', 700);
define ('CH_AVA_PRE_RESIZE_H', 700);

/**
 * Avatar module by Cheetah
 *
 * This module allow user to crop avatar image from any photo.
 * This is default module and Cheetah can not work properly without this module.
 * 'Avatar' field in Profiles table is used to store current user avatar id.
 * Avatar id is used to store avatar image, so it is easy to get avarat image if you know avatar id.
 * You can get avatar image directly, using defines in include.php file.
 * Refer to include.php file for all defines descriptions.
 *
 * Example of using this module to get avatar images:
 *
 * include_once (CH_DIRECTORY_PATH_MODULES . 'cheetah/avatar/include.php')
 * echo CH_AVA_URL_USER_AVATARS . $aProfile['Avatar'] . CH_AVA_EXT; // avatar image
 * echo CH_AVA_URL_USER_AVATARS . $aProfile['Avatar'] . 'i'. CH_AVA_EXT; // avatar icon
 *
 *
 *
 * Profile's Wall:
 * 'add avatar' and 'change avatar' events are displayed on profile's wall
 *
 *
 *
 * Spy:
 * no spy activity
 *
 *
 *
 * Memberships/ACL:
 * avatar upload - CH_AVATAR_UPLOAD
 * avatar edit any - CH_AVATAR_EDIT_ANY
 * avatar delete any - CH_AVATAR_DELETE_ANY
 *
 *
 *
 * Service methods:
 *
 * Set image for cropping.
 * @see ChAvaModule::serviceSetImageForCropping
 * ChWsbService::call('avatar', 'set_image_for_cropping', array($iProfileId, $sOrigPath));
 *
 * Crop image html
 * @see ChAvaModule::serviceCropTool
 * ChWsbService::call('avatar', 'crop_tool', array($aParams));
 *
 * Delete all profile's avatars
 * @see ChAvaModule::serviceDeleteProfileAvatars
 * ChWsbService::call('avatar', 'delete_profile_avatars', array($iProfileId));
 *
 * After join redirection
 * @see ChAvaModule::serviceJoin
 * ChWsbService::call('avatar', 'join', array($iMemID, $sStatusText));
 *
 * Site avatars html
 * @see ChAvaModule::serviceGetSiteAvatars
 * ChWsbService::call('avatar', 'get_site_avatars', array($iPage));
 *
 * My avatars html
 * @see ChAvaModule::serviceGetMyAvatars
 * ChWsbService::call('avatar', 'get_my_avatars', array($iProfileId));
 *
 * make avatar from particular image, also it makes the image square
 * @see ChAvaModule::serviceMakeAvatarFromImage
 * ChWsbService::call ('avatar', 'make_avatar_from_image', array ('/tmp/abc.jpg'));
 *
 * make avatar from image url, also it makes the image square
 * @see ChAvaModule::serviceMakeAvatarFromImageUrl
 * ChWsbService::call ('avatar', 'make_avatar_from_image_url', array ('http://abc.com/123.jpg'));
 *
 * Get image from photos module and make avbatar from it, also it makes it square
 * @see ChAvaModule::serviceMakeAvatarFromSharedPhotoAuto
 * ChWsbService::call ('avatar', 'make_avatar_from_shared_photo_auto', array (80));
 *
 *
 * Alerts:
 * Alerts type/unit - 'ch_avatar'
 * The following alerts are rised
 *
 *  add - new avatar is added
 *      $iObjectId - avatar id
 *      $iSenderId - avatar's owner
 *
 *  change - avatar was changed
 *      $iObjectId - avatar id
 *      $iSenderId - avatar's owner
 *
 *  delete - avatar was deleted
 *      $iObjectId - avatar id
 *      $iSenderId - avatar's owner
 *
 */
class ChAvaModule extends ChWsbModule
{
    var $_iProfileId;
    var $_aAllowedExt = array (
        'jpg',
        'jpeg',
        'gif',
        'png',
    );

    function __construct(&$aModule)
    {
        parent::__construct($aModule);
        $this->_iProfileId = getLoggedId();
        $GLOBALS['oChAvaModule'] = &$this;
    }

    function actionHome ()
    {
        if (!$this->_iProfileId) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        if ('ch_photos_thumb' == getParam('sys_member_info_thumb') && 'ch_photos_icon' == getParam('sys_member_info_thumb_icon')) {
            $sProfilePhotosUrl = ChWsbService::call('photos', 'get_manage_profile_photo_url', array($this->_iProfileId, 'profile_album_name'));
            header('Location: ' . $sProfilePhotosUrl);
            exit;
        }

        $this->_oTemplate->pageStart();

        if ($_GET['make_avatar_from_shared_photo'] > 0) {
            if (!$this->isAllowedAdd()) {
                $aVars = array ('msg' => _t('_ch_ava_msg_access_denied'));
                echo $this->_oTemplate->parseHtmlByName ('error_plank', $aVars);
            } else {
                $this->_makeAvatarFromSharedPhoto((int)$_GET['make_avatar_from_shared_photo']);
            }
        }

        if (isset($_GET['join_text']) && $_GET['join_text']) {
            echo MsgBox(_t(strip_tags($_GET['join_text'])));
        }

        if (isset($_POST['set_avatar'])) {
            if (!$this->isAllowedAdd ())
                $aVars = array ('msg' => _t('_ch_ava_msg_access_denied'));
            elseif (!$this->_cropAvatar ())
                $aVars = array ('msg' => _t('_ch_ava_set_avatar_error'));
            if (!empty($aVars))
                echo $this->_oTemplate->parseHtmlByName ('error_plank', $aVars);
        }

        if (isset($_POST['remove_avatar'])) {
            $sImagePath = CH_AVA_DIR_TMP . $this->_iProfileId . CH_AVA_EXT;
            if (@unlink($sImagePath)) {
                $aVars = array ('msg' => _t('_ch_ava_msg_avatar_was_deleted'));
                echo $this->_oTemplate->parseHtmlByName ('error_plank', $aVars);
            }
        }

        ch_avatar_import ('PageMain');
        $oPage = new ChAvaPageMain ($this);
        echo $oPage->getCode();
        $this->_oTemplate->addCss (array('main.css', 'colors.css', 'imgareaselect-default.css'));
        $this->_oTemplate->addJs ('jquery.imgareaselect.min.js');
        $this->_oTemplate->pageCode(_t('_ch_ava_page_title_home'), false, false);
    }

    function actionGetSiteAvatars ($iPage)
    {
        echo $this->serviceGetSiteAvatars ((int)$iPage);
        exit;
    }

    function actionSetSiteAvatar ($sImg)
    {
        $iAvatar = (int)$sImg;
        if (!$iAvatar || 0 !== strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo _t('_ch_ava_msg_error_occured');
            exit;
        }

        if (!$this->isAllowedAdd()) {
            echo _t('_ch_ava_msg_access_denied');
            exit;
        }

        if (!$this->_addAvatar (CH_AVA_DIR_SITE_AVATARS . $iAvatar . CH_AVA_EXT)) {
            echo _t('_ch_ava_msg_error_occured');
            exit;
        }

        $this->isAllowedAdd(true); // perform action

        echo $this->serviceGetMyAvatars ($this->_iProfileId);
        exit;
    }

    function actionSetAvatar ($iProfileId, $sImg)
    {
        $this->_setAvatar ($iProfileId, $sImg, false);
    }

    function actionSetAvatarCouple ($iProfileId, $sImg)
    {
        $this->_setAvatar ($iProfileId, $sImg, true);
    }

    function _setAvatar ($iProfileId, $sImg, $isSetAvatarForCouple = false)
    {
        if (!$this->isAdmin())
            $iProfileId = $iProfileIdOrig = $this->_iProfileId;
        else
            $iProfileIdOrig = $iProfileId;

        $iAvatar = (int)$sImg;
        if (!$iAvatar || 0 !== strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo _t('_ch_ava_msg_error_occured');
            exit;
        }

        $aDataEntry = $this->_oDb->getAvatarByIdAndOwner ($iAvatar, $iProfileId, $this->isAdmin());
        if (!$aDataEntry) {
            echo _t('_ch_ava_msg_access_denied');
            exit;
        }

        $aProfileCouple = array ();
        if ($isSetAvatarForCouple) {
            $aProfileMain = getProfileInfo($iProfileId);
            if ($aProfileMain['Couple']) {
                $aProfileCouple = getProfileInfo($aProfileMain['Couple']);
                $iProfileId = $aProfileCouple['ID'];
            }
        }

        if (!$this->_oDb->updateProfile ($iAvatar, $iProfileId, true)) {
            echo _t('_ch_ava_msg_access_denied');
            exit;
        }

        $this->onEventChanged ($iProfileIdOrig, $iAvatar, $aProfileCouple ? $aProfileCouple['ID'] : 0, false);

        echo $this->serviceGetMyAvatars ($iProfileIdOrig);
        exit;
    }

    function actionRemoveAvatar ($iProfileId, $sImg)
    {
        if (!$this->isAdmin())
            $iProfileId = $this->_iProfileId;

        $iAvatar = (int)$sImg;
        if (!$iAvatar || 0 !== strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo _t('_ch_ava_msg_error_occured');
            exit;
        }

        $aDataEntry = $this->_oDb->getAvatarByIdAndOwner ($iAvatar, $iProfileId, $this->isAdmin());
        if (!$this->isAllowedDelete($aDataEntry)) {
            echo _t('_ch_ava_msg_access_denied');
            exit;
        }

        if (!$this->_removeAvatar ($iProfileId, $iAvatar)) {
            echo _t('_ch_ava_msg_error_occured');
            exit;
        }

        $this->isAllowedDelete($aDataEntry, true); // perform action

        echo $this->serviceGetMyAvatars ($iProfileId);
        exit;
    }

    function actionMemberThumb ($iProfileId)
    {
        echo $GLOBALS['oFunctions']->getMemberThumbnail(-1 == $iProfileId ? $this->_iProfileId : $iProfileId);
    }

    function actionAdministration ()
    {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $iId = $this->_oDb->getSettingsCategory();
        if(empty($iId)) {
            echo MsgBox(_t('_ch_ava_msg_page_not_found'));
            $this->_oTemplate->pageCodeAdmin (_t('_ch_ava_administration'));
            return;
        }

        ch_import('ChWsbAdminSettings');

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) {
            $oSettings = new ChWsbAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new ChWsbAdminSettings($iId);
        $sResult = $oSettings->getForm();

        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;

        $aVars = array (
            'content' => $sResult,
        );
        echo $this->_oTemplate->adminBlock ($this->_oTemplate->parseHtmlByName('default_padding', $aVars), _t('_ch_ava_administration'));

        $this->_oTemplate->addCssAdmin (array('main.css', 'colors.css'));
        $this->_oTemplate->addCssAdmin ('forms_adv.css');
        $this->_oTemplate->pageCodeAdmin (_t('_ch_ava_administration'));
    }

    // ================================== external actions

    /**
     * Set image for cropping.
     * You can point image for cropping and then redirect to avatar module page,
     * your image will be ready for cropping. You can use it in your module, if you want allow users
     * to set any image from your module as avatar.
     *
     * @param $iProfileId - profile id to set avatar for
     * @param $sOrigPath - image path
     * @return true on succrss, false on error
     */
    function serviceSetImageForCropping ($iProfileId, $sOrigPath)
    {
        $sImagePath = CH_AVA_DIR_TMP . ((int)$iProfileId ? (int)$iProfileId : $this->_iProfileId) . CH_AVA_EXT;
        if (@copy($sOrigPath, $sImagePath)) {
            return IMAGE_ERROR_SUCCESS == imageResize($sImagePath, '', CH_AVA_PRE_RESIZE_W, CH_AVA_PRE_RESIZE_H, true) ? true : false;
        }
        return false;
    }

    /**
     * make avatar from particular image, also it makes it square
     * @param $sImg path to the image
     * @return true on success or false on error
     */
    function serviceMakeAvatarFromImage ($sImg)
    {
        if (!$this->_iProfileId)
            return false;

        if (!file_exists($sImg))
            return false;

        $sImagePath = CH_AVA_DIR_TMP . '_' . $this->_iProfileId . CH_AVA_EXT;

        $o =& ChWsbImageResize::instance();
        $o->removeCropOptions ();
        $o->setJpegOutput (true);
        $o->setSize (CH_AVA_W, CH_AVA_W);
        $o->setSquareResize (true);
        if (IMAGE_ERROR_SUCCESS != $o->resize($sImg, $sImagePath))
            return false;

        return $this->_addAvatar ($sImagePath, true);
    }

    /**
     * make avatar from image url, also it makes it square
     * @param $sImg url to the image
     * @return true on success or false on error
     */
    function serviceMakeAvatarFromImageUrl ($sImgUrl)
    {
        if (!$this->_iProfileId)
            return false;

        $s = ch_file_get_contents ($sImgUrl);
        if (!$s)
            return false;

        $sImagePath = CH_AVA_DIR_TMP . '_' . $this->_iProfileId . CH_AVA_EXT;
        if (!file_put_contents($sImagePath, $s))
            return false;

        return $this->serviceMakeAvatarFromImage($sImagePath);
    }

    /**
     * get image from photos module and make avatar from it
     * also it makes it square
     * @param $iSharedPhotoId photo id from photos module
     * @return true on success or false on error
     */
    function serviceMakeAvatarFromSharedPhotoAuto ($iSharedPhotoId)
    {
        if (!$this->_iProfileId)
            return false;

        $aImageFile = ChWsbService::call('photos', 'get_photo_array', array((int)$iSharedPhotoId, 'file'), 'Search');
        if (!$aImageFile)
            return false;

        return $this->serviceMakeAvatarFromImage($aImageFile['path']);
    }

    /**
     * Returns crop image html, it consists of image for cropping, cropping preview and descriptions
     * @param $aParams
     *          dir_image - image path
     *          url_image - image url
     * @returns error message string of html string
     */
    function serviceCropTool ($aParams)
    {
        if (!$aParams || !is_array($aParams)) {
            return _t('_ch_ava_msg_error_occured');
        }

        if (!file_exists($aParams['dir_image'])) {
            return _t ('_ch_ava_no_crop_image');
        }

        ch_import('ChWsbImageResize');
        $aSizes = ChWsbImageResize::getImageSize ($aParams['dir_image']);
        $aVars = array (
            'url_img' => $aParams['url_image'],
            'img_width' => $aSizes['w'] ? $aSizes['w'] : 0,
            'img_height' => $aSizes['h'] ? $aSizes['h'] : 0,
            'action' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri(),
        );
        return $this->_oTemplate->parseHtmlByName ('crop_tool', $aVars);
    }

    /**
     * Delete all profile's avatars
     * @param $iProfileId - profile ID to delete avatar for
     * @return true on success, false on error
     */
    function serviceDeleteProfileAvatars ($iProfileId)
    {
        if (!(int)$iProfileId)
            return false;
        $aDataEntries = $this->_oDb->getAvatarsByAuthor ((int)$iProfileId);
        foreach ($aDataEntries as $iEntryId) {
            if ($this->_oDb->deleteAvatarByIdAndOwner($iEntryId, (int)$iProfileId, $this->isAdmin())) {
                @unlink (CH_AVA_DIR_USER_AVATARS . $iEntryId . CH_AVA_EXT);
                @unlink (CH_AVA_DIR_USER_AVATARS . $iEntryId . 'i' . CH_AVA_EXT);
                @unlink (CH_AVA_DIR_USER_AVATARS . $iEntryId . 'b' . CH_AVA_EXT);
                $this->onEventDeleted ($iEntryId);
            }
        }
        return true;
    }

    /**
     * After join redirection
     * This serice automatically log in joined user and redirects him to avatar copping page
     * @param $iMemID - joined profile ID
     * @param $sStatusText - status text to display at the top of page, like 'join success'
     * @return false on error,  'EXIT' string on success
     */
    function serviceJoin ($iMemID, $sStatusText)
    {
        $sPwd = db_value("SELECT `Password` FROM `Profiles` WHERE `ID` = '".(int)$iMemID."' LIMIT 1");
        if ($sPwd) {

            ch_import('ChWsbPermalinks');
            $o = new ChWsbPermalinks();

            header('Location: ' . CH_WSB_URL_ROOT . $o->permalink('modules/?r=avatar/') . '&join_text=' . $sStatusText); // redirect to upload avatar page

            return 'EXIT';
        }
        return false;
    }

    /**
     * Site avatars html
     * @param $iPage - current page in site avatars
     * @return html with site avatars
     */
    function serviceGetSiteAvatars ($iPage)
    {
        $iPage = (int)$iPage ? (int)$iPage : 1;
        $iPerPage = 12;
        $iCounter = 0;
        $iStart = ($iPage-1) * $iPerPage;
        $aFiles = array ();
        if ($h = opendir(CH_AVA_DIR_SITE_AVATARS)) {
            while (($sFile = readdir($h)) !== false) {
                if ('.' == $sFile[0])
                    continue;
                if ($iCounter++ < $iStart)
                    continue;
                if ($iCounter - $iStart <= $iPerPage)
                    $aFiles[] = array ('url' => CH_AVA_URL_SITE_AVATARS . $sFile, 'name' => $sFile);
            }
            closedir($h);
        }

        ch_import('ChWsbPaginate');
        $oPaginate = new ChWsbPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $iCounter,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'on_change_page' => "getHtmlData('ch_ava_site_avatars', '" . $this->_oConfig->getBaseUri() . "get_site_avatars/{page}');",
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate('', -1, -1, false);

        $sScript = "<script>
            $(document).ready(function() {
                $('#ch_ava_site_avatars .ch_ava_tar .ch_ava_actions a').bind('click', function (e) {
                    var e = $(this);
                    getHtmlData('ch_ava_my_avatars', '" . $this->_oConfig->getBaseUri() . "set_site_avatar/' + $(this).attr('alt'), function () {
                        e.html('" . ch_js_string(_t('_ch_ava_ok'), CH_ESCAPE_STR_APOS) . "');
                    }, 'post');
                });
            });
        </script>";

        $aVars = array (
            'ch_repeat:avatars' => $aFiles,
        );
        return $sAjaxPaginate . '<div class="ch-def-padding-thd">' . $GLOBALS['oFunctions']->centerContent($this->_oTemplate->parseHtmlByName ('avatars_site', $aVars), '.ch_ava_tar') . '</div>' . $sAjaxPaginate . $sScript;
    }

    function serviceManageAvatars ($iProfileId, $isAddJsCss = false)
    {
        if (!$this->isAdmin())
            return '';

        $this->_oTemplate->addCss (array('main.css', 'colors.css', 'imgareaselect-default.css'));

        $aVars = array (
            'content' => $this->serviceGetMyAvatars ($iProfileId),
        );
        return array($this->_oTemplate->parseHtmlByName ('manage_avatars', $aVars), array(), array(), false);
    }

    /**
     * My avatars html
     * @param $iProfileId - profile's ID to display avatars for
     * @return html with my avatars
     */
    function serviceGetMyAvatars ($iProfileId)
    {
        if (!(int)$iProfileId)
            return _t('_ch_ava_msg_access_denied');

        $aProfileCouple = array();
        $aProfileMain = getProfileInfo((int)$iProfileId);
        if ($aProfileMain['Couple'])
            $aProfileCouple = getProfileInfo($aProfileMain['Couple']);
        $aAvatars = $this->_oDb->getAvatarsByAuthor((int)$iProfileId);

        $iAvatarCurrent = $this->_oDb->getCurrentAvatar((int)$iProfileId, $this->isAdmin());
        $iAvatarCurrentCouple = $this->_oDb->getCurrentAvatar($aProfileCouple['ID'], $this->isAdmin());

        $aFiles = array ();
        foreach ($aAvatars as $iAvatar) {
            $aFiles[] = array (
                'url' => CH_AVA_URL_USER_AVATARS . $iAvatar . CH_AVA_EXT,
                'name' => $iAvatar . CH_AVA_EXT,
                'ch_if:is_not_main' => array ('condition' => $iAvatarCurrent != $iAvatar, 'content' => array('name' => $iAvatar . CH_AVA_EXT)),
                'ch_if:is_not_main_couple' => array ('condition' => $aProfileCouple && $iAvatarCurrentCouple != $iAvatar, 'content' => array('name' => $iAvatar . CH_AVA_EXT)),
            );
        }

        $sScript = "<script>
            $(document).ready(function() {
                $('#ch_ava_my_avatars .ch_ava_tar .ch_ava_actions a.ch_ava_set_couple').bind('click', function (e) {
                    getHtmlData('ch_ava_my_avatars', '" . $this->_oConfig->getBaseUri() . "set_avatar_couple/$iProfileId/' + $(this).attr('alt'), false, 'post');
                });
                $('#ch_ava_my_avatars .ch_ava_tar .ch_ava_actions a.ch_ava_set').bind('click', function (e) {
                    getHtmlData('ch_ava_my_avatars', '" . $this->_oConfig->getBaseUri() . "set_avatar/$iProfileId/' + $(this).attr('alt'), false, 'post');
                });
                $('#ch_ava_my_avatars .ch_ava_tar .ch_ava_actions a.ch_ava_del').bind('click', function (e) {
                    getHtmlData('ch_ava_my_avatars', '" . $this->_oConfig->getBaseUri() . "remove_avatar/$iProfileId/' + $(this).attr('alt'), false, 'post');
                });
                getHtmlData('ch_ava_current', '" . $this->_oConfig->getBaseUri() . "member_thumb/-1');
            });
        </script>";

        if (!$aFiles)
            return MsgBox(_t('_Empty')) . $sScript;

        $aVars = array (
            'ch_repeat:avatars' => $aFiles,
        );
        return $GLOBALS['oFunctions']->centerContent($this->_oTemplate->parseHtmlByName ('avatars_my', $aVars), '.ch_ava_tar') . $sScript;
    }

    /**
     * Html to display on profile wall, when user uploads new avatar
     * @param $aEvent - wall event parameters
     * @return html to post on profile's wall
     */
    function serviceGetWallPost ($aEvent)
    {
        $aProfile = getProfileInfo($aEvent['owner_id']);
        if(empty($aProfile))
            return array('perform_delete' => true);

        if($aProfile['Status'] != 'Active')
            return "";

        $sCss = '';
        if($aEvent['js_mode'])
            $sCss = $this->_oTemplate->addCss('wall_post.css', true);
        else
            $this->_oTemplate->addCss('wall_post.css');

        $sOwner = getNickName((int)$aEvent['owner_id']);
        $sTxtWallObject = _t('_ch_ava_wall_object');

        return array(
            'title' => _t('_ch_ava_wall_title_' . $aEvent['action'], $sOwner, $sTxtWallObject),
            'description' => '',
            'content' => $sCss . $this->_oTemplate->parseHtmlByName('wall_post', array(
	            'cpt_user_name' => $sOwner,
	            'cpt_added_new' => _t('_ch_ava_wall_' . $aEvent['action']),
	            'cpt_object' => $sTxtWallObject,
	            'post_id' => $aEvent['id'],
	        ))
        );
    }

    /**
     * Register 'add avatar' and 'change avatar' events for wall
     * @returm array of parameters
     */
    function serviceGetWallData ()
    {
    	$sUri = $this->_oConfig->getUri();
    	$sName = 'ch_' . $sUri;

        return array(
            'handlers' => array(
                array('alert_unit' => $sName, 'alert_action' => 'add', 'module_uri' => $sUri, 'module_class' => 'Module', 'module_method' => 'get_wall_post', 'timeline' => 1, 'outline' => 0),
                array('alert_unit' => $sName, 'alert_action' => 'change', 'module_uri' => $sUri, 'module_class' => 'Module', 'module_method' => 'get_wall_post', 'timeline' => 1, 'outline' => 0)
            ),
            'alerts' => array(
                array('unit' => $sName, 'action' => 'add'),
                array('unit' => $sName, 'action' => 'change')
            )
        );
    }

    // ================================== admin actions


    // ================================== events

    function onEventCreate ($iEntryId)
    {
        $oAlert = new ChWsbAlerts('ch_avatar', 'add', $iEntryId, $this->_iProfileId);
        $oAlert->alert();
    }

    function onEventChanged ($iProfileId, $iEntryId, $iCoupleProfileId = 0, $isTryToSuspend = true)
    {
        if ($iCoupleProfileId) {
            createUserDataFile($iCoupleProfileId);
        }

        if ($isTryToSuspend && $iProfileId && !$this->isAdmin() && !getParam('autoApproval_ifProfile'))
            $this->_oDb->suspendProfile($iProfileId);

        createUserDataFile($iProfileId);
        $GLOBALS['MySQL']->cleanCache('sys_browse_people');

        $oAlert = new ChWsbAlerts('ch_avatar', 'change', $iEntryId, $iProfileId);
        $oAlert->alert();
    }

    function onEventDeleted ($iEntryId, $aDataEntry = array())
    {
        createUserDataFile($this->_iProfileId);
        // arise alert
        $oAlert = new ChWsbAlerts('ch_avatar', 'delete', $iEntryId, $this->_iProfileId);
        $oAlert->alert();
    }

    // ================================== permissions

    function isAllowedAdd ($isPerformAction = false)
    {
        if ($this->isAdmin()) return true;
        if (!$GLOBALS['logged']['member']) return false;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_AVATAR_UPLOAD, $isPerformAction, 0, false);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedEdit ($aDataEntry, $isPerformAction = false)
    {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId)) return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_AVATAR_EDIT_ANY, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedDelete (&$aDataEntry, $isPerformAction = false)
    {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId)) return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, CH_AVATAR_DELETE_ANY, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAdmin ()
    {
        return $GLOBALS['logged']['admin'] || $GLOBALS['logged']['moderator'];
    }

    function _defineActions ()
    {
        defineMembershipActions(array('avatar upload', 'avatar edit any', 'avatar delete any'));
    }

    // ================================== other function

    function _removeAvatar ($iProfileId, $iAvatar)
    {
        if (!$this->isAdmin())
            $iProfileId = $this->_iProfileId;

        $iAvatar = (int)$iAvatar;

        @unlink(CH_AVA_DIR_USER_AVATARS . $iAvatar . CH_AVA_EXT);
        @unlink(CH_AVA_DIR_USER_AVATARS . $iAvatar . 'i' . CH_AVA_EXT);
        @unlink(CH_AVA_DIR_USER_AVATARS . $iAvatar . 'b' . CH_AVA_EXT);

        $iAvatarCurrent = $this->_oDb->getCurrentAvatar($iProfileId, $this->isAdmin());

        if ($this->_oDb->deleteAvatarByIdAndOwner ($iAvatar, $iProfileId, $this->isAdmin())) {
            if ($iAvatarCurrent == $iAvatar) {
                if ($this->_oDb->updateProfile (-1, $iProfileId, $this->isAdmin())) {
                    $this->onEventChanged ($iProfileId, $iAvatar, 0, false);
                }
            }
            return true;
        }
        return false;
    }

    function _addAvatar ($sFullPath, $isMove = false, $isUpdateProfile = true)
    {
        if (!file_exists($sFullPath))
            return false;

        if (!($iAvatar = $this->_oDb->addAvatar($this->_iProfileId)))
            return false;

		$sImageBrowse = CH_AVA_DIR_USER_AVATARS . $iAvatar . 'b' . CH_AVA_EXT;
        $sImageThumb = CH_AVA_DIR_USER_AVATARS . $iAvatar . CH_AVA_EXT;
        $sImageIcon = CH_AVA_DIR_USER_AVATARS . $iAvatar . 'i' . CH_AVA_EXT;


        if ($isMove)
            $isOk = rename ($sFullPath, $sImageBrowse);
        else
            $isOk = copy ($sFullPath, $sImageBrowse);

        if ($isOk) {
        	if(!$this->_resizeImage($sImageBrowse, $sImageThumb, CH_AVA_W, CH_AVA_H)) {
        		$this->_oDb->deleteAvatarByIdAndOwner ($iAvatar, $this->_iProfileId, $this->isAdmin());
        		@unlink($sImageBrowse);
                @unlink($sImageThumb);
                return false;
        	}

        	if(!$this->_resizeImage($sImageBrowse, $sImageIcon, CH_AVA_ICON_W, CH_AVA_ICON_H)) {
        		$this->_oDb->deleteAvatarByIdAndOwner ($iAvatar, $this->_iProfileId, $this->isAdmin());
        		@unlink($sImageBrowse);
                @unlink($sImageThumb);
                @unlink($sImageIcon);
                return false;
        	}
        }

        if (!$isOk) {
            $this->_oDb->deleteAvatarByIdAndOwner ($iAvatar, $this->_iProfileId, $this->isAdmin());
        } else if ($isUpdateProfile) {
            $this->onEventCreate ($iAvatar);
            if ($this->_oDb->updateProfile ($iAvatar, $this->_iProfileId, $this->isAdmin())) {
                $this->onEventChanged ($this->_iProfileId, $iAvatar);
            }
        }

        return $isOk ? $iAvatar : false;
    }

    function _resizeImage($sImageSrc, $sImageDst, $iWidth, $iHeight, $bSquareResize = false, $bJpegOutput = true)
    {
    	$o =& ChWsbImageResize::instance();
		$o->setJpegOutput($bJpegOutput);
		$o->removeCropOptions();
		$o->setSize($iWidth, $iHeight);
		$o->setSquareResize ($bSquareResize);

		return $o->resize($sImageSrc, $sImageDst) == IMAGE_ERROR_SUCCESS;
    }

    function _uploadImage ($iProfileId = 0)
    {
        $iProfileId = (int)$iProfileId;
        $sImagePath = CH_AVA_DIR_TMP . ($iProfileId ? $iProfileId : $this->_iProfileId) . CH_AVA_EXT;


        $i = strrpos($_FILES['image']['name'], '.');
        if (false === $i)
            return false;
        $sExt = strtolower(substr($_FILES['image']['name'], $i + 1));
        if (!in_array($sExt, $this->_aAllowedExt))
            return false;


        if (move_uploaded_file($_FILES['image']['tmp_name'], $sImagePath)) {
            if ($_POST['copy_to_profile_photos']) {
                if (ChWsbRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader')) {
                    ch_import('ChWsbPrivacyQuery');
                    $oPrivacy = new ChWsbPrivacyQuery();

                    $aFileInfo = array (
                        'medTitle' => _t('_ch_ava_avatar'),
                        'medDesc' => _t('_ch_ava_avatar'),
                        'medTags' => _t('_ProfilePhotos'),
                        'Categories' => array(_t('_ProfilePhotos')),
                        'album' => str_replace('{nickname}', getUsername($iProfileId), getParam('ch_photos_profile_album_name')),
                        'albumPrivacy' => $oPrivacy->getDefaultValueModule('photos', 'album_view'),
                    );

                    $_POST[CH_WSB_UPLOADER_EP_PREFIX . 'album'] = uriFilter($aFileInfo['album']);
                    ChWsbService::call('photos', 'perform_photo_upload', array($sImagePath, $aFileInfo, false), 'Uploader');
                }
            }
            return IMAGE_ERROR_SUCCESS == imageResize($sImagePath, '', CH_AVA_PRE_RESIZE_W, CH_AVA_PRE_RESIZE_H, true) ? true : false;
        }
        return false;
    }

    function _makeAvatarFromSharedPhoto ($iSharedPhotoId)
    {
        $aImageFile = ChWsbService::call('photos', 'get_photo_array', array((int)$iSharedPhotoId, 'file'), 'Search');
        if (!$aImageFile)
            return false;

        $sImagePath = CH_AVA_DIR_TMP . $this->_iProfileId . CH_AVA_EXT;
        if (!@copy($aImageFile['path'], $sImagePath))
            return false;

        return IMAGE_ERROR_SUCCESS == imageResize($sImagePath, '', CH_AVA_PRE_RESIZE_W, CH_AVA_PRE_RESIZE_H, true) ? true : false;
    }

    function _cropAvatar ()
    {
        $sSrcImagePath = CH_AVA_DIR_TMP . $this->_iProfileId . CH_AVA_EXT;
        $sDstImagePath = CH_AVA_DIR_TMP . 'tmp' . $this->_iProfileId . CH_AVA_EXT;
        $o =& ChWsbImageResize::instance();
        $o->setJpegOutput (true);
        $o->setJpegQuality (getParam('ch_avatar_quality'));
        $o->setSize (CH_AVA_BROWSE_W, CH_AVA_BROWSE_H);

        if ((int)$_POST['w'] && (int)$_POST['h']) {
            //echo (int)$_POST['w'];
            //exit;
            $o->setSquareResize (false);
            $aSize = $o->getImageSize($sSrcImagePath);
            //echo '<pre>' . print_r($aSize, true) . '</pre>';
            //exit;
            $bRet = $aSize ? $o->crop($aSize['w'], $aSize['h'], (int)$_POST['x1'], (int)$_POST['y1'], (int)$_POST['w'], (int)$_POST['h'], 0, $sSrcImagePath) : IMAGE_ERROR_WRONG_TYPE;
        } else {
            $o->setSquareResize (true);
            $bRet = IMAGE_ERROR_SUCCESS;
        }

        if (IMAGE_ERROR_SUCCESS == $bRet && IMAGE_ERROR_SUCCESS == $o->resize($sSrcImagePath, $sDstImagePath))
            $bRet = $this->_addAvatar ($sDstImagePath, true) ? IMAGE_ERROR_SUCCESS : IMAGE_ERROR_WRONG_TYPE;

        if (IMAGE_ERROR_SUCCESS == $bRet) {
            @unlink($sSrcImagePath);
            return true;
        }
        @unlink($sDstImagePath);
        return false;
    }
}
