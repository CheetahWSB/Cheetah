UPDATE `sys_injections` SET `data` = 'return getAdminSwitch();' WHERE `name` = 'admin_switch';

-- last step is to update current version
UPDATE `sys_options` SET `VALUE` = '1.2.0' WHERE `Name` = 'sys_tmp_version';