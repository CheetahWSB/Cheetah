ALTER TABLE `sys_messages` CHANGE `Text` `Text` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

-- Maint Mode and Two factor auth
INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES
('system', 'design_before_output', 1);


-- Maint mode
SET @iCatGeneral = 3;
INSERT INTO `sys_options` VALUES
('sys_maint_mode_enabled', '', @iCatGeneral, 'Enable Maintenance Mode', 'checkbox', '', '', 200, ''),
('sys_maint_mode_admin', '', @iCatGeneral, 'Allow admins to view site while in maintenance mode', 'checkbox', '', '', 210, ''),
('sys_maint_mode_msg', 'Sorry. Site is currently down for maintenance. Please check back later.', @iCatGeneral, 'Maintenance mode page block text', 'text', '', '', 220, '');

INSERT INTO `sys_page_compose_pages` VALUES ('site_maintenance', 'Site Maintenance', 33, 1);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, `Cache`) VALUES
('site_maintenance', '1140px', 'Site Maintenance', '_site_maintenance', 2, 1, 'BlockOne', '', 1, 28.1, 'non,memb', 0, 0);

-- 2FA
SET @iCatGeneral = 3;
INSERT INTO `sys_options` VALUES
('two_factor_auth', '', @iCatGeneral, 'Enable Two Factor Authentication', 'checkbox', '', '', 240, ''),
('two_factor_auth_required', '', @iCatGeneral, 'Require Two Factor Authentication', 'checkbox', '', '', 250, '');


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

-- Message Dialogs
INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Movable`, `Clonable`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(179, 'Dialogs', '_Dialog', 'mail.php?mode=dialog', 4, 'memb', '', '', '', 3, 1, 1, 1, 1, 'custom', '', '', 0, '');

UPDATE `sys_objects_actions` SET `Eval` = 'if (isAdmin({member_id}) && {member_id} != {ID}) return _t(\'_Login_As\');' WHERE `Url` = 'member.php?loginas=true&id={ID}';

-- last step is to update current version
UPDATE `sys_options` SET `VALUE` = '1.3.0' WHERE `Name` = 'sys_tmp_version';
