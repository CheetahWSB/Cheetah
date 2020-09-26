DELETE FROM `sys_menu_admin` WHERE `name` = 'mobile_pages';
DELETE FROM `sys_objects_actions` WHERE `Caption` = '{cpt_get_mail}';
DELETE FROM `sys_options` WHERE `Name` = 'anon_mode';


-- last step is to update current version
UPDATE `sys_options` SET `VALUE` = '1.1.0' WHERE `Name` = 'sys_tmp_version';
