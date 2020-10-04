DELETE FROM `sys_menu_admin` WHERE `name` = 'mobile_pages';
DELETE FROM `sys_objects_actions` WHERE `Caption` = '{cpt_get_mail}';
DELETE FROM `sys_options` WHERE `Name` = 'anon_mode';
INSERT INTO `sys_options` VALUES ('enable_browser_check', '', 3, 'Enable obsolete browser check', 'checkbox', '', '', 111, '');
INSERT INTO `sys_injections` (`name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES ('browser_check', 0, 'injection_footer', 'php', 'if(getParam(\'enable_browser_check\') && $_GET[\'enable_browser_check\'] != \'false\') { $sCode = \'<script>var $buoop={required:{e:-4,f:-3,o:-3,s:-1,c:-3},insecure:!0,api:2020.09};function $buo_f(){var e=document.createElement("script");e.src="//browser-update.org/update.min.js",document.body.appendChild(e)}try{document.addEventListener("DOMContentLoaded",$buo_f,!1)}catch(e){window.attachEvent("onload",$buo_f)}</script>\'; echo $sCode; }', 0, 1);

-- last step is to update current version
UPDATE `sys_options` SET `VALUE` = '1.1.0' WHERE `Name` = 'sys_tmp_version';
