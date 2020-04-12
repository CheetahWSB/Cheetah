<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

class ChPollResponse extends ChWsbAlertsResponse
{
    function response($o)
    {
        if($o->sUnit == 'profile') {
            switch($o->sAction) {
                case 'delete':
                    $oPoll = ChWsbModule::getInstance('ChPollModule');

                    $aPolls = $oPoll->_oDb->getAllPolls(null, $o->iObject);
                    foreach($aPolls as $aPoll)
                        $oPoll->deletePoll($aPoll['id_poll']);
                    break;
            }
        }
    }
}
