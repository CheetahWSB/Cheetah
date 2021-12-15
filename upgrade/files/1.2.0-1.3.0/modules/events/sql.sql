ALTER TABLE `ch_events_main` DROP INDEX `Title`;
ALTER TABLE `ch_events_main` CHANGE `Title` `Title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_events_main` CHANGE `Description` `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_events_main` CHANGE `City` `City` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_events_main` CHANGE `Place` `Place` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_events_main` CHANGE `Tags` `Tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_events_main` CHANGE `Categories` `Categories` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `ch_events_main` ADD FULLTEXT KEY `Title` (`Title`,`Description`,`City`,`Place`,`Tags`,`Categories`);

-- update module version
UPDATE `sys_modules` SET `version` = '1.3.0' WHERE `uri` = 'events' AND `version` = '1.2.0';
