
-- drop table
DROP TABLE IF EXISTS `ch_zip_countries_geonames`;
DROP TABLE IF EXISTS `ch_zip_countries_google`;

-- permalink
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=zipcodesearch/';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'ch_zip';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'ZIP Code Search' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'ch_zip_permalinks';

