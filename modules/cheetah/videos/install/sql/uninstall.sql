DROP TABLE IF EXISTS `[db_prefix]_favorites`;
DROP TABLE IF EXISTS `[db_prefix]_cmts`;
DROP TABLE IF EXISTS `[db_prefix]_cmts_albums`;
DROP TABLE IF EXISTS `[db_prefix]_rating`;
DROP TABLE IF EXISTS `[db_prefix]_voting_track`;
DROP TABLE IF EXISTS `[db_prefix]_views_track`;

DELETE FROM `sys_albums_objects`, `sys_albums` USING `sys_albums_objects`, `sys_albums` WHERE `sys_albums_objects`.`id_album` = `sys_albums`.`ID` AND `sys_albums`.`Type` = 'ch_videos';
DELETE FROM `sys_albums` WHERE `Type` = 'ch_videos';

SET @iKatID = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Videos' LIMIT 1);

DELETE FROM `sys_options` WHERE `kateg` = @iKatID;

DELETE FROM `sys_options_cats` WHERE `ID` = @iKatID;

DELETE FROM `sys_page_compose_pages` WHERE `Name` LIKE '%ch_videos%';
DELETE FROM `sys_page_compose` WHERE `Caption` LIKE '%ch_videos%' OR `Page` LIKE 'ch_videos%';

SET @iTMParentId = (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Videos' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Name` IN('Videos', 'VideosUnit', 'VideosAlbum') OR `Parent` = @iTMParentId;

DELETE FROM `sys_menu_member` WHERE `Name` = 'ch_videos';

DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'ch_videos' LIMIT 1;

DELETE FROM `sys_permalinks` WHERE `check` = 'ch_videos_permalinks' LIMIT 1;

DELETE FROM `sys_options` WHERE `Name` = 'ch_videos_permalinks' LIMIT 1;

DELETE FROM `sys_objects_cmts` WHERE `ObjectName` LIKE 'ch_videos%';

DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'ch_videos';

DELETE FROM `sys_objects_views` WHERE `name` = 'ch_videos';

DELETE FROM `sys_categories` WHERE `Type` = 'ch_videos';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'ch_videos';

DELETE FROM `sys_tags` WHERE `Type` = 'ch_videos';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'ch_videos';

DELETE FROM `sys_email_templates` WHERE `Name` LIKE '%ch_videos%';

DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='ch_videos';
DELETE FROM `sys_sbs_types` WHERE `unit`='ch_videos';

DELETE FROM `sys_stat_member` WHERE `Type` = 'pvi';

DELETE FROM `sys_stat_site` WHERE `Name` = 'pvi';

DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_ch_videos';

DELETE FROM `sys_objects_actions` WHERE `Type` IN ('ch_videos', 'ch_videos_title', 'ch_videos_album');

DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` LIKE 'videos%';
DELETE FROM `sys_acl_actions` WHERE `Name` LIKE 'videos%';

DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'videos';

DELETE FROM `sys_menu_admin` WHERE `name` = 'ch_videos';

DELETE FROM `sys_cron_jobs` WHERE `name` = 'ch_videos';

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'ch_videos_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- mobile
DELETE FROM `sys_menu_mobile` WHERE `type` = '[db_prefix]';

-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = '[db_prefix]' OR `object` = '[db_prefix]_albums';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = '[db_prefix]';

-- export
DELETE FROM `sys_objects_exports` WHERE `object` = '[db_prefix]';
