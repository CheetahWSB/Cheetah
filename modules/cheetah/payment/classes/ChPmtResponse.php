<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbAlerts.php');

class ChPmtResponse extends ChWsbAlertsResponse
{
    var $_oModule;

    /**
     * Constructor
     * @param ChWallModule $oModule - an instance of current module
     */
    function __construct($oModule)
    {
        parent::__construct();

        $this->_oModule = $oModule;
    }
    /**
     * Overwtire the method of parent class.
     *
     * @param ChWsbAlerts $oAlert an instance of alert.
     */
    function response($oAlert)
    {

    }
}
