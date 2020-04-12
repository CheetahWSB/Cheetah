<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'images.inc.php' );

$logged['admin'] = member_auth( 1, true, true );

ch_import('ChWsbAdminBuilder');
class ChWsbAdminMobileBuilder extends ChWsbAdminBuilder
{
    var $_sPage;

    function __construct ($sPage)
    {
        parent::__construct(
            '`sys_menu_mobile`',
            CH_WSB_URL_ADMIN . 'mobileBuilder.php',
            array (
                '1' => _t('_adm_mobile_builder_cont_active'),
                '0' => _t('_adm_mobile_builder_cont_inactive'),
            ));
        $this->_sPage = process_db_input($sPage);
    }

    function getItemsForContainer ($sKey)
    {
        global $MySQL;
        return $MySQL->getAll("SELECT * FROM `sys_menu_mobile` WHERE `page` = '" . $this->_sPage . "' AND `active` = ? ORDER BY `order`", [$sKey]);
    }

    function getItem ($aItem)
    {
        $a = array (
            'content' => _t($aItem['title']),
        );
        return $GLOBALS['oAdmTemplate']->parseHtmlByName('mobile_builder_box.html', $a);
    }

    function addExternalResources ()
    {
        parent::addExternalResources ();
        $GLOBALS['oAdmTemplate']->addCss(array(
            'pageBuilder.css',
            'forms_adv.css',
        ));
    }

    function getBuilderPage ()
    {
        $aPagesForTemplate = array (
            array(
                'value' => '',
                'title' => _t('_adm_txt_pb_select_page'),
                'selected' => empty($this->_sPage) ? 'selected="selected"' : ''
            )
        );

        $aPages = $this->_getPages();
        foreach ($aPages as $r)
            $aPagesForTemplate[] = array(
                'value' => $r['page'],
                'title' => htmlspecialchars_adv(_t($r['title'])),
                'selected' => $r['page'] == $this->_sPage ? 'selected="selected"' : '',
            );

        $sPagesSelector = $GLOBALS['oAdmTemplate']->parseHtmlByName('mobile_builder_pages_selector.html', array(
            'ch_repeat:pages' => $aPagesForTemplate,
            'url' => ch_html_attribute(CH_WSB_URL_ADMIN . 'mobileBuilder.php'),
        ));

        $sPagesSelector = $GLOBALS['oAdmTemplate']->parseHtmlByName('designbox_top_controls.html', array(
            'top_controls' => $sPagesSelector
        ));

        if (empty($this->_sPage))
            $this->addExternalResources ();

        return $sPagesSelector . (!empty($this->_sPage) ? parent::getBuilderPage () : MsgBox(_t('_Empty')));
    }

    function _getPages()
    {
        global $MySQL;
        return $MySQL->getAll("SELECT * FROM `sys_menu_mobile_pages` ORDER BY `order`");
    }
}

$oAdminMobileBuilder = new ChWsbAdminMobileBuilder (ch_get('page'));

if (0 === strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
    $oAdminMobileBuilder->handlePostActions($_POST);
    exit;
}

$sPageContent = $oAdminMobileBuilder->getBuilderPage();

$iNameIndex = 0;
$_page = array(
    'name_index' => $iNameIndex,
    'header' => _t('_adm_mobile_builder_title'),
    'header_text' => _t('_adm_mobile_builder_title'),
);
$_page_cont[$iNameIndex]['page_main_code'] = $sPageContent;

PageCodeAdmin();
