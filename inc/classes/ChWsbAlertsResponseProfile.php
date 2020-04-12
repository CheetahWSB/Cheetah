<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbTags');
ch_import('ChWsbAlerts');
ch_import('ChWsbProfilesController');

class ChWsbAlertsResponseProfile extends ChWsbAlertsResponse
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

    function _processProfileBeforeJoin($oAlert) {}

    function _processProfileJoin($oAlert)
    {
        $oPC = new ChWsbProfilesController();

        //--- reparse profile tags
        $oTags = new ChWsbTags();
        $oTags->reparseObjTags('profile', $oAlert->iObject);

        //--- send new user notification
        if(getParam('newusernotify') == 'on' )
            $oPC->sendNewUserNotify($oAlert->iObject);

        //--- Promotional membership
        if(getParam('enable_promotion_membership') == 'on') {
            $iMemershipDays = getParam('promotion_membership_days');
            setMembership($oAlert->iObject, MEMBERSHIP_ID_PROMOTION, $iMemershipDays, true);
        }
    }

    function _processProfileBeforeLogin($oAlert) {}

    function _processProfileLogin($oAlert) {}

    function _processProfileLogout($oAlert) {}

    function _processProfileEdit ($oAlert)
    {
        //--- reparse profile tags
        $oTags = new ChWsbTags();
        $oTags->reparseObjTags('profile', $oAlert->iObject);
    }

    function _processProfileDelete ($oAlert)
    {
    	$oPC = new ChWsbProfilesController();
    	if(getParam('unregisterusernotify') == 'on' )
    		$oPC->sendUnregisterUserNotify($oAlert->aExtras['profile_info']);
    }
}
