<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin.inc.php' );

ch_import('ChWsbPaginate');
ch_import('ChWsbAdminIpBlockList');
ch_import('ChWsbCacheUtilities');

$logged['admin'] = member_auth(1, true, true);

$aCacheTypes = array (
    array('action' => 'all', 'title' => _t('_adm_txt_dashboard_cache_all')),
    array('action' => 'db', 'title' => _t('_adm_txt_dashboard_cache_db')),
    array('action' => 'pb', 'title' => _t('_adm_txt_dashboard_cache_pb')),
    array('action' => 'template', 'title' => _t('_adm_txt_dashboard_cache_template')),
    array('action' => 'css', 'title' => _t('_adm_txt_dashboard_cache_css')),
    array('action' => 'js', 'title' => _t('_adm_txt_dashboard_cache_js')),
    array('action' => 'users', 'title' => _t('_adm_txt_dashboard_cache_users')),
    array('action' => 'member_menu', 'title' => _t('_adm_txt_dashboard_cache_member_menu')),
);

$oCacheUtilities = new ChWsbCacheUtilities();

if (!empty($_POST['clear_cache'])) {
    $aResult = array();
    switch ($_POST['clear_cache']) {
        case 'all':
            foreach ($aCacheTypes as $r) {
                $aResult = $oCacheUtilities->clear($r['action']);
                if ($aResult['code'] != 0)
                    break 2;
            }
            break;
        case 'member_menu':
        case 'pb':
        case 'users':
        case 'db':
        case 'template':
        case 'css':
        case 'js':
            $aResult = $oCacheUtilities->clear($_POST['clear_cache']);
            break;
        default:
            $aResult = array('code' => 1, 'message' => _t('_Error Occured'));
    }

	// add cache size data for chart in case of successful cache cleaning
    if($aResult['code'] == 0) {
        $aResult['chart_data'] = array ();
        foreach ($aCacheTypes as $r) {
            if('all' == $r['action'])
				continue;

			$aResult['chart_data'][] = array(
				'value' => round($oCacheUtilities->size($r['action']) / 1024, 2),
				'color' => '#' . dechex(rand(0x000000, 0xFFFFFF)),
				'highlight' => '',
				'label' => ch_js_string($r['title'], CH_ESCAPE_STR_APOS),
			);
        }
    }

    echo json_encode($aResult);
    exit;
}

$iNameIndex = 3;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array(),
    'js_name' => array(),
    'header' => _t('_adm_txt_cache'),
    'header_text' => _t('_adm_txt_cache'),
);

$aPages = array (
    'clear' => array (
        'title' => _t('_adm_txt_clear_cache'),
        'url' => CH_WSB_URL_ADMIN . 'cache.php?mode=clear',
        'func' => 'PageCodeClear',
        'func_params' => array(),
    ),
    'engines' => array (
        'title' => _t('_adm_admtools_cache_engines'),
        'url' => CH_WSB_URL_ADMIN . 'cache.php?mode=engines',
        'func' => 'PageCodeEngines',
        'func_params' => array(),
    ),
    'settings' => array (
        'title' => _t('_Settings'),
        'url' => CH_WSB_URL_ADMIN . 'cache.php?mode=settings',
        'func' => 'PageCodeSettings',
        'func_params' => array(),
    ),
);

if (!isset($_GET['mode']) || !isset($aPages[$_GET['mode']]))
    $sMode = 'clear';
else
    $sMode = $_GET['mode'];

$aTopItems = array();
foreach ($aPages as $k => $r)
    $aTopItems['dbmenu_' . $k] = array(
        'href' => $r['url'],
        'title' => $r['title'],
        'active' => $k == $sMode ? 1 : 0
    );

$_page['css_name'] = 'cache.css';
$_page_cont[$iNameIndex]['page_main_code'] = call_user_func_array($aPages[$sMode]['func'], $aPages[$sMode]['func_params']);

PageCodeAdmin();

function PageCodeClear ()
{
    global $oAdmTemplate, $oCacheUtilities, $aCacheTypes;

    $aChartData = array();
    foreach ($aCacheTypes as $r) {
    	if ('all' == $r['action'])
    		continue;

    	$aChartData[] = array(
			'value' => round($oCacheUtilities->size($r['action']) / 1024, 2),
			'color' => '#' . dechex(rand(0x000000, 0xFFFFFF)),
			'highlight' => '',
			'label' => ch_js_string($r['title'], CH_ESCAPE_STR_APOS),
		);
    }
    $sChartData = json_encode($aChartData);

    $oAdmTemplate->addJsTranslation(array(
    	'_sys_kilobyte'
    ));
	$oAdmTemplate->addJsSystem(array(
		'chart.min.js',
	));

    $s = $oAdmTemplate->parseHtmlByName('cache.html', array(
        'ch_repeat:clear_action' => $aCacheTypes,
        'chart_data' => $sChartData,
    ));

    return DesignBoxAdmin(_t('_adm_txt_cache'), $s, $GLOBALS['aTopItems'], '', 11);
}

function PageCodeEngines ()
{
    ch_import('ChWsbAdminTools');
    $oAdmTools = new ChWsbAdminTools();
    $s = $oAdmTools->GenCommonCode();
    $s .= $oAdmTools->GenCacheEnginesTable();

    return DesignBoxAdmin(_t('_adm_txt_cache'), $s, $GLOBALS['aTopItems'], '', 11);
}

function PageCodeSettings ()
{
    ch_import('ChWsbAdminSettings');
    $oSettings = new ChWsbAdminSettings(24);

    $sResults = false;
    if (isset($_POST['save']) && isset($_POST['cat']))
        $sResult = $oSettings->saveChanges($_POST);

    $s = $oSettings->getForm();
    if ($sResult)
        $s = $sResult . $s;

    return DesignBoxAdmin(_t('_adm_txt_cache'), $s, $GLOBALS['aTopItems'], '', 11);
}
