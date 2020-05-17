UPDATE `sys_menu_top` SET `Caption` = '_ch_polls_profile' WHERE `Parent` = 4 AND `Name` = 'My Polls';
UPDATE `sys_menu_top` SET `Caption` = '_ch_ads_Ads_profile' WHERE `Parent` = 4 AND `Name` = 'Profile Ads';
UPDATE `sys_menu_top` SET `Caption` = '_ch_ads_Ads_profile' WHERE `Parent` = 9 AND `Name` = 'Profile Ads';

UPDATE `sys_options` SET `AvailableValues` = 'File,Memcache' WHERE `Name` = 'sys_db_cache_engine';
UPDATE `sys_options` SET `AvailableValues` = 'File,Memcache' WHERE `Name` = 'sys_pb_cache_engine';
UPDATE `sys_options` SET `AvailableValues` = 'File,Memcache' WHERE `Name` = 'sys_mm_cache_engine';
UPDATE `sys_options` SET `AvailableValues` = 'FileHtml,Memcache' WHERE `Name` = 'sys_template_cache_engine';

-- last step is to update current version

UPDATE `sys_options` SET `VALUE` = '1.0.0.B3' WHERE `Name` = 'sys_tmp_version';
