<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModuleTemplate');

class ChWsbConnectTemplate extends ChWsbModuleTemplate
{
    protected $_sPageIcon;

    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);
    }

    /**
     * Function will generate default cheetah's page;
     *
     * @param  : $sPageCaption   (string) - page's title;
     * @param  : $sPageContent   (string) - page's content;
     * @return : (text) html presentation data;
     */
    function getPage($sPageCaption, $sPageContent)
    {
        global $_page;
        global $_page_cont;

        $_page['name_index'] = 0;

        // set module's icon;
        $GLOBALS['oTopMenu'] -> setCustomSubIconUrl(false === strpos($this->_sPageIcon, '.') ? $this->_sPageIcon : $this -> getIconUrl($this->_sPageIcon));
        $GLOBALS['oTopMenu'] -> setCustomSubHeader($sPageCaption);

        $_page['header'] = $sPageCaption;
        $_page['header_text'] = $sPageCaption;

        $_page_cont[0]['page_main_code'] = $sPageContent;

        PageCode($this);

        exit;
    }
}
