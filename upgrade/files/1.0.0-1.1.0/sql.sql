CREATE TABLE IF NOT EXISTS `sys_custom_code_blocks` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Eval` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `sys_sa_tokens` (
  `token` varchar(40) NOT NULL,
  `memid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DELETE FROM `sys_menu_admin` WHERE `name` = 'mobile_pages';
DELETE FROM `sys_objects_actions` WHERE `Caption` = '{cpt_get_mail}';
DELETE FROM `sys_options` WHERE `Name` = 'anon_mode';
INSERT INTO `sys_options` VALUES ('enable_browser_check', '', 3, 'Enable obsolete browser check', 'checkbox', '', '', 111, '');
INSERT INTO `sys_injections` (`name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES ('browser_check', 0, 'injection_footer', 'php', 'if(getParam(\'enable_browser_check\') && $_GET[\'enable_browser_check\'] != \'false\') { $sCode = \'<script>var $buoop={required:{e:-4,f:-3,o:-3,s:-1,c:-3},insecure:!0,api:2020.09};function $buo_f(){var e=document.createElement("script");e.src="//browser-update.org/update.min.js",document.body.appendChild(e)}try{document.addEventListener("DOMContentLoaded",$buo_f,!1)}catch(e){window.attachEvent("onload",$buo_f)}</script>\'; echo $sCode; }', 0, 1);

UPDATE `sys_page_compose` SET `Desc` = 'Raw HTML Block', `Caption` = '_RAW_Html_Block' WHERE `Func` = 'Sample' AND `Content` = 'Text';
INSERT INTO `sys_page_compose` VALUES (NULL, '', '1140px', 'Simple Text Block', '_Text Block', 0, 0, 'Sample', 'TrueText', 11, 0, 'non,memb', 0, 0);
ALTER TABLE `sys_page_compose` CHANGE `Content` `Content` MEDIUMTEXT NOT NULL;

ALTER TABLE `Profiles` ADD `DateLastPage` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `DateLastNav`;
ALTER TABLE `Profiles` ADD `CurrentPageTitle` varchar(255) NOT NULL AFTER `DateLastPage`;
ALTER TABLE `Profiles` ADD `ExtendedRole` text NOT NULL AFTER `Role`;

-- CAT: FFmpeg
SET @iCatFFmpeg = 17;
INSERT INTO `sys_options` VALUES
('usex264', 'on', @iCatFFmpeg, 'Use H264 codec', 'checkbox', '', '', 10, ''),
('video_player_height', '400', @iCatFFmpeg, 'Video Player Height(px) Min 330 - Max 600', 'digit', '', '', 20, ''),
('videoListSource', 'Top', @iCatFFmpeg, 'Videos List Source', 'select', '', '', 30, 'Top,Related,Member'),
('audioListSource', 'Top', @iCatFFmpeg, 'Music List Source', 'select', '', '', 40, 'Top,Related,Member'),
('videoListCount', '10', @iCatFFmpeg, 'Maximum number of Video files to List. 1-30', 'digit', '', '', 50, ''),
('audioListCount', '10', @iCatFFmpeg, 'Maximum number of Music files to List. 1-30', 'digit', '', '', 60, ''),
('enable_download', 'on', @iCatFFmpeg, 'Enable Downloading', 'checkbox', '', '', 70, ''),
('saveMobile', 'on', @iCatFFmpeg, 'Enable mobile video files playing', 'checkbox', '', '', 80, ''),
('videoAutoApprove', 'on', @iCatFFmpeg, 'Auto Approve Video Files', 'checkbox', '', '', 90, ''),
('audioAutoApprove', 'on', @iCatFFmpeg, 'Auto Approve Music Files', 'checkbox', '', '', 100, ''),
('auto_play', '', @iCatFFmpeg, 'Enable Autoplay', 'checkbox', '', '', 110, ''),
('processCount', '2', @iCatFFmpeg, 'Max files to process', 'digit', '', '', 120, ''),
('failedTimeout', '1', @iCatFFmpeg, 'Failure timeout for converting files(days)', 'digit', '', '', 130, ''),
('autohide_controls', 'on', @iCatFFmpeg, 'Enable autohide of controls', 'checkbox', '', '', 140, ''),
('videoBitrate', '3000', @iCatFFmpeg, 'Video conversion bitrate', 'digit', '', '', 150, ''),
('audioBitrate', '128', @iCatFFmpeg, 'Audio conversion bitrate', 'digit', '', '', 160, ''),
('video_recording_quality', '100', @iCatFFmpeg, 'Video Recording Quality(0-100)', 'digit', '', '', 170, ''),
('video_recording_fps', '30', @iCatFFmpeg, 'Video Recording FPS(10-60)', 'digit', '', '', 180, ''),
('microphone_rate', '44', @iCatFFmpeg, 'Microphone Rate kHz', 'digit', '', '', 190, ''),
('max_recording_time', '60', @iCatFFmpeg, 'Maximum Recording Time(secs)', 'digit', '', '', 200, '');

-- CAT: Admin Profile
SET @iAdminProfile = 18;
INSERT INTO `sys_options` VALUES
('default_overview_mode', 'Quick Links', @iAdminProfile, 'Default Overview Mode', 'select', '', '', 10, 'Quick Links,Tags,Search,Settings'),
('default_view_mode', 'Simple', @iAdminProfile, 'Default Members Mode', 'select', '', '', 10, 'Simple,Extended,Geeky'),
('default_order_by', 'None', @iAdminProfile, 'Default Order By', 'select', '', '', 20, 'None,User Name,Last Join,Last Activity'),
('default_per_page', '50', @iAdminProfile, 'Default Per Page', 'select', '', '', 30, '10,20,50,100,200');

DELETE FROM `sys_menu_admin` WHERE `name` = 'flash_apps';
UPDATE `sys_injections` SET `active` = '0' WHERE `id` = 1;
UPDATE `sys_injections_admin` SET `active` = '0' WHERE `id` = 1;

ALTER TABLE `RayVideoFiles` CHANGE `Status` `Status` ENUM('approved','disapproved','pending','processing','failed','onhold') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'onhold';
ALTER TABLE `RayMp3Files` CHANGE `Status` `Status` ENUM('approved','disapproved','pending','processing','failed','onhold') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'onhold';
ALTER TABLE `RayMp3Files` ADD `ThumbUrl` text NOT NULL AFTER `Title`;

INSERT INTO `sys_injections` (`name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES
('admin_switch', 0, 'injection_header', 'php', 'if(isset($_COOKIE[\'satoken\'])) {\r\n	$sCode = \'\r\n	<div class=\"back_to_admin\"><a href=\"member.php?loginas=admin&id=0\">\' . _t(\'_back_to_admin\') . \'</a></div>\r\n	\';\r\n	return $sCode;\r\n}\r\n', 0, 1);

INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES
('{evalResult}', 'sign-in', 'member.php?loginas=true&id={ID}', '', 'if (isAdmin({member_id})) return _t(\'_Login_As\');', 1, 'Profile', 0);

-- last step is to update current version
UPDATE `sys_options` SET `VALUE` = '1.1.0' WHERE `Name` = 'sys_tmp_version';
