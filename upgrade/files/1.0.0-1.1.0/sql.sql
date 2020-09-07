DELETE FROM `sys_menu_admin` WHERE `name` = 'mobile_pages';


-- last step is to update current version
UPDATE `sys_options` SET `VALUE` = '1.1.0' WHERE `Name` = 'sys_tmp_version';
