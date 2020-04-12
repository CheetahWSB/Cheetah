<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChAdsPageView extends ChWsbPageView
{
    var $oAds;
    var $iAdId;

    function __construct(&$oAd, $iAdId)
    {
        $this->iAdId = $iAdId;
        $this->oAds = &$oAd;
        parent::__construct('ads');
    }

    function getBlockCode_AdPhotos()
    {
        return $this->oAds->sTAPhotosContent;
    }

    function getBlockCode_ActionList()
    {
        return $this->oAds->sTAActionsContent;
    }

    function getBlockCode_ViewComments()
    {
        return $this->oAds->sTACommentsContent;
    }

    function getBlockCode_AdInfo()
    {
        return array($this->oAds->sTAInfoContent);
    }

    function getBlockCode_UserOtherAds()
    {
        return $this->oAds->sTAOtherListingContent;
    }

    function getBlockCode_Rate()
    {
        return $this->oAds->sTARateContent;
    }

    function getBlockCode_AdDescription()
    {
        return $this->oAds->sTADescription;
    }

    function getBlockCode_AdCustomInfo()
    {
        $sContent = $this->oAds->sTAOtherInfo;
        return array($sContent, array(), array(), false);
    }

    function getBlockCode_SocialSharing()
    {
        $aAd = $this->oAds->_oDb->getAdInfo($this->iAdId);
        if (!$aAd || !$this->oAds->isAllowedShare($aAd))
            return '';

        $sUrl = $this->oAds->genUrl($this->iAdId, $aAd['EntryUri'], 'entry');
        $sTitle = $aAd['title'];
        $sImgUrl = false;
        $aCustomParams = false;
        if ($aAd['Media'] && ($sImgUrl = $this->oAds->getAdCover($aAd['Media'], 'big_thumb', false))) {
            $aCustomParams = array (
                'img_url' => $sImgUrl,
                'img_url_encoded' => rawurlencode($sImgUrl)
            );
        }

        ch_import('ChTemplSocialSharing');
        $sCode = ChTemplSocialSharing::getInstance()->getCode($sUrl, $sTitle, $aCustomParams);
        return array($sCode, array(), array(), false);
    }
}
