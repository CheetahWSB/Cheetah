<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbConfig.php');

    class ChSpyConfig extends ChWsbConfig
    {
        var $_sAlertSystemName;
        var $iPerPage;
        var $iUpdateTime;
        var $iDaysForRows;

        var $iSpeedToggleUp;
        var $iSpeedToggleDown;
        var $iMemberMenuNotifyCount = 5;
        var $bTrackGuestsActivites;

        /**
         * Class constructor;
         */
        function __construct($aModule)
        {
            parent::__construct($aModule);
            $this -> iUpdateTime      = getParam('ch_spy_update_time');
            $this -> iDaysForRows     = getParam('ch_spy_keep_rows_days');
            $this -> iSpeedToggleUp   = getParam('ch_spy_toggle_up');
            $this -> iSpeedToggleDown = getParam('ch_spy_toggle_down');
            $this -> iPerPage         = getParam('ch_spy_per_page');
            $this -> _sAlertSystemName = 'ch_spy_content_activity';
            $this -> bTrackGuestsActivites = getParam('ch_spy_guest_allow') ? true : false;
        }

        function getAlertSystemName()
        {
            return $this -> _sAlertSystemName;
        }
    }
