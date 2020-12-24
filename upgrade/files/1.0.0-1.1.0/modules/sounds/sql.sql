SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sounds' LIMIT 1);
SET @pos := 0;
UPDATE `sys_options` SET `order_in_kateg` = ( SELECT @pos := @pos + 10 ) WHERE `kateg` = @iCategId ORDER BY `order_in_kateg`;
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('ch_sounds_cat_required', 'on', @iCategId, 'Require category when editing', 'checkbox', '', '', '15', '');
SET @pos := 0;
UPDATE `sys_options` SET `order_in_kateg` = ( SELECT @pos := @pos + 10 ) WHERE `kateg` = @iCategId ORDER BY `order_in_kateg`;
UPDATE `sys_options` SET `VALUE` = 'html5', `AvailableValues` = 'html5' WHERE `Name` = 'ch_sounds_uploader_switcher';

-- update module version

UPDATE `sys_modules` SET `version` = '1.1.0' WHERE `uri` = 'sounds' AND `version` = '1.0.0';
