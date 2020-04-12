<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbConnectConfig');

class ChFaceBookConnectConfig extends ChWsbConnectConfig
{
    public $mApiID;
    public $mApiSecret;

    public $sPageReciver;

    public $bAutoFriends;
    public $aFaceBookReqParams;
    public $sFaceBookFields;

    public $sDefaultCountryCode = 'US';

    function __construct($aModule)
    {
        parent::__construct($aModule);

        $this -> mApiID		  = getParam('ch_facebook_connect_api_key');
        $this -> mApiSecret   = getParam('ch_facebook_connect_secret');
        $this -> sPageReciver = CH_WSB_URL_ROOT . $this -> getBaseUri() . 'login_callback';

        $this -> sSessionUid = 'facebook_session';
        $this -> sSessionProfile = 'facebook_session_profile';

        $this -> sEmailTemplatePasswordGenerated = 't_fb_connect_password_generated';
        $this -> sDefaultTitleLangKey = '_ch_facebook';

        $this -> sRedirectPage = getParam('ch_facebook_connect_redirect_page');

        $this -> bAutoFriends = 'on' == getParam('ch_facebook_connect_auto_friends')
            ? true
            : false;

        $this -> aFaceBookReqParams = array(
            'scope' => getParam('ch_facebook_connect_extended_info')
                ? 'email,public_profile,user_friends,user_birthday,user_about_me,user_hometown,user_location'
                : 'email,public_profile',
            'redirect_uri' => $this -> sPageReciver,
        );

        $this -> sFaceBookFields = getParam('ch_facebook_connect_extended_info')
            ? 'name,email,first_name,last_name,gender,birthday,bio,hometown,location'
            : 'name,email,first_name,last_name';
    }
}
