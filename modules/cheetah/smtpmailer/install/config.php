<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'SMTP Mailer',
    'version' => '1.3.0',
    'vendor' => 'Cheetah',
    'update_url' => '',

    'compatible_with' => array(
        '1.3.0'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'cheetah/smtpmailer/',
    'home_uri' => 'smtpmailer',

    'db_prefix' => 'ch_smtp_',
    'class_prefix' => 'ChSMTP',
    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
        'show_introduction' => 0,
        'change_permissions' => 1,
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
        'recompile_injections' => 0,
        'recompile_permalinks' => 1,
        'recompile_alerts' => 0,
        'show_conclusion' => 0,
    ),
    'uninstall' => array (
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
        'recompile_injections' => 0,
        'recompile_permalinks' => 1,
        'recompile_alerts' => 0,
        'show_conclusion' => 0,
    ),
    /**
     * Dependencies Section
     */
    'dependencies' => array(),

    /**
     * Category for language keys.
     */
    'language_category' => 'SMTP Mailer',

    /**
     * Permissions Section
     */
    'install_permissions' => array(
        'writable' => array(
            'data/logs/',
        ),
    ),
    'uninstall_permissions' => array(),

    /**
     * Introduction and Conclusion Section.
     */
    'install_info' => array(
        'introduction' => '',
        'conclusion' => ''
    ),
    'uninstall_info' => array(
        'introduction' => '',
        'conclusion' => ''
    )
);
