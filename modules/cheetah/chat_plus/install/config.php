<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Chat+',
    'version' => '1.3.0',
    'vendor' => 'Cheetah',
    'update_url' => '',

    'compatible_with' => array(
        '1.3.0'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'cheetah/chat_plus/',
    'home_uri' => 'chat_plus',

    'db_prefix' => 'ch_chat_plus_',
    'class_prefix' => 'ChChatPlus',

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
        'check_dependencies' => 0,
        'show_introduction' => 0,
        'change_permissions' => 0,
        'execute_sql' => 1,
        'update_languages' => 1,
        'recompile_main_menu' => 0,
        'recompile_member_menu' => 0,
        'recompile_site_stats' => 0,
        'recompile_page_builder' => 0,
        'recompile_profile_fields' => 0,
        'recompile_comments' => 0,
        'recompile_member_actions' => 0,
        'recompile_tags' => 0,
        'recompile_votes' => 0,
        'recompile_categories' => 0,
        'recompile_search' => 0,
        'recompile_browse' => 0,
        'recompile_injections' => 0,
        'recompile_permalinks' => 1,
        'recompile_alerts' => 0,
        'recompile_global_paramaters' => 1,
        'clear_db_cache'  => 1,
        'show_conclusion' => 0,
    ),
    'uninstall' => array (
        'check_dependencies' => 0,
        'show_introduction' => 0,
        'change_permissions' => 0,
        'execute_sql' => 1,
        'update_languages' => 1,
        'recompile_main_menu' => 0,
        'recompile_member_menu' => 0,
        'recompile_site_stats' => 0,
        'recompile_page_builder' => 0,
        'recompile_profile_fields' => 0,
        'recompile_comments' => 0,
        'recompile_member_actions' => 0,
        'recompile_tags' => 0,
        'recompile_votes' => 0,
        'recompile_categories' => 0,
        'recompile_search' => 0,
        'recompile_browse' => 0,
        'recompile_injections' => 0,
        'recompile_permalinks' => 1,
        'recompile_alerts' => 0,
        'recompile_global_paramaters' => 1,
        'clear_db_cache'  => 1,
        'show_conclusion' => 0,
    ),

    /**
     * Dependencies Section
     */
    'dependencies' => array(
        'oauth2' => 'Cheetah OAuth2 Server Module'
    ),

    /**
     * Category for language keys.
     */
    'language_category' => 'Chat+',

    /**
     * Permissions Section
     */
    'install_permissions' => array(),
    'uninstall_permissions' => array(),

    /**
     * Introduction and Conclusion Section.
     */
    'install_info' => array(
        'introduction' => '',
        'conclusion' => '',
    ),
    'uninstall_info' => array(
        'introduction' => '',
        'conclusion' => '',
    )
);
