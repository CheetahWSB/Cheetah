<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    $GLOBALS['ch_profiler_disable'] = 1;

    include("../inc/header.inc.php");
    require_once(CH_DIRECTORY_PATH_INC . 'admin.inc.php');

    require_once(CH_DIRECTORY_PATH_ROOT . 'xmlrpc/ChWsbXMLRPCUtil.php');
    require_once(CH_DIRECTORY_PATH_ROOT . 'xmlrpc/ChWsbXMLRPCUser.php');
    require_once(CH_DIRECTORY_PATH_ROOT . 'xmlrpc/ChWsbXMLRPCMessages.php');
    require_once(CH_DIRECTORY_PATH_ROOT . 'xmlrpc/ChWsbXMLRPCSearch.php');
    require_once(CH_DIRECTORY_PATH_ROOT . 'xmlrpc/ChWsbXMLRPCFriends.php');
    require_once(CH_DIRECTORY_PATH_ROOT . 'xmlrpc/ChWsbXMLRPCMedia.php');
    require_once(CH_DIRECTORY_PATH_ROOT . 'xmlrpc/ChWsbXMLRPCImages.php');
    require_once(CH_DIRECTORY_PATH_ROOT . 'xmlrpc/ChWsbXMLRPCMediaAudio.php');
    require_once(CH_DIRECTORY_PATH_ROOT . 'xmlrpc/ChWsbXMLRPCMediaVideo.php');

    require_once(CH_DIRECTORY_PATH_ROOT . 'xmlrpc/ChWsbXMLRPCProfileView.php');

    require_once(CH_DIRECTORY_PATH_ROOT . 'xmlrpc/lib/xmlrpc.inc');
    require_once(CH_DIRECTORY_PATH_ROOT . 'xmlrpc/lib/xmlrpcs.inc');
    require_once(CH_DIRECTORY_PATH_ROOT . 'xmlrpc/lib/xmlrpc_wrappers.inc');

    $s = new xmlrpc_server(
        array(

            // util

            "cheetah.concat" => array(
                "function" => "ChWsbXMLRPCUtil::concat",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "concat two strings",
            ),

            "cheetah.getContacts" => array(
                "function" => "ChWsbXMLRPCUtil::getContacts",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get user contacts",
            ),

            "cheetah.getCountries" => array(
                "function" => "ChWsbXMLRPCUtil::getCountries",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get countries list",
            ),

            "cheetah.service" => array(
                "function" => "ChWsbXMLRPCUtil::service",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcArray, $xmlrpcString)),
                "docstring" => "perform serice call",
            ),

            // user related

            "cheetah.login" => array(
                "function" => "ChWsbXMLRPCUser::login",
                "signature" => array (array ($xmlrpcInt, $xmlrpcString, $xmlrpcString)),
                "docstring" => "returns user id on success or 0 if login failed",
            ),
            "cheetah.login2" => array(
                "function" => "ChWsbXMLRPCUser::login2",
                "signature" => array (array ($xmlrpcInt, $xmlrpcString, $xmlrpcString)),
                "docstring" => "returns user id on success or 0 if login failed (v.2)",
            ),
            "cheetah.login4" => array(
                "function" => "ChWsbXMLRPCUser::login4",
                "signature" => array (array ($xmlrpcInt, $xmlrpcString, $xmlrpcString)),
                "docstring" => "returns user id on success or 0 if login failed (v.4)",
            ),
            "cheetah.getHomepageInfo" => array(
                "function" => "ChWsbXMLRPCUser::getHomepageInfo",
                "signature" => array (array ($xmlrpcStruct, $xmlrpcString, $xmlrpcString)),
                "docstring" => "return logged in user information to dispay on homepage",
            ),
            "cheetah.getHomepageInfo2" => array(
                "function" => "ChWsbXMLRPCUser::getHomepageInfo2",
                "signature" => array (array ($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "return logged in user information to dispay on homepage (v.2)",
            ),
            "cheetah.getUserInfo" => array(
                "function" => "ChWsbXMLRPCUser::getUserInfo",
                "signature" => array (array ($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "return user information",
            ),
            "cheetah.getUserInfo2" => array(
                "function" => "ChWsbXMLRPCUser::getUserInfo2",
                "signature" => array (array ($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "return user information (v.2)",
            ),
            "cheetah.getUserInfoExtra" => array(
                "function" => "ChWsbXMLRPCUser::getUserInfoExtra",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "return extended users information",
            ),

            "cheetah.updateStatusMessage" => array(
                "function" => "ChWsbXMLRPCUser::updateStatusMessage",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "update user status message, returns 0 on error, or 1 on success",
            ),

            "cheetah.getUserLocation" => array(
                "function" => "ChWsbXMLRPCUser::getUserLocation",
                "signature" => array (array ($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get user location, returns struct on succees, 0 on error, -1 on access denied",
            ),

            "cheetah.updateUserLocation" => array(
                "function" => "ChWsbXMLRPCUser::updateUserLocation",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "update user location, returns 1 on succees, 0 on error",
            ),

            // messages

            "cheetah.getMessagesInbox" => array(
                "function" => "ChWsbXMLRPCMessages::getMessagesInbox",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get user's inbox messages",
            ),
            "cheetah.getMessagesSent" => array(
                "function" => "ChWsbXMLRPCMessages::getMessagesSent",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get user's sent messages",
            ),
            "cheetah.getMessageInbox" => array(
                "function" => "ChWsbXMLRPCMessages::getMessageInbox",
                "signature" => array (array ($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get user's inbox message",
            ),
            "cheetah.getMessageSent" => array(
                "function" => "ChWsbXMLRPCMessages::getMessageSent",
                "signature" => array (array ($xmlrpcScruct, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get user's sent message",
            ),

            "cheetah.sendMessage" => array(
                "function" => "ChWsbXMLRPCMessages::sendMessage",
                "signature" => array (array ($xmlrpcScruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "send message",
            ),

            // search

            "cheetah.getSeachHomeMenu3" => array(
                "function" => "ChWsbXMLRPCSearch::getSeachHomeMenu3",
                "signature" => array (array ($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get search homepage menu",
            ),

            "cheetah.getSearchResultsLocation" => array(
                "function" => "ChWsbXMLRPCSearch::getSearchResultsLocation",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get search results by location",
            ),
            "cheetah.getSearchResultsKeyword" => array(
                "function" => "ChWsbXMLRPCSearch::getSearchResultsKeyword",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString,$xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get search results by keyword",
            ),
            "cheetah.getSearchResultsNearMe" => array(
                "function" => "ChWsbXMLRPCSearch::getSearchResultsNearMe",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get search results near specified location",
            ),

            // friends

            "cheetah.getFriends" => array(
                "function" => "ChWsbXMLRPCFriends::getFriends",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get user's friends",
            ),
            "cheetah.getFriendRequests" => array(
                "function" => "ChWsbXMLRPCFriends::getFriendRequests",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get friend requests",
            ),
            "cheetah.declineFriendRequest" => array(
                "function" => "ChWsbXMLRPCFriends::declineFriendRequest",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "decline friend request",
            ),
            "cheetah.acceptFriendRequest" => array(
                "function" => "ChWsbXMLRPCFriends::acceptFriendRequest",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "accept friend request",
            ),
            "cheetah.removeFriend" => array(
                "function" => "ChWsbXMLRPCFriends::removeFriend",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "remove friend",
            ),
            "cheetah.addFriend" => array(
                "function" => "ChWsbXMLRPCFriends::addFriend",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "add friend",
            ),

            // images
/*
            "cheetah.getImages" => array(
                "function" => "ChWsbXMLRPCImages::getImages",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get profile's images",
            ),
*/
            "cheetah.removeImage" => array(
                "function" => "ChWsbXMLRPCImages::removeImage",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "remove user image by id",
            ),
            "cheetah.makeThumbnail" => array(
                "function" => "ChWsbXMLRPCImages::makeThumbnail",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "make primary image by image id",
            ),
            "cheetah.getImageAlbums" => array(
                "function" => "ChWsbXMLRPCImages::getImageAlbums",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get profile's images albums",
            ),
            "cheetah.uploadImage" => array(
                "function" => "ChWsbXMLRPCImages::uploadImage",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "upload new image",
            ),
            "cheetah.getImagesInAlbum" => array(
                "function" => "ChWsbXMLRPCImages::getImagesInAlbum",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get profile's images in specified album",
            ),

            // audio

            "cheetah.removeAudio" => array(
                "function" => "ChWsbXMLRPCMediaAudio::removeAudio5",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "remove user sound by id (v.5)",
            ),
            "cheetah.getAudioAlbums" => array(
                "function" => "ChWsbXMLRPCMediaAudio::getAudioAlbums",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get profile's sound albums",
            ),
            "cheetah.getAudioInAlbum" => array(
                "function" => "ChWsbXMLRPCMediaAudio::getAudioInAlbum",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get profile's sounds in specified album",
            ),

            // video

            "cheetah.removeVideo" => array(
                "function" => "ChWsbXMLRPCMediaVideo::removeVideo5",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "remove user video by id (v.5)",
            ),
            "cheetah.getVideoAlbums" => array(
                "function" => "ChWsbXMLRPCMediaVideo::getVideoAlbums",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get profile's video albums",
            ),
            "cheetah.uploadVideo" => array(
                "function" => "ChWsbXMLRPCMediaVideo::uploadVideo5",
                "signature" => array (array ($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "upload new video (v.5)",
            ),
            "cheetah.getVideoInAlbum" => array(
                "function" => "ChWsbXMLRPCMediaVideo::getVideoInAlbum",
                "signature" => array (array ($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
                "docstring" => "get profile's video in specified album",
            ),

        ),
        0
    );

    $s->functions_parameters_type = 'phpvals';
    $GLOBALS['xmlrpc_internalencoding'] = 'UTF-8';
    $s->service();
