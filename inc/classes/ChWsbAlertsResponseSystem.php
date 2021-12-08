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
      // Start Maintenance mode Check
      if (getParam('sys_maint_mode_enabled')) {
    		if((!getParam('sys_maint_mode_admin')) || (getParam('sys_maint_mode_admin') && !isAdmin((int)$_COOKIE['memberID']))) {
    			if ($oAlert->aExtras['_page']['header'] != 'Maintenance') {
    				header('Location: ' . CH_WSB_URL_ROOT . 'site_maintenance.php');
    			}
    		}
    	}
      // End Maintenance mode Check
    }
}
