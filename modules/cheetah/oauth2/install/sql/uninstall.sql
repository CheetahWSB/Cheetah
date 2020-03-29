
DROP TABLE IF EXISTS `ch_oauth_access_tokens`, `ch_oauth_authorization_codes`, `ch_oauth_clients`, `ch_oauth_refresh_tokens`, `ch_oauth_scopes`;

-- permalink
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=oauth2/';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'ch_oauth2';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'OAuth2 Server' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'ch_oauth2_permalinks';

