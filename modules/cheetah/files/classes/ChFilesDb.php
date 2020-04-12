<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbFilesDb.php');

class ChFilesDb extends ChWsbFilesDb
{
    /*
     * Constructor.
     */
    function __construct(&$oConfig)
    {
        parent::__construct($oConfig);

        $this->sFileTable = 'ch_files_main';
        $this->sFavoriteTable = 'ch_files_favorites';
        $this->sMimeTypeTable = 'ch_files_types';

        $aAddFields = array(
            'medExt'   => 'Ext',
            'medDesc'  => 'Desc',
            'medSize'  => 'Size',
            'Type'     => 'Type',
            'DownloadsCount' => 'DownloadsCount',
            'AllowDownload'  => 'AllowDownload'
        );
        $this->aFileFields = array_merge($this->aFileFields, $aAddFields);

        $this->aFavoriteFields = array(
            'fileId'  => 'ID',
            'ownerId' => 'Profile',
            'favDate' => 'Date'
        );
    }

    function getTypeIcon ($sType)
    {
        $sType = process_db_input($sType, CH_TAGS_STRIP);
        $sqlQuery = "SELECT `Icon` FROM `{$this->sMimeTypeTable}` WHERE `{$this->aFileFields['Type']}`='$sType' LIMIT 1";
        return $this->getOne($sqlQuery);
    }

    function getTypeToIconArray()
    {
        return $this->getPairs("SELECT `Type`, `Icon` FROM `{$this->sMimeTypeTable}` WHERE 1", "Type", "Icon");
    }

    function getDownloadsCount ($iFile)
    {
        $iFile = (int)$iFile;
        return $this->query("SELECT `{$this->aFileFields['DownloadsCount']}` FROM `{$this->sFileTable}` WHERE `{$this->aFileFields['medID']}` = '$iFile'");
    }

    function updateDownloadsCount ($sFileUri)
    {
        $sFileUri = process_db_input($sFileUri, CH_TAGS_STRIP);
        $this->query("UPDATE `{$this->sFileTable}` SET `{$this->aFileFields['DownloadsCount']}` = `{$this->aFileFields['DownloadsCount']}` + 1 WHERE `{$this->aFileFields['medUri']}`='$sFileUri'");
    }

    function insertMimeType ($sMimeType)
    {
        $sMimeType = process_db_input($sMimeType, CH_TAGS_STRIP);
        $sqlQuery = "INSERT INTO `{$this->sMimeTypeTable}` SET `Type`='$sMimeType'";
        $this->res($sqlQuery);
    }

    function updateMimeTypePic ($mixedMimeTypes, $sPic)
    {
        $mixedMimeTypes = process_db_input($mixedMimeTypes, CH_TAGS_STRIP);
        if (is_array($mixedMimeTypes))
            $sqlCond = "IN('" . implode("', '", $mixedMimeTypes) . "')";
        else
           $sqlCond = "= '$mixedMimeTypes'";

        $sqlQuery = "UPDATE `{$this->sMimeTypeTable}` SET `Icon` = '$sPic' WHERE `Type` $sqlCond";
        $this->res($sqlQuery);
    }

    function checkMimeTypeExist ($sMimeType)
    {
        $sMimeType = process_db_input($sMimeType, CH_TAGS_STRIP);
        $sqlQuery = "SELECT COUNT(*) FROM `{$this->sMimeTypeTable}` WHERE `Type`='$sMimeType'";
        return (int)$this->getOne($sqlQuery);
    }

    function getSettingsCategory ()
    {
        return (int)$this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Files' LIMIT 1");
    }
}
