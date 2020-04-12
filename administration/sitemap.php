<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../inc/header.inc.php' );

$GLOBALS['iAdminPage'] = 1;

require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin.inc.php' );

ch_import('ChWsbSiteMaps');

class ChWsbAdmFormSitemap extends ChTemplFormView
{
    function __construct ()
    {
        $aCustomForm = array(

            'form_attrs' => array(
                'id' => 'sys-adm-sitemap',
                'name' => 'sys-adm-sitemap',
                'method' => 'post',
            ),

            'inputs' => array(

                'sys_sitemap_generated' => array(
                    'type' => 'custom',
                    'content' => mb_strlen(@file_get_contents(ChWsbSiteMaps::getSiteMapIndexPath())) > 32 ? '<span class="sys-adm-enabled">' . _t('_Yes') . '</span>' : '<span class="sys-adm-disabled">' . _t('_No') . '</span>',
                    'caption' => _t('_sys_sitemap_form_caption_sitemap_generated'),
                ),

                'sys_sitemap_enable' => array(
                    'type' => 'checkbox',
                    'name' => 'sys_sitemap_enable',
                    'value' => 'on',
                    'checked' => 'on' == getParam('sys_sitemap_enable') ? true : false,
                    'caption' => _t('_sys_sitemap_form_caption'),
                ),

                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'sitemap_enable',
                    'value' => _t('_Submit'),
                ),
            ),
        );

        parent::__construct ($aCustomForm);
    }
}

$logged['admin'] = member_auth(1, true, true);

// process actions
if (isset($_POST['sitemap_enable'])) {
    setParam('sys_sitemap_enable', $_POST['sys_sitemap_enable'] ? 'on' : '');
    ChWsbSiteMaps::generateAllSiteMaps();
}

$iNameIndex = 9;

$sPageTitle = _t('_sys_sitemap');
$_page_cont[$iNameIndex]['page_main_code'] = PageCodeSitemap();

$_page = array(
    'name_index' => $iNameIndex,
    'header' => $sPageTitle,
    'header_text' => $sPageTitle,
    'css_name' => array('forms_adv.css'),
);

PageCodeAdmin();

function PageCodeSitemap()
{
    global $aPages;

    $oForm = new ChWsbAdmFormSitemap();
    $aList = ChWsbSiteMapsQuery::getAllActiveSystemsFromCache();
    $aListForTemplate = array();
    foreach ($aList as $i => $a)
        $aListForTemplate[] = array('title_translated' => _t($a['title']));

    $s = $GLOBALS['oAdmTemplate']->parseHtmlByName('sitemap.html', array(
        'form' => $oForm->getCode(),
        'desc' => _t('_sys_sitemap_desc', ChWsbSiteMaps::getSiteMapIndexUrl()),
        'ch_repeat:list' => $aListForTemplate,
    ));

    return DesignBoxAdmin ($GLOBALS['sPageTitle'], $s);
}
