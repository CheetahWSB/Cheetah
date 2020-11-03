<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbFilesDb.php');

class ChVideosDb extends ChWsbFilesDb
{
    /*
     * Constructor.
     */
    function __construct (&$oConfig)
    {
        parent::__construct($oConfig);
        $this->aFileFields['medExt'] = 'Video';
        $this->aFileFields['medSource'] = 'Source';
        $this->sFileTable = 'RayVideoFiles';
        $this->sFavoriteTable = 'ch_videos_favorites';
    }

    function getSettingsCategory ()
    {
        return (int)$this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Videos' LIMIT 1");
    }

    function updateVideo($iId, $aData) {
        // process all recived fields;
        foreach($aData as $sKey => $mValue) {
            $mValue = process_db_input($mValue, CH_TAGS_VALIDATE, CH_SLASHES_AUTO);
            $sKey = process_db_input($sKey, CH_TAGS_STRIP, CH_SLASHES_NO_ACTION);
            $sFields .= "`{$sKey}` = '{$mValue}', ";
        }

        $sFields = preg_replace( '/,$/', '', trim($sFields) );

        $sQuery = "UPDATE `RayVideoFiles` SET {$sFields} WHERE `ID` = '$iId'";
        $this -> query($sQuery);
    }

    function getVideoData($iId) {
        $sQuery = "SELECT * FROM `RayVideoFiles` WHERE `ID` = '$iId'";
        return $this -> getRow($sQuery);
    }

}
