<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    ch_import('ChWsbCron');
    require_once('ChShoutBoxModule.php');

    class ChShoutBoxCron extends ChWsbCron
    {
        var $oModule;
        var $iLifeTime;

        /**
         * Class constructor;
         */
        function __construct()
        {
            $this -> oModule     = ChWsbModule::getInstance('ChShoutBoxModule');
            $this -> iLifeTime   = $this -> oModule -> _oConfig -> iLifeTime;
        }

        /**
         * Function will delete all old data;
         */
        function processing()
        {
            $this -> oModule -> _oDb -> deleteOldMessages($this -> iLifeTime);
        }
    }
