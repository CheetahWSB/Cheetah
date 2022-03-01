ALTER TABLE `ch_blogs_posts` DROP INDEX `PostCaption`;
ALTER TABLE `ch_blogs_posts` CHANGE `PostCaption` `PostCaption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_blogs_posts` CHANGE `PostText` `PostText` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_blogs_posts` CHANGE `Tags` `Tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_blogs_posts` ADD FULLTEXT KEY `PostCaption` (`PostCaption`,`PostText`,`Tags`);

-- update module version
UPDATE `sys_modules` SET `version` = '1.3.0' WHERE `uri` = 'blogs' AND `version` = '1.2.0';
