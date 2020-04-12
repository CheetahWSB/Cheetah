<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'db.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'prof.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'languages.inc.php');

ch_import('ChWsbPermalinks');
ch_import('ChWsbTemplateAdmin');
ch_import('ChWsbAdminMenu');

$oAdmTemplate = new ChWsbTemplateAdmin($admin_dir);
$oAdmTemplate->init();
$oAdmTemplate->addCss(array(
    'default.css',
    'common.css',
    'general.css',
    'anchor.css',
    'icons.css',
    'colors.css',
    'loading.css'
));
$oAdmTemplate->addJs(array(
    'jquery.js',
    'jquery-migrate.min.js',
    'jquery.ui.position.min.js',
    'jquery.form.min.js',
    'jquery.webForms.js',
    'jquery.dolPopup.js',
    'jquery.dolRetina.js',
    'jquery.float_info.js',
    'jquery.jfeed.js',
    'jquery.dolRSSFeed.js',
    'common_anim.js',
    'functions.js',
    'functions.admin.js'
));

function PageCodeAdmin($oTemplate = null)
{
    chPageCodeAdmin($oTemplate);
}

function DesignBoxAdmin($sTitle, $sContent, $mixedTopItems = '', $sBottomItems = '', $iIndex = 1)
{
    if (is_array($mixedTopItems)) {
        $bFirst = true;
        $mixedButtons = array();
        foreach ($mixedTopItems as $sId => $aAction) {
            $mixedButtons[] = array(
                'id' => $sId,
                'title' => htmlspecialchars_adv(_t($aAction['title'])),
                'class' => isset($aAction['class']) ? ' class="' . $aAction['class'] . '"' : '',
                'icon' => isset($aAction['icon']) ? $GLOBALS['oFunctions']->sysImage($aAction['icon']) : '',
                'href' => isset($aAction['href']) ? ' href="' . htmlspecialchars_adv($aAction['href']) . '"' : '',
                'target' => isset($aAction['target']) ? ' target="' . $aAction['target'] . '"' : '',
                'on_click' => isset($aAction['onclick']) ? ' onclick="' . $aAction['onclick'] . '"' : '',
                'ch_if:hide_active' => array(
                    'condition' => !isset($aAction['active']) || $aAction['active'] != 1,
                    'content' => array()
                ),
                'ch_if:hide_inactive' => array(
                    'condition' => isset($aAction['active']) && $aAction['active'] == 1,
                    'content' => array()
                ),
                'ch_if:show_bullet' => array(
                    'condition' => !$bFirst,
                    'content' => array()
                )
            );

            $bFirst = false;
        }
    } else {
        $mixedButtons = $mixedTopItems;
    }

    return $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_' . (int) $iIndex . '.html', array(
        'title' => $sTitle,
        'ch_repeat:actions' => $mixedButtons,
        'content' => $sContent,
        'bottom_items' => $sBottomItems
    ));
}
function LoginFormAdmin()
{
    global $_page, $_page_cont, $oAdmTemplate;

    $sUrlRelocate = ch_get('relocate');
    if (empty($sUrlRelocate) || basename($sUrlRelocate) == 'index.php') {
        $sUrlRelocate = '';
    }

    $iNameIndex = 2;
    $_page = array(
        'name_index' => $iNameIndex,
        'css_name' => '',
        'header' => _t('_adm_page_cpt_login')
    );

    $bLicense = getParam('license_code') != '';
    $sLicenseData = getParam('license_keydata');
    if ($sLicenseData != '') {
        $aLicenseData = chJsonDecode($sLicenseData);
    }
    //$bFooter = getParam('enable_cheetah_footer') == 'on';

    $_page_cont[$iNameIndex]['page_main_code'] = $oAdmTemplate->parseHtmlByName('login.html', array(
        'action_url' => $GLOBALS['site']['url_admin'] . 'index.php',
        'relocate_url' => ch_html_attribute($sUrlRelocate),
        'ch_if:show_unregistered' => array(
            'condition' => (int)$aLicenseData['active'] == 0,
            'content' => array()
        )
    ));

    $oAdmTemplate->addCss(array(
        'forms_adv.css',
        'login.css',
        'login_phone.css'
    ));
    $oAdmTemplate->addJs(array(
        'login.js'
    ));
    PageCodeAdmin();
}

function adm_hosting_promo()
{
    if (getParam('feeds_enable') != 'on') {
        return '';
    }

    return DesignBoxAdmin(_t('_adm_txt_hosting_title'), $GLOBALS['oAdmTemplate']->parseHtmlByName('hosting_promo.html', array()), '', '', 11);
}
