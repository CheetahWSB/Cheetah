<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . "ChWsbInstaller.php");

class ChFaceBookConnectInstaller extends ChWsbInstaller
{
    function __construct(&$aConfig)
    {
        parent::__construct($aConfig);

		$this->_aActions['check_requirements'] = array(
			'title' => 'Check requirements:',
		);
    }

    function actionCheckRequirements()
    {
        $bError = version_compare(PHP_VERSION, '5.4.0') >= 0
            ? CH_WSB_INSTALLER_SUCCESS
            : CH_WSB_INSTALLER_FAILED;

        return $bError;
    }

    function actionCheckRequirementsFailed()
    {
        return '
            <div style="border:1px solid red; padding:10px;">
                <u>PHP 5.4</u> or higher is required!
            </div>';
    }
}
