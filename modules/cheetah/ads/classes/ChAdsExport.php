<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');
ch_import('ChWsbInstallerUtils');

class ChAdsExport extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'ch_ads_cmts' => '`cmt_author_id` = {profile_id}',
            'ch_ads_main' => '`IDProfile` = {profile_id}',
            'ch_ads_main_media' => array(
                'query' => "SELECT `f`.* FROM `ch_ads_main_media` AS `f` INNER JOIN `ch_ads_main` AS `m` ON (`m`.`Media` = `f`.`MediaID`) WHERE `m`.`IDProfile` = {profile_id}"),
            'ch_ads_rating' => array(
                'query' => "SELECT `r`.* FROM `ch_ads_rating` AS `r` INNER JOIN `ch_ads_main` AS `m` ON (`m`.`ID` = `r`.`ads_id`) WHERE `m`.`IDProfile` = {profile_id}"),
            'ch_ads_views_track' => array(
                'query' => "SELECT `t`.`id`, IF(`t`.`viewer` = {profile_id}, `t`.`viewer`, 0), IF(`t`.`viewer` = {profile_id}, `t`.`ip`, 0), `t`.`ts` FROM `ch_ads_views_track` AS `t` INNER JOIN `ch_ads_main` AS `m` ON (`m`.`ID` = `t`.`id`) WHERE `m`.`IDProfile` = {profile_id} OR `t`.`viewer` = {profile_id}"), // anonymize some data
            'ch_ads_voting_track' => array(
                'query' => "SELECT `t`.`ads_id`, 0, `t`.`ads_date` FROM `ch_ads_voting_track` AS `t` INNER JOIN `ch_ads_main` AS `m` ON (`m`.`ID` = `t`.`ads_id`) WHERE `m`.`IDProfile` = {profile_id}"), // anonymize some data
        );
        $this->_sFilesBaseDir = 'media/images/classifieds/';
        $this->_aTablesWithFiles = array(
            'ch_ads_main_media' => array( // table name
                'MediaFile' => array ( // field name
                    // prefixes & extensions
                    'big_thumb_' => '',
                    'icon_' => '',
                    'img_' => '',
                    'thumb_' => ''),
            ),
        );

        if (ChWsbInstallerUtils::isModuleInstalled('wmap')) {
            $this->_aTables['ch_wmap_locations'] = array(
                'query' => "SELECT `t`.* FROM `ch_wmap_locations` AS `t` INNER JOIN `ch_ads_main` AS `m` ON (`m`.`ID` = `t`.`id`) WHERE `m`.`IDProfile` = {profile_id} AND `part` = 'ads'");
        }
    }
}
