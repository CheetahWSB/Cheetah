
    DROP TABLE IF EXISTS `[db_prefix]data`;

    --
    -- `sys_options_cats` ;
    --

    SET @iKategId = (SELECT `id` FROM `sys_options_cats` WHERE `name` = 'Polls' LIMIT 1);
    DELETE FROM `sys_options_cats` WHERE `id` = @iKategId;

    --
    -- `sys_options` ;
    --

    DELETE FROM `sys_options` WHERE `kateg` = @iKategId;

    --
    -- Site stats;
    --

    DELETE FROM `sys_stat_site` WHERE `Name` = 'pls' AND `Title` = 'ch_polls';

    --
    -- Admin menu ;
    --

    DELETE FROM `sys_menu_admin` WHERE `name` = 'Polls';

    --
    -- Top menu ;
    --

    SET @iMenuParentId := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Polls' AND `Caption` = '_ch_polls' LIMIT 1);
    DELETE FROM `sys_menu_top` WHERE `Parent` = @iMenuParentId OR `ID` = @iMenuParentId;
    DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'My Polls' AND `Caption` = '_ch_polls_profile';
    DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Polls' AND `Caption` = '_ch_poll';
    DELETE FROM `sys_menu_top` WHERE `Parent` = 0 AND `Name` = 'Poll unit' AND `Type` = 'system';

    --
    -- member menu
    --

    DELETE FROM `sys_menu_member` WHERE `Name` = 'ch_poll';

    --
    -- sys_account_custom_stat_elements ;
    --

    DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_ch_polls';

    --
    -- sys_stat_member ;
    --

    DELETE FROM `sys_stat_member` WHERE `Type` = 'spo';

    --
    -- sys_page_compose ;
    --

    DELETE FROM `sys_page_compose`  WHERE `Page` = 'profile' AND `Caption`   = '_ch_polls';
    DELETE FROM `sys_page_compose`  WHERE `Page` = 'index'   AND `Caption`   = '_ch_polls';
    DELETE FROM `sys_page_compose`  WHERE `Page` = 'show_poll_info';
    DELETE FROM `sys_page_compose`  WHERE `Page` = 'poll_home';

    DELETE FROM `sys_page_compose_pages` WHERE `Name` = 'show_poll_info';
    DELETE FROM `sys_page_compose_pages` WHERE `Name` = 'poll_home';

    DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'ch_poll';

    DROP TABLE IF EXISTS `[db_prefix]voting_track`;
    DROP TABLE IF EXISTS `[db_prefix]rating`;
    DROP TABLE IF EXISTS `[db_prefix]cmts_track`;
    DROP TABLE IF EXISTS `[db_prefix]cmts`;

    DELETE FROM
        `sys_objects_actions`
    WHERE
        `Type` IN ('ch_poll', 'ch_poll_title');

    DELETE FROM
        `sys_permalinks`
    WHERE
        `standard`  = 'modules/?r=poll/';

    DELETE FROM
        `sys_options`
    WHERE
        `Name` = 'ch_poll_permalinks';

    DELETE FROM
        `sys_objects_tag`
    WHERE
        `ObjectName` = 'ch_poll';

    DELETE FROM
        `sys_privacy_actions`
    WHERE
        `module_uri` = 'poll';

    DELETE FROM
        `sys_tags`
    WHERE
        `Type` = 'ch_poll';

    DELETE FROM
        `sys_objects_categories`
    WHERE
        `ObjectName` = 'ch_poll';

    DELETE FROM
        `sys_categories`
    WHERE
        `Type` = 'ch_poll';

    DELETE FROM
        `sys_objects_cmts`
    WHERE
        `ObjectName` = 'ch_poll';

    DELETE FROM `sys_sbs_types` WHERE `unit` = 'ch_poll';

    DELETE FROM `sys_email_templates` WHERE `Name` = 't_sbsPollComments';

    SET @iHandlerId = (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'ch_poll'  LIMIT 1);

    DELETE FROM
        `sys_alerts_handlers`
    WHERE
        `id` = @iHandlerId;

    DELETE FROM `sys_alerts` WHERE `unit` = 'ch_poll' OR `handler_id` =  @iHandlerId ;

    --
    -- `sys_objects_search` ;
    --

    DELETE FROM
        `sys_objects_search`
    WHERE
        `ObjectName` = 'poll';

    SET @iActionId := (SELECT `ID` FROM  `sys_acl_actions` WHERE `Name` =  'create polls' LIMIT 1);
    DELETE FROM `sys_acl_actions` WHERE `Name` = 'create polls';
    DELETE FROM `sys_acl_matrix` WHERE `IDAction` = @iActionId;


    -- sitemap

    DELETE FROM `sys_objects_site_maps` WHERE `object` = 'ch_poll';


    -- chart

    DELETE FROM `sys_objects_charts` WHERE `object` = 'ch_poll';

    -- export

    DELETE FROM `sys_objects_exports` WHERE `object` = 'ch_poll';
