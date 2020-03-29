
    DROP TABLE IF EXISTS `[db_prefix]accounts`;
	DELETE FROM `sys_email_templates` WHERE `Name` = 't_fb_connect_password_generated';

    --
    -- Dumping data for table `sys_objects_auths`
    --

    DELETE FROM
        `sys_objects_auths`
    WHERE
        `Name` = 'facebook';

    --
    -- `sys_alerts_handlers` ;
    --

    SET @iHandlerId := (SELECT `id` FROM `sys_alerts_handlers`  WHERE `name`  =  'ch_facebook_connect');

    DELETE FROM
        `sys_alerts_handlers`
    WHERE
        `id`  = @iHandlerId;

    --
    -- `sys_alerts` ;
    --

    DELETE FROM
        `sys_alerts`
    WHERE
        `handler_id` =  @iHandlerId ;

    --
    -- need for compatibility with old style login, will need remove it in a feature version!
    --

	ALTER TABLE `Profiles`
		DROP INDEX `FacebookProfile`;

    ALTER TABLE `Profiles`
        DROP `FacebookProfile`;

    --
    -- `sys_menu_admin`;
    --

    DELETE FROM
        `sys_menu_admin`
    WHERE
        `title` = '_ch_facebook';

    --
    -- `sys_options_cats` ;
    --

    SET @iKategId = (SELECT `id` FROM `sys_options_cats` WHERE `name` = 'Facebook connect' LIMIT 1);
    DELETE FROM `sys_options_cats` WHERE `id` = @iKategId;

    --
    -- `sys_options` ;
    --

    DELETE FROM `sys_options` WHERE `kateg` = @iKategId;

    --
    -- permalink
    --

    DELETE FROM
        `sys_permalinks`
    WHERE
        `standard`  = 'modules/?r=facebook_connect/';

    --
    -- settings
    --

    DELETE FROM
        `sys_options`
    WHERE
        `Name` = 'ch_facebook_connect_permalinks'
            AND
        `kateg` = 26;

    --
    -- chart
    --

    DELETE FROM `sys_objects_charts` WHERE `object` = 'ch_facebook';

    --
    -- export
    --

    DELETE FROM `sys_objects_exports` WHERE `object` = 'ch_facebook';

