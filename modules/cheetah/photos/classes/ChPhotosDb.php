<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbFilesDb.php');

class ChPhotosDb extends ChWsbFilesDb
{
    /*
     * Constructor.
     */
    function __construct (&$oConfig)
    {
        parent::__construct($oConfig);
        $this->sFileTable = 'ch_photos_main';
        $this->sFavoriteTable = 'ch_photos_favorites';
        $this->aFileFields['medDesc'] = 'Desc';
        $this->aFileFields['medExt']  = 'Ext';
        $this->aFileFields['medSize'] = 'Size';
        $this->aFileFields['Hash'] = 'Hash';
    }

    function getSettingsCategory ()
    {
        return (int)$this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Photos' LIMIT 1");
    }

    function getIdByHash ($sHash)
    {
        $sHash = process_db_input($sHash, CH_TAGS_STRIP);
        return (int)$this->fromMemory('ch_photos_' . $sHash, 'getOne', "
        SELECT `{$this->aFileFields['medID']}`
        FROM `{$this->sFileTable}`
        WHERE `{$this->aFileFields['Hash']}` = '$sHash'");
    }

    function setAvatar($iFileId, $iAlbumId)
    {
        $this->query("UPDATE `sys_albums_objects` SET `obj_order` = `obj_order` + 1 WHERE `id_album` = " . (int)$iAlbumId);
        return $this->query("UPDATE `sys_albums_objects` SET `obj_order` = 0 WHERE `id_object` = " . (int)$iFileId . " AND `id_album` = " . (int)$iAlbumId);
    }
}
