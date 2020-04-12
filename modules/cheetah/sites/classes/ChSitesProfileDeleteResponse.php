<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbAlerts');

class ChSitesProfileDeleteResponse extends ChWsbAlertsResponse
{
    function response ($oTag)
    {
        if (!($iProfileId = (int)$oTag->iObject))
            return;

        if (!defined('CH_SITES_ON_PROFILE_DELETE'))
            define ('CH_SITES_ON_PROFILE_DELETE', 1);

        $_GET['r'] = 'sites/delete_profile_sites/' . $iProfileId;
        chdir(CH_DIRECTORY_PATH_MODULES);
        include(CH_DIRECTORY_PATH_MODULES . 'index.php');
    }
}
