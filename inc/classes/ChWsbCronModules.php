<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbCron');
ch_import('ChWsbModuleDb');
ch_import('ChWsbInstallerUi');
ch_import('ChWsbEmailTemplates');

class ChWsbCronModules extends ChWsbCron
{
    function __construct()
    {
        parent::__construct();
    }

	function processing()
    {
    	$oModules = new ChWsbModuleDb();
        $aModules = $oModules->getModules();

        $aResult = array();
        foreach($aModules as $aModule) {
        	$aCheckInfo = ChWsbInstallerUi::checkForUpdates($aModule);
        	if(isset($aCheckInfo['version']))
        		$aResult[] = _t('_adm_txt_modules_update_text_ext', $aModule['title'], $aCheckInfo['version']);
        }
        if(empty($aResult))
        	return;

    	$aAdmins = $GLOBALS['MySQL']->getAll("SELECT * FROM `Profiles` WHERE `Role`&" . CH_WSB_ROLE_ADMIN . "<>0 AND `EmailNotify`='1'");
        if(empty($aAdmins))
        	return;

		$oEmailTemplate = new ChWsbEmailTemplates();
        $sMessage = implode('<br />', $aResult);

		foreach($aAdmins as $aAdmin) {
        	$aTemplate = $oEmailTemplate->getTemplate('t_ModulesUpdates', $aAdmin['ID']);

			sendMail(
				$aAdmin['Email'],
		        $aTemplate['Subject'],
		        $aTemplate['Body'],
		        $aAdmin['ID'],
		        array(
		        	'MessageText' => $sMessage
				)
			);
		}
    }
}
