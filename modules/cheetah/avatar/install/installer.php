<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbInstaller');

class ChAvaInstaller extends ChWsbInstaller
{
    function __construct($aConfig)
    {
        parent::__construct($aConfig);
    }

    function install($aParams)
    {
        $aResult = parent::install($aParams);

        if($aResult['result'] && ChWsbRequest::serviceExists('wall', 'update_handlers'))
            ChWsbService::call('wall', 'update_handlers', array($this->_aConfig['home_uri'], true));

        return $aResult;
    }

    function uninstall($aParams)
    {
        if(ChWsbRequest::serviceExists('wall', 'update_handlers'))
            ChWsbService::call('wall', 'update_handlers', array($this->_aConfig['home_uri'], false));

        $aResult = parent::uninstall($aParams);

        if ($aResult['result']) {
            foreach ($this->_aConfig['install_permissions']['writable'] as $sDir) {
                $sPath = CH_DIRECTORY_PATH_MODULES . $this->_aConfig['home_dir'] . $sDir;
                if (is_dir($sPath))
                    ch_clear_folder($sPath);
            }
            ch_import('ChWsbCacheUtilities');
            $oCacheUtilities = new ChWsbCacheUtilities();
            $oCacheUtilities->clear('users');
        }
        return $aResult;
    }
}
