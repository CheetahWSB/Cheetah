UPDATE `sys_injections` SET `data` = 'return getAdminSwitch();' WHERE `name` = 'admin_switch';

ALTER TABLE `Profiles` ADD `DateLastNavA` DATETIME NOT NULL DEFAULT ' 0000-00-00 00:00:00 ' AFTER `DateLastNav`, ADD INDEX `DateLastNavA` (`DateLastNavA`);

UPDATE `sys_objects_editor` SET `skin` = 'oxide' WHERE `object` = 'sys_tinymce';

-- last step is to update current version
UPDATE `sys_options` SET `VALUE` = '1.2.0' WHERE `Name` = 'sys_tmp_version';
