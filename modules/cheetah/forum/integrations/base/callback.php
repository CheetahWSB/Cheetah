<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

/**
 *
 * redefine callback functions in Forum class
 *******************************************************************************/

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbAlerts.php');
require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbProfile.php');

global $f;

$f->getUserInfo = 'getUserInfo';
$f->getUserPerm = 'getUserPerm';
$f->getLoginUser = 'getLoginUser';
$f->onPostReply = 'onPostReply';
$f->onPostEdit = 'onPostEdit'; // $arrayTopic, $intPostId, $stringPostText, $stringUser
$f->onPostDelete = 'onPostDelete'; // $arrayTopic, $intPostId, $stringUser
$f->onNewTopic = 'onNewTopic'; // $intForumId, $stringTopicSubject, $stringTopicText, $isTopicSticky, $stringUser, $stringTopicUri
$f->onVote = 'onVote'; // $intPostId, $stringUser, $intVote (1 or -1)
$f->onReport = 'onReport'; // $intPostId, $stringUser
$f->onFlag = 'onFlag'; // $intTopicId, $stringUser
$f->onUnflag = 'onUnflag'; // $intTopicId, $stringUser

function onPostReply ($aTopic, $sPostText, $sUser)
{
    $oProfile = new ChWsbProfile ($sUser);
    $iProfileId = $oProfile->getID();
    if (CH_ORCA_INTEGRATION == 'cheetah' && !isAdmin($iProfileId)) {
        defineForumActions();
        $iActionId = CH_FORUM_PUBLIC_POST;
        if (isset($aTopic['forum_type']) && 'private' == $aTopic['forum_type'])
            $iActionId = CH_FORUM_PRIVATE_POST;
        checkAction($iProfileId, $iActionId, true); // perform action
    }

    $aPlusOriginal = array (
        'PosterUrl' => $iProfileId ? getProfileLink($iProfileId) : 'javascript:void(0);' ,
        'PosterNickName' => $iProfileId ? getNickName($iProfileId) : $sUser,
        'TopicTitle' => $aTopic['topic_title'],
        'ReplyText' => $sPostText,
    );

    $oEmailTemplate = new ChWsbEmailTemplates();

    $fdb = new DbForum ();
    $a = $fdb->getSubscribersToTopic ($aTopic['topic_id']);
    foreach ($a as $r) {
        if ($r['user'] == $sUser)
            continue;
        $oRecipient = new ChWsbProfile ($r['user']);
        $aRecipient = getProfileInfo($oRecipient->_iProfileID);
        $aPlus = array_merge (array ('Recipient' => ' ' . getNickName($aRecipient['ID'])), $aPlusOriginal);

        $aTemplate = $oEmailTemplate->getTemplate('ch_forum_notifier', $oRecipient->_iProfileID);
        sendMail(trim($aRecipient['Email']), $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus);
    }

    forumAlert('reply', $aTopic['topic_id'], $iProfileId);
}

function onPostEdit ($aTopic, $iPostId, $sPostText, $sUser)
{
    $oProfile = new ChWsbProfile ($sUser);
    $fdb = new DbForum ();
    $aPost = $fdb->getPost($iPostId, false);
    if (CH_ORCA_INTEGRATION == 'cheetah' && $sUser != $aPost['user'] && !isAdmin($oProfile->getID())) {
        defineForumActions();
        checkAction($oProfile->getID(), CH_FORUM_EDIT_ALL, true); // perform action
    }

    forumAlert('post_edit', $iPostId, $oProfile->getID(), array('post' => $aPost));
}

function onPostDelete ($aTopic, $iPostId, $sUser)
{
    $oProfile = new ChWsbProfile ($sUser);
    $fdb = new DbForum ();
    $aPost = $fdb->getPost($iPostId, false);
    if (CH_ORCA_INTEGRATION == 'cheetah' && $sUser != $aPost['user'] && !isAdmin($oProfile->getID())) {
        defineForumActions();
        checkAction($oProfile->getID(), CH_FORUM_DELETE_ALL, true); // perform action
    }

    forumAlert('edit_del', $iPostId, $oProfile->getID(), array('post' => $aPost));
}

function onNewTopic ($iForumId, $sTopicSubject, $sTopicText, $isTopicSticky, $sUser, $sTopicUri, $iPostId)
{
    $fdb = new DbForum ();

    $oProfile = new ChWsbProfile ($sUser);
    $aTopic = $fdb->getTopicByUri($sTopicUri);

    if (CH_ORCA_INTEGRATION == 'cheetah' && !isAdmin($oProfile->getID())) {
        defineForumActions();
        $aForum = $fdb->getForum($iForumId);
        $iActionId = CH_FORUM_PUBLIC_POST;
        if (isset($aForum['forum_type']) && 'private' == $aForum['forum_type'])
            $iActionId = CH_FORUM_PRIVATE_POST;
        checkAction($oProfile->getID(), $iActionId, true); // perform action
    }

    $a = array ($iForumId, $sTopicSubject, $sTopicText, $isTopicSticky, $sUser);
    forumAlert('new_topic', $aTopic['topic_id'], $oProfile->getID(), $a);
}

function onVote ($iPostId, $sUser, $iVote)
{
    $oProfile = new ChWsbProfile ($sUser);
    $a = array ($sUser, $iVote);
    forumAlert('vote', $iPostId, $oProfile->getID(), $a);
}

function onReport ($iPostId, $sUser)
{
    $oProfile = new ChWsbProfile ($sUser);
    forumAlert('post_report', $iPostId, $oProfile->getID());
}

function onFlag ($iTopicId, $sUser)
{
    $oProfile = new ChWsbProfile ($sUser);
    forumAlert('flag', $iTopicId, $oProfile->getID());
}

function onUnflag ($iTopicId, $sUser)
{
    $oProfile = new ChWsbProfile ($sUser);
    forumAlert('unflag', $iTopicId, $oProfile->getID());
}

function getUserInfo ($sUser)
{
    global $gConf;

    $aRoles = array (
        '_ch_forum_role_admin' => CH_WSB_ROLE_ADMIN,
        '_ch_forum_role_moderator' => CH_WSB_ROLE_MODERATOR,
        '_ch_forum_role_affiliate' => CH_WSB_ROLE_AFFILIATE,
        '_ch_forum_role_member' => CH_WSB_ROLE_MEMBER,
        '_ch_forum_role_guest' => CH_WSB_ROLE_GUEST,
    );

    require_once( CH_DIRECTORY_PATH_ROOT . 'inc/utils.inc.php' );
    require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbProfile.php' );

    $aRet = array();

    $oProfile = new ChWsbProfile (getID($sUser));

    $aRet['profile_onclick'] = '';
    $aRet['profile_url'] = $oProfile->_iProfileID ? getProfileLink($oProfile->_iProfileID) : '';
    $aRet['profile_title'] = $oProfile->_iProfileID ? getNickName($oProfile->_iProfileID) : $sUser;
    $aRet['admin'] = $oProfile->_iProfileID ? isAdmin($oProfile->_iProfileID) : false;
    $aRet['special'] = false;
    $aRet['join_date'] = '';
    $aRet['role'] = _t('_ch_forum_role_undefined');

    if ($oProfile->_iProfileID) {
        foreach ($aRoles as $sRolelangKey => $iRoleMask) {
            if (isRole($iRoleMask, $oProfile->_iProfileID)) {
                $aRet['role'] = _t($sRolelangKey);
                break;
            }
        }
    }

    if ($gConf['robot'] == $sUser) {
        $aRet['profile_title'] = _t('_ch_forum_robot');
        $aRet['role'] = _t('_ch_forum_role_robot');
    } elseif ($gConf['anonymous'] == $sUser) {
        $aRet['profile_title'] = _t('_ch_forum_anonymous');
    }

    $aRet['avatar'] = $GLOBALS['oFunctions']->getSexPic('male', 'small');
    $aRet['avatar64'] = $GLOBALS['oFunctions']->getSexPic('male', 'medium');
    if ($oProfile->_iProfileID) {
        $aRet['avatar'] = $GLOBALS['oFunctions']->getMemberAvatar($oProfile->_iProfileID, 'small');
        $aRet['avatar64'] = $GLOBALS['oFunctions']->getMemberAvatar($oProfile->_iProfileID, 'medium');
    }

    return $aRet;
}

function getUserPerm ($sUser, $sType, $sAction, $iForumId)
{
    $iMemberId = getLoggedId();

    $isOrcaAdmin = isAdmin();

    $isLoggedIn = $iMemberId ? 1 : 0;

    defineForumActions();

    $isPublicForumReadAllowed  =                ($aCheck = checkAction($iMemberId, CH_FORUM_PUBLIC_READ)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT] ? 1 : 0;
    $isPublicForumPostAllowed  = $isLoggedIn && ($aCheck = checkAction($iMemberId, CH_FORUM_PUBLIC_POST)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT] ? 1 : 0;
    $isPrivateForumReadAllowed = $isLoggedIn && ($aCheck = checkAction($iMemberId, CH_FORUM_PRIVATE_READ)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT] ? 1 : 0;
    $isPrivateForumPostAllowed = $isLoggedIn && ($aCheck = checkAction($iMemberId, CH_FORUM_PRIVATE_POST)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT] ? 1 : 0;
    $isEditAllAllowed = ($aCheck = checkAction($iMemberId, CH_FORUM_EDIT_ALL)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT] ? 1 : 0;
    $isDelAllAllowed = ($aCheck = checkAction($iMemberId, CH_FORUM_DELETE_ALL)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT] ? 1 : 0;

    return array (
        'read_public' => $isOrcaAdmin || $isPublicForumReadAllowed,
        'post_public' => $isOrcaAdmin || $isPublicForumPostAllowed ? 1 : 0,
        'edit_public' => $isOrcaAdmin || $isEditAllAllowed ? 1 : 0,
        'del_public'  => $isOrcaAdmin || $isEditAllAllowed ? 1 : 0,

        'read_private' => $isOrcaAdmin || $isPrivateForumReadAllowed ? 1 : 0,
        'post_private' => $isOrcaAdmin || $isPrivateForumPostAllowed ? 1 : 0,
        'edit_private' => $isOrcaAdmin || $isEditAllAllowed ? 1 : 0,
        'del_private'  => $isOrcaAdmin || $isDelAllAllowed ? 1 : 0,

        'edit_own' => 1,
        'del_own' => 1,

        'download_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, CH_FORUM_FILES_DOWNLOAD)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'search_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, CH_FORUM_SEARCH)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'sticky_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, CH_FORUM_MAKE_STICKY)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'lock_' => $isOrcaAdmin ? 1 : 0,

        'del_topics_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, CH_FORUM_DEL_TOPICS)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'move_topics_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, CH_FORUM_MOVE_TOPICS)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'hide_topics_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, CH_FORUM_HIDE_TOPICS)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'unhide_topics_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, CH_FORUM_HIDE_TOPICS)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'hide_posts_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, CH_FORUM_HIDE_TOPICS)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'unhide_posts_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, CH_FORUM_HIDE_TOPICS)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
    );
}

function getLoginUser ()
{
    if ($iId = getLoggedId())
        return getUsername($iId);

    return '';
}

function defineForumActions()
{
    defineMembershipActions (array ('forum public read', 'forum public post', 'forum private read', 'forum private post', 'forum search', 'forum edit all', 'forum delete all', 'forum make sticky', 'forum del topics', 'forum move topics', 'forum hide topics', 'forum unhide topics', 'forum hide posts', 'forum unhide posts', 'forum files download'));
}

function forumAlert ($sAction, $iObjectId, $iSenderId, $aExtra = array())
{
    $sAlertUnitPostfix = CH_ORCA_INTEGRATION == 'cheetah' ? '' : '_' . CH_ORCA_INTEGRATION;
    $oAlert = new ChWsbAlerts('ch_forum' . $sAlertUnitPostfix, $sAction, $iObjectId, $iSenderId, $aExtra);
    $oAlert->alert();
}
