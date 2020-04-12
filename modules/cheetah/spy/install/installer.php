<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    require_once(CH_DIRECTORY_PATH_CLASSES . "ChWsbInstaller.php");

    class ChSpyInstaller extends ChWsbInstaller
    {
        function __construct(&$aConfig)
        {
            parent::__construct($aConfig);
        }

        function install($aParams)
        {
            $aResult = parent::install($aParams);

            if($aResult['result']) {
                ChWsbService::call($this->_aConfig['home_uri'], 'update_handlers');
            }

            return $aResult;
        }
    }
