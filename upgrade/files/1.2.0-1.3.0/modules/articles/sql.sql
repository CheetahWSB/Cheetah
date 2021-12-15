ALTER TABLE `ch_arl_entries` DROP INDEX `search_group`;
ALTER TABLE `ch_arl_entries` CHANGE `caption` `caption` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_arl_entries` CHANGE `content` `content` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_arl_entries` CHANGE `tags` `tags` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_arl_entries` CHANGE `categories` `categories` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_arl_entries` ADD FULLTEXT KEY `search_group` (`caption`, `content`, `tags`, `categories`);

-- update module version
UPDATE `sys_modules` SET `version` = '1.3.0' WHERE `uri` = 'articles' AND `version` = '1.2.0';
