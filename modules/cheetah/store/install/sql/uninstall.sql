
-- tables
DROP TABLE IF EXISTS `[db_prefix]products`;
DROP TABLE IF EXISTS `[db_prefix]customers`;
DROP TABLE IF EXISTS `[db_prefix]product_images`;
DROP TABLE IF EXISTS `[db_prefix]product_videos`;
DROP TABLE IF EXISTS `[db_prefix]product_files`;
DROP TABLE IF EXISTS `[db_prefix]rating`;
DROP TABLE IF EXISTS `[db_prefix]rating_track`;
DROP TABLE IF EXISTS `[db_prefix]cmts`;
DROP TABLE IF EXISTS `[db_prefix]cmts_track`;
DROP TABLE IF EXISTS `[db_prefix]views_track`;

-- forum tables
DROP TABLE IF EXISTS `[db_prefix]forum`;
DROP TABLE IF EXISTS `[db_prefix]forum_cat`;
DROP TABLE IF EXISTS `[db_prefix]forum_cat`;
DROP TABLE IF EXISTS `[db_prefix]forum_flag`;
DROP TABLE IF EXISTS `[db_prefix]forum_post`;
DROP TABLE IF EXISTS `[db_prefix]forum_topic`;
DROP TABLE IF EXISTS `[db_prefix]forum_user`;
DROP TABLE IF EXISTS `[db_prefix]forum_user_activity`;
DROP TABLE IF EXISTS `[db_prefix]forum_user_stat`;
DROP TABLE IF EXISTS `[db_prefix]forum_vote`;
DROP TABLE IF EXISTS `[db_prefix]forum_actions_log`;
DROP TABLE IF EXISTS `[db_prefix]forum_attachments`;
DROP TABLE IF EXISTS `[db_prefix]forum_signatures`;

-- compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('ch_store_view', 'ch_store_celendar', 'ch_store_main', 'ch_store_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN('ch_store_view', 'ch_store_celendar', 'ch_store_main', 'ch_store_my');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Desc` = 'Store';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'User Store';

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=store/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'ch_store';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'ch_store';
DELETE FROM `sys_objects_views` WHERE `name` = 'ch_store';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'ch_store';
DELETE FROM `sys_categories` WHERE `Type` = 'ch_store';
DELETE FROM `sys_categories` WHERE `Type` = 'ch_photos' AND `Category` = 'Store';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'ch_store';
DELETE FROM `sys_tags` WHERE `Type` = 'ch_store';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'ch_store';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'ch_store' OR `Type` = 'ch_store_title';
DELETE FROM `sys_stat_site` WHERE `Name` = 'ch_store';
DELETE FROM `sys_stat_member` WHERE TYPE IN('ch_store', 'ch_storep');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_ch_store';

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` = 'ch_store_broadcast' OR `Name` = 'ch_store_sbs';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Store' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Store' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Store';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Store';

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'ch_store';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'ch_store';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Store' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'ch_store_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('store view product', 'store browse', 'store search', 'store add product', 'store product comments delete and edit', 'store edit any product', 'store delete any product', 'store mark as featured', 'store approve product', 'store broadcast message');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('store view product', 'store browse', 'store search', 'store add product', 'store product comments delete and edit', 'store edit any product', 'store delete any product', 'store mark as featured', 'store approve product', 'store broadcast message');

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'ch_store_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'ch_store_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'store';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='ch_store';
DELETE FROM `sys_sbs_types` WHERE `unit`='ch_store';

-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'ch_store';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'ch_store';

-- export
DELETE FROM `sys_objects_exports` WHERE `object` = 'ch_store';

