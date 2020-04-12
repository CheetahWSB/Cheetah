<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbConfig');

class ChWsbConnectConfig extends ChWsbConfig
{
    public $sDefaultRedirectUrl;
    public $sRedirectPage;

    public $sSessionKey;
    public $sSessionUid;
    public $sSessionProfile;

    public $sEmailTemplatePasswordGenerated;
    public $sDefaultTitleLangKey;

    function __construct($aModule)
    {
        parent::__construct($aModule);

        $this->sDefaultRedirectUrl = CH_WSB_URL_ROOT . 'member.php';
    }
}
