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

ch_import('ChWsbModule');

class ChDolphinImporterModule extends ChWsbModule
{
    public $sModuleUrl;
    public $sModulePath;

    public function __construct(&$aModule)
    {
        parent::__construct($aModule);
        $this->sModulePath = CH_DIRECTORY_PATH_MODULES . $aModule['path'];
        $this->sModuleUrl = CH_DOL_URL_ROOT . $this->_oConfig->getBaseUri();
    }

    /*
    function actionHome () {
    $this->_oTemplate->pageStart();

    $sDateFormat = getParam ('me_blgg_date_format');
    $isShowUserTime = getParam('me_blgg_enable_js_date') ? true : false;
    $aVars = array (
    'server_time' => date($sDateFormat),
    'bx_if:show_user_time' => array(
    'condition' => $isShowUserTime,
    'content' => array(),
    ),
    );
    echo $this->_oTemplate->parseHtmlByName('main', $aVars);
    $this->_oTemplate->pageCode(_t('_cheetah_dolphin_importer'), true);
    }
    */

    public function actionAdministration()
    {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied();
            return;
        }

        $this->_oTemplate->pageStart();

        $iId = $this->_oDb->getSettingsCategory();
        if (empty($iId)) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin(_t('_cheetah_dolphin_importer'));
            return;
        }

        ch_import('ChWsbAdminSettings');

        $mixedResult = '';
        if (isset($_POST['save']) && isset($_POST['cat'])) {
            $oSettings = new ChWsbAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new ChWsbAdminSettings($iId);
        $sResult = $oSettings->getForm();

        if ($mixedResult !== true && !empty($mixedResult)) {
            $sResult = $mixedResult . $sResult;
        }


        $sResult = '<div class="ch-def-bc-margin">' . $sResult . '</div>';

        // If a menu is needed, uncomment this section and remove the line below it.
        $aMenu = array(
            'add_unit' => array(
                'title' => _t('_dbcs_HI_AddNew'),
                'href' => CH_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/&section=add',
                'active' => 0
            ),
            'show_list' => array(
                'title' => _t('_dbcs_HI_List'),
                'href' => CH_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/',
                'active' => 0
            )
        );
        echo DesignBoxAdmin(_t('_cheetah_dolphin_importer'), $sResult, $aMenu);

        //echo DesignBoxAdmin(_t('_cheetah_dolphin_importer'), $sResult);


        $this->_oTemplate->pageCodeAdmin(_t('_cheetah_dolphin_importer'));
    }
}
