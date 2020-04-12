<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

class ChWsbXMLRPCMediaVideo extends ChWsbXMLRPCMedia
{

    function removeVideo5 ($sUser, $sPwd, $iFileId)
    {
        if (!($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        if (ChWsbService::call('videos', 'remove_object', array((int)$iFileId)))
            return new xmlrpcval ("ok");
        return new xmlrpcval ("fail");
    }

    function getVideoAlbums ($sUser, $sPwd, $sNick)
    {
        $iIdProfile = ChWsbXMLRPCUtil::getIdByNickname ($sNick);
        if (!$iIdProfile || !($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        // create user's default album if there is no one
        if ($sUser == $sNick) {
            $sCaption = str_replace('{nickname}', $sUser, getParam('ch_videos_profile_album_name'));
            ch_import('ChWsbAlbums');
            $oAlbum = new ChWsbAlbums('ch_videos');
            $aData = array(
                'caption' => $sCaption,
                'location' => _t('_ch_videos_undefined'),
                'owner' => $iId,
                'AllowAlbumView' => CH_WSB_PG_ALL,
            );
            $oAlbum->addAlbum($aData);
        }

        return ChWsbXMLRPCMedia::_getMediaAlbums ('video', $iIdProfile, $iId, true);
    }

    function uploadVideo5 ($sUser, $sPwd, $sAlbum, $binImageData, $iDataLength, $sTitle, $sTags, $sDesc, $sExt)
    {
        return ChWsbXMLRPCMedia::_uploadFile ('video', $sUser, $sPwd, $sAlbum, $binImageData, $iDataLength, $sTitle, $sTags, $sDesc, $sExt);
    }

    function getVideoInAlbum($sUser, $sPwd, $sNick, $iAlbumId)
    {
        $iIdProfile = ChWsbXMLRPCUtil::getIdByNickname ($sNick);
        if (!$iIdProfile || !($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        return ChWsbXMLRPCMedia::_getFilesInAlbum ('videos', $iIdProfile, $iId, $iAlbumId, 'video', 'getToken', 'flash/modules/video/get_mobile.php?id=');
    }
}
