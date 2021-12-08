<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbAlerts');

class ChWsbAlertsResponseSystem extends ChWsbAlertsResponse
{
    function __construct()
    {
        parent::__construct();
    }

    function response($oAlert)
    {
        $sMethodName = '_process' . ucfirst($oAlert->sUnit) . str_replace(' ', '', ucwords(str_replace('_', ' ', $oAlert->sAction)));
        if(method_exists($this, $sMethodName))
            $this->$sMethodName($oAlert);
    }

    function _processSystemBegin($oAlert) {}

    function _processSystemDesignBeforeOutput($oAlert) {
        // Start Two factor auth check.
        // See if the admin has two factor auth turned on.
        if (getParam('two_factor_auth')) {
            $iMemberID = getLoggedId();
            // NOTE: The cookie for two factor auth will have a very long expire date, but will be deleted when a normal login is performed
            // such as when the normal login cookie expires. This allows for 2fa to be performed only after a login
            // or when a member deletes their browser cookies.
            $bTwoFactorAuthCheckDone = (int)$_COOKIE['tfa'] ? true : false;
            // See if member is logged in and has not done the two factor auth check.
            if((int)$iMemberID && !$bTwoFactorAuthChecked) {
                // See if the admin has two factor auth required for all members.
                if (getParam('two_factor_auth_required')) {
                    // See if current member has two factor auth setup.
                    // If not setup, setup will have to be performed first.




                } else {
                  // See if this member has two factor auth turned on.


                  // See if current member has two factor auth setup.
                  // If not setup, setup will have to be performed first.



                }
            }
        }
        // End Two factor auth check.
    }

}
