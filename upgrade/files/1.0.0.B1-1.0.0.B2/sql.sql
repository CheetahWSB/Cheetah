INSERT IGNORE INTO `sys_options` VALUES ('sys_php_block_enabled', '', 3, 'Enable PHP Block in Page Builders.', 'checkbox', '', '', 190, '');
UPDATE `sys_menu_admin_top` SET `url` = 'https://www.cheetahwsb.com/m/market' WHERE `name` = 'extensions';
UPDATE `sys_menu_admin_top` SET `url` = 'https://www.cheetahwsb.com/m/cheetah_docs/chapter/' WHERE `name` = 'info';
UPDATE `sys_page_compose` SET `Content` = 'https://www.cheetahwsb.com/m/news/act_rss/4' WHERE `Caption` = '_Cheetah News';
UPDATE `sys_page_compose` SET `Cache` = '300' WHERE `Caption` = '_Site Stats';
UPDATE `sys_page_compose` SET `Cache` = '300' WHERE `Caption` = '_Cheetah News';
UPDATE `sys_page_compose` SET `Cache` = '3600' WHERE `Caption` = '_Member_Login';
UPDATE `sys_page_compose` SET `Cache` = '3600' WHERE `Caption` = '_sys_box_title_download';
UPDATE `sys_page_compose` SET `Cache` = '3600' WHERE `Caption` = '_sys_box_title_search_keyword';
UPDATE `sys_page_compose` SET `Cache` = '3600' WHERE `Caption` = '_Login';
UPDATE `sys_page_compose` SET `Cache` = '3600' WHERE `Caption` = '_tags_search_form';
UPDATE `sys_page_compose` SET `Cache` = '3600' WHERE `Caption` = '_categ_caption_search_form';

-- last step is to update current version

UPDATE `sys_options` SET `VALUE` = '1.0.0.B2' WHERE `Name` = 'sys_tmp_version';

