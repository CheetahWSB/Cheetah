<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');

class ChPollExport extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'ch_poll_cmts' => '`cmt_author_id` = {profile_id}',
            'ch_poll_cmts_track' => '`cmt_rate_author_id` = {profile_id}',
            'ch_poll_data' => '`id_profile` = {profile_id}',
            'ch_poll_rating' => array(
                'query' => "SELECT `f`.* FROM `ch_poll_rating` AS `f` INNER JOIN `ch_poll_data` AS `m` ON (`m`.`id_poll` = `f`.`id`) WHERE `m`.`id_profile` = {profile_id}"),
            'ch_poll_voting_track' => array(
                'query' => "SELECT `t`.`id`, 0, `t`.`date` FROM `ch_poll_voting_track` AS `t` INNER JOIN `ch_poll_data` AS `m` ON (`m`.`id_poll` = `t`.`id`) WHERE `m`.`id_profile` = {profile_id}"), // anonymize some data
        );
    }
}
