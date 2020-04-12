<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbConfig.php');

    class ChSimpleMessengerConfig extends ChWsbConfig
    {
        // contain Db table's name ;
        var $sTablePrefix;
        var $iUpdateTime;
        var $iVisibleMessages;
        var $iCountRetMessages;
        var $iCountAllowedChatBoxes;
        var $sOutputBlock;
        var $sOutputBlockPrefix;
        var $bSaveChatHistory;
        var $iBlinkCounter;
        var $sMessageDateFormat;

        /**
         * Class constructor;
         */
        function __construct( $aModule )
        {
            parent::__construct($aModule);

            // define the tables prefix ;
            $this -> sTablePrefix = $this -> getDbPrefix();

            // time (in seconds) script checks for messages ;
            $this -> iUpdateTime       = getParam('simple_messenger_update_time');

            // number of visible messages into chat box ;
            $this -> iVisibleMessages  = getParam('simple_messenger_visible_messages');

            // limit of returning messages in message box;
            $this -> iCountRetMessages = 10;

            // flashing signals amount of the non-active window ;
            $this -> iBlinkCounter = getParam('simple_messenger_blink_counter');

            // save messenger's chat history ;
            $this -> bSaveChatHistory = false;

            // contains block's id where the list of messages will be generated ;
            $this -> sOutputBlock = 'extra_area';

            // contain history block's prefix (need for defines the last message);
            $this -> sOutputBlockPrefix = 'messages_history_';

            // number of allowed chat boxes;
            $this -> iCountAllowedChatBoxes  = getParam('simple_messenger_allowed_chatbox');

            $this -> sMessageDateFormat = getLocaleFormat(CH_WSB_LOCALE_DATE, CH_WSB_LOCALE_DB);
        }
    }
