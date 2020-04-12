<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

class ChWsbXMLRPCMediaAudio extends ChWsbXMLRPCMedia
{

    function removeAudio5 ($sUser, $sPwd, $iFileId)
    {
        if (!($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        if (ChWsbService::call('sounds', 'remove_object', array((int)$iFileId)))
            return new xmlrpcval ("ok");
        return new xmlrpcval ("fail");
    }

    function getAudioAlbums ($sUser, $sPwd, $sNick)
    {
        $iIdProfile = ChWsbXMLRPCUtil::getIdByNickname ($sNick);
        if (!$iIdProfile || !($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        return ChWsbXMLRPCMedia::_getMediaAlbums ('music', $iIdProfile, $iId);
    }

    function getAudioInAlbum($sUser, $sPwd, $sNick, $iAlbumId)
    {
        $iIdProfile = ChWsbXMLRPCUtil::getIdByNickname ($sNick);
        if (!$iIdProfile || !($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        return ChWsbXMLRPCMedia::_getFilesInAlbum ('sounds', $iIdProfile, $iId, $iAlbumId, 'mp3', 'getMp3Token', 'flash/modules/mp3/get_file.php?id=');
    }
}
