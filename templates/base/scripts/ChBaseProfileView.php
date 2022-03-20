<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbProfileFields');
ch_import('ChWsbPageView');

class ChBaseProfileView extends ChWsbPageView
{
    var $oProfileGen;

    var $aConfSite;
    var $aConfDir;

    function __construct(&$oPr, &$aSite, &$aDir)
    {
        $this->oProfileGen = &$oPr;
        $this->aConfSite = $aSite;
        $this->aConfDir  = $aDir;
        parent::__construct('profile');

        ch_import('ChWsbMemberInfo');

        // Try profile photo first.
        $sProfilePhoto = ChWsbService::call('photos', 'profile_photo', array($oPr->_aProfile['ID'], 'file', 'file_url'), 'Search');
        // If no photo, then try for avatar.
        if (!$sProfilePhoto) {
            $o = ChWsbMemberInfo::getObjectInstance(getParam('sys_member_info_thumb'));
            $sProfilePhoto = $o ? $o->get($oPr->_aProfile) : '';
        }

        $aProfile = getProfileInfoDirect($oPr->_aProfile['ID']);
        $sDesc = $aProfile['DescriptionMe'];

        $GLOBALS['oSysTemplate']->setPageDescription($sDesc);

        $GLOBALS['oSysTemplate']->setOpenGraphInfo(array(
            'url' => CH_WSB_URL_ROOT . getUsername($oPr->_aProfile['ID']),
            'title' => getNickName($oPr->_aProfile['ID']),
            'type' => 'profile',
            'description' => $sDesc,
        ));
        if ($sProfilePhoto)
            $GLOBALS['oSysTemplate']->setOpenGraphInfo(array('image' => $sProfilePhoto));

        $GLOBALS['oSysTemplate']->setOpenGraphInfo(array(
            'url' => CH_WSB_URL_ROOT . getUsername($oPr->_aProfile['ID']),
            'title' => getNickName($oPr->_aProfile['ID']),
            'description' => $sDesc,
        ), 'twitter');
        if ($sProfilePhoto)
            $GLOBALS['oSysTemplate']->setOpenGraphInfo(array('image:src' => $sProfilePhoto), 'twitter');

    }

    function genBlock( $iBlockID, $aBlock, $bStatic = true, $sDynamicType = 'tab' )
    {
        //--- Privacy for Profile page ---//
        $oPrivacy = new ChWsbPrivacy('sys_page_compose_privacy', 'id', 'user_id');

        $iPrivacyId = (int)$GLOBALS['MySQL']->getOne("SELECT `id` FROM `sys_page_compose_privacy` WHERE `user_id`='" . $this->oProfileGen->_iProfileID . "' AND `block_id`='" . $iBlockID . "' LIMIT 1");
        if($iPrivacyId != 0 && !$oPrivacy->check('view_block', $iPrivacyId, $this->iMemberID))
            return false;
        //--- Privacy for Profile page ---//

        return parent::genBlock($iBlockID, $aBlock, $bStatic, $sDynamicType);
    }
	function getBlockCode_Cover()
    {
    	return $this->oProfileGen->showBlockCover('', true);
    }
    function getBlockCode_ActionsMenu()
    {
        return $this->oProfileGen->showBlockActionsMenu('', true);
    }
    function getBlockCode_FriendRequest()
    {
        return $this->oProfileGen->showBlockFriendRequest('', $this, true);
    }
    function getBlockCode_PFBlock( $iBlockID, $sContent )
    {
        return $this->oProfileGen->showBlockPFBlock($iBlockID, '', $sContent, true);
    }
    function getBlockCode_RateProfile()
    {
        return $this->oProfileGen->showBlockRateProfile('', true);
    }
    function getBlockCode_Friends()
    {
        return $this->oProfileGen->showBlockFriends('', $this, true);
    }
    function getBlockCode_MutualFriends()
    {
        return $this->oProfileGen->showBlockMutualFriends('', true);
    }
    function getBlockCode_Comments()
    {
        return $this->oProfileGen->showBlockComments('', true);
    }

    function getBlockCode_Cmts ()
    {
        return $this->oProfileGen->showBlockCmts();
    }

    function getBlockCode_Description()
    {
        global $oSysTemplate;
        $sName = 'DescriptionMe';

        $oPF = new ChWsbProfileFields(2);
        if( !$oPF->aBlocks)
            return '';

        $aItem = false;
        foreach ($oPF->aBlocks as $aBlock) {
            foreach ($aBlock['Items'] as $a) {
                if ($sName == $a['Name']) {
                    $aItem = $a;
                    break 2;
                }
            }
        }

        $aProfileInfo = getProfileInfo($this -> oProfileGen -> _iProfileID);
        if(!trim($aProfileInfo[$sName]))
            return MsgBox(_t('_Empty'));

        return array ($aItem ? $oPF->getViewableValue($aItem, $aProfileInfo[$sName]) : htmlspecialchars_adv($aProfileInfo[$sName]));
    }

    function _getBlockCaptionCode($iBlockID, $aBlock, $aBlockCode, $bStatic = true, $sDynamicType = 'tab')
    {
        //--- Privacy for Profile page ---//
        $sCode = "";
        if($this->iMemberID == $this->oProfileGen->_iProfileID) {
            $sAlt = "";
            $sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('ps_page_chooser.html', array(
                'alt' => $sAlt,
                'page_name' => $this->sPageName,
                'profile_id' => $this->oProfileGen->_iProfileID,
                'block_id' => $iBlockID
            ));
        }
        //--- Privacy for Profile page ---//

        return $sCode . parent::_getBlockCaptionCode($iBlockID, $aBlock, $aBlockCode, $bStatic, $sDynamicType);
    }
}
