<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');

class ChBlogsExport extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'ch_blogs_cmts' => '`cmt_author_id` = {profile_id}',
            'ch_blogs_main' => '`OwnerID` = {profile_id}',
            'ch_blogs_posts' => '`OwnerID` = {profile_id}',
            'ch_blogs_rating' => array(
                'query' => "SELECT `r`.* FROM `ch_blogs_rating` AS `r` INNER JOIN `ch_blogs_posts` AS `m` ON (`m`.`PostID` = `r`.`blogp_id`) WHERE `m`.`OwnerID` = {profile_id}"),
            'ch_blogs_views_track' => array(
                'query' => "SELECT `t`.`id`, IF(`t`.`viewer` = {profile_id}, `t`.`viewer`, 0), IF(`t`.`viewer` = {profile_id}, `t`.`ip`, 0), `t`.`ts` FROM `ch_blogs_views_track` AS `t` INNER JOIN `ch_blogs_posts` AS `m` ON (`m`.`PostID` = `t`.`id`) WHERE `m`.`OwnerID` = {profile_id} OR `t`.`viewer` = {profile_id}"), // anonymize some data
            'ch_blogs_voting_track' => array(
                'query' => "SELECT `t`.`blogp_id`, 0, `t`.`blogp_date` FROM `ch_blogs_voting_track` AS `t` INNER JOIN `ch_blogs_posts` AS `m` ON (`m`.`PostID` = `t`.`blogp_id`) WHERE `m`.`OwnerID` = {profile_id}"), // anonymize some data
        );
        $this->_sFilesBaseDir = 'media/images/blog/';
        $this->_aTablesWithFiles = array(
            'ch_blogs_posts' => array( // table name
                'PostPhoto' => array ( // field name
                    // prefixes & extensions
                    'big_' => '',
                    'browse_' => '',
                    'orig_' => '',
                    'small_' => '',
                ),
            ),
        );
    }
}
