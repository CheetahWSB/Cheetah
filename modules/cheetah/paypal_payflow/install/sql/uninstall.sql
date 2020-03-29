SET @sModuleName = 'ch_payflow';


DROP TABLE IF EXISTS `[db_prefix]providers`;
DROP TABLE IF EXISTS `[db_prefix]providers_options`;
DROP TABLE IF EXISTS `[db_prefix]user_values`;
DROP TABLE IF EXISTS `[db_prefix]cart`;
DROP TABLE IF EXISTS `[db_prefix]transactions`;
DROP TABLE IF EXISTS `[db_prefix]transactions_pending`;
DROP TABLE IF EXISTS `[db_prefix]modules`;


-- options
SET @iCategoryId = (SELECT `ID` FROM `sys_options_cats` WHERE `name`='PayPal PayFlow Pro' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `name`='PayPal PayFlow Pro' LIMIT 1;
DELETE FROM `sys_options` WHERE `kateg`=@iCategoryId OR `Name` IN ('permalinks_module_payflow');


-- permalinks
DELETE FROM `sys_permalinks` WHERE `check`='permalinks_module_payflow';


-- pages and blocks
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN ('ch_pfw_cart', 'ch_pfw_history', 'ch_pfw_orders', 'ch_pfw_details');
DELETE FROM `sys_page_compose` WHERE `Page` IN ('ch_pfw_cart', 'ch_pfw_history', 'ch_pfw_orders', 'ch_pfw_details');


-- menus
DELETE FROM `sys_menu_admin` WHERE `name`=@sModuleName;


-- alert
SET @iHandlerId = (SELECT `id` FROM `sys_alerts_handlers` WHERE `name`=@sModuleName LIMIT 1);
DELETE FROM `sys_alerts_handlers` WHERE `id`=@iHandlerId LIMIT 1;
DELETE FROM `sys_alerts` WHERE `handler_id`=@iHandlerId;


-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('ch_pfw_paid_need_join');


-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = @sModuleName;


-- export
DELETE FROM `sys_objects_exports` WHERE `object` = @sModuleName;


-- payments
DELETE FROM `sys_objects_payments` WHERE `object` = @sModuleName;
