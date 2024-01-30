ALTER TABLE `sys_messages` CHANGE `Text` `Text` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

INSERT INTO `sys_options_cats` (`ID`, `name`, `menu_order`) VALUES
(19, 'Other Settings', 19);

UPDATE `sys_options` SET `kateg` = 19, `order_in_kateg` = 1 WHERE `name` = 'sys_php_block_enabled';

-- Maint Mode and Two factor auth
INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES
('system', 'design_before_output', 1);


-- Maint mode
SET @iCatExtra = 19;
INSERT INTO `sys_options` VALUES
('sys_maint_mode_enabled', '', @iCatExtra, 'Enable Maintenance Mode', 'checkbox', '', '', 2, ''),
('sys_maint_mode_admin', '', @iCatExtra, 'Allow admins to view site while in maintenance mode', 'checkbox', '', '', 3, ''),
('sys_maint_mode_msg', 'Sorry. Site is currently down for maintenance. Please check back later.', @iCatExtra, 'Maintenance mode page block text', 'text', '', '', 4, '');

INSERT INTO `sys_page_compose_pages` VALUES ('site_maintenance', 'Site Maintenance', 33, 1);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, `Cache`) VALUES
('site_maintenance', '1140px', 'Site Maintenance', '_site_maintenance', 1, 0, 'BlockOne', '', 1, 100, 'non,memb', 0, 0);

-- 2FA
SET @iCatExtra = 19;
INSERT INTO `sys_options` VALUES
('two_factor_auth', '', @iCatExtra, 'Enable Two Factor Authentication', 'checkbox', '', '', 5, ''),
('two_factor_auth_required', '', @iCatExtra, 'Require Two Factor Authentication', 'checkbox', '', '', 6, '');


CREATE TABLE IF NOT EXISTS `sys_2fa_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `memberid` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `secretkey` varchar(32) NOT NULL,
  `backupkeys` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `memberid` (`memberid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`, `System`) VALUES
('two_factor_auth', 'Two Factor Auth', 33, 1);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('two_factor_auth', '1140px', 'Two Factor Auth Get Code Block', '_two_factor_auth', 1, 0, 'GetCode', '', 0, 100, 'non,memb', 0);


INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Movable`, `Clonable`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(118, 'TwoFactorAuth', '_two_factor_auth_short', 'two_factor_auth.php?mode=status|two_factor_auth.php?mode=setup|two_factor_auth.php?mode=sbcodes', 11, 'memb', '', '', 'if (getParam(\'two_factor_auth\')) return true;', 3, 1, 1, 1, 1, 'custom', 'key', 'key', 1, '');


-- Forum Subscribe
SET @iCatExtra = 19;
INSERT INTO `sys_options` VALUES
('auto_subscribe_forum', '', @iCatExtra, 'Auto subscribe members to their own forum topics', 'checkbox', '', '', 7, '');

INSERT INTO `sys_alerts_handlers` (`name`, `class`, `file`) VALUES
('ch_forum', 'ChForumAlertResponse', 'modules/cheetah/forum/alert_response.php');
SET @iHandlerId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES
('ch_forum', 'new_topic', @iHandlerId);


-- Message Dialogs
INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Movable`, `Clonable`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(179, 'Dialogs', '_Dialog', 'mail.php?mode=dialog', 4, 'memb', '', '', '', 3, 1, 1, 1, 1, 'custom', '', '', 0, '');

UPDATE `sys_objects_actions` SET `Eval` = 'if (isAdmin({member_id}) && {member_id} != {ID}) return _t(\'_Login_As\');' WHERE `Url` = 'member.php?loginas=true&id={ID}';

-- Remove download block. ISSUE #221 Only removed if not currently in use.
DELETE FROM `sys_page_compose` WHERE `Caption` = '_sys_box_title_download' AND `Column` = 0;
-- Not going to remove its table during upgrade just to be safe.
-- DROP TABLE IF EXISTS `sys_box_download`;

-- last step is to update current version
UPDATE `sys_options` SET `VALUE` = '1.3.0' WHERE `Name` = 'sys_tmp_version';
