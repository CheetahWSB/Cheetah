<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    class ChSimpleMessengerResponse extends ChWsbAlertsResponse
    {
        function response($o)
        {
            if ( $o -> sUnit == 'profile' ) {
                switch ( $o -> sAction ) {
                    case 'delete' :
                       $oModule = ChWsbModule::getInstance('ChSimpleMessengerModule');
                       $oModule -> _oDb -> deleteAllMessagesHistory($o -> iObject);
                    break;
                }
            }
        }
    }
