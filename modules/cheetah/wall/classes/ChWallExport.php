<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');

class ChWallExport extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'ch_wall_comments' => "`cmt_author_id` = {profile_id}",
            'ch_wall_comments_track' => "`cmt_rate_author_id` = {profile_id}",
            'ch_wall_events' => "`owner_id` = {profile_id} OR IF(SUBSTRING(`type`, 1, 11) = 'wall_common', `object_id` = {profile_id}, 0)",
            'ch_wall_repost_track' => "`author_id` = {profile_id}",
            'ch_wall_voting' => array(
                'query' => "SELECT `v`.* FROM `ch_wall_voting` AS `v` INNER JOIN `ch_wall_events` AS `m` ON (`m`.`id` = `v`.`wall_id`) WHERE `owner_id` = {profile_id} OR IF(SUBSTRING(`type`, 1, 11) = 'wall_common', `object_id` = {profile_id}, 0)"),
            'ch_wall_voting_track' => array(
                'query' => "SELECT `t`.`wall_id`, 0, `t`.`wall_date` FROM `ch_wall_voting_track` AS `t` INNER JOIN `ch_wall_events` AS `m` ON (`m`.`id` = `t`.`wall_id`) WHERE `owner_id` = {profile_id} OR IF(SUBSTRING(`type`, 1, 11) = 'wall_common', `object_id` = {profile_id}, 0)"), // anonymize some data
        );
    }
}
