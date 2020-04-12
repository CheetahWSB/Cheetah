<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

class ChWsbConnectAlerts extends ChWsbAlertsResponse
{
    var $oModule;

    function response($oAlert)
    {
        if($o->sUnit == 'system') {
            switch($o->sAction) {
                case 'join_after_payment';
                    $this->oModule->processJoinAfterPayment($o);
                    break;
            }
        }

        if ( $o -> sUnit == 'profile' ) {
            switch ( $o -> sAction ) {
                case 'join':
                        ch_import('ChWsbSession');
                        $oSession = ChWsbSession::getInstance();

                        $iRemoteProfileId = $oSession -> getValue($this -> oModule -> _oConfig -> sSessionUid);

                        if($iRemoteProfileId) {
                            $oSession -> unsetValue($this -> oModule -> _oConfig -> sSessionUid);

                            // save remote profile id
                            $this -> oModule -> _oDb -> saveRemoteId($o -> iObject, $iRemoteProfileId);
                        }
                    break;

                case 'delete':
                    // remove remote account
                    $this -> oModule -> _oDb -> deleteRemoteAccount($o -> iObject);
                    break;
            }
        }
    }
}
