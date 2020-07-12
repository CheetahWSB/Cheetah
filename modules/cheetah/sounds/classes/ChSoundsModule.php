<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbFilesModule');
define('PROFILE_SOUND_CATEGORY', 'Profile sounds');

class ChSoundsModule extends ChWsbFilesModule
{
    function __construct (&$aModule)
    {
        parent::__construct($aModule);

        // add more sections for administration
        $this->aSectionsAdmin['processing'] = array('exclude_btns' => 'all');
        $this->aSectionsAdmin['failed'] = array(
            'exclude_btns' => array('activate', 'deactivate', 'featured', 'unfeatured')
        );
    }

    function actionGetFile($iFileId)
    {
        $aInfo = $this->_oDb->getFileInfo(array('fileId'=>(int)$iFileId), false, array('medID', 'medProfId', 'medUri', 'albumId', 'Approved'));

        if ($aInfo && $this->isAllowedDownload($aInfo)) {

            $sPathFull = $this->_oConfig->getFilesPath() . $aInfo['medID'] . '.mp3';
            if (file_exists($sPathFull)) {
                $this->isAllowedDownload($aInfo, true);
                header('Connection: close');
                header('Content-Type: audio/mpeg');
                header('Content-Length: ' . filesize($sPathFull));
                header('Last-Modified: ' . gmdate('r', filemtime($sPathFull)));
                header('Content-Disposition: attachment; filename="' . $aInfo['medUri'] . '.mp3";');
                readfile($sPathFull);
                exit;
            } else {
                $this->_oTemplate->displayPageNotFound();
            }

        } elseif (!$aInfo) {
            $this->_oTemplate->displayPageNotFound();
        } else {
            $this->_oTemplate->displayAccessDenied();
        }
    }

    function serviceGetProfileCat ()
    {
        return PROFILE_SOUND_CATEGORY;
    }

    function serviceGetMemberMenuItem ($sIcon = 'music')
    {
        return parent::serviceGetMemberMenuItem ($sIcon);
    }
    function serviceGetMemberMenuItemAddContent ($sIcon = 'music')
    {
        return parent::serviceGetMemberMenuItemAddContent ($sIcon);
    }

    function getEmbedCode ($iFileId)
    {
        return $this->_oTemplate->getEmbedCode($iFileId);
    }

	function isAllowedShare(&$aDataEntry)
    {
    	if($aDataEntry['AllowAlbumView'] != CH_WSB_PG_ALL)
    		return false;

        return true;
    }

    function isAllowedDownload (&$aFile, $isPerformAction = false)
    {
        if (getSettingValue('mp3', "save") != TRUE_VAL)
            return false;
        return $this->isAllowedView($aFile, $isPerformAction);
    }

	function serviceGetWallPost($aEvent)
    {
        return $this->getWallPost($aEvent, 'music');
    }

    function serviceGetWallPostOutline($aEvent)
    {
        return $this->getWallPostOutline($aEvent, 'music');
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
