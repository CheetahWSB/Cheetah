--
-- Table structure for table `[db_prefix]entries`
--

CREATE TABLE IF NOT EXISTS `[db_prefix]entries` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `author_id` int(11) unsigned NOT NULL default '0',
  `caption` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
  `snippet` text NOT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `when` int(11) NOT NULL default '0',
  `uri` varchar(100) NOT NULL default '',
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
  `categories` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
  `comment` tinyint(0) NOT NULL default '0',
  `vote` tinyint(0) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `featured` tinyint(4) NOT NULL default '0',
  `rate` int(11) NOT NULL default '0',
  `rate_count` int(11) NOT NULL default '0',
  `view_count` int(11) NOT NULL default '0',
  `cmts_count` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  FULLTEXT KEY `search_group` (`caption`, `content`, `tags`, `categories`),
  FULLTEXT KEY `search_caption` (`caption`),
  FULLTEXT KEY `search_content` (`content`),
  FULLTEXT KEY `search_tags` (`tags`),
  FULLTEXT KEY `search_categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]comments`
--

CREATE TABLE IF NOT EXISTS `[db_prefix]comments` (
  `cmt_id` int(11) NOT NULL auto_increment,
  `cmt_parent_id` int(11) NOT NULL default '0',
  `cmt_object_id` int(11) NOT NULL default '0',
  `cmt_author_id` int(10) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL,
  `cmt_mood` tinyint(4) NOT NULL default '0',
  `cmt_rate` int(11) NOT NULL default '0',
  `cmt_rate_count` int(11) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cmt_id`),
  KEY `cmt_object_id` (`cmt_object_id`,`cmt_parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]comments_track`
--

CREATE TABLE IF NOT EXISTS `[db_prefix]comments_track` (
  `cmt_system_id` int(11) NOT NULL default '0',
  `cmt_id` int(11) NOT NULL default '0',
  `cmt_rate` tinyint(4) NOT NULL default '0',
  `cmt_rate_author_id` int(10) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int(11) unsigned NOT NULL default '0',
  `cmt_rate_ts` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cmt_system_id`,`cmt_id`,`cmt_rate_author_nip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]voting`
--
CREATE TABLE `[db_prefix]voting` (
  `arl_id` bigint(8) NOT NULL default '0',
  `arl_rating_count` int(11) NOT NULL default '0',
  `arl_rating_sum` int(11) NOT NULL default '0',
  UNIQUE KEY `arl_id` (`arl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]voting_track`
--
CREATE TABLE `[db_prefix]voting_track` (
  `arl_id` bigint(8) NOT NULL default '0',
  `arl_ip` varchar(20) default NULL,
  `arl_date` datetime default NULL,
  KEY `arl_ip` (`arl_ip`,`arl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]views_track`
--
CREATE TABLE IF NOT EXISTS `[db_prefix]views_track` (
  `id` int(10) unsigned NOT NULL,
  `viewer` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `ts` int(10) unsigned NOT NULL,
  KEY `id` (`id`,`viewer`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


SET @iTMOrder = (SELECT MAX(`Order`) FROM `sys_menu_top` WHERE `Parent`='0');
INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(0, 'Articles', '_articles_top_menu_item', 'modules/?r=articles/index/|modules/?r=articles/', @iTMOrder+1, 'non,memb', '', '', '', 1, 1, 1, 'top', 'file', 0, '');

SET @iTMParentId = LAST_INSERT_ID( );
INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(@iTMParentId, 'ArticlesHome', '_articles_home_top_menu_sitem', 'modules/?r=articles/index/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesArchive', '_articles_archive_top_menu_sitem', 'modules/?r=articles/archive/', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesTop', '_articles_top_top_menu_sitem', 'modules/?r=articles/top/', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesPopular', '_articles_popular_top_menu_sitem', 'modules/?r=articles/popular/', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesFeatured', '_articles_featured_top_menu_sitem', 'modules/?r=articles/featured/', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesTags', '_articles_tags_top_menu_sitem', 'modules/?r=articles/tags/', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesCategories', '_articles_categories_top_menu_sitem', 'modules/?r=articles/categories/', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesCalendar', '_articles_calendar_top_menu_sitem', 'modules/?r=articles/calendar/', 7, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesSearch', '_articles_search_top_menu_sitem', 'searchKeyword.php?type=ch_articles', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(0, 'ArticlesView', '_articles_view_top_menu_sitem', 'modules/?r=articles/view/', 0, 'non,memb', '', '', '', 1, 1, 1, 'system', 'file', 0, '');

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, 'ch_articles', '_articles_admin_menu_sitem', '{siteUrl}modules/?r=articles/admin/', 'For managing articles', 'file', '', '', @iOrder+1);


INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES('modules/?r=articles/', 'm/articles/', 'permalinks_module_articles');


SET @iCategoryOrder = (SELECT MAX(`menu_order`) FROM `sys_options_cats`) + 1;
INSERT INTO `sys_options_cats` (`name` , `menu_order` ) VALUES ('Articles', @iCategoryOrder);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
('permalinks_module_articles', 'on', 26, 'Enable friendly articles permalink', 'checkbox', '', '', 0),
('category_auto_app_ch_articles', 'on', 0, 'Autoapprove for categories', 'checkbox', '', '', 0),
('articles_autoapprove', 'on', @iCategoryId, 'Publish articles automatically', 'checkbox', '', '', 1),
('articles_comments', 'on', @iCategoryId, 'Allow comments for articles', 'checkbox', '', '', 2),
('articles_votes', 'on', @iCategoryId, 'Allow votes for articles', 'checkbox', '', '', 3),
('articles_index_number', '10', @iCategoryId, 'The number of articles on home page', 'digit', '', '', 4),
('articles_member_number', '10', @iCategoryId, 'The number of articles on account page', 'digit', '', '', 5),
('articles_per_page', '10', @iCategoryId, 'The number of items shown on the page', 'digit', '', '', 6),
('articles_rss_length', '10', @iCategoryId, 'The number of items shown in the RSS feed', 'digit', '', '', 7);

INSERT INTO `sys_objects_cmts` (`ObjectName`, `TableCmts`, `TableTrack`, `AllowTags`, `Nl2br`, `SecToEdit`, `PerView`, `IsRatable`, `ViewingThreshold`, `AnimationEffect`, `AnimationSpeed`, `IsOn`, `IsMood`, `RootStylePrefix`, `TriggerTable`, `TriggerFieldId`, `TriggerFieldComments`, `ClassName`, `ClassFile`) VALUES
('ch_articles', '[db_prefix]comments', '[db_prefix]comments_track', 0, 1, 90, 10, 1, -3, 'none', 0, 1, 0, 'cmt', '[db_prefix]entries', 'id', 'cmts_count', 'ChArlCmts', 'modules/cheetah/articles/classes/ChArlCmts.php');

INSERT INTO `sys_objects_vote` (`ObjectName`, `TableRating`, `TableTrack`, `RowPrefix`, `MaxVotes`, `PostName`, `IsDuplicate`, `IsOn`, `className`, `classFile`, `TriggerTable`, `TriggerFieldRate`, `TriggerFieldRateCount`, `TriggerFieldId`, `OverrideClassName`, `OverrideClassFile`) VALUES
('ch_articles', '[db_prefix]voting', '[db_prefix]voting_track', 'arl_', 5, 'vote_send_result', 'CH_PERIOD_PER_VOTE', 1, '', '', '[db_prefix]entries', 'rate', 'rate_count', 'id', 'ChArlVoting', 'modules/cheetah/articles/classes/ChArlVoting.php');

INSERT INTO `sys_objects_tag` (`ObjectName`, `Query`, `PermalinkParam`, `EnabledPermalink`, `DisabledPermalink`, `LangKey`) VALUES
('ch_articles', 'SELECT `tags` FROM `[db_prefix]entries` WHERE `id`={iID} AND `status`=0', 'permalinks_module_articles', 'm/articles/tag/{tag}', 'modules/?r=articles/tag/{tag}', '_articles_lcaption_tags');

INSERT INTO `sys_objects_categories` (`ObjectName`, `Query`, `PermalinkParam`, `EnabledPermalink`, `DisabledPermalink`, `LangKey`)
VALUES ('ch_articles', 'SELECT `categories` FROM `[db_prefix]entries` WHERE `id`=''{iID}'' AND `status`=''0''', 'permalinks_module_articles', 'm/articles/category/{tag}', 'modules/?r=articles/category/{tag}', '_articles_lcaption_categories');

INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES
('Default', '0', 'ch_articles', '0', 'active'),
('Cheetah Products', '0', 'ch_articles', '0', 'active'),
('Some Useful Info', '0', 'ch_articles', '0', 'active');

INSERT INTO `sys_objects_search` (`ObjectName`, `Title`, `ClassName`, `ClassPath`) VALUES
('ch_articles', '_articles_lcaption_search_object', 'ChArlSearchResult', 'modules/cheetah/articles/classes/ChArlSearchResult.php');

INSERT INTO `sys_objects_views`(`name`, `table_track`, `period`, `trigger_table`, `trigger_field_id`, `trigger_field_views`, `is_on`) VALUES
('ch_articles', '[db_prefix]views_track', 86400, '[db_prefix]entries', 'id', 'view_count', 1);


SET @iPCPOrder = (SELECT MAX(`Order`) FROM `sys_page_compose_pages`);
INSERT INTO `sys_page_compose_pages`(`Name`, `Title`, `Order`) VALUES ('articles_single', 'Articles View Article', @iPCPOrder+1);

SET @iPCPOrder = (SELECT MAX(`Order`) FROM `sys_page_compose_pages`);
INSERT INTO `sys_page_compose_pages`(`Name`, `Title`, `Order`) VALUES ('articles_home', 'Articles Home', @iPCPOrder+1);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('index', '1140px', 'Show list of featured articles', '_articles_bcaption_featured', 0, 0, 'PHP', 'return ChWsbService::call(\'articles\', \'featured_block_index\', array(0, 0, false));', 1, 71.9, 'non,memb', 0),
('index', '1140px', 'Show list of latest articles', '_articles_bcaption_latest', 0, 0, 'PHP', 'return ChWsbService::call(\'articles\', \'archive_block_index\', array(0, 0, false));', 1, 71.9, 'non,memb', 0),
('member', '1140px', 'Show list of featured articles', '_articles_bcaption_featured', 0, 0, 'PHP', 'return ChWsbService::call(\'articles\', \'featured_block_member\', array(0, 0, false));', 1, 71.9, 'memb', 0),
('member', '1140px', 'Show list of latest articles', '_articles_bcaption_latest', 0, 0, 'PHP', 'return ChWsbService::call(\'articles\', \'archive_block_member\', array(0, 0, false));', 1, 71.9, 'memb', 0),
('articles_single', '1140px', 'Articles main content', '_articles_bcaption_view_main', 1, 0, 'Content', '', 1, 71.9, 'non,memb', 0),
('articles_single', '1140px', 'Articles comments', '_articles_bcaption_view_comment', 1, 1, 'Comment', '', 1, 71.9, 'non,memb', 0),
('articles_single', '1140px', 'Articles info', '_articles_bcaption_view_info', 2, 0, 'Info', '', 1, 28.1, 'non,memb', 0),
('articles_single', '1140px', 'Articles actions', '_articles_bcaption_view_action', 2, 1, 'Action', '', 1, 28.1, 'non,memb', 0),
('articles_single', '1140px', 'Articles rating', '_articles_bcaption_view_vote', 2, 2, 'Vote', '', 1, 28.1, 'non,memb', 0),
('articles_single', '1140px', 'Social sharing', '_sys_block_title_social_sharing', 2, 3, 'SocialSharing', '', 1, 28.1, 'non,memb', 0),
('articles_home', '1140px', 'Articles featured', '_articles_bcaption_featured', 0, 0, 'Featured', '', 1, 71.9, 'non,memb', 0),
('articles_home', '1140px', 'Articles latest', '_articles_bcaption_latest', 1, 1, 'Latest', '', 1, 71.9, 'non,memb', 0),
('articles_home', '1140px', 'Articles calendar', '_articles_bcaption_calendar', 0, 0, 'Calendar', '', 1, 28.1, 'non,memb', 0),
('articles_home', '1140px', 'Articles categories', '_articles_bcaption_categories', 2, 1, 'Categories', '', 1, 28.1, 'non,memb', 0),
('articles_home', '1140px', 'Articles tags', '_articles_bcaption_tags', 2, 2, 'Tags', '', 1, 28.1, 'non,memb', 0);

INSERT INTO `sys_objects_actions`(`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES
('{sbs_articles_title}', 'paperclip', '', '{sbs_articles_script}', '', 1, 'ch_articles', 0),
('{del_articles_title}', 'remove', '', '{del_articles_script}', '', 2, 'ch_articles', 0),
('{share_articles_title}', 'share-square-o', '', '{share_articles_script}', '', 3, 'ch_articles', 0);

INSERT INTO `sys_sbs_types`(`unit`, `action`, `template`, `params`) VALUES
('ch_articles', '', '', 'return ChWsbService::call(\'articles\', \'get_subscription_params\', array($arg1, $arg2, $arg3));'),
('ch_articles', 'commentPost', 't_sbsArticlesComments', 'return ChWsbService::call(\'articles\', \'get_subscription_params\', array($arg1, $arg2, $arg3));');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('t_sbsArticlesComments', 'New Comments To An Article', '<ch_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RealName></b>,</p>\r\n\r\n<p>The <a href="<ViewLink>">article you subscribed to got new comments</a>!</p>\r\n\r\n<ch_include_auto:_email_footer.html />', 'Subscription: new comments to article', 0);

INSERT INTO `sys_acl_actions`(`Name`, `AdditionalParamName`) VALUES ('Articles Delete', '');

INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
('ch_articles', '*/5 * * * *', 'ChArlCron', 'modules/cheetah/articles/classes/ChArlCron.php', '');

-- mobile
SET @iMaxOrderHomepage = (SELECT MAX(`order`)+1 FROM `sys_menu_mobile` WHERE `page` = 'homepage');
INSERT INTO `sys_menu_mobile` (`type`, `page`, `title`, `icon`, `action`, `action_data`, `eval_bubble`, `eval_hidden`, `order`, `active`) VALUES
('ch_articles', 'homepage', '_articles_bcaption_all', '{site_url}modules/cheetah/articles/templates/base/images/icons/mobile_icon.png', 100, '{xmlrpc_url}r.php?url=modules%2F%3Fr%3Darticles%2Fmobile_latest_entries%2F&user={member_username}&pwd={member_password}', '', '', @iMaxOrderHomepage, 1);

-- site stats
SET @iStatSiteOrder := (SELECT `StatOrder` + 1 FROM `sys_stat_site` WHERE 1 ORDER BY `StatOrder` DESC LIMIT 1);
INSERT INTO `sys_stat_site` VALUES(NULL, 'arl', 'articles_ss', 'modules/?r=articles/archive/', 'SELECT COUNT(`ID`) FROM `[db_prefix]entries` WHERE `status`=''0''', 'modules/?r=articles/admin/', 'SELECT COUNT(`ID`) FROM `[db_prefix]entries` WHERE `status`=''1''', 'file', @iStatSiteOrder);

-- sitemap
SET @iMaxOrderSiteMaps = (SELECT MAX(`order`)+1 FROM `sys_objects_site_maps`);
INSERT INTO `sys_objects_site_maps` (`object`, `title`, `priority`, `changefreq`, `class_name`, `class_file`, `order`, `active`) VALUES
('ch_articles', '_articles_sitemap', '0.8', 'auto', 'ChArlSiteMaps', 'modules/cheetah/articles/classes/ChArlSiteMaps.php', @iMaxOrderSiteMaps, 1);

-- chart
SET @iMaxOrderCharts = (SELECT MAX(`order`)+1 FROM `sys_objects_charts`);
INSERT INTO `sys_objects_charts` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `query`, `active`, `order`) VALUES
('ch_articles', '_articles_chart', 'ch_arl_entries', 'when', '', '', 1, @iMaxOrderCharts);

-- export
SET @iMaxOrderExports = (SELECT MAX(`order`)+1 FROM `sys_objects_exports`);
INSERT INTO `sys_objects_exports` (`object`, `title`, `class_name`, `class_file`, `order`, `active`) VALUES
('ch_articles', '_articles', 'ChArlExport', 'modules/cheetah/articles/classes/ChArlExport.php', @iMaxOrderExports, 1);
