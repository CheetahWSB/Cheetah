-- last step is to update current version
UPDATE `sys_options` SET `VALUE` = '1.3.0' WHERE `Name` = 'sys_tmp_version';
