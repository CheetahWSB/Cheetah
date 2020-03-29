<?php

/**
 * This work, "Cheetah - https://cheetah.deanbassett.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_MODULES . 'cheetah/payment/classes/ChPmtOrders.php');

class ChPfwOrders extends ChPmtOrders
{
    /*
     * Constructor.
     */
    function __construct($iUserId, &$oDb, &$oConfig, &$oTemplate)
    {
    	parent::__construct($iUserId, $oDb, $oConfig, $oTemplate);
    }
}
