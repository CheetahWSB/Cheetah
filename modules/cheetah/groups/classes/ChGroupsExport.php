<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');
ch_import('ChWsbInstallerUtils');

class ChGroupsExport extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'ch_groups_admins' => '`id_profile` = {profile_id}',
            'ch_groups_cmts' => '`cmt_author_id` = {profile_id}',
            'ch_groups_cmts_track' => '`cmt_rate_author_id` = {profile_id}',
            'ch_groups_files' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_files` AS `f` INNER JOIN `ch_groups_main` AS `m` ON (`m`.`ID` = `f`.`entry_id`) WHERE `m`.`author_id` = {profile_id}"),
            'ch_groups_images' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_images` AS `f` INNER JOIN `ch_groups_main` AS `m` ON (`m`.`ID` = `f`.`entry_id`) WHERE `m`.`author_id` = {profile_id}"),
            'ch_groups_main' => '`author_id` = {profile_id}',
            'ch_groups_fans' => '`id_profile` = {profile_id}',
            'ch_groups_rating' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_rating` AS `f` INNER JOIN `ch_groups_main` AS `m` ON (`m`.`ID` = `f`.`gal_id`) WHERE `m`.`author_id` = {profile_id}"),
            'ch_groups_rating_track' => array(
                'query' => "SELECT `t`.`gal_id`, 0, `t`.`gal_date` FROM `ch_groups_rating_track` AS `t` INNER JOIN `ch_groups_main` AS `m` ON (`m`.`id` = `t`.`gal_id`) WHERE `m`.`author_id` = {profile_id}"), // anonymize some data
            'ch_groups_shoutbox' => '`OwnerID` = {profile_id}',
            'ch_groups_sounds' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_sounds` AS `f` INNER JOIN `ch_groups_main` AS `m` ON (`m`.`id` = `f`.`entry_id`) WHERE `m`.`author_id` = {profile_id}"),
            'ch_groups_videos' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_videos` AS `f` INNER JOIN `ch_groups_main` AS `m` ON (`m`.`id` = `f`.`entry_id`) WHERE `m`.`author_id` = {profile_id}"),
            'ch_groups_views_track' => array(
                'query' => "SELECT `t`.`id`, IF(`t`.`viewer` = {profile_id}, `t`.`viewer`, 0), IF(`t`.`viewer` = {profile_id}, `t`.`ip`, 0), `t`.`ts` FROM `ch_groups_views_track` AS `t` INNER JOIN `ch_groups_main` AS `m` ON (`m`.`id` = `t`.`id`) WHERE `m`.`author_id` = {profile_id} OR `t`.`viewer` = {profile_id}"), // anonymize some data

            // groups forum
            'ch_groups_forum' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_forum` AS `f` INNER JOIN `ch_groups_main` AS `m` ON (`m`.`id` = `f`.`entry_id`) WHERE `m`.`author_id` = {profile_id}"),
            'ch_groups_forum_actions_log' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_forum_actions_log` AS `f` INNER JOIN `Profiles` AS `p` ON (`p`.`NickName` = `f`.`user_name`) WHERE `p`.`ID` = {profile_id}"),
            'ch_groups_forum_attachments' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_forum_attachments` AS `f` INNER JOIN `ch_groups_forum_post` AS `m` ON (`m`.`post_id` = `f`.`post_id`) INNER JOIN `Profiles` AS `p` ON (`p`.`NickName` = `m`.`user`) WHERE `p`.`ID` = {profile_id}"),
            'ch_groups_forum_flag' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_forum_flag` AS `f` INNER JOIN `Profiles` AS `p` ON (`p`.`NickName` = `f`.`user`) WHERE `p`.`ID` = {profile_id}"),
            'ch_groups_forum_post' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_forum_post` AS `f` INNER JOIN `Profiles` AS `p` ON (`p`.`NickName` = `f`.`user`) WHERE `p`.`ID` = {profile_id}"),
            'ch_groups_forum_signatures' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_forum_signatures` AS `f` INNER JOIN `Profiles` AS `p` ON (`p`.`NickName` = `f`.`user`) WHERE `p`.`ID` = {profile_id}"),
            'ch_groups_forum_topic' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_forum_topic` AS `f` INNER JOIN `Profiles` AS `p` ON (`p`.`NickName` = `f`.`first_post_user`) WHERE `p`.`ID` = {profile_id}"),
            'ch_groups_forum_user_activity' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_forum_user_activity` AS `f` INNER JOIN `Profiles` AS `p` ON (`p`.`NickName` = `f`.`user`) WHERE `p`.`ID` = {profile_id}"),
            'ch_groups_forum_user_stat' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_forum_user_stat` AS `f` INNER JOIN `Profiles` AS `p` ON (`p`.`NickName` = `f`.`user`) WHERE `p`.`ID` = {profile_id}"),
            'ch_groups_forum_vote' => array(
                'query' => "SELECT `f`.* FROM `ch_groups_forum_vote` AS `f` INNER JOIN `Profiles` AS `p` ON (`p`.`NickName` = `f`.`user_name`) WHERE `p`.`ID` = {profile_id}"),
        );
        $this->_sFilesBaseDir = 'modules/cheetah/forum/data/attachments/';
        $this->_aTablesWithFiles = array(
            'ch_groups_forum_attachments' => array( // table name
                'att_hash' => array ( // field name
                    '', // prefixes & extensions
                ),
            ),
        );

        if (ChWsbInstallerUtils::isModuleInstalled('wmap')) {
            $this->_aTables['ch_wmap_locations'] = array(
                'query' => "SELECT `t`.* FROM `ch_wmap_locations` AS `t` INNER JOIN `ch_groups_main` AS `m` ON (`m`.`id` = `t`.`id`) WHERE `m`.`author_id` = {profile_id} AND `part` = 'groups'");
        }
    }

    protected function _getFilePath($sTableName, $sField, $sFileName, $sPrefix, $sExt)
    {
        $s = $sFileName;
        return $this->_sFilesBaseDir . substr($s, 0, 1) . '/' . substr($s, 0, 2) . '/' . substr($s, 0, 3) . '/' . $s;
    }
}
