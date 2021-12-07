ALTER TABLE `ch_forum_post` CHANGE `post_text` `post_text` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

-- update module version
UPDATE `sys_modules` SET `version` = '1.3.0' WHERE `uri` = 'forum' AND `version` = '1.2.0';
