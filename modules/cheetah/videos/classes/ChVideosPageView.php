<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

require_once('ChVideosCmts.php');
require_once('ChVideosSearch.php');

class ChVideosPageView extends ChWsbPageView
{
    var $iProfileId;
    var $aFileInfo;

    var $oModule;
    var $oTemplate;
    var $oConfig;
    var $oDb;
    var $oSearch;

    function __construct (&$oShared, &$aFileInfo)
    {
        parent::__construct('ch_videos_view');
        $this->aFileInfo = $aFileInfo;
        $this->iProfileId = &$oShared->_iProfileId;

        $this->oModule = $oShared;
        $this->oTemplate = $oShared->_oTemplate;
        $this->oConfig = $oShared->_oConfig;
        $this->oDb = $oShared->_oDb;
        $this->oSearch = new ChVideosSearch();
        $this->oTemplate->addCss(array('view.css', 'search.css'));
        ch_import ('ChWsbViews');
        new ChWsbViews('ch_' . $this->oConfig->getUri(), $this->aFileInfo['medID']);
    }

    function getBlockCode_ActionList ()
    {
        $sCode = null;
        $sMainPrefix = $this->oConfig->getMainPrefix();

        ch_import('ChWsbSubscription');
        $oSubscription = ChWsbSubscription::getInstance();
        $aButton = $oSubscription->getButton($this->iProfileId, $sMainPrefix, '', (int)$this->aFileInfo['medID']);
        $sCode .= $oSubscription->getData();

        $aReplacement = array(
            'favorited' => $this->aFileInfo['favorited'] == false ? '' : 'favorited',
            'featured' => (int)$this->aFileInfo['Featured'],
            'featuredCpt' => '',
            'approvedCpt' => '',
            'approvedAct' => '',
            'moduleUrl' => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri(),
            'fileUri' => $this->aFileInfo['medUri'],
            'iViewer' => $this->iProfileId,
            'ID' => (int)$this->aFileInfo['medID'],
            'Owner' => (int)$this->aFileInfo['medProfId'],
            'OwnerName' => $this->aFileInfo['NickName'],
            'AlbumUri' => $this->aFileInfo['albumUri'],
            'sbs_' . $sMainPrefix . '_title' => $aButton['title'],
            'sbs_' . $sMainPrefix . '_script' => $aButton['script'],
            'shareCpt' => $this->oModule->isAllowedShare($this->aFileInfo) ? _t('_Share') : '',
            'downloadCpt' => $this->oModule->isAllowedDownload($this->aFileInfo) && empty($this->aFileInfo['medSource']) ? _t('_Download') : '',
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

    function getBlockCode_FileAuthor()
    {
        return $this->oTemplate->getFileAuthor($this->aFileInfo);
    }

    function getBlockCode_ViewAlbum ()
    {
        $oAlbum = new ChWsbAlbums($this->oConfig->getMainPrefix());
        $aAlbum = $oAlbum->getAlbumInfo(array('fileId' => $this->aFileInfo['albumId']));
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

        $oCmtsView = new ChVideosCmts('ch_' . $this->oConfig->getUri(), $this->aFileInfo['medID']);
        if(!$oCmtsView->isEnabled())
        	return '';

		return $oCmtsView->getCommentsFirst();
    }

    function getBlockCode_ViewFile ()
    {
        $this->aFileInfo['favCount'] = $this->oDb->getFavoritesCount($this->aFileInfo['medID']);
        $sCode = $this->oTemplate->getViewFile($this->aFileInfo);
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
        $aFile = $this->oSearch->serviceGetEntry($this->aFileInfo['medID'], 'poster');

        ch_import('ChTemplSocialSharing');
        $sCode = ChTemplSocialSharing::getInstance()->getCode($sUrl, $sTitle, array (
            'img_url' => $aFile['file'],
            'img_url_encoded' => rawurlencode($aFile['file']),
        ));

        return array($sCode, array(), array(), false);
    }
}
