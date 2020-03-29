-- tables
DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]images`;
DROP TABLE IF EXISTS `[db_prefix]videos`;
DROP TABLE IF EXISTS `[db_prefix]sounds`;
DROP TABLE IF EXISTS `[db_prefix]files`;
DROP TABLE IF EXISTS `[db_prefix]participants`;
DROP TABLE IF EXISTS `[db_prefix]admins`;
DROP TABLE IF EXISTS `[db_prefix]rating`;
DROP TABLE IF EXISTS `[db_prefix]rating_track`;
DROP TABLE IF EXISTS `[db_prefix]cmts`;
DROP TABLE IF EXISTS `[db_prefix]cmts_track`;
DROP TABLE IF EXISTS `[db_prefix]views_track`;
DROP TABLE IF EXISTS `[db_prefix]shoutbox`;

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
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('ch_events_view', 'ch_events_celendar', 'ch_events_main', 'ch_events_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN('ch_events_view', 'ch_events_celendar', 'ch_events_main', 'ch_events_my');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Desc` = 'Events';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'Joined Events';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'User Events';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'Joined Events';

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=events/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'ch_events';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'ch_events';
DELETE FROM `sys_objects_views` WHERE `name` = 'ch_events';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'ch_events';
DELETE FROM `sys_categories` WHERE `Type` = 'ch_events';
DELETE FROM `sys_categories` WHERE `Type` = 'ch_photos' AND `Category` = 'Events';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'ch_events';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'ch_events';
DELETE FROM `sys_tags` WHERE `Type` = 'ch_events';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'ch_events' OR `Type` = 'ch_events_title';
DELETE FROM `sys_stat_site` WHERE `Name` = 'evs';
DELETE FROM `sys_stat_member` WHERE TYPE IN('ch_events', 'ch_eventsp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_ch_events';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Events' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Events' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Events';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Events';

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'ch_events';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'ch_events';

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` = 'ch_events_invitation' OR `Name` = 'ch_events_broadcast' OR `Name` = 'ch_events_sbs' OR `Name` = 'ch_events_join_request' OR `Name` = 'ch_events_join_reject' OR `Name` = 'ch_events_join_confirm' OR `Name` = 'ch_events_fan_remove' OR `Name` = 'ch_events_fan_become_admin' OR `Name` = 'ch_events_admin_become_fan';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Events' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'ch_events_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('events view', 'events browse', 'events search', 'events add', 'events comments delete and edit', 'events edit any event', 'events delete any event', 'events mark as featured', 'events approve', 'events broadcast message');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('events view', 'events browse', 'events search', 'events add', 'events comments delete and edit', 'events edit any event', 'events delete any event', 'events mark as featured', 'events approve', 'events broadcast message');

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'ch_events_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'ch_events_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'ch_events_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'ch_events_set_param' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'events';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='ch_events';
DELETE FROM `sys_sbs_types` WHERE `unit`='ch_events';

-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'ch_events';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'ch_events';

-- export
DELETE FROM `sys_objects_exports` WHERE `object` = 'ch_events';

