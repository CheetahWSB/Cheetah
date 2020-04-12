<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbConnectConfig');

class ChDolphConConfig extends ChWsbConnectConfig
{
    var $sApiID;
    var $sApiSecret;
    var $sApiUrl;

    var $sPageStart;
    var $sPageHandle;

    var $sScope = 'basic';

    function __construct($aModule)
    {
        parent::__construct($aModule);

        $this -> sApiID = getParam('ch_dolphcon_api_key');
        $this -> sApiSecret = getParam('ch_dolphcon_connect_secret');
        $this -> sApiUrl = trim(getParam('ch_dolphcon_connect_url'), '/') . (getParam('ch_dolphcon_connect_url_rewrite') ? '/m/oauth2/' : '/modules/?r=oauth2/');

        $this -> sSessionUid = 'dolphcon_session';
        $this -> sSessionProfile = 'dolphcon_session_profile';

        $this -> sEmailTemplatePasswordGenerated = 't_ch_dolphcon_password_generated';
        $this -> sDefaultTitleLangKey = '_ch_dolphcon';

        $this -> sPageStart = CH_WSB_URL_ROOT . $this -> getBaseUri() . 'start';
        $this -> sPageHandle = CH_WSB_URL_ROOT . $this -> getBaseUri() . 'handle';

        $this -> sRedirectPage = getParam('ch_dolphcon_connect_redirect_page');
    }
}
