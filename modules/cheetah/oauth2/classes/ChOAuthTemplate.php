<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import ('ChWsbTwigTemplate');

class ChOAuthTemplate extends ChWsbTwigTemplate
{
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);
    }

    function pageError($sErrorMsg)
    {
        $this->_page(_t('_ch_oauth_authorization'), MsgBox($sErrorMsg));
    }

    function pageAuth($sTitle)
    {
        $this->_page(_t('_ch_oauth_authorization'), $this->parseHtmlByName('page_auth.html', array(
            'text' => _t('_ch_oauth_authorize_app', htmlspecialchars_adv($sTitle)),
            'url' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'auth',
            'client_id' => ch_get('client_id'),
            'response_type' => ch_get('response_type'),
            'redirect_uri' => ch_get('redirect_uri'),
            'state' => ch_get('state'),
        )));
    }

    function _page($sTitle, $sContent)
    {
        global $_page, $_page_cont;

        $this->addCss('main.css');

    	$_page['name_index'] = 0;
        $_page['header'] = $_page['header_text'] = $sTitle;
        $_page_cont[0]['page_main_code'] = $sContent;

        PageCode();
        exit;
    }

}
