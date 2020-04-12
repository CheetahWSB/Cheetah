<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');

class ChWsbExportFlash extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'RayBoardBoards' => '`OwnerID` = {profile_id}',
            'RayBoardUsers' => '`User` = {profile_id}',

            'RayChatHistory' => '`Sender` = {profile_id}',
            'RayChatMessages' => '`Sender` = {profile_id}',
            'RayChatProfiles' => '`ID` = {profile_id}',
            'RayChatRoomsUsers' => '`User` = {profile_id}',

            'RayImContacts' => '`SenderID` = {profile_id}',
            'RayImPendings' => '`SenderID` = {profile_id}',

            'RayMp3Files' => '`Owner` = {profile_id}',

            'RayVideoFiles' => '`Owner` = {profile_id}',

            'RayVideo_commentsFiles' => '`Owner` = {profile_id}',
        );
        $this->_sFilesBaseDir = 'flash/modules/';
        $this->_aTablesWithFiles = array(
            'RayMp3Files' => array( // table name
                'ID' => array ( // field name
                    '.mp3', '.ogg', // prefixes & extensions
                ),
            ),
            'RayVideoFiles' => array( // table name
                'ID' => array ( // field name
                    '.m4v', '.mp4', '.webm', '.flv', '.jpg', '_small.jpg', '_small_2x.jpg', // prefixes & extensions
                ),
            ),
            'RayVideo_commentsFiles' => array( // table name
                'ID' => array ( // field name
                    '.m4v', '.mp4', '.webm', '.flv', '.jpg', '_small.jpg', '_small_2x.jpg', // prefixes & extensions
                ),
            ),
        );
    }

    protected function _getFilePath($sTableName, $sField, $sFileName, $sPrefix, $sExt)
    {
        switch ($sTableName) {
        case 'RayMp3Files':
            $sPrefix = 'mp3/files/';
            break;
        case 'RayVideoFiles':
            $sPrefix = 'video/files/';
            break;
        case 'RayVideo_commentsFiles':
            $sPrefix = 'video_comments/files/';
            break;
        }
        return $this->_sFilesBaseDir . $sPrefix . $sFileName . $sExt;
    }
}
