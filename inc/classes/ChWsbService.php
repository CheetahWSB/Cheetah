<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbRequest.php');
require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbModuleDb.php');

/**
 * Service calls to modules' methods.
 *
 * The class has one static method is needed to make service calls
 * to module's methods from the Cheetah's core or the other modules.
 *
 *
 * Example of usage:
 * ChWsbService::call('payment', 'get_add_to_cart_link', array($iVendorId, $mixedModuleId, $iItemId, $iItemCount));
 *
 *
 * Memberships/ACL:
 * Doesn't depend on user's membership.
 *
 *
 * Alerts:
 * no alerts available
 *
 */
class ChWsbService
{
    public static function call($mixed, $sMethod, $aParams = array(), $sClass = 'Module')
    {
        $oDb = new ChWsbModuleDb();

        if(is_string($mixed))
            $aModule = $oDb->getModuleByUri($mixed);
        else
            $aModule = $oDb->getModuleById($mixed);

        return empty($aModule) ? '' : ChWsbRequest::processAsService($aModule, $sMethod, $aParams, $sClass);
    }

    public static function callArray($a)
    {
        if (!isset($a['module']) || !isset($a['method']))
            return false;

        return self::call($a['module'], $a['method'], isset($a['params']) ? $a['params'] : array(), isset($a['class']) ? $a['class'] : 'Module');
    }

}
