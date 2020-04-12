<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

/**
 *
 * redefine callback functions in Forum class
 *******************************************************************************/

$aPathInfo = pathinfo(__FILE__);
require_once ($aPathInfo['dirname'] . '/../base/callback.php');

global $f;

$f->getUserPerm = 'getUserPermStore';

function getUserPermStore ($sUser, $sType, $sAction, $iForumId)
{
    $iMemberId = getLoggedId();

    $aPerm = ChWsbService::call('store', 'get_forum_permission', array ($iMemberId, $iForumId));
    $isOrcaAdmin = $aPerm['admin'];

    $isLoggedIn = $iMemberId || $isOrcaAdmin ? 1 : 0;

    $isPublicForumReadAllowed  =                $aPerm['read'];
    $isPublicForumPostAllowed  = $isLoggedIn && $aPerm['post'];
    $isPrivateForumReadAllowed = $isPublicForumReadAllowed;
    $isPrivateForumPostAllowed = $isPublicForumPostAllowed;
    $isEditAllAllowed = false;
    $isDelAllAllowed = false;

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

        'download_' => $isOrcaAdmin  || $isPublicForumReadAllowed ? 1 : 0,
        'search_' => 0,
        'sticky_' => $isOrcaAdmin,

        'del_topics_' => $isOrcaAdmin || $isEditAllAllowed ? 1 : 0,
        'move_topics_' => isAdmin() ? 1 : 0,
        'hide_topics_' => $isOrcaAdmin || $isEditAllAllowed ? 1 : 0,
        'unhide_topics_' => $isOrcaAdmin || $isEditAllAllowed ? 1 : 0,
        'hide_posts_' => $isOrcaAdmin || $isEditAllAllowed ? 1 : 0,
        'unhide_posts_' => $isOrcaAdmin || $isEditAllAllowed ? 1 : 0,
    );
}
