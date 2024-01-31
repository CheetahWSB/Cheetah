INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, `Cache`) VALUES
('index', '1140px', 'Featured Photo Block', '_featured_photo_index', 0, 0, 'PHP', 'require_once(CH_DIRECTORY_PATH_MODULES . \'cheetah/photos/classes/ChPhotosSearch.php\');\n$sClassSearch = \'ChPhotosSearch\';\n$oSearch = new $sClassSearch();\necho $oSearch->getFeaturedPhotoBlock(\'home\');\n', 1, 28.1, 'non,memb', 0, 0);

SELECT @iKatID := `ID` FROM `sys_options_cats` WHERE `name` = 'Photos';
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('ch_photos_featured_photo_block_show_vote', '', @iKatID, 'Use vote on home page featured photo block', 'checkbox', '', '', 290, ''),
('ch_photos_featured_photo_block_sort', 'Ordered By Views', @iKatID, 'Display mode for home page featured photo block', 'select', '', '', 300, 'Ordered By Views,Random,Latest'),
('ch_photos_featured_photo_block_photo_pos', 'center', @iKatID, 'If photo is to small to fit home page featured photo block', 'select', '', '', 310, 'center,fill');

ALTER TABLE `ch_photos_main` ADD `FeaturedViews` BIGINT NOT NULL DEFAULT '0' AFTER `Featured`; 

-- update module version
UPDATE `sys_modules` SET `version` = '1.3.0' WHERE `uri` = 'photos' AND `version` = '1.2.0';
