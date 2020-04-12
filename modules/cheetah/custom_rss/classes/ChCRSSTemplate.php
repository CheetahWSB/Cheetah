<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModuleTemplate');

class ChCRSSTemplate extends ChWsbModuleTemplate
{
    /*
    * Constructor.
    */
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);

        $this->_aTemplates = array('crss_unit', 'view', 'member_rss_list_loaded');
    }

    function loadTemplates()
    {
        parent::loadTemplates();
    }

    function parseHtmlByTemplateName($sName, $aVariables, $mixedKeyWrapperHtml = null)
    {
        return $this->parseHtmlByContent($this->_aTemplates[$sName], $aVariables);
    }
}
