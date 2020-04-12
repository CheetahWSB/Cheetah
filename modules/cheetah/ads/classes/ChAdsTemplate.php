<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import ('ChWsbModuleTemplate');

class ChAdsTemplate extends ChWsbModuleTemplate
{
    /*
    * Constructor.
    */
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);

        $this->_aTemplates = array('unit_ads', 'category', 'filter_form', 'ad_of_day', 'wall_outline_extra_info');
    }

    function loadTemplates()
    {
        parent::loadTemplates();
    }

    function parseHtmlByTemplateName($sName, $aVariables, $mixedKeyWrapperHtml = null)
    {
        return $this->parseHtmlByContent($this->_aTemplates[$sName], $aVariables);
    }

    function displayAccessDenied ()
    {
        return MsgBox(_t('_ch_ads_msg_access_denied'));
    }

    function pageCode($aPage = array(), $aPageCont = array(), $aCss = array(), $aJs = array(), $bAdminMode = false, $isSubActions = true)
    {
        if (!empty($aPage)) {
            foreach ($aPage as $sKey => $sValue)
                $GLOBALS['_page'][$sKey] = $sValue;
        }
        if (!empty($aPageCont)) {
            foreach ($aPageCont as $sKey => $sValue)
                $GLOBALS['_page_cont'][$aPage['name_index']][$sKey] = $sValue;
        }
        if (!empty($aCss))
            $this->addCss($aCss);
        if (!empty($aJs))
            $this->addJs($aJs);

        if (!$bAdminMode)
            PageCode($this);
        else
            PageCodeAdmin();
    }

}
