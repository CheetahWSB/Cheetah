<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');

class ChFdbExport extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'ch_fdb_comments' => '`cmt_author_id` = {profile_id}',
            'ch_fdb_comments_track' => '`cmt_rate_author_id` = {profile_id}',
            'ch_fdb_entries' => '`author_id` = {profile_id}',
            'ch_fdb_voting' => array(
                'query' => "SELECT `v`.* FROM `ch_fdb_voting` AS `v` INNER JOIN `ch_fdb_entries` AS `m` ON (`m`.`id` = `v`.`fdb_id`) WHERE `m`.`author_id` = {profile_id}"),
            'ch_fdb_voting_track' => array(
                'query' => "SELECT `t`.`fdb_id`, 0, `t`.`fdb_date` FROM `ch_fdb_voting_track` AS `t` INNER JOIN `ch_fdb_entries` AS `m` ON (`m`.`id` = `t`.`fdb_id`) WHERE `m`.`author_id` = {profile_id}"), // anonymize some data
        );
    }
}
