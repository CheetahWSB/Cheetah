<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModuleTemplate');

/*
 * Quotes module View
 */
class ChQuotesTemplate extends ChWsbModuleTemplate
{
    /**
    * Constructor
    */
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);

        $this->_aTemplates = array('unit', 'adm_unit');
    }

    function loadTemplates()
    {
        parent::loadTemplates();
    }

    function parseHtmlByName ($sName, $aVars, $mixedKeyWrapperHtml = NULL, $sCheckIn = CH_WSB_TEMPLATE_CHECK_IN_BOTH)
    {
        return parent::parseHtmlByName ($sName.'.html', $aVars, $mixedKeyWrapperHtml, $sCheckIn);
    }
}
