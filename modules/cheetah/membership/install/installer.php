<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import("ChWsbInstaller");

class ChMbpInstaller extends ChWsbInstaller
{
    public function __construct($aConfig)
    {
        parent::__construct($aConfig);
        $this->_aActions['check_payment'] = array(
            'title' => _t('_adm_txt_modules_check_dependencies'),
        );
    }

    public function actionCheckPayment($bInstall = true)
    {
        if (!$bInstall) {
            return CH_WSB_INSTALLER_SUCCESS;
        }

        $aError = array('code' => CH_WSB_INSTALLER_FAILED, 'content' => _t('_adm_txt_modules_wrong_dependency_install_payment'));

        $sPayment = getParam('sys_default_payment');
        if (empty($sPayment)) {
            return $aError;
        }

        $oModuleDb = new ChWsbModuleDb();
        $aPayment = $oModuleDb->getModuleByUri($sPayment);
        if (empty($aPayment) || !is_array($aPayment)) {
            return $aError;
        }

        return CH_WSB_INSTALLER_SUCCESS;
    }

    public function actionCheckPaymentFailed($mixedResult)
    {
        return $mixedResult['content'];
    }

    public function install($aParams)
    {
        $aResult = parent::install($aParams);

        if ($aResult['result'] && ChWsbRequest::serviceExists('payment', 'update_dependent_modules')) {
            ChWsbService::call('payment', 'update_dependent_modules', array($this->_aConfig['home_uri'], true));
        }

        return $aResult;
    }

    public function uninstall($aParams)
    {
        if (ChWsbRequest::serviceExists('payment', 'update_dependent_modules')) {
            ChWsbService::call('payment', 'update_dependent_modules', array($this->_aConfig['home_uri'], false));
        }

        return parent::uninstall($aParams);
    }
}
