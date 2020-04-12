<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');

class ChVideosExport extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'ch_videos_cmts' => '`cmt_author_id` = {profile_id}',
            'ch_videos_cmts_albums' => '`cmt_author_id` = {profile_id}',
            'ch_videos_favorites' => '`Profile` = {profile_id}',
            'ch_videos_rating' => array(
                'query' => "SELECT `r`.* FROM `ch_videos_rating` AS `r` INNER JOIN `RayVideoFiles` AS `m` ON (`m`.`ID` = `r`.`gal_id`) WHERE `m`.`Owner` = {profile_id}"),
            'ch_videos_views_track' => array(
                'query' => "SELECT `t`.`id`, IF(`t`.`viewer` = {profile_id}, `t`.`viewer`, 0), IF(`t`.`viewer` = {profile_id}, `t`.`ip`, 0), `t`.`ts` FROM `ch_videos_views_track` AS `t` INNER JOIN `RayVideoFiles` AS `m` ON (`m`.`ID` = `t`.`id`) WHERE `m`.`Owner` = {profile_id} OR `t`.`viewer` = {profile_id}"), // anonymize some data
            'ch_videos_voting_track' => array(
                'query' => "SELECT `t`.`gal_id`, 0, `t`.`gal_date` FROM `ch_videos_voting_track` AS `t` INNER JOIN `RayVideoFiles` AS `m` ON (`m`.`ID` = `t`.`gal_id`) WHERE `m`.`Owner` = {profile_id}"), // anonymize some data
        );
    }
}
