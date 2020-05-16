UPDATE `sys_menu_top` SET `Caption` = '_ch_polls_profile' WHERE `Parent` = 4 AND `Name` = 'My Polls';
UPDATE `sys_menu_top` SET `Caption` = '_ch_ads_Ads_profile' WHERE `Parent` = 4 AND `Name` = 'Profile Ads';
UPDATE `sys_menu_top` SET `Caption` = '_ch_ads_Ads_profile' WHERE `Parent` = 9 AND `Name` = 'Profile Ads';

-- last step is to update current version

UPDATE `sys_options` SET `VALUE` = '1.0.0.B3' WHERE `Name` = 'sys_tmp_version';
