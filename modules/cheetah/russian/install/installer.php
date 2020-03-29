<?php

/**
 * This work, "Cheetah - https://cheetah.deanbassett.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbInstaller');

class ChRuInstaller extends ChWsbInstaller
{
    protected $sFileName = 'lang-ru.php';
    protected $sFilePath;

    function __construct($aConfig)
    {
        parent::__construct($aConfig);
        $this->sFilePath = CH_DIRECTORY_PATH_MODULES . $aConfig['home_dir'] . 'data/' . $this->sFileName;
    }

    function install($aParams)
    {
        $bInclude = @include($this->sFilePath);
        if (!$bInclude || empty($LANG) || empty($LANG_INFO) || !$this->_addLanguage($LANG, $LANG_INFO))
            return array(
                'operation_title' => _t('_adm_txt_modules_operation_install', $this->_aConfig['title']),
                'message' => 'Language file parse error or such language already exists: ' . $this->sFileName,
                'result' => false);

        $iLangId = getLangIdByName($LANG_INFO['Name']);
        $this->_recompileLanguageForAllModules($iLangId);
        compileLanguage($iLangId);

        return parent::install($aParams);
    }

    function uninstall($aParams)
    {
        $aResult = parent::uninstall($aParams);

        if ($aResult['result']) {
            $bInclude = @include($this->sFilePath);
            if (!$bInclude || empty($LANG) || empty($LANG_INFO) || !$this->_removeLanguage($LANG, $LANG_INFO))
                return array(
                    'operation_title' => _t('_adm_txt_modules_operation_install', $this->_aConfig['title']),
                    'message' => 'Language file parse error: ' . $this->sFileName,
                    'result' => false);

            // delete compiled lang file
            @unlink(CH_DIRECTORY_PATH_ROOT . "langs/lang-{$LANG_INFO['Name']}.php");
            $GLOBALS['MySQL']->cleanCache('checkLangExists_' . $LANG_INFO['Name']);
            if (ch_lang_name() == $LANG_INFO['Name']) {
                getCurrentLangName(true);
            }
        }

        return $aResult;
    }

}
