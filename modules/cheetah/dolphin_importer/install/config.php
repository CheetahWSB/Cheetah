<?php
/***************************************************************************
* Date Released		: December 8, 2020
* Last Updated		: December 8, 2020
*
* Copywrite			: (c) 2020 by Dean J. Bassett Jr.
* Website			: https://www.cheetahwsb.com
*
* Product Name		: Dolphin Importer
* Product Version	: 1.0.0
*
* IMPORTANT: This is a commercial product made by Dean J. Bassett Jr.
* and cannot be modified other than personal use.
*
* This product cannot be redistributed for free or a fee without written
* permission from Dean J. Bassett Jr.
*
* You may use the product on one dolphin website only. You need to purchase
* additional copies if you intend to use this on other websites.
***************************************************************************/

$aConfig = array(
	'title' => 'Dolphin Importer',
  'version' => '1.0.0',
	'vendor' => 'Cheetah',
	'update_url' => '',

	'compatible_with' => array(
        '1.x.x',
    ),

	'home_dir' => 'cheetah/dolphin_importer/',
	'home_uri' => 'dolphin_importer',

	'db_prefix' => 'ch_dolphin_importer_',
    'class_prefix' => 'ChDolphinImporter',

	'install' => array(
		'check_requirements' => 0,
		'check_dependencies' => 0,
		'show_introduction' => 0,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_main_menu' => 1,
		'recompile_member_menu' => 0,
		'recompile_site_stats' => 0,
		'recompile_page_builder' => 1,
		'recompile_profile_fields' => 1,
		'recompile_comments' => 0,
		'recompile_member_actions' => 1,
		'recompile_tags' => 0,
		'recompile_votes' => 0,
		'recompile_categories' => 0,
		'recompile_search' => 0,
		'recompile_browse' => 0,
		'recompile_injections' => 1,
		'recompile_permalinks' => 1,
		'recompile_alerts' => 1,
		'show_conclusion' => 1,
		'recompile_global_paramaters' => 1,
		'clear_db_cache'  => 1,
	),
	'uninstall' => array (
		'check_requirements' => 0,
		'check_dependencies' => 0,
		'show_introduction' => 0,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_main_menu' => 1,
		'recompile_member_menu' => 0,
		'recompile_site_stats' => 0,
		'recompile_page_builder' => 1,
		'recompile_profile_fields' => 1,
		'recompile_comments' => 0,
		'recompile_member_actions' => 1,
		'recompile_tags' => 0,
		'recompile_votes' => 0,
		'recompile_categories' => 0,
		'recompile_search' => 0,
		'recompile_browse' => 0,
		'recompile_injections' => 1,
		'recompile_permalinks' => 1,
		'recompile_alerts' => 1,
		'show_conclusion' => 1,
		'recompile_global_paramaters' => 1,
		'clear_db_cache'  => 1,
    ),

	'language_category' => 'Cheetah - Dolphin Importer',

	'install_permissions' => array(),
    'uninstall_permissions' => array(),

	'install_info' => array(
		'introduction' => '',
		'conclusion' => ''
	),
	'uninstall_info' => array(
		'introduction' => '',
		'conclusion' => ''
	)
);
