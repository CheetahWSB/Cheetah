UPDATE `sys_menu_top` SET `Caption` = '_ch_polls_profile' WHERE `Parent` = 4 AND `Name` = 'My Polls';
UPDATE `sys_menu_top` SET `Caption` = '_ch_ads_Ads_profile' WHERE `Parent` = 4 AND `Name` = 'Profile Ads';
UPDATE `sys_menu_top` SET `Caption` = '_ch_ads_Ads_profile' WHERE `Parent` = 9 AND `Name` = 'Profile Ads';

UPDATE `sys_options` SET `AvailableValues` = 'File,Memcache' WHERE `Name` = 'sys_db_cache_engine';
UPDATE `sys_options` SET `AvailableValues` = 'File,Memcache' WHERE `Name` = 'sys_pb_cache_engine';
UPDATE `sys_options` SET `AvailableValues` = 'File,Memcache' WHERE `Name` = 'sys_mm_cache_engine';
UPDATE `sys_options` SET `AvailableValues` = 'FileHtml,Memcache' WHERE `Name` = 'sys_template_cache_engine';

DELETE FROM `sys_options` WHERE `Name` = 'sys_antispam_smart_check';
UPDATE `sys_options` SET `order_in_kateg` = '10' WHERE `Name` = 'sys_antispam_bot_check';
UPDATE `sys_options` SET `order_in_kateg` = '20' WHERE `Name` = 'sys_dnsbl_enable';
UPDATE `sys_options` SET `order_in_kateg` = '30' WHERE `Name` = 'sys_dnsbl_behaviour';
UPDATE `sys_options` SET `order_in_kateg` = '40' WHERE `Name` = 'sys_uridnsbl_enable';
UPDATE `sys_options` SET `order_in_kateg` = '50' WHERE `Name` = 'sys_akismet_enable';
UPDATE `sys_options` SET `order_in_kateg` = '60' WHERE `Name` = 'sys_akismet_api_key';
UPDATE `sys_options` SET `order_in_kateg` = '70' WHERE `Name` = 'sys_stopforumspam_enable';
UPDATE `sys_options` SET `order_in_kateg` = '80' WHERE `Name` = 'sys_stopforumspam_api_key';
UPDATE `sys_options` SET `order_in_kateg` = '90' WHERE `Name` = 'sys_antispam_block';
UPDATE `sys_options` SET `order_in_kateg` = '100' WHERE `Name` = 'sys_antispam_report';
UPDATE `sys_options` SET `order_in_kateg` = '110' WHERE `Name` = 'sys_antispam_add_nofollow';

-- last step is to update current version

UPDATE `sys_options` SET `VALUE` = '1.0.0.B3' WHERE `Name` = 'sys_tmp_version';
