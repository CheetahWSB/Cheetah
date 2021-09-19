SET @iCategId = (SELECT `kateg` FROM `sys_options` WHERE `Name` = 'ch_avatar_site_avatars');
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('ch_avatar_allow_upload_to_photos', '', @iCategId, 'Enable option to allow upload of avatar to profile photo album', 'checkbox', '', '', '1', '');

UPDATE `sys_options` SET `VALUE` = '' WHERE `Name` = 'ch_avatar_site_avatars';

-- update module version
UPDATE `sys_modules` SET `version` = '1.2.0' WHERE `uri` = 'avatar' AND `version` = '1.1.0';
