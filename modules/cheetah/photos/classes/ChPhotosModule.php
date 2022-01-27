<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbFilesModule');

define('PROFILE_PHOTO_CATEGORY', 'Profile photos');

class ChPhotosModule extends ChWsbFilesModule
{
    var $iHeaderCacheTime = 0;
    function __construct (&$aModule)
    {
        parent::__construct($aModule);
        $this->aSectionsAdmin['pending'] = array(
            'exclude_btns' => array('featured', 'unfeatured')
        );
        $this->iHeaderCacheTime = (int)$this->_oConfig->getGlParam('header_cache');
    }

    function actionGetCurrentImage ($iPicId)
    {
        $iPicId = (int)$iPicId;
        if ($iPicId > 0) {
            ch_import('Search', $this->_aModule);
            $oMedia = new ChPhotosSearch();
            $aInfo = $oMedia->serviceGetPhotoArray($iPicId, 'file');
            $aInfo['ownerUrl'] = getProfileLink($aInfo['owner']);
            $aInfo['ownerName'] = getNickName($aInfo['owner']);
            $aInfo['date'] = defineTimeInterval($aInfo['date']);
            $oMedia->getRatePart();
            $aInfo['rate'] = $oMedia->oRate->getJustVotingElement(0, 0, $aInfo['rate']);
            $aLinkAddon = $oMedia->getLinkAddByPrams();
            $oPaginate = new ChWsbPaginate(array(
                'count' => (int)$_GET['total'],
                'per_page' => 1,
                'page' => (int)$_GET['page'],
                'on_change_page' => 'getCurrentImage({page})',
            ));
            $aInfo['paginate'] = $oPaginate->getPaginate();
            header('Content-Type:text/javascript; charset=utf-8');
            echo json_encode($aInfo);
        }
    }

    function actionGetImage ($sParamValue, $sParamValue1)
    {
        $sParamValue  = clear_xss($sParamValue);
        $sParamValue1 = clear_xss($sParamValue1);
        $iPointPos    = strrpos($sParamValue1, '.');

        $iId = (int)$this->_oDb->getIdByHash(substr($sParamValue1, 0, $iPointPos));
        if(empty($iId)) {
            header("Location: " . $this->_oTemplate->getIconUrl('no_image.png'));
            exit;
        }

        $aInfo = $this->_oDb->getFileInfo(array('fileId' => $iId));
        if(empty($aInfo) || !is_array($aInfo)) {
            header("Location: " . $this->_oTemplate->getIconUrl('no_image.png'));
            exit;
        }

        if($aInfo['AllowAlbumView'] != CH_WSB_PG_HIDDEN && !$this->isAllowedView($aInfo)) {
            header("Location: " . $this->_oTemplate->getIconUrl('private.png'));
            exit;
        }

        $sExt = substr($sParamValue1, $iPointPos + 1);
        switch ($sExt) {
            case 'png':
                $sCntType = 'image/x-png';
                break;
            case 'gif':
                $sCntType = 'image/gif';
                break;
            default:
                $sCntType = 'image/jpeg';
        }
        $sPath = $this->_oConfig->getFilesPath() . $iId . str_replace('{ext}', $sExt, $this->_oConfig->aFilePostfix[$sParamValue]);
        $sAdd = '';
        if ($this->iHeaderCacheTime > 0) {
            $iLastModTime = filemtime($sPath);
            $sAdd = ", max-age={$this->iHeaderCacheTime}, Last-Modified: " . gmdate("D, d M Y H:i:s", $iLastModTime) . " GMT";
        }
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0" . $sAdd);
        header("Content-Type:" . $sCntType);
        header("Content-Length: " . filesize($sPath));
        readfile($sPath);
    }

    function actionCropPerform($iPhotoID)
    {
        $img = $_POST['imgBase64'];
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        $aInfo = $this->_oDb->getFileInfo(array('fileId' => $iPhotoID));

        if (empty($aInfo))
            die(json_encode(array(
                'status' => 'error',
                'message' => _t('_sys_media_not_found'),
            )));

        if (!$this->isAllowedEdit($aInfo))
            die(json_encode(array(
                'status' => 'error',
                'message' => _t('_Access denied'),
            )));

        $sSrcFileName = $this->_oConfig->getFilesPath() . $aInfo['medID'] . str_replace('{ext}', $aInfo['medExt'], $this->_oConfig->aFilesConfig['original']['postfix']);
        // use image type file if original is not found.
        if(!$sSrcFileName) $sSrcFileName = $this->_oConfig->getFilesPath() . $aInfo['medID'] . str_replace('{ext}', $aInfo['medExt'], $this->_oConfig->aFilesConfig['file']['postfix']);

        $sTmpFileName = CH_DIRECTORY_PATH_ROOT . 'tmp/' . $this->_oConfig->getMainPrefix() . mt_rand() . '.' . $aInfo['medExt'];

        $success = file_put_contents($sTmpFileName, $data);

        $aInfo['medTitle'] = process_db_input($_POST['title'], CH_TAGS_STRIP);
        $aInfo['medTags'] = process_db_input($_POST['tags'], CH_TAGS_STRIP);
        $aInfo['medDesc'] = process_db_input($_POST['desc'], CH_TAGS_STRIP);
        $aInfo['Categories'] = str_replace(',', CATEGORIES_DIVIDER, process_db_input($_POST['cats'], CH_TAGS_STRIP));

        ch_import('Uploader', $this->_aModule);
        $sClassName = $this->_oConfig->getClassPrefix() . 'Uploader';
        $oUploader = new $sClassName();
        $a = $oUploader->performUpload ($sTmpFileName, 'cropped.jpg', $aInfo, false);
        @unlink($sTmpFileName);

        if (!empty($a['error']))
            die(json_encode(array(
                'status' => 'error',
                'message' => $a['error'],
            )));

        $aInfoNew = $this->_oDb->getFileInfo(array('fileId' => $a['id']));

        $iId = $a['id'];

        for ($x = 0; $x <= 10; $x++) {
            $sUri = substr(md5(microtime(true) . $x), 0, 16);
            if (uriCheckUniq($sUri, 'ch_photos_main', 'Uri')) {
                continue;
            }
        }

        $GLOBALS['MySQL']->query("UPDATE `ch_photos_main` SET `Uri` = '$sUri' WHERE `ID` = '$iId'");

        // Delete photo used for cropping if box was checked.
        if((int)$_POST['delorg'] == 1) {
            $this->_deleteFile((int)$_POST['delval']);
        }

        ch_import('Search', $this->_aModule);
        $oSearch = new ChPhotosSearch();
        $sImgUrl = $oSearch->getImgUrl($aInfoNew['Hash'], 'file');

        echo(json_encode(array(
            'status' => 'success',
            'url' => $sImgUrl,
            'redirect_url' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $sUri,
        )));
    }

    function actionCrop($iPhotoID)
    {
        $aInfo = $this->_oDb->getFileInfo(array('fileId' => $iPhotoID));

        if (empty($aInfo)) {
            $this->_oTemplate->displayPageNotFound();
            return;
        }

        if (!$this->isAllowedEdit($aInfo)) {
            $this->_oTemplate->displayAccessDenied();
            return;
        }

        ch_import('PageView', $this->_aModule);
        $sClassName = $this->_oConfig->getClassPrefix() . 'PageView';
        $oPage = new $sClassName($this, $aInfo, $this->_oConfig->getMainPrefix() . '_crop');
        $sCode = $oPage->getCode();

        $this->aPageTmpl['header'] = $aInfo['medTitle'];
        $GLOBALS['oTopMenu']->setCustomSubHeader(_t('_sys_album_x_photo_x', $aInfo['albumCaption'], $aInfo['medTitle']));
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_' . $this->_oConfig->getMainPrefix()) => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aInfo['albumCaption'] => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/album/' . $aInfo['albumUri'] . '/owner/' . $aInfo['NickName'],
            $sKey => '',
        ));

        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
    }

    function actionSetAvatar ($iPhotoID)
    {
        if ($this->serviceSetAvatar($iPhotoID)) {
            $aInfo = $this->_oDb->getFileInfo(array('fileId' => $iPhotoID));
            $sRedirect = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aInfo['medUri'];
            $sJQueryJS = genAjaxyPopupJS($iPhotoID, 'ajaxy_popup_result_div', $sRedirect);
            $sLangKey = '_Success';
        } else {
            $sJQueryJS = genAjaxyPopupJS($iPhotoID, 'ajaxy_popup_result_div');
            $sLangKey = '_Error occured';
        }
        header('Content-Type: text/html; charset=UTF-8');
        echo MsgBox(_t($sLangKey)) . $sJQueryJS;
        exit;
    }

    function serviceSetAvatar($iPhotoID, $iAuthorId = 0)
    {
        if (!$iAuthorId)
            $iAuthorId = getLoggedId();

        if (!($aFileInfo = $this->_oDb->getFileInfo(array('fileId' => $iPhotoID))))
            return false;

        if ($aFileInfo['medProfId'] != $iAuthorId)
            return false;

		ch_import('ChWsbAlbums');
        $sProfileAlbumUri = ChWsbAlbums::getAbumUri($this->_oConfig->getGlParam('profile_album_name'), $iAuthorId);
        if ($sProfileAlbumUri != $aFileInfo['albumUri'])
            return false;

        return $this->_oDb->setAvatar($iPhotoID, $aFileInfo['albumId']);
    }

    function serviceGetProfileCat ()
    {
        return PROFILE_PHOTO_CATEGORY;
    }

    function serviceGetBlockFavorited ($iBlockId)
    {
        if ($this->_iProfileId == 0)
            return;
        ch_import('Search', $this->_aModule);
        $oMedia = new ChPhotosSearch();
        $oMedia->clearFilters(array('activeStatus', 'allow_view', 'album_status', 'albumType', 'ownerStatus'), array('albumsObjects', 'albums'));
        if (isset($oMedia->aAddPartsConfig['favorite']) && !empty($oMedia->aAddPartsConfig['favorite'])) {
            $oMedia->aCurrent['join']['favorite'] = $oMedia->aAddPartsConfig['favorite'];
            $oMedia->aCurrent['restriction']['fav'] = array(
                'value' => $iUserId,
                'field' => $oMedia->aAddPartsConfig['favorite']['userField'],
                'operator' => '=',
                'table' => $oMedia->aAddPartsConfig['favorite']['table']
            );
        }
        $oMedia->aCurrent['paginate']['perPage'] = (int)$this->oConfig->getGlParam('number_top');
        $sCode = $oMedia->displayResultBlock();
        if ($oMedia->aCurrent['paginate']['totalNum'] > 0) {
            $oMedia->aConstants['linksTempl']['favorited'] = 'browse/favorited';
            $sCode = $GLOBALS['oFunctions']->centerContent($sCode, '.sys_file_search_unit');
            $aTopMenu = array();
            $aBottomMenu = $oMedia->getBottomMenu('favorited', 0, '');
            return array($sCode, $aTopMenu, $aBottomMenu, false);
        }
    }

    function serviceGetMemberMenuItem ($sIcon = 'picture-o')
    {
        return parent::serviceGetMemberMenuItem ($sIcon);
    }

    function serviceGetMemberMenuItemAddContent ($sIcon = 'picture-o')
    {
        return parent::serviceGetMemberMenuItemAddContent ($sIcon);
    }

	function isAllowedShare(&$aDataEntry)
    {
    	if($aDataEntry['AllowAlbumView'] != CH_WSB_PG_ALL)
    		return false;

        return true;
    }

	function serviceGetQuickUploaderUrl($iProfileId, $sSelectedAlbum = '')
    {
    	ch_import('ChWsbAlbums');

    	$aDefaultAlbums = $this->_oConfig->getDefaultAlbums();
		if(!empty($sSelectedAlbum) && in_array($sSelectedAlbum, $aDefaultAlbums))
			$sSelectedAlbum = ChWsbAlbums::getAbumUri($this->_oConfig->getGlParam($sSelectedAlbum), $iProfileId);

        return CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'upload/' . $sSelectedAlbum;
    }

    function serviceGetAlbumUploaderUrl($iProfileId, $sSelectedAlbum = '')
    {
    	ch_import('ChWsbAlbums');

    	$aDefaultAlbums = $this->_oConfig->getDefaultAlbums();
		if(!empty($sSelectedAlbum) && in_array($sSelectedAlbum, $aDefaultAlbums))
			$sSelectedAlbum = ChWsbAlbums::getAbumUri($this->_oConfig->getGlParam($sSelectedAlbum), $iProfileId);

		return CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'albums/my/add_objects/' . $sSelectedAlbum . '/owner/' . getUsername($iProfileId);
    }

    function serviceGetManageProfilePhotoUrl($iProfileId, $sSelectedAlbum = '')
    {
    	ch_import('ChWsbAlbums');

    	$aDefaultAlbums = $this->_oConfig->getDefaultAlbums();
		if(!empty($sSelectedAlbum) && in_array($sSelectedAlbum, $aDefaultAlbums))
			$sSelectedAlbum = ChWsbAlbums::getAbumUri($this->_oConfig->getGlParam($sSelectedAlbum), $iProfileId);

		return CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'albums/my/manage_profile_photos/' . $sSelectedAlbum . '/owner/' . getUsername($iProfileId);
    }

	function serviceGetWallPost($aEvent)
    {
        return $this->getWallPost($aEvent, 'picture-o');
    }

    function serviceGetWallPostOutline($aEvent)
    {
        return $this->getWallPostOutline($aEvent, 'picture-o');
    }

		function actionView ($sUri) {
			$aInfo = $this->_oDb->getFileInfo(array('fileUri' => $sUri));
			if(isBlocked($aInfo['medProfId'], getLoggedId())) {
			    $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => MsgBox(_t('_sys_txt_error_you_are_blocked'))));
			    return;
			}

			parent::actionView($sUri);
		}

		function actionBrowse ($sParamName = '', $sParamValue = '', $sParamValue1 = '', $sParamValue2 = '', $sParamValue3 = '') {
			if ($sParamName == 'album' && $sParamValue1 == 'owner') {
			    $iOwnerId = getID($sParamValue2);
			    if(isBlocked($iOwnerId, getLoggedId())) {
				$this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => MsgBox(_t('_sys_txt_error_you_are_blocked'))));
				return;
				}
			}

			parent::actionBrowse($sParamName, $sParamValue, $sParamValue1, $sParamValue2, $sParamValue3);
		}

		function actionAlbums ($sParamName = '', $sParamValue = '', $sParamValue1 = '', $sParamValue2 = '', $sParamValue3 = '') {
			if($sParamName == 'browse' && $sParamValue == 'owner') {
			    $iOwnerId = getID($sParamValue1);
			    if(isBlocked($iOwnerId, getLoggedId())) {
				$this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => MsgBox(_t('_sys_txt_error_you_are_blocked'))));
				return;
				}
			}

			parent::actionAlbums($sParamName, $sParamValue, $sParamValue1, $sParamValue2, $sParamValue3);
		}

}
