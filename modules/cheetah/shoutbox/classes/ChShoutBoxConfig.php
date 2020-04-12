<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbConfig.php');

    class ChShoutBoxConfig extends ChWsbConfig
    {
        // contain Db table's name ;
        var $sTablePrefix;
        var $iLifeTime;

        var $iUpdateTime;
        var $iAllowedMessagesCount;

        /**
         * Class constructor;
         */
        function __construct($aModule)
        {
            parent::__construct($aModule);

            // define the tables prefix ;
            $this -> sTablePrefix 			= $this -> getDbPrefix();
            $this -> iLifeTime 				= (int) getParam('shoutbox_clean_oldest'); //in seconds

            $this -> iUpdateTime            = (int) getParam('shoutbox_update_time'); //(in milliseconds)
            $this -> iAllowedMessagesCount  = (int) getParam('shoutbox_allowed_messages');

            $this -> iBlockExpirationSec   = (int) getParam('shoutbox_block_sec'); //in seconds
        }
    }
