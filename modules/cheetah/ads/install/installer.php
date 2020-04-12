<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . "ChWsbInstaller.php");

class ChAdsInstaller extends ChWsbInstaller
{
    function __construct($aConfig)
    {
        parent::__construct($aConfig);
    }

    function install($aParams)
    {
        $aResult = parent::install($aParams);

        if (!$aResult['result'])
            return $aResult;

        if (ChWsbRequest::serviceExists('wall', 'update_handlers'))
            ChWsbService::call('wall', 'update_handlers', array($this->_aConfig['home_uri'], true));

        if (ChWsbRequest::serviceExists('spy', 'update_handlers'))
            ChWsbService::call('spy', 'update_handlers', array($this->_aConfig['home_uri'], true));

        ChWsbService::call('ads', 'map_install');

        return $aResult;
    }

    function uninstall($aParams)
    {
        if (ChWsbRequest::serviceExists('wall', 'update_handlers'))
            ChWsbService::call('wall', 'update_handlers', array($this->_aConfig['home_uri'], false));

        if (ChWsbRequest::serviceExists('spy', 'update_handlers'))
            ChWsbService::call('spy', 'update_handlers', array($this->_aConfig['home_uri'], false));

        $aResult = parent::uninstall($aParams);

        if ($aResult['result'] && ChWsbModule::getInstance('ChWmapModule'))
            ChWsbService::call('wmap', 'part_uninstall', array('ads'));

        return $aResult;
    }
}
