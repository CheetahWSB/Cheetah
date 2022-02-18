UPDATE `sys_injections` SET `data` = 'return getAdminSwitch();' WHERE `name` = 'admin_switch';

ALTER TABLE `Profiles` ADD `DateLastNavA` DATETIME NOT NULL DEFAULT ' 0000-00-00 00:00:00 ' AFTER `DateLastNav`, ADD INDEX `DateLastNavA` (`DateLastNavA`);

UPDATE `sys_objects_editor` SET `skin` = 'oxide' WHERE `object` = 'sys_tinymce';

-- Update areas where language keys can be used to length needed to support
-- the language keys maxinum length.
ALTER TABLE `sys_menu_admin` CHANGE `title` `title` VARCHAR(255);
ALTER TABLE `sys_menu_admin_top` CHANGE `caption` `caption` VARCHAR(255);
ALTER TABLE `sys_menu_service` CHANGE `Caption` `Caption` VARCHAR(255);
ALTER TABLE `sys_menu_bottom` CHANGE `Caption` `Caption` VARCHAR(255);
ALTER TABLE `sys_menu_member` CHANGE `Caption` `Caption` VARCHAR(255);
ALTER TABLE `sys_menu_member` CHANGE `Description` `Description` VARCHAR(255);
ALTER TABLE `sys_objects_search` CHANGE `Title` `Title` VARCHAR(255);
ALTER TABLE `sys_objects_tag` CHANGE `LangKey` `LangKey` VARCHAR(255);
ALTER TABLE `sys_menu_top` CHANGE `Caption` `Caption` VARCHAR(255);
ALTER TABLE `sys_box_download` CHANGE `title` `title` VARCHAR(255);


SET @iCatGeneral = 3;
INSERT INTO `sys_options` VALUES
('enable_tiny_in_mail', 'on', @iCatGeneral, 'Enable WYSIWYG editor in mail', 'checkbox', '', '', 32, '');

-- CAT: Categories
SET @iCatCategories = 27;
INSERT INTO `sys_options` VALUES
('categ_sort', 'Ascending', @iCatCategories, 'Categories sort order', 'select', '', '', 40, 'None,Ascending,Descending');

-- last step is to update current version
UPDATE `sys_options` SET `VALUE` = '1.2.0' WHERE `Name` = 'sys_tmp_version';
