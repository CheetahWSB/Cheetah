ALTER TABLE `ch_fdb_entries` DROP INDEX `search_group`;
ALTER TABLE `ch_fdb_entries` CHANGE `caption` `caption` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_fdb_entries` CHANGE `content` `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_fdb_entries` CHANGE `tags` `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_fdb_entries` ADD FULLTEXT KEY `search_group` (`caption`, `content`, `tags`);

-- update module version
UPDATE `sys_modules` SET `version` = '1.3.0' WHERE `uri` = 'feedback' AND `version` = '1.2.0';
