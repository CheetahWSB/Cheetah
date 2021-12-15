<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');
ch_import('ChWsbMemberMenu');

class ChBaseAccountView extends ChWsbPageView
{
    var $iMember;
    var $aMemberInfo;
    var $aConfSite;
    var $aConfDir;

    function __construct($iMember, &$aSite, &$aDir)
    {
        $this->iMember = (int)$iMember;
        $this->aMemberInfo = getProfileInfo($this->iMember);

        $this->aConfSite = $aSite;
        $this->aConfDir  = $aDir;

        parent::__construct('member');
    }

    function getBlockCode_FriendRequests()
    {
        global $oSysTemplate;

        ch_import('ChTemplCommunicator');
        $oCommunicator = new ChTemplCommunicator(array('member_id' => $this->iMember));

        $oSysTemplate->addCss($oCommunicator->getCss());
        $oSysTemplate->addJs($oCommunicator->getJs());
        return $oCommunicator->getBlockCode_FriendRequests(false);
    }

    function getBlockCode_NewMessages()
    {
        global $oSysTemplate;

        ch_import('ChTemplMailBox');
        $aSettings = array(
            'member_id' => $this->iMember,
            'recipient_id' => $this->iMember,
            'mailbox_mode' => 'inbox_new'
        );
        $oMailBox = new ChTemplMailBox('mail_page', $aSettings);

        $oSysTemplate->addCss($oMailBox->getCss());
        $oSysTemplate->addJs($oMailBox->getJs());
        return $oMailBox->getBlockCode_NewMessages(false);
    }

    function getBlockCode_AccountControl()
    {
        global $oTemplConfig, $aPreValues;

        //Labels
        $sProfileStatusC = _t('_Profile status');
        $sPresenceC = _t('_Presence');
        $sMembershipC = _t('_Membership2');
        $sLastLoginC = _t('_Last login');
        $sRegistrationC = _t('_Registration');
        $sEmailC = _t('_Email');
        $sMembersC = ' ' . _t('_Members');
        $sProfileC = _t('_Profile');
        $sContentC = _t('_Content');
        $sTwoFactorAuthC = _t('_two_factor_auth_ws');

        //--- General Info block ---//
        $sProfileStatus = _t( "__{$this->aMemberInfo['Status']}" );
        $sProfileStatusMess = '';
        switch ( $this->aMemberInfo['Status'] ) {
            case 'Unconfirmed':
                $sProfileStatusMess = _t( "_ATT_UNCONFIRMED", $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight );
                break;
            case 'Approval':
                $sProfileStatusMess = _t( "_ATT_APPROVAL", $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight );
                break;
            case 'Active':
                $sProfileStatusMess = _t( "_ATT_ACTIVE", $this->aMemberInfo['ID'], $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight );
                break;
            case 'Rejected':
                $sProfileStatusMess = _t( "_ATT_REJECTED", $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight );
                break;
            case 'Suspended':
                $sProfileStatusMess = _t( "_ATT_SUSPENDED", $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight );
                break;
        }

        $oForm = ch_instance('ChWsbFormCheckerHelper');
        $sMembStatus = GetMembershipStatus($this->aMemberInfo['ID']);

        $sLastLogin = 'never';
        if (!empty($this->aMemberInfo['DateLastLogin']) && $this->aMemberInfo['DateLastLogin'] != "0000-00-00 00:00:00") {
            $sLastLoginTS = $oForm->_passDateTime($this->aMemberInfo['DateLastLogin']);
            $sLastLogin = getLocaleDate($sLastLoginTS, CH_WSB_LOCALE_DATE);
        }

        $sRegistration = 'never';
        if(!empty($this->aMemberInfo['DateReg']) && $this->aMemberInfo['DateReg'] != "0000-00-00 00:00:00" ) {
            $sRegistrationTS = $oForm->_passDateTime($this->aMemberInfo['DateReg']);
            $sRegistration = getLocaleDate($sRegistrationTS, CH_WSB_LOCALE_DATE);
        }

        if (getParam('two_factor_auth')) {
            $iEnabled = (int)$GLOBALS['MySQL']->getOne("SELECT `enabled` FROM `sys_2fa_data` WHERE `memberid` = '$this->iMember'");
            $sTwoFactorAuthStatus = $iEnabled ? _t('_two_factor_auth_status_enabled') : _t('_two_factor_auth_status_disabled');
            $sTwoFactorAuthStatusMess = '';

            if($iEnabled) {
                if (!getParam('two_factor_auth_required')) {
                    $sTwoFactorAuthStatusMess = '<a href="' . CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=disable">' . _t('_two_factor_auth_disable') . '</a>';
                    $sTwoFactorAuthStatusMess .= '<span class="sys-bullet"></span>';
                }
                $sTwoFactorAuthStatusMess .= '<a href="' . CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=setup">' . _t('_two_factor_auth_showqr') . '</a>';
                $sTwoFactorAuthStatusMess .= '<span class="sys-bullet"></span>';
                $sTwoFactorAuthStatusMess .= '<a href="' . CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=sbcodes">' . _t('_two_factor_auth_show_backup') . '</a>';
                $sTwoFactorAuthStatusMess .= '<span class="sys-bullet"></span>';
                $sTwoFactorAuthStatusMess .= '<a href="' . CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=apps">' . _t('_two_factor_auth_show_apps') . '</a>';                
            } else {
                $sTwoFactorAuthStatusMess = '<a href="' . CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=enable">' . _t('_two_factor_auth_enable') . '</a>';
            }
        }

        //--- Presence block ---//
        require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbUserStatusView.php' );
        $oStatusView = new ChWsbUserStatusView();
        $sUserStatus = $oStatusView->getMemberMenuStatuses();

        //--- Content block ---//
        $aAccountCustomStatElements = $GLOBALS['MySQL']->fromCache('sys_account_custom_stat_elements', 'getAllWithKey', 'SELECT * FROM `sys_account_custom_stat_elements`', 'ID');
        $aPQStatisticsElements = $GLOBALS['MySQL']->fromCache('sys_stat_member', 'getAllWithKey', 'SELECT * FROM `sys_stat_member`', 'Type');

        $aCustomElements = array();
        foreach($aAccountCustomStatElements as $iID => $aMemberStats) {
            $sUnparsedLabel = $aMemberStats['Label'];
            $sUnparsedValue = $aMemberStats['Value'];

            $sLabel = _t($sUnparsedLabel);
            $sUnparsedValue = str_replace('__site_url__', CH_WSB_URL_ROOT, $sUnparsedValue);

            //step 1 - replacements of keys
            $sLblTmpl = '__l_';
            $sTmpl = '__';
            while(($iStartPos = strpos($sUnparsedValue, $sLblTmpl)) !== false) {
                $iEndPos = strpos($sUnparsedValue, $sTmpl, $iStartPos + 1);
                if($iEndPos <= $iStartPos)
                    break;

                $sSubstr = substr($sUnparsedValue, $iStartPos + strlen($sLblTmpl), $iEndPos-$iStartPos - strlen($sLblTmpl));
                $sKeyValue = mb_strtolower(_t('_' . $sSubstr));
                $sUnparsedValue = str_replace($sLblTmpl.$sSubstr.$sTmpl, $sKeyValue, $sUnparsedValue);
            }

            //step 2 - replacements of Stat keys
            while(($iStartPos = strpos($sUnparsedValue, $sTmpl, 0)) !== false) {
                $iEndPos = strpos($sUnparsedValue, $sTmpl, $iStartPos + 1);
                if($iEndPos <= $iStartPos)
                    break;

                $iCustomCnt = 0;
                $sSubstr = process_db_input( substr($sUnparsedValue, $iStartPos + strlen($sTmpl), $iEndPos-$iStartPos - strlen($sTmpl)), CH_TAGS_STRIP);
                if ($sSubstr) {
                    $sCustomSQL = $aPQStatisticsElements[$sSubstr]['SQL'];
                    $sCustomSQL = str_replace('__member_id__', $this->aMemberInfo['ID'], $sCustomSQL);
                    $sCustomSQL = str_replace('__profile_media_define_photo__', _t('_ProfilePhotos'), $sCustomSQL);
                    $sCustomSQL = str_replace('__profile_media_define_music__', _t('_ProfileMusic'), $sCustomSQL);
                    $sCustomSQL = str_replace('__profile_media_define_video__', _t('_ProfileVideos'), $sCustomSQL);
                    $sCustomSQL = str_replace('__member_nick__', process_db_input($this->aMemberInfo['NickName'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION), $sCustomSQL);
                    $iCustomCnt = ($sCustomSQL!='') ? (int)db_value($sCustomSQL) : '';
                }
                $sUnparsedValue = str_replace($sTmpl . $sSubstr . $sTmpl, $iCustomCnt, $sUnparsedValue);
            }

            $sTrimmedLabel = trim($sUnparsedLabel, '_');
            $aCustomElements[$sTrimmedLabel] = array(
                'type' => 'custom',
                'name' => $sTrimmedLabel,
                'content' => '<b>' . $sLabel . ':</b> ' . $sUnparsedValue,
                'colspan' => true
            );
        }
        $aForm = array(
            'form_attrs' => array(
                'action' => '',
                'method' => 'post',
            ),
            'params' => array(
                'remove_form' => true,
            ),
            'inputs' => array(
                'header1' => array(
                    'type' => 'block_header',
                    'caption' => $sProfileC,
                    'collapsable' => true
                ),
                'Info' => array(
                    'type' => 'custom',
                    'name' => 'Info',
                    'content' => get_member_thumbnail($this->aMemberInfo['ID'], 'none', true),
                    'colspan' => true
                ),
                'Status' => array(
                    'type' => 'custom',
                    'name' => 'Status',
                    'content' => '<b>' . $sProfileStatusC . ':</b> ' . $sProfileStatus . '<br />' . $sProfileStatusMess,
                    'colspan' => true
                ),
                'Email' => array(
                    'type' => 'custom',
                    'name' => 'Email',
                    'content' => '<b>' . $sEmailC . ':</b> ' . $this->aMemberInfo['Email'] . '<br />' . _t('_sys_txt_ac_manage_subscriptions'),
                    'colspan' => true
                ),
                'Membership' => array(
                    'type' => 'custom',
                    'name' => 'Membership',
                    'content' => '<b>' . $sMembershipC . ':</b> ' . $sMembStatus,
                    'colspan' => true
                ),
                'LastLogin' => array(
                    'type' => 'custom',
                    'name' => 'LastLogin',
                    'content' => '<b>' . $sLastLoginC . ':</b> ' . $sLastLogin,
                    'colspan' => true
                ),
                'Registration' => array(
                    'type' => 'custom',
                    'name' => 'Registration',
                    'content' => '<b>' . $sRegistrationC . ':</b> ' . $sRegistration,
                    'colspan' => true
                ),
                'TwoFactorAuth' => array(
                    'type' => 'custom',
                    'name' => 'TwoFactorAuth',
                    'content' => '<b>' . $sTwoFactorAuthC . ':</b> ' . $sTwoFactorAuthStatus . '<br />' . $sTwoFactorAuthStatusMess,
                    'colspan' => true
                ),
                'header1_end' => array(
                    'type' => 'block_end'
                ),
                'header2' => array(
                    'type' => 'block_header',
                    'caption' => $sPresenceC,
                    'collapsable' => true,
                    'collapsed' => true,
                    'attrs' => array (
                        'id' => 'user_status_ac',
                    ),
                ),
                'UserStatus' => array(
                    'type' => 'custom',
                    'name' => 'Info',
                    'content' => $sUserStatus,
                    'colspan' => true
                ),
                'header2_end' => array(
                    'type' => 'block_end'
                )
             ),
        );

        //custom

        if (!getParam('two_factor_auth')) {
            unset($aForm['inputs']['TwoFactorAuth']);
        }

        if(!empty($aCustomElements)) {
            $aForm['inputs'] = array_merge(
                $aForm['inputs'],
                array('header5' => array(
                    'type' => 'block_header',
                    'caption' => $sContentC,
                    'collapsable' => true,
                    'collapsed' => true
                )),
                $aCustomElements,
                array('header5_end' => array(
                    'type' => 'block_end'
                ))
            );
        }

        $oForm = new ChTemplFormView($aForm);
        $sContent = $GLOBALS['oSysTemplate']->parseHtmlByName('member_account_control.html', array(
            'content' => $oForm->getCode()
        ));

        return array($sContent, array(), array(), false);
    }

    function getBlockCode_Friends()
    {
        $iLimit = 10;
        $sContent = $sPaginate = '';

        $sAllFriends = 'viewFriends.php?iUser=' . $this->iMember;

        // count all friends ;
        $iCount = getFriendNumber($this->iMember);
        if($iCount == 0)
            return;

        $iPages = ceil($iCount/$iLimit);
        $iPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

        if($iPage < 1)
            $iPage = 1;

        if($iPage > $iPages)
            $iPage = $iPages;

        $sSqlFrom = ($iPage - 1) * $iLimit;
        $sSqlLimit = "LIMIT {$sSqlFrom}, {$iLimit}";
        $aFriends = getMyFriendsEx($this->iMember, '', 'image', $sSqlLimit);

        $aTmplParams['ch_repeat:friends'] = array();
        foreach ($aFriends as $iId => $aFriend)
            $aTmplParams['ch_repeat:friends'][] = array(
                'content' => get_member_thumbnail( $iId, 'none', true, 'visitor', array('is_online' => $aFriend[5]))
            );
        $sContent = $GLOBALS['oSysTemplate']->parseHtmlByName('member_friends.html', $aTmplParams);

        $oPaginate = new ChWsbPaginate(array(
            'page_url' => CH_WSB_URL_ROOT . 'member.php',
            'count' => $iCount,
            'per_page' => $iLimit,
            'page' => $iPage,
            'on_change_page' => 'return !loadDynamicBlock({id}, \'member.php?page={page}&per_page={per_page}\');',
        ));
        $sPaginate = $oPaginate->getSimplePaginate($sAllFriends);

        return array($sContent, array(), $sPaginate);
    }

    function getBlockCode_QuickLinks()
    {
        ch_import('ChTemplMenuQlinks2');
        $oMenu = new ChTemplMenuQlinks2();
        $sCodeBlock = $oMenu->getCode();
        return $sCodeBlock;
    }
}
