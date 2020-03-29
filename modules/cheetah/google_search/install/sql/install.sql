
-- page compose pages
SET @iMaxOrder = (SELECT `Order` + 1 FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('ch_gsearch', 'Search Google', @iMaxOrder);

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
    ('ch_gsearch', '1140px', 'Search Form', '_ch_gsearch_box_title_search_form', '1', '0', 'SearchForm', '', '1', '28.1', 'non,memb', '0'),
    ('ch_gsearch', '1140px', 'Search Results', '_ch_gsearch_box_title_search_results', '2', '0', 'SearchResults', '', '1', '71.9', 'non,memb', '0'),
    ('search_home', '1140px', 'Google Search', '_ch_gsearch_box_title', 0, 0, 'PHP', 'return ChWsbService::call(''google_search'', ''get_search_control'', array());', 1, 71.9, 'non,memb', 0);

-- permalinks
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=google_search/', 'm/google_search/', 'ch_gsearch_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Google Search', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('ch_gsearch_permalinks', 'on', 26, 'Enable friendly permalinks in Google Site Search', 'checkbox', '', '', '0', ''),
('ch_gsearch_id', '', @iCategId, 'Search engine ID', 'digit', '', '', '10', '');


-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'ch_gsearch', '_ch_gsearch', '{siteUrl}modules/?r=google_search/administration/', 'Google Site Search module by Cheetah', 'google-plus', @iMax+1);

-- top menu
SET @iCatOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 138 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 138, 'Google Search', '_ch_gsearch_menu_title', 'modules/?r=google_search/', IFNULL(@iCatOrder, 0), 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
