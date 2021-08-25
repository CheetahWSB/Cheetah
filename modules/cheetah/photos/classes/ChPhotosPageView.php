<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

require_once('ChPhotosCmts.php');
require_once('ChPhotosSearch.php');

class ChPhotosPageView extends ChWsbPageView
{
    var $iProfileId;
    var $aFileInfo;

    var $oModule;
    var $oTemplate;
    var $oConfig;
    var $oDb;
    var $oSearch;

    function __construct (&$oShared, &$aFileInfo, $sPage = 'ch_photos_view')
    {
        parent::__construct($sPage);
        $this->aFileInfo = $aFileInfo;
        $this->iProfileId = &$oShared->_iProfileId;

        $this->oModule = $oShared;
        $this->oTemplate = $oShared->_oTemplate;
        $this->oConfig = $oShared->_oConfig;
        $this->oDb = $oShared->_oDb;
        $this->oSearch = new ChPhotosSearch();
        $this->oTemplate->addCss('view.css');
        ch_import ('ChWsbViews');
        new ChWsbViews($this->oConfig->getMainPrefix(), $this->aFileInfo['medID']);
    }

    function getBlockCode_ActionList ()
    {
        $sCode = null;
        $sMainPrefix = $this->oConfig->getMainPrefix();

        ch_import('ChWsbSubscription');
        $oSubscription = ChWsbSubscription::getInstance();
        $aButton = $oSubscription->getButton($this->iProfileId, $sMainPrefix, '', (int)$this->aFileInfo['medID']);
        $sCode .= $oSubscription->getData();

        ch_import('ChWsbAlbums');
        $sProfileAlbumUri = ChWsbAlbums::getAbumUri($this->oConfig->getGlParam('profile_album_name'), $this->iProfileId);

        $aReplacement = array(
            'favorited' => $this->aFileInfo['favorited'] == false ? '' : 'favorited',
            'featured' => (int)$this->aFileInfo['Featured'],
            'featuredCpt' => '',
            'approvedCpt' => '',
            'approvedAct'     => '',
            'moduleUrl' => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri(),
            'fileUri' => $this->aFileInfo['medUri'],
            'fileKey' => $this->aFileInfo['Hash'],
            'fileExt' => $this->aFileInfo['medExt'],
            'iViewer' => $this->iProfileId,
            'ID' => (int)$this->aFileInfo['medID'],
            'Owner' => (int)$this->aFileInfo['medProfId'],
            'OwnerName' => $this->aFileInfo['NickName'],
            'AlbumUri' => $this->aFileInfo['albumUri'],
            'Tags' => ch_php_string_apos($this->aFileInfo['medTags']),
            'TitleAvatar' => $this->aFileInfo['medProfId'] == $this->iProfileId && 'sys_avatar' == getParam('sys_member_info_thumb') ? _t('_' . $sMainPrefix . '_set_as_avatar') : '',
            'SetAvatarCpt' => $this->aFileInfo['medProfId'] == $this->iProfileId && $sProfileAlbumUri == $this->aFileInfo['albumUri'] && 'ch_photos_thumb' == getParam('sys_member_info_thumb') ? _t('_' . $sMainPrefix . '_set_as_avatar') : '',
            'sbs_' . $sMainPrefix . '_title' => $aButton['title'],
            'sbs_' . $sMainPrefix . '_script' => $aButton['script'],
            'shareCpt' => $this->oModule->isAllowedShare($this->aFileInfo) ? _t('_Share') : '',
            'cropCpt' => $this->oModule->isAllowedEdit($this->aFileInfo) && $this->aFileInfo['medProfId'] == $this->iProfileId ? _t('_ch_photos_crop_action') : '',
        );
        if (isAdmin($this->iProfileId)) {
            $sMsg = $aReplacement['featured'] > 0 ? 'un' : '';
            $aReplacement['featuredCpt'] = _t('_' . $sMainPrefix . '_action_' . $sMsg . 'feature');
        }
        if ($this->oModule->isAllowedApprove($this->aFileInfo)) {
            $sMsg = '';
            $iAppr = 1;
            if ($this->aFileInfo['Approved'] == 'approved')
            {
                $sMsg = 'de';
                $iAppr = 0;
            }
            $aReplacement['approvedCpt'] = _t('_' . $sMainPrefix . '_admin_' . $sMsg . 'activate');
            $aReplacement['approvedAct'] = $iAppr;
        }

        $aReplacement['repostCpt'] = $aReplacement['repostScript'] = '';
        if(ChWsbRequest::serviceExists('wall', 'get_repost_js_click')) {
        	$sCode .= ChWsbService::call('wall', 'get_repost_js_script');

			$aReplacement['repostCpt'] = _t('_Repost');
			$aReplacement['repostScript'] = ChWsbService::call('wall', 'get_repost_js_click', array($this->iProfileId, $sMainPrefix, 'add', (int)$this->aFileInfo['medID']));
        }

        $sActionsList = $GLOBALS['oFunctions']->genObjectsActions($aReplacement, $sMainPrefix);
        if(is_null($sActionsList))
        	return '';

        return $sCode . $sActionsList;
    }

    function getBlockCode_FileAuthor ()
    {
        return $this->oTemplate->getFileAuthor($this->aFileInfo);
    }

    function getBlockCode_ViewAlbum ()
    {
        $oAlbum = new ChWsbAlbums($this->oConfig->getMainPrefix());
        $aAlbum = $oAlbum->getAlbumInfo(array('fileId' => $this->aFileInfo['albumId']));
        $aAlbum['show_as_list'] = true;

        return array($this->oSearch->displayAlbumUnit($aAlbum), array(), array(), false);
    }

    function getBlockCode_RelatedFiles ()
    {
        $this->oSearch->clearFilters(array('activeStatus', 'albumType', 'allow_view', 'album_status'), array('albumsObjects', 'albums'));
        $bLike = getParam('useLikeOperator');
        if ($bLike != 'on') {
            $aRel = array($this->aFileInfo['medTitle'], $this->aFileInfo['medDesc'], $this->aFileInfo['medTags'], $this->aFileInfo['Categories']);
            $sKeywords = getRelatedWords($aRel);
            if (!empty($sKeywords)) {
                $this->oSearch->aCurrent['restriction']['keyword'] = array(
                    'value' => $sKeywords,
                    'field' => '',
                    'operator' => 'against'
                );
            }
        } else {
            $sKeywords = $this->aFileInfo['medTitle'].' '.$this->aFileInfo['medTags'];
            $aWords = explode(' ', $sKeywords);
            foreach (array_unique($aWords) as $iKey => $sValue) {
                if (strlen($sValue) > 2) {
                    $this->oSearch->aCurrent['restriction']['keyword'.$iKey] = array(
                        'value' => trim(addslashes($sValue)),
                        'field' => '',
                        'operator' => 'against'
                    );
                }
            }
        }
        $this->oSearch->aCurrent['restriction']['id'] = array(
            'value' => $this->aFileInfo['medID'],
            'field' => $this->oSearch->aCurrent['ident'],
            'operator' => '<>',
            'paramName' => 'fileID'
        );
        $this->oSearch->aCurrent['sorting'] = 'score';
        $iLimit = (int)$this->oConfig->getGlParam('number_related');
        $iLimit = $iLimit == 0 ? 2 : $iLimit;

        $this->oSearch->aCurrent['paginate']['perPage'] = $iLimit;
        $sCode = $this->oSearch->displayResultBlock();
        $aBottomMenu = array();
        $bWrap = true;
        if ($this->oSearch->aCurrent['paginate']['totalNum'] > 0) {
            $sCode = $GLOBALS['oFunctions']->centerContent($sCode, '.sys_file_search_unit');
            $aBottomMenu = $this->oSearch->getBottomMenu('category', 0, $this->aFileInfo['Categories']);
            $bWrap = '';
        }
        return array($sCode, array(), $aBottomMenu, $bWrap);
    }

    function getBlockCode_ViewComments ()
    {
        $this->oTemplate->addCss('cmts.css');

        $oCmtsView = new ChPhotosCmts($this->oConfig->getMainPrefix(), $this->aFileInfo['medID']);
        if (!$oCmtsView->isEnabled())
        	return '';

		return $oCmtsView->getCommentsFirst();
    }

    function getBlockCode_ViewFile ()
    {
        $oVotingView = new ChTemplVotingView($this->oConfig->getMainPrefix(), $this->aFileInfo['medID']);
        $iWidth = (int)$this->oConfig->getGlParam('file_width');
        if ($this->aFileInfo['prevItem'] > 0)
            $aPrev = $this->oDb->getFileInfo(array('fileId'=>$this->aFileInfo['prevItem']), true, array('medUri', 'medTitle'));
        if ($this->aFileInfo['nextItem'] > 0)
            $aNext = $this->oDb->getFileInfo(array('fileId'=>$this->aFileInfo['nextItem']), true, array('medUri', 'medTitle'));
        $aUnit = array(
            'pic' => $this->oSearch->getImgUrl($this->aFileInfo['Hash'], 'file'),
            'width' => $iWidth,
            'fileTitle' => $this->aFileInfo['medTitle'],
            'fileDescription' => nl2br($this->aFileInfo['medDesc']),
            'rate' => $oVotingView->isEnabled() ? $oVotingView->getBigVoting(1, $this->aFileInfo['Rate']): '',
            'favInfo' => $this->oDb->getFavoritesCount($this->aFileInfo['medID']),
            'viewInfo' => $this->aFileInfo['medViews'],
            'albumUri' => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/album/' . $this->aFileInfo['albumUri'] . '/owner/' . $this->aFileInfo['NickName'],
            'albumCaption' => $this->aFileInfo['albumCaption'],
            'ch_if:prev' => array(
                'condition' => $this->aFileInfo['prevItem'] > 0,
                'content' => array(
                    'linkPrev'  => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aPrev['medUri'],
                    'titlePrev' => $aPrev['medTitle'],
                    'percent' => $this->aFileInfo['nextItem'] > 0 ? 50 : 100,
                )
            ),
            'ch_if:next' => array(
                'condition' => $this->aFileInfo['nextItem'] > 0,
                'content' => array(
                    'linkNext'  => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aNext['medUri'],
                    'titleNext' => $aNext['medTitle'],
                    'percent' => $this->aFileInfo['prevItem'] > 0 ? 50 : 100,
                )
            ),
        );

        $sCode = $this->oTemplate->parseHtmlByName('view_unit.html', $aUnit);
        return array($sCode, array(), array(), false);
    }

    function getBlockCode_MainFileInfo ()
    {
        return $this->oTemplate->getFileInfoMain($this->aFileInfo);
    }

    function getBlockCode_SocialSharing ()
    {
    	if(!$this->oModule->isAllowedShare($this->aFileInfo))
    		return '';

        $sUrl = CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $this->aFileInfo['medUri'];
        $sTitle = $this->aFileInfo['medTitle'];
        $sImgUrl = $this->oSearch->getImgUrl($this->aFileInfo['Hash'], 'file');

        ch_import('ChTemplSocialSharing');
        $sCode = ChTemplSocialSharing::getInstance()->getCode($sUrl, $sTitle, array (
            'img_url' => $sImgUrl,
            'img_url_encoded' => rawurlencode($sImgUrl),
        ));
        return array($sCode, array(), array(), false);
    }

    function getBlockCode_Crop ()
    {
        $this->oTemplate->addCss(array(
            //'font-awesome.min.css',
            'bootstrap.css',
            'plugins/cropperjs/css/|cropper.css',
            'colors.css',
            'crop.css',
        ));

        $this->oTemplate->addJs(array(
            'popper.min.js',
            'bootstrap.min.js',
            'plugins/cropperjs/js/|cropper.js',
            'crop.js',
        ));

        $sImageUrl = $this->oSearch->getImgUrl($this->aFileInfo['Hash'], 'original');
        $aImageSize = getimagesize($sImageUrl);
        if(!$aImageSize) {
            // use image type file if original is not found.
            $sImageUrl = $this->oSearch->getImgUrl($this->aFileInfo['Hash'], 'file');
            $aImageSize = getimagesize($sImageUrl);
        }
        $iMaxWidth = $aImageSize[0];
        $iImageWidth = $aImageSize[0];
        $iImageHeight = $aImageSize[1];
        if($iMaxWidth > 1080) $iImageWidth = $iMaxWidth;

        ch_import('ChWsbCategories');
        $oCategories = new ChWsbCategories();
        $oCategories->getTagObjectConfig ();
        $aCategories = $oCategories->getCategoriesList('ch_photos', (int)$iProfileId, true);
        $aCatSelect = array();
        $iCnt = 0;
        foreach ($aCategories as $id => $value) {
            $iCnt++;
            $aCatSelect[] = array(
                'id' => $iCnt,
                'value' => $id,
                'name' => $value,
            );
        }

        $iOwnerId = getLoggedId();
        $sQuery = "SELECT `Caption`,`Uri` FROM `sys_albums` WHERE `Type` = 'ch_photos' AND `Owner` = '$iOwnerId'";

        $sUri1 = ChWsbAlbums::getAbumUri($this->oConfig->getGlParam('profile_album_name'), $iOwnerId);
        $sUri2 = ChWsbAlbums::getAbumUri($this->oConfig->getGlParam('profile_cover_album_name'), $iOwnerId);

        $aAlbums = db_res_assoc_arr($sQuery);
        $aAlbumSelect = array();
        foreach ($aAlbums as $id => $value) {
            if($value['Caption'] == 'Hidden') continue;
            $aAlbumSelect[] = array(
                'uri' => $value['Uri'],
                'caption' => $value['Caption'],
            );
        }

        $aVars = array(
            'crop_url' =>  CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'crop_perform/' . $this->aFileInfo['medID'],
            'preload_image' => $sImageUrl,
            'ch_repeat:options' => $aCatSelect,
            'ch_repeat:albums' => $aAlbumSelect,
            'default_album_uri' => $sUri1,
            'cover_album_uri' => $sUri2,
            'image_id' => $this->aFileInfo['medID'],
            'title' => '<b>Title:</b> ' . ($this->aFileInfo['medTitle'] ? $this->aFileInfo['medTitle'] : 'None'),
        );
        $sCode = $this->oTemplate->parseHtmlByName('crop.html', $aVars);

        return $sCode;
    }
}
