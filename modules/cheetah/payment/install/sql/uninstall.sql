SET @sModuleName = 'Payment';

DROP TABLE IF EXISTS `[db_prefix]providers`;
DROP TABLE IF EXISTS `[db_prefix]providers_options`;
DROP TABLE IF EXISTS `[db_prefix]user_values`;
DROP TABLE IF EXISTS `[db_prefix]cart`;
DROP TABLE IF EXISTS `[db_prefix]transactions`;
DROP TABLE IF EXISTS `[db_prefix]transactions_pending`;
DROP TABLE IF EXISTS `[db_prefix]modules`;

SET @iCategoryId = (SELECT `ID` FROM `sys_options_cats` WHERE `name`=@sModuleName LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `name`=@sModuleName LIMIT 1;
DELETE FROM `sys_options` WHERE `kateg`=@iCategoryId OR `Name`='permalinks_module_payment';

DELETE FROM `sys_permalinks` WHERE `check`='permalinks_module_payment';

DELETE FROM `sys_page_compose_pages` WHERE `Name` IN ('ch_pmt_cart', 'ch_pmt_history', 'ch_pmt_orders', 'ch_pmt_details');
DELETE FROM `sys_page_compose` WHERE `Page` IN ('ch_pmt_cart', 'ch_pmt_history', 'ch_pmt_orders', 'ch_pmt_details');

DELETE FROM `sys_menu_admin` WHERE `name`='ch_payment';

-- alert
SET @iHandlerId = (SELECT `id` FROM `sys_alerts_handlers` WHERE `name`='ch_payment' LIMIT 1);
DELETE FROM `sys_alerts_handlers` WHERE `id`=@iHandlerId LIMIT 1;
DELETE FROM `sys_alerts` WHERE `handler_id`=@iHandlerId;

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('ch_pmt_paid_need_join');

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'ch_pmt';

-- export
DELETE FROM `sys_objects_exports` WHERE `object` = 'ch_pmt';

-- payments
DELETE FROM `sys_objects_payments` WHERE `object` = @sModuleName;
