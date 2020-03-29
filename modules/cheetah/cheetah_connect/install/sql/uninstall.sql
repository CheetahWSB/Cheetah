
DROP TABLE IF EXISTS `ch_dolphcon_accounts`;

-- Email template

DELETE FROM `sys_email_templates` WHERE `Name` = 't_ch_dolphcon_password_generated';

-- Auth objects

DELETE FROM `sys_objects_auths` WHERE `Name` = 'ch_dolphcon';

-- Alerts

SET @iHandlerId := (SELECT `id` FROM `sys_alerts_handlers`  WHERE `name`  =  'ch_dolphcon');

DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandlerId;
DELETE FROM `sys_alerts` WHERE `handler_id` =  @iHandlerId;

-- Options

SET @iKategId = (SELECT `id` FROM `sys_options_cats` WHERE `name` = 'Cheetah connect' LIMIT 1);

DELETE FROM `sys_options_cats` WHERE `id` = @iKategId;
DELETE FROM `sys_options` WHERE `kateg` = @iKategId;
DELETE FROM `sys_options` WHERE `Name` = 'ch_dolphcon_permalinks' AND `kateg` = 26;

-- Menu Admin

DELETE FROM `sys_menu_admin` WHERE `title` = '_ch_dolphcon';

-- Permalinks

DELETE FROM `sys_permalinks` WHERE `standard`  = 'modules/?r=dolphcon/';

-- Chart

DELETE FROM `sys_objects_charts` WHERE `object` = 'ch_dolphcon';

-- Export

DELETE FROM `sys_objects_exports` WHERE `object` = 'ch_dolphcon';

