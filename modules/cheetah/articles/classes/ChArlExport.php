<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');

class ChArlExport extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'ch_arl_comments' => '`cmt_author_id` = {profile_id}',
            'ch_arl_comments_track' => '`cmt_rate_author_id` = {profile_id}',
            'ch_arl_entries' => '`author_id` = {profile_id}',
            'ch_arl_views_track' => array(
                'query' => "SELECT `t`.`id`, IF(`t`.`viewer` = {profile_id}, `t`.`viewer`, 0), IF(`t`.`viewer` = {profile_id}, `t`.`ip`, 0), `t`.`ts` FROM `ch_arl_views_track` AS `t` INNER JOIN `ch_arl_entries` AS `m` ON (`m`.`id` = `t`.`id`) WHERE `m`.`author_id` = {profile_id} OR `t`.`viewer` = {profile_id}"), // anonymize some data
            'ch_arl_voting' => array(
                'query' => "SELECT `v`.* FROM `ch_arl_voting` AS `v` INNER JOIN `ch_arl_entries` AS `m` ON (`m`.`id` = `v`.`arl_id`) WHERE `m`.`author_id` = {profile_id}"),
            'ch_arl_voting_track' => array(
                'query' => "SELECT `t`.`arl_id`, 0, `t`.`arl_date` FROM `ch_arl_voting_track` AS `t` INNER JOIN `ch_arl_entries` AS `m` ON (`m`.`id` = `t`.`arl_id`) WHERE `m`.`author_id` = {profile_id}"), // anonymize some data
        );
    }
}
