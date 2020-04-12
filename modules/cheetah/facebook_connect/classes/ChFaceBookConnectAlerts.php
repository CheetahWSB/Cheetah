<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbConnectAlerts');

class ChFaceBookConnectAlerts extends ChWsbConnectAlerts
{
    function __construct()
    {
        parent::__construct();
        $this -> oModule = ChWsbModule::getInstance('ChFaceBookConnectModule');
    }
}
