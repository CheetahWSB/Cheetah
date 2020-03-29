DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]rating`;
DROP TABLE IF EXISTS `[db_prefix]rating_track`;
DROP TABLE IF EXISTS `[db_prefix]cmts`;
DROP TABLE IF EXISTS `[db_prefix]cmts_track`;
DROP TABLE IF EXISTS `[db_prefix]views_track`;

-- top menu
SET @iTMParentId = (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Sites' AND `Parent` = 0 AND `Link` = 'modules/?r=sites/home/|modules/?r=sites/' LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iTMParentId;
DELETE FROM `sys_menu_top` WHERE `Parent` = 0 AND `Name` = 'Sites' AND `Caption` = '_ch_sites';
DELETE FROM `sys_menu_top` WHERE (`Parent` = 4 OR `Parent` = 9) AND `Name` = 'Sites';

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'ch_sites';

-- permalinks
DELETE FROM `sys_permalinks` WHERE `check`='ch_sites_permalinks';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sites');
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'ch_sites_permalinks';

DELETE FROM `sys_page_compose_pages` WHERE `Name` like 'ch_sites_%';
DELETE FROM `sys_page_compose` WHERE `Page` like '%ch_sites%' OR `Caption` like '%ch_sites%';

DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'ch_sites';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'ch_sites';
DELETE FROM `sys_objects_views` WHERE `name` = 'ch_sites';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'ch_sites';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'ch_sites';
DELETE FROM `sys_categories` WHERE `Type` = 'ch_sites';
DELETE FROM `sys_categories` WHERE `Type` = 'ch_photos' AND `Category` = 'Sites';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'ch_sites';
DELETE FROM `sys_tags` WHERE `Type` = 'ch_sites';
DELETE FROM `sys_objects_actions` WHERE `Type` like 'ch_sites%';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('sites view', 'sites browse', 'sites search', 'sites delete', 'sites edit any site', 'sites delete any site', 'sites mark as featured', 'sites approve');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('sites view', 'sites browse', 'sites search', 'sites add', 'sites edit any site', 'sites delete any site', 'sites mark as featured', 'sites approve');

-- site stats
DELETE FROM `sys_stat_site` WHERE `Name` = 'sts';

-- PQ statistics
DELETE FROM `sys_stat_member` WHERE TYPE IN('ch_sites', 'ch_sitesp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_ch_sites';

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'ch_sites' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler OR `unit` = 'ch_sites';
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'ch_sites';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri`='ch_sites';

-- subscriptions
DELETE FROM `sys_email_templates` WHERE `Name` = 't_sbsSitesComments';
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='ch_sites';
DELETE FROM `sys_sbs_types` WHERE `unit`='ch_sites';

-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'ch_sites';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'ch_sites';

-- export
DELETE FROM `sys_objects_exports` WHERE `object` = 'ch_sites';


-- begin stw integration
-- stw requests
DROP TABLE IF EXISTS `[db_prefix]stw_requests`;

-- stw account info
DROP TABLE IF EXISTS `[db_prefix]stwacc_info`;
-- end stw integration
