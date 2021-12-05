
-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Cheetah - Dolphin Importer', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('cheetah_dolphin_importer_permalinks', 'on', 26, 'Enable friendly permalinks in Dolphin Importer', 'checkbox', '', '', '0', ''),
('cheetah_dolphin_importer_date_format', 'Y-m-d H:i', @iCategId, 'Format for server date/time', 'digit', '', '', '1', ''),
('cheetah_dolphin_importer_enable_js_date', 'on', @iCategId, 'Show user time', 'checkbox', '', '', '2', '');

-- permalinks
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=dolphin_importer/', 'm/dolphin_importer/', 'cheetah_dolphin_importer_permalinks');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'Dolphin Importer', '_cheetah_dolphin_importer', '{siteUrl}modules/?r=dolphin_importer/administration/', 'Dolphin Importer by Cheetah', 'database', @iMax+1);
