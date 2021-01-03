<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . "ChWsbInstaller.php");

class ChVideosInstaller extends ChWsbInstaller
{
    function __construct($aConfig)
    {
        parent::__construct($aConfig);
        $this->_aActions = array_merge($this->_aActions, array(
            'check_requirements' => array(
                'title' => 'Check Videos Requirements',
            ),
        ));
    }

    function install($aParams)
    {
        $aResult = parent::install($aParams);

        if($aResult['result'] && ChWsbRequest::serviceExists('wall', 'update_handlers'))
            ChWsbService::call('wall', 'update_handlers', array($this->_aConfig['home_uri'], true));

        if($aResult['result'] && ChWsbRequest::serviceExists('spy', 'update_handlers'))
            ChWsbService::call('spy', 'update_handlers', array($this->_aConfig['home_uri'], true));

        return $aResult;
    }

    function uninstall($aParams)
    {
        if(ChWsbRequest::serviceExists('wall', 'update_handlers'))
            ChWsbService::call('wall', 'update_handlers', array($this->_aConfig['home_uri'], false));

        if(ChWsbRequest::serviceExists('spy', 'update_handlers'))
            ChWsbService::call('spy', 'update_handlers', array($this->_aConfig['home_uri'], false));

        return parent::uninstall($aParams);
    }

    function actionCheckRequirements ()
    {
        $iErrors = 0;
        if(getFfmpegPath() == '') $iErrors++;
        if(getFfprobePath() == '') $iErrors++;
        return array('code' => !$iErrors ? CH_WSB_INSTALLER_SUCCESS : CH_WSB_INSTALLER_FAILED, 'content' => '');
    }

    function actionCheckRequirementsFailed ()
    {
        return '
            <div style="border:1px solid red; padding:10px;margin-top: 10px;">FFmpeg package not found. The FFmpeg package needs to be installed for this module. You can download it here. <a href="https://www.cheetahwsb.com/page/downloads" target="_blank">https://www.cheetahwsb.com/page/downloads</a></div>
        ';
    }

}
