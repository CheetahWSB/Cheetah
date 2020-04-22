<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('../inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'admin_design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'utils.inc.php');
ch_import('ChRSS');
ch_import('ChWsbAdminDashboard');

define('CH_WSB_ADMIN_INDEX', 1);

$bLogged = isLogged();

if (isset($_POST['ID']) && isset($_POST['Password'])) {
    $iId = getID($_POST['ID']);
    $sPassword = process_pass_data($_POST['Password']);

    if (!$bLogged) {
        $oZ = new ChWsbAlerts('profile', 'before_login', 0, 0, array('login' => $iId, 'password' => $sPassword, 'ip' => getVisitorIP()));
        $oZ->alert();
    }

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
        echo check_password($iId, $sPassword, CH_WSB_ROLE_ADMIN, false) ? 'OK' : 'Fail';
    } elseif (check_password($iId, $sPassword, CH_WSB_ROLE_ADMIN)) {
        ch_login($iId, (bool)$_POST['rememberMe']);
        header('Location: ' . $sUrlRelocate);
    }
    exit;
}

if (!isAdmin()) {
    send_headers_page_changed();
    login_form("", 1);
    exit();
}

if (ch_get('cheetah_news') !== false) {
    setParam("news_enable", (int)ch_get('cheetah_news'));
}

$logged['admin'] = member_auth(1, true, true);

if (ch_get('cat') !== false) {
    PageCategoryCode(ch_get('cat'));
} else {
    PageMainCode();
}

PageCodeAdmin();

function PageMainCode()
{
    $oDashboard = new ChWsbAdminDashboard();
    $sResult = $oDashboard->getCode();

    $iNameIndex = 1;
    $GLOBALS['_page'] = array(
        'name_index' => $iNameIndex,
        'css_name' => array('index.css'),
        'header' => _t('_adm_page_cpt_dashboard')
    );

    $GLOBALS['_page_cont'][$iNameIndex]['page_main_code'] = $sResult;
    if (getParam('news_enable') == 'on') {
        $GLOBALS['_page_cont'][$iNameIndex]['page_main_code'] .= DesignBoxAdmin(_t('_adm_box_cpt_cheetah_news'), '
            <div class="RSSAggrCont" rssid="cheetah_news" rssnum="5" member="0">' . $GLOBALS['oFunctions']->loadingBoxInline() . '</div>');
    }

    if (getParam('feeds_enable') == 'on') {
        $GLOBALS['_page_cont'][$iNameIndex]['page_main_code'] .= DesignBoxAdmin(_t('_adm_box_cpt_featured_modules'), '
            <div class="RSSAggrCont" rssid="cheetah_market_featured" rssnum="5" member="0">' . $GLOBALS['oFunctions']->loadingBoxInline() . '</div>');
    }
}

function PageCategoryCode($sCategoryName)
{
    global $oAdmTemplate, $MySQL;

    $aItems = $MySQL->getAll("SELECT `tma1`.`title` AS `title`, `tma1`.`url` AS `url`, `tma1`.`description` AS `description`, `tma1`.`icon` AS `icon`, `tma1`.`check` AS `check`
              FROM `sys_menu_admin` AS `tma1` LEFT JOIN `sys_menu_admin` AS `tma2` ON `tma1`.`parent_id`=`tma2`.`id` WHERE `tma2`.`name`= ? ORDER BY `tma1`.`Order`", [$sCategoryName]);

    foreach ($aItems as $aItem) {
        if (strlen($aItem['check']) > 0) {
            $oFunction = function () use ($aItem) {
                return eval($aItem['check']);
            };

            if (!$oFunction()) {
                continue;
            }
        }

        $aItem['url'] = str_replace(array('{siteUrl}', '{siteAdminUrl}'), array(CH_WSB_URL_ROOT, CH_WSB_URL_ADMIN), $aItem['url']);
        list($sLink, $sOnClick) = ChWsbAdminMenu::getMainMenuLink($aItem['url']);

        $aVariables[] = array(
            'ch_if:icon' => array(
                'condition' => false !== strpos($aItem['icon'], '.'),
                'content' => array(
                    'icon' => $oAdmTemplate->getIconUrl($aItem['icon'])
                )
            ),
            'ch_if:texticon' => array(
                'condition' => false === strpos($aItem['icon'], '.'),
                'content' => array(
                    'icon' => $aItem['icon']
                )
            ),
            'link' => $sLink,
            'onclick' => $sOnClick,
            'title' => _t($aItem['title']),
            'description' => $aItem['description']
        );
    }

    $iNameIndex = 0;
    $sPageTitle = _t($MySQL->getOne("SELECT `title` FROM `sys_menu_admin` WHERE `name`='" . $sCategoryName . "' LIMIT 1"));
    $sPageContent = $oAdmTemplate->parseHtmlByName('categories.html', array('ch_repeat:items' => $aVariables));

    $GLOBALS['_page'] = array(
        'name_index' => $iNameIndex,
        'css_name' => array('index.css'),
        'header' => $sPageTitle,
        'header_text' => $sPageTitle
    );
    $GLOBALS['_page_cont'][$iNameIndex]['page_main_code'] = $sPageContent;
}
