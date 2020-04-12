<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

class ChWsbXMLRPCImages extends ChWsbXMLRPCMedia
{

    function removeImage ($sUser, $sPwd, $iImageId)
    {
        if (!($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        if (ChWsbService::call('photos', 'remove_object', array((int)$iImageId)))
            return new xmlrpcval ("ok");
        return new xmlrpcval ("fail");
    }

    function makeThumbnail ($sUser, $sPwd, $iImageId)
    {
        if (!($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        switch (getParam('sys_member_info_thumb')) {
        case 'sys_avatar':
            if (ChWsbService::call('avatar', 'make_avatar_from_shared_photo_auto', array((int)$iImageId)))
                return new xmlrpcval ("ok");
            break;
        case 'ch_photos_thumb':
            if (ChWsbService::call('photos', 'set_avatar', array((int)$iImageId)))
                return new xmlrpcval ("ok");
            break;
        }
        return new xmlrpcval ("fail");
    }

    function getImageAlbums ($sUser, $sPwd, $sNick)
    {
        $iIdProfile = ChWsbXMLRPCUtil::getIdByNickname ($sNick);
        if (!$iIdProfile || !($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        // create user's default album if there is no one
        if ($sUser == $sNick) {
            $sCaption = str_replace('{nickname}', $sUser, getParam('ch_photos_profile_album_name'));
            ch_import('ChWsbAlbums');
            $oAlbum = new ChWsbAlbums('ch_photos');
            $aData = array(
                'caption' => $sCaption,
                'location' => _t('_ch_photos_undefined'),
                'owner' => $iId,
                'AllowAlbumView' => CH_WSB_PG_ALL,
            );
            $oAlbum->addAlbum($aData);
        }

        return ChWsbXMLRPCMedia::_getMediaAlbums ('photo', $iIdProfile, $iId, $iIdProfile == $iId);
    }

    function uploadImage ($sUser, $sPwd, $sAlbum, $binImageData, $iDataLength, $sTitle, $sTags, $sDesc)
    {
        return ChWsbXMLRPCMedia::_uploadFile ('photo', $sUser, $sPwd, $sAlbum, $binImageData, $iDataLength, $sTitle, $sTags, $sDesc, "jpg");
    }

    function getImagesInAlbum($sUser, $sPwd, $sNick, $iAlbumId)
    {
        $iIdProfile = ChWsbXMLRPCUtil::getIdByNickname ($sNick);
        if (!$iIdProfile || !($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        return ChWsbXMLRPCMedia::_getFilesInAlbum ('photos', $iIdProfile, $iId, (int)$iAlbumId);
    }

}
