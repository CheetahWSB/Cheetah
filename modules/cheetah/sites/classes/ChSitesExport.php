<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');

class ChSitesExport extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'ch_sites_cmts' => '`cmt_author_id` = {profile_id}',
            'ch_sites_cmts_track' => '`cmt_rate_author_id` = {profile_id}',
            'ch_sites_main' => '`ownerid` = {profile_id}',
            'ch_sites_rating' => array(
                'query' => "SELECT `r`.* FROM `ch_sites_rating` AS `r` INNER JOIN `ch_sites_main` AS `m` ON (`m`.`id` = `r`.`sites_id`) WHERE `m`.`ownerid` = {profile_id}"),
            'ch_sites_rating_track' => array(
                'query' => "SELECT `t`.`sites_id`, 0, `t`.`sites_date` FROM `ch_sites_rating_track` AS `t` INNER JOIN `ch_sites_main` AS `m` ON (`m`.`id` = `t`.`sites_id`) WHERE `m`.`ownerid` = {profile_id}"), // anonymize some data
            'ch_sites_views_track' => array(
                'query' => "SELECT `t`.`id`, IF(`t`.`viewer` = {profile_id}, `t`.`viewer`, 0), IF(`t`.`viewer` = {profile_id}, `t`.`ip`, 0), `t`.`ts` FROM `ch_sites_views_track` AS `t` INNER JOIN `ch_sites_main` AS `m` ON (`m`.`ID` = `t`.`id`) WHERE `m`.`ownerid` = {profile_id} OR `t`.`viewer` = {profile_id}"), // anonymize some data
        );
    }
}
