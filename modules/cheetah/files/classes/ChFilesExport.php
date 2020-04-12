<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');

class ChFilesExport extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'ch_files_cmts' => '`cmt_author_id` = {profile_id}',
            'ch_files_cmts_albums' => '`cmt_author_id` = {profile_id}',
            'ch_files_favorites' => '`Profile` = {profile_id}',
            'ch_files_main' => '`Owner` = {profile_id}',
            'ch_files_rating' => array(
                'query' => "SELECT `r`.* FROM `ch_files_rating` AS `r` INNER JOIN `ch_files_main` AS `m` ON (`m`.`ID` = `r`.`gal_id`) WHERE `m`.`Owner` = {profile_id}"),
            'ch_files_views_track' => array(
                'query' => "SELECT `t`.`id`, IF(`t`.`viewer` = {profile_id}, `t`.`viewer`, 0), IF(`t`.`viewer` = {profile_id}, `t`.`ip`, 0), `t`.`ts` FROM `ch_files_views_track` AS `t` INNER JOIN `ch_files_main` AS `m` ON (`m`.`ID` = `t`.`id`) WHERE `m`.`Owner` = {profile_id} OR `t`.`viewer` = {profile_id}"), // anonymize some data
            'ch_files_voting_track' => array(
                'query' => "SELECT `t`.`gal_id`, 0, `t`.`gal_date` FROM `ch_files_voting_track` AS `t` INNER JOIN `ch_files_main` AS `m` ON (`m`.`ID` = `t`.`gal_id`) WHERE `m`.`Owner` = {profile_id}"), // anonymize some data
        );

        $this->_sFilesBaseDir = 'modules/cheetah/files/data/files/';
        $this->_aTablesWithFiles = array(
            'ch_files_main' => new ChFilesExportFiles($this->_sFilesBaseDir),
        );
    }
}

class ChFilesExportFiles extends ChWsbExportFiles
{
    public function perform($aRow, &$aFiles)
    {
        $sFile = $this->_sBaseDir . $aRow['ID'] . '_' . sha1($aRow['Date']);
        if(file_exists($sFile))
            $aFiles[] = $sFile;
    }
}
