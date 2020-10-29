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

-- last step is to update current version
UPDATE `sys_options` SET `VALUE` = '1.1.0' WHERE `Name` = 'sys_tmp_version';
