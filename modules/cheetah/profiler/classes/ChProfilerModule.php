<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModule');

/**
 * Profiler module by Cheetah
 *
 * This module estimate timining, like page openings, mysql queries execution and service calls.
 * Also it can log too long queries, so you can later investigate these bottle necks and speedup whole script.
 *
 * To enable profiler you need install it and add the following lines to the beginning of inc/header.inc.php file
 *
 * define ('CH_PROFILER', true);
 * if (CH_PROFILER && !isset($GLOBALS['ch_profiler_start']))
 *     $GLOBALS['ch_profiler_start'] = microtime ();
 *
 */
class ChProfilerModule extends ChWsbModule
{
    function __construct(&$aModule)
    {
        parent::__construct($aModule);
    }

    function actionHome ()
    {
        $this->_oTemplate->pageStart();
        echo $this->_aModule['title'];
        $this->_oTemplate->pageCode($this->_aModule['title']);
    }

    function actionAdministration ()
    {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $iId = $this->_oDb->getSettingsCategory();
        if(empty($iId)) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_ch_profiler_administration'));
            return;
        }

        ch_import('ChWsbAdminSettings');

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) {
            $oSettings = new ChWsbAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new ChWsbAdminSettings($iId);
        $sResult = $oSettings->getForm();

        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;

        $aVars = array (
            'content' => $sResult,
        );
        echo $this->_oTemplate->adminBlock ($this->_oTemplate->parseHtmlByName('default_padding', $aVars), _t('_ch_profiler_administration'));

        $this->_oTemplate->addCssAdmin ('main.css');
        $this->_oTemplate->addCssAdmin ('forms_adv.css');
        $this->_oTemplate->pageCodeAdmin (_t('_ch_profiler_administration'));
    }

    function isAdmin ()
    {
        return $GLOBALS['logged']['admin'];
    }
}
