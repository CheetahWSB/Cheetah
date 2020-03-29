
-- delete tables of the module
DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]units`;
DROP TABLE IF EXISTS `[db_prefix]themes`;
DROP TABLE IF EXISTS `[db_prefix]images`;

-- delete permalinks
DELETE FROM `sys_permalinks` WHERE `check`='ch_sctr_permalinks';

-- delete settings
DELETE FROM `sys_options` WHERE `Name` IN ('ch_sctr_permalinks', 'ch_sctr_enable');

-- delete action
DELETE FROM `sys_menu_member` WHERE `Name`='SiteCustomizer';

-- delete from admin-menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'ch_sctr';

-- injection
DELETE FROM `sys_injections` WHERE `name` IN ('ch_sctr_style', 'ch_sctr_block');
