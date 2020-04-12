<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModuleTemplate');

class ChBlogsTemplate extends ChWsbModuleTemplate
{
    /*
    * Constructor.
    */
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);

        $this->_aTemplates = array('blog_unit', 'blog', 'blogpost_unit', 'admin_page', 'blogpost_unit_mobile', 'browse_unit_private_mobile');
    }

    function loadTemplates()
    {
        parent::loadTemplates();
    }

    function parseHtmlByTemplateName($sName, $aVariables, $mixedKeyWrapperHtml = null)
    {
        return $this->parseHtmlByContent($this->_aTemplates[$sName], $aVariables);
    }

    function displayAccessDenied()
    {
        return MsgBox(_t('_ch_blog_msg_access_denied'));
    }
}
