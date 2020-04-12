<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbInstaller');

class ChH5avInstaller extends ChWsbInstaller
{

    function __construct($aConfig)
    {
        parent::__construct($aConfig);
    }

    function install($aParams)
    {
        return parent::install($aParams);
    }

    function uninstall($aParams)
    {
        $aResult = parent::uninstall($aParams);
        return $aResult;
    }

}
