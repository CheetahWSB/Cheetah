ALTER TABLE `sys_messages` CHANGE `Text` `Text` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

-- last step is to update current version
UPDATE `sys_options` SET `VALUE` = '1.3.0' WHERE `Name` = 'sys_tmp_version';
