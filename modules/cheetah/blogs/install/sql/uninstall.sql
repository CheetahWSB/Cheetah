-- tables
DROP TABLE IF EXISTS `[db_prefix]_posts`, `[db_prefix]_rating`, `[db_prefix]_voting_track`, `[db_prefix]_main`, `[db_prefix]_cmts`, `[db_prefix]_views_track`;

-- PQ statistics
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_ch_blog_Blog';

-- settings
SET @iCategoryID := (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Blogs' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategoryID;
DELETE FROM `sys_options` WHERE `kateg` = @iCategoryID;
DELETE FROM `sys_options` WHERE `Name` = 'permalinks_blogs';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'Blogs';

-- categories
DELETE FROM `sys_categories` WHERE `Type` = 'ch_blogs';

-- category objects
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'ch_blogs';

-- comments objects
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'ch_blogs';

-- page compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN ('ch_blogs', 'ch_blogs_home');

DELETE FROM `sys_page_compose` WHERE `Page`='index' AND `Desc`='Recently posted blogs' AND `Caption`='_ch_blog_Blogs' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Page`='index' AND `Desc`='Blogs calendar' AND `Caption`='_ch_blog_Calendar' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Page`='profile' AND `Desc`='Member blog block' AND `Caption`='_ch_blog_Blog' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Page` IN ('ch_blogs', 'ch_blogs_home');

-- PQ statistics
DELETE FROM `sys_stat_member` WHERE `Type`='mbp';
DELETE FROM `sys_stat_member` WHERE `Type`='mbpc';

-- site stats
DELETE FROM `sys_stat_site` WHERE `Name`='blg';

-- search objects
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'blog';

-- tag objects
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'blog';
DELETE FROM `sys_tags` WHERE `Type` = 'blog';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Blogs' LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Profile Blog';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Profile Blog';
DELETE FROM `sys_menu_top` WHERE `Parent` = 0 AND `Name` = 'Blog Post';

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'ch_blogs';

-- vote objects
DELETE FROM `sys_objects_vote` WHERE `ObjectName`='ch_blogs';

-- permalinks
DELETE FROM `sys_permalinks` WHERE `check` = 'permalinks_blogs';

-- Alerts Handler and Events
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'ch_blogs_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- views objects
DELETE FROM `sys_objects_views` WHERE `name` = 'ch_blogs';

-- Membership
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('blog view', 'blog post view', 'blogs browse', 'blogs posts browse', 'blog post search', 'blog post add', 'blog posts edit any post', 'blog posts delete any post', 'blog posts approving', 'blog posts comments delete and edit');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('blog view', 'blog post view', 'blogs browse', 'blogs posts browse', 'blog post search', 'blog post add', 'blog posts edit any post', 'blog posts delete any post', 'blog posts approving', 'blog posts comments delete and edit');

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri`='blogs';

-- actions
DELETE FROM `sys_objects_actions` WHERE `Type` = 'ch_blogs';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'ch_blogs_m';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id` = `sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit` = 'ch_blogs';
DELETE FROM `sys_sbs_types` WHERE `unit` = 'ch_blogs';

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('t_sbsBlogpostsComments');

-- mobile
DELETE FROM `sys_menu_mobile` WHERE `type` = 'ch_blogs';

-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'ch_blogs';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'ch_blogs';

-- export
DELETE FROM `sys_objects_exports` WHERE `object` = 'ch_blogs';
