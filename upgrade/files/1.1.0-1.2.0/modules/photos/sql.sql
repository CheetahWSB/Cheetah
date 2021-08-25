SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Photos' LIMIT 1);
SET @pos := 0;
UPDATE `sys_options` SET `order_in_kateg` = ( SELECT @pos := @pos + 10 ) WHERE `kateg` = @iCategId ORDER BY `order_in_kateg`;
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('ch_photos_cat_required', 'on', @iCategId, 'Require category when editing', 'checkbox', '', '', '15', '');
SET @pos := 0;
UPDATE `sys_options` SET `order_in_kateg` = ( SELECT @pos := @pos + 10 ) WHERE `kateg` = @iCategId ORDER BY `order_in_kateg`;
UPDATE `sys_options` SET `VALUE` = 'html5', `AvailableValues` = 'html5,embed' WHERE `Name` = 'ch_photos_uploader_switcher';

INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('ch_photos_delete_orig', '', @iCategId, 'Delete origional photo after upload', 'checkbox', '', '', 32, ''),

UPDATE `sys_objects_actions` SET `Caption` = '{evalResult}', `Eval` = 'if(getParam(''ch_photos_delete_orig'') != ''on'') return _t(''_ch_photos_action_view_original'');' WHERE `Caption` = '_ch_photos_action_view_original'

-- update module version
UPDATE `sys_modules` SET `version` = '1.2.0' WHERE `uri` = 'photos' AND `version` = '1.1.0';
