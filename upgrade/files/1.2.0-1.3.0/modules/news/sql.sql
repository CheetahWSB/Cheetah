ALTER TABLE `ch_news_entries` DROP INDEX `search_group`;
ALTER TABLE `ch_news_entries` CHANGE `caption` `caption` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_news_entries` CHANGE `content` `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_news_entries` CHANGE `tags` `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_news_entries` CHANGE `categories` `categories` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_news_entries` ADD FULLTEXT KEY `search_group` (`caption`, `content`, `tags`, `categories`);

-- update module version
UPDATE `sys_modules` SET `version` = '1.3.0' WHERE `uri` = 'news' AND `version` = '1.2.0';
