<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

define('CH_XMLRPC_PROTOCOL_VER', 5);

class ChWsbXMLRPCUser
{
    function login($sUser, $sPwd)
    {
        $iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd);
        return new xmlrpcresp(new xmlrpcval($iId, "int"));
    }

    function login4($sUser, $sPwdClear)
    {
        $iId = 0;
        $aProfileInfo = getProfileInfo(getID($sUser));
        if ($aProfileInfo && ((32 == strlen($sPwdClear) || 40 == strlen($sPwdClear)) && ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwdClear)))
            $iId = $aProfileInfo['ID'];
        elseif ($aProfileInfo && getParam('enable_cheetah_footer') != 'on' && check_password ($aProfileInfo['ID'], $sPwdClear, CH_WSB_ROLE_MEMBER, false))
            $iId = $aProfileInfo['ID'];

        return new xmlrpcresp(new xmlrpcval(array(
            'member_id' => new xmlrpcval($iId, "int"),
            'member_pwd_hash' => new xmlrpcval($iId ? $aProfileInfo['Password'] : ""),
            'member_username' => new xmlrpcval($iId ? getUsername($iId) : ""),
            'protocol_ver' => new xmlrpcval(CH_XMLRPC_PROTOCOL_VER, "int"),
        ), "struct"));
    }

    function login2($sUser, $sPwd)
    {
        $iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd);
        return new xmlrpcresp(new xmlrpcval(array(
            'member_id' => new xmlrpcval($iId, "int"),
            'protocol_ver' => new xmlrpcval(CH_XMLRPC_PROTOCOL_VER, "int"),
        ), "struct"));
    }

    function updateUserLocation ($sUser, $sPwd, $sLat, $sLng, $sZoom, $sMapType)
    {
        if (!($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)) || !preg_match('/^[A-Za-z0-9]*$/', $sMapType))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        $iRet = ChWsbService::call('wmap', 'update_location_manually', array ('profiles', $iId, (float)$sLat, (float)$sLng, (int)$sZoom, $sMapType)) ? '1' : '0';

        return new xmlrpcresp(new xmlrpcval(false === $iRet || 404 == $iRet || 403 == $iRet ? false : true));
    }

    function getUserLocation ($sUser, $sPwd, $sNick)
    {
        if (!($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        $iProfileId = getID($sNick, false);
        $aLocation = ChWsbService::call('wmap', 'get_location', array('profiles', $iProfileId, $iId));
        if (-1 == $aLocation) // access denied
            return new xmlrpcval("-1");
        if (!is_array($aLocation)) // location is undefined
            return new xmlrpcval("0");

        return new xmlrpcval(array(
            'lat' => new xmlrpcval($aLocation['lat']),
            'lng' => new xmlrpcval($aLocation['lng']),
            'zoom' => new xmlrpcval($aLocation['zoom']),
            'type' => new xmlrpcval($aLocation['type']),
            'address' => new xmlrpcval($aLocation['address']),
            'country' => new xmlrpcval($aLocation['country']),
        ), 'struct');
    }

    function getHomepageInfo($sUser, $sPwd)
    {
        if (!($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        $aRet = ChWsbXMLRPCUtil::getUserInfo($iId);

        $aRet['unreadLetters'] = new xmlrpcval(getNewLettersNum($iId));
        $aFriendReq =  db_arr( "SELECT count(*) AS `num` FROM `sys_friend_list` WHERE `Profile` = {$iId} AND  `Check` = '0'" );
        $aRet['friendRequests'] = new xmlrpcval($aFriendReq['num']);

        return new xmlrpcval ($aRet, "struct");
    }

    function getHomepageInfo2($sUser, $sPwd, $sLang)
    {
        if (!($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        ChWsbXMLRPCUtil::setLanguage ($sLang);

        $aRet = ChWsbXMLRPCUtil::getUserInfo($iId);

        $aMarkersReplace = array (
            'member_id' => $iId,
            'member_username' => rawurlencode($sUser),
            'member_password' => $sPwd,
        );
        $aRet['menu'] = new xmlrpcval(ChWsbXMLRPCUtil::getMenu('homepage', $aMarkersReplace), 'array');

        ch_import('ChWsbMemberInfo');
        $oMemberInfo = ChWsbMemberInfo::getObjectInstance(getParam('sys_member_info_thumb'));
        $aRet['search_with_photos'] = new xmlrpcval($oMemberInfo->isAvatarSearchAllowed() ? 1 : 0);

        return new xmlrpcval ($aRet, "struct");
    }

    function getUserInfo2($sUser, $sPwd, $sNick, $sLang)
    {
        $iIdProfile = ChWsbXMLRPCUtil::getIdByNickname ($sNick);
        if (!$iIdProfile || !($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        ChWsbXMLRPCUtil::setLanguage ($sLang);

        $mixedRet = ChWsbXMLRPCUser::_checkUserPrivacy ($iId, $iIdProfile);
        if (true !== $mixedRet)
            return $mixedRet;

        $aRet['info'] = new xmlrpcval (ChWsbXMLRPCUtil::getUserInfo($iIdProfile, 0, false), "struct");

        $aMarkersReplace = array (
            'member_id' => $iId,
            'member_username' => rawurlencode($sUser),
            'member_password' => $sPwd,
            'profile_id' => $iIdProfile,
            'profile_username' => $sNick,
        );
        $aRet['menu'] = new xmlrpcval(ChWsbXMLRPCUtil::getMenu('profile', $aMarkersReplace), 'array');

        return new xmlrpcval ($aRet, "struct");
    }

    function getUserInfo($sUser, $sPwd, $sNick, $sLang)
    {
        $iIdProfile = ChWsbXMLRPCUtil::getIdByNickname ($sNick);
        if (!$iIdProfile || !($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        ChWsbXMLRPCUtil::setLanguage ($sLang);

        $mixedRet = ChWsbXMLRPCUser::_checkUserPrivacy ($iId, $iIdProfile);
        if (true !== $mixedRet)
            return $mixedRet;

        $aRet = ChWsbXMLRPCUtil::getUserInfo($iIdProfile, 0, true);
        return new xmlrpcval ($aRet, "struct");
    }

    function _checkUserPrivacy($iId, $iIdProfile)
    {
        $mixedAccessDenied = false;

        if ($iIdProfile != $iId) {
            // membership
            $aCheckRes = checkAction($iId, ACTION_ID_VIEW_PROFILES, true, $iIdProfile);
            if ($aCheckRes[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
                $mixedAccessDenied = strip_tags($aCheckRes[CHECK_ACTION_MESSAGE]);

            // privacy
            if (false === $mixedAccessDenied) {
                ch_import('ChWsbPrivacy');
                $oPrivacy = new ChWsbPrivacy('Profiles', 'ID', 'ID');
                if ($iIdProfile != $iId && !$oPrivacy->check('view', $iIdProfile, $iId))
                    $mixedAccessDenied = '-1';
            }
        }

        ch_import('ChWsbAlerts');
        $oZ = new ChWsbAlerts('mobile', 'view_profile', $iIdProfile, $iId, array('access_denied' => &$mixedAccessDenied));
        $oZ->alert();

        if (false !== $mixedAccessDenied)
            return new xmlrpcval ($mixedAccessDenied);

        return true;
    }

    function getUserInfoExtra($sUser, $sPwd, $sNick, $sLang)
    {
        $iIdProfile = ChWsbXMLRPCUtil::getIdByNickname ($sNick);
        if (!$iIdProfile || !($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        ChWsbXMLRPCUtil::setLanguage ($sLang);

        $o = new ChWsbXMLRPCProfileView ($iIdProfile, $iId);
        return $o->getProfileInfoExtra();
    }

    function updateStatusMessage ($sUser, $sPwd, $sStatusMsg)
    {
        if (!($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        ob_start();
        $_GET['action'] = '1';
        require_once( CH_DIRECTORY_PATH_ROOT . 'list_pop.php' );
        ob_end_clean();

        $_POST['status_message'] = $sStatusMsg;
        ActionChangeStatusMessage ($iId);

        return new xmlrpcresp(new xmlrpcval($iRet, "int"));
    }

}
