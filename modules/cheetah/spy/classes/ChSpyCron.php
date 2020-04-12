<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    ch_import('ChWsbCron');

    require_once('ChSpyModule.php');

    class ChSpyCron extends ChWsbCron
    {
        var $oSpyObject;
        var $iDaysForRows;

        /**
         * Class constructor;
         */
        function __construct()
        {
            $this -> oSpyObject = ChWsbModule::getInstance('ChSpyModule');
            $this -> iDaysForRows = $this -> oSpyObject -> _oConfig -> iDaysForRows;
        }

        /**
         * Function will delete all unnecessary events;
         */
        function processing()
        {
            if ($this -> iDaysForRows > 0) {
                $this -> oSpyObject -> _oDb -> deleteUselessData($this -> iDaysForRows);
            }
        }
    }
