<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbDb');
ch_import('ChWsbModuleDb');
ch_import('ChTemplSearchResult');

class ChWsbInstallerUi extends ChWsbDb
{
    var $_sDefVersion;
    var $_aCheckPathes;
    var $_aTypesConfig = array (
            'module' => array (
                'configfile' => '/install/config.php',
                'configvar' => 'aConfig',
                'configvarindex' => 'home_dir',
                'folder' => 'modules/',
                'subfolder' => '{configvar}',
            ),
            'update' => array (
                'configfile' => '/install/config.php',
                'configvar' => 'aConfig',
                'configvarindex' => 'home_dir',
                'folder' => 'modules/',
                'subfolder' => '{configvar}',
            ),
            'template' => array (
                'configfile' => '/scripts/ChTemplName.php',
                'configvar' => 'sTemplName',
                'folder' => 'templates/',
                'subfolder' => '{packagerootfolder}',
            ),
        );

    function __construct()
    {
        parent::__construct();

        $this->_sDefVersion = '0.0.0';
        $this->_aCheckPathes = array();
    }
    function getUploader($sResult, $sPackageTitleKey = '_adm_txt_modules_module', $bUnsetUpdate = false, $sAction = false)
    {
        $aForm = array(
            'form_attrs' => array(
                'id' => 'module_upload_form',
                'action' => $sAction ? $sAction : ch_html_attribute($_SERVER['PHP_SELF']),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ),
            'inputs' => array (
                'header1' => array(
                    'type' => 'block_header',
                    'caption' => _t('_adm_txt_modules_package_to_upload'),
                ),
                'module' => array(
                    'type' => 'file',
                    'name' => 'module',
                    'caption' => _t($sPackageTitleKey),
                ),
                'update' => array(
                    'type' => 'file',
                    'name' => 'update',
                    //'caption' => _t('_adm_btn_modules_update'), // Deano - Temporarily disable until update system can be changed for Cheetahs use.
                    'caption' => '',
                ),
                'header2' => array(
                    'type' => 'block_header',
                    'caption' => _t('_adm_txt_modules_ftp_access'),
                ),
                'host' => array(
                    'type' => 'text',
                    'name' => 'host',
                    'caption' => _t('_adm_txt_modules_host'),
                    'value' => getParam('sys_ftp_login')
                ),
                'login' => array(
                    'type' => 'text',
                    'name' => 'login',
                    'caption' => _t('_adm_txt_modules_login'),
                    'value' => getParam('sys_ftp_login')
                ),
                'password' => array(
                    'type' => 'password',
                    'name' => 'password',
                    'caption' => _t('_Password'),
                    'value' => getParam('sys_ftp_password')
                ),
                'path' => array(
                    'type' => 'text',
                    'name' => 'path',
                    'caption' => _t('_adm_txt_modules_path_to_cheetah'),
                    'value' => !($sPath = getParam('sys_ftp_dir')) ? 'public_html/' : $sPath
                ),
                'submit_upload' => array(
                    'type' => 'submit',
                    'name' => 'submit_upload',
                    'value' => _t('_adm_box_cpt_upload'),
                )
            )
        );

        if ($bUnsetUpdate)
            unset($aForm['inputs']['update']);

        $oForm = new ChBaseFormView($aForm);
        $sContent = $oForm->getCode();

        if(!empty($sResult))
            $sContent = MsgBox(_t($sResult), 10) . $sContent;

        return $GLOBALS['oAdmTemplate']->parseHtmlByName('modules_uploader.html', array(
            'content' => $sContent
        ));
    }
    function getInstalled()
    {
        //--- Get Items ---//
        $oModules = new ChWsbModuleDb();
        $aModules = $oModules->getModules();

        $aItems = array();
        foreach($aModules as $aModule) {
			if(strpos($aModule['dependencies'], $aModule['uri']) !== false)
        		continue;

            $sUpdate = '';
            if(in_array($aModule['path'], $this->_aCheckPathes)) {
            	$aCheckInfo = ChWsbInstallerUi::checkForUpdates($aModule);
            	$sUpdate = $this->_parseUpdate($aCheckInfo);
            }

            $aItems[] = array(
                'name' => $aModule['uri'],
                'value' => $aModule['path'],
                'title'=> _t('_adm_txt_modules_title_module', $aModule['title'], !empty($aModule['version']) ? $aModule['version'] : $this->_sDefVersion, $aModule['vendor']),
            	'can_update' => isset($aModule['update_url']) && !empty($aModule['update_url']) ? 1 : 0,
                'update' => $sUpdate,
            );
        }

        //--- Get Controls ---//
        $aButtons = array(
            'modules-uninstall' => array('type' => 'submit', 'name' => 'modules-uninstall', 'value' => _t('_adm_btn_modules_uninstall'), 'onclick' => 'onclick="javascript: return ' . CH_WSB_ADM_MM_JS_NAME . '.onSubmitUninstall(this);"'),
            'modules-recompile-languages' => _t('_adm_btn_modules_recompile_languages')
        );

        $oZ = new ChWsbAlerts('system', 'admin_modules_buttons', 0, 0, array(
        	'place' => 'installed',
		    'buttons' => &$aButtons,
		));
		$oZ->alert();

        $sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('modules_list.html', array(
            'type' => 'installed',
            'ch_repeat:items' => !empty($aItems) ? $aItems : MsgBox(_t('_Empty')),
            'controls' => ChTemplSearchResult::showAdminActionsPanel('modules-installed-form', $aButtons, 'pathes')
        ));

        // Deano - Temporarily disable until update system can be changed for Cheetahs use.
        //$aTopMenu = array(
        //	'modules-update' => array('title' => '_adm_btn_modules_update', 'href' => 'javascript:void(0)', 'onclick' => 'javascript:' . CH_WSB_ADM_MM_JS_NAME . '.checkForUpdates(this);')
        //);
        $aTopMenu = array(
        );

        $GLOBALS['oAdmTemplate']->addJsTranslation(array('_adm_txt_modules_data_will_be_lost'));
        return DesignBoxAdmin(_t('_adm_box_cpt_installed_modules'), $sContent, $aTopMenu);
    }
    function getNotInstalled($sResult)
    {
        //--- Get Items ---//
        $oModules = new ChWsbModuleDb();
        $aModules = $oModules->getModules();

        $aInstalled = array();
        foreach($aModules as $aModule)
            $aInstalled[] = $aModule['path'];

        $aNotInstalled = array();
        $sPath = CH_DIRECTORY_PATH_ROOT . 'modules/';
        if($rHandleVendor = opendir($sPath)) {

            while(($sVendor = readdir($rHandleVendor)) !== false) {
                if(substr($sVendor, 0, 1) == '.' || !is_dir($sPath . $sVendor)) continue;

                if($rHandleModule = opendir($sPath . $sVendor)) {
                    while(($sModule = readdir($rHandleModule)) !== false) {
                        if(!is_dir($sPath . $sVendor . '/' . $sModule) || substr($sModule, 0, 1) == '.' || in_array($sVendor . '/' . $sModule . '/', $aInstalled))
                            continue;

                        $sConfigPath = $sPath . $sVendor . '/' . $sModule . '/install/config.php';
                        if(!file_exists($sConfigPath)) continue;

                        include($sConfigPath);
                        $aNotInstalled[$aConfig['title']] = array(
                            'name' => $aConfig['home_uri'],
                            'value' => $aConfig['home_dir'],
                            'title' => _t('_adm_txt_modules_title_module', $aConfig['title'], !empty($aConfig['version']) ? $aConfig['version'] : $this->_sDefVersion, $aConfig['vendor']),
                        	'can_update' => '0',
                            'update' => ''
                        );
                    }
                    closedir($rHandleModule);
                }
            }
            closedir($rHandleVendor);
        }
        ksort($aNotInstalled);

        //--- Get Controls ---//
        $aButtons = array(
            'modules-install' => _t('_adm_btn_modules_install'),
            'modules-delete' => _t('_adm_btn_modules_delete')
        );

        $oZ = new ChWsbAlerts('system', 'admin_modules_buttons', 0, 0, array(
        	'place' => 'uninstalled',
		    'buttons' => &$aButtons,
		));
		$oZ->alert();

        $sControls = ChTemplSearchResult::showAdminActionsPanel('modules-not-installed-form', $aButtons, 'pathes');

        if(!empty($sResult))
            $sResult = MsgBox(_t($sResult), 10);

        return $sResult . $GLOBALS['oAdmTemplate']->parseHtmlByName('modules_list.html', array(
            'type' => 'not-installed',
            'ch_repeat:items' => !empty($aNotInstalled) ? array_values($aNotInstalled) : MsgBox(_t('_Empty')),
            'controls' => $sControls
        ));
    }
    function getUpdates($sResult)
    {
        $aUpdates = array();
        $sPath = CH_DIRECTORY_PATH_ROOT . 'modules/';
        if($rHandleVendor = opendir($sPath)) {
            while(($sVendor = readdir($rHandleVendor)) !== false) {
                if(substr($sVendor, 0, 1) == '.' || !is_dir($sPath . $sVendor))
                    continue;

                if($rHandleModule = opendir($sPath . $sVendor . '/')) {
                    while(($sModule = readdir($rHandleModule)) !== false) {
                        if(!is_dir($sPath . $sVendor . '/' . $sModule) || substr($sModule, 0, 1) == '.')
                            continue;

                        if($rHandleUpdate = @opendir($sPath . $sVendor . '/' . $sModule . '/updates/')) {
                            while(($sUpdate = readdir($rHandleUpdate)) !== false) {
                                if(!is_dir($sPath . $sVendor . '/' . $sModule . '/updates/' . $sUpdate) || substr($sUpdate, 0, 1) == '.')
                                    continue;

                                $sConfigPath = $sPath . $sVendor . '/' . $sModule . '/updates/' . $sUpdate . '/install/config.php';
                                if(!file_exists($sConfigPath))
                                    continue;

                                include($sConfigPath);
                                $sName = $aConfig['title'] . $aConfig['module_uri'] . $aConfig['version_from'] . $aConfig['version_to'];
                                $aUpdates[$sName] = array(
                                    'name' => md5($sName),
                                    'value' => $aConfig['home_dir'],
                                    'title' => _t('_adm_txt_modules_title_update', $aConfig['title'], $aConfig['version_from'], $aConfig['version_to']),
                                	'can_update' => '0',
                                	'update' => ''
                                );
                            }
                            closedir($rHandleUpdate);
                        }
                    }
                    closedir($rHandleModule);
                }
            }
            closedir($rHandleVendor);
        }
        ksort($aUpdates);

        //--- Get Controls ---//
        $aButtons = array(
            'updates-install' => _t('_adm_btn_modules_install'),
            'updates-delete' => _t('_adm_btn_modules_delete')
        );
        $sControls = ChTemplSearchResult::showAdminActionsPanel('modules-updates-form', $aButtons, 'pathes');

        if(!empty($sResult))
            $sResult = MsgBox(_t($sResult), 10);

        return $sResult . $GLOBALS['oAdmTemplate']->parseHtmlByName('modules_list.html', array(
            'type' => 'updates',
            'ch_repeat:items' => !empty($aUpdates) ? array_values($aUpdates) : MsgBox(_t('_Empty')),
            'controls' => $sControls
        ));
    }

    //--- Get/Set methods ---//
    function setCheckPathes($aPathes)
    {
        $this->_aCheckPathes = is_array($aPathes) ? $aPathes : array();
    }

    //--- Actions ---//
    function actionUpload($sType, $aFile, $aFtpInfo)
    {
    	$sHost = htmlspecialchars_adv(clear_xss($aFtpInfo['host']));
        $sLogin = htmlspecialchars_adv(clear_xss($aFtpInfo['login']));
        $sPassword = htmlspecialchars_adv(clear_xss($aFtpInfo['password']));
        $sPath = htmlspecialchars_adv(clear_xss($aFtpInfo['path']));

        setParam('sys_ftp_host', $sHost);
        setParam('sys_ftp_login', $sLogin);
        setParam('sys_ftp_password', $sPassword);
        setParam('sys_ftp_dir', $sPath);

        $sErrMsg = false;

        $sName = time();
        $sAbsolutePath = CH_DIRECTORY_PATH_ROOT . "tmp/" . $sName . '.zip';
        $sPackageRootFolder = false;

        if (!class_exists('ZipArchive'))
            $sErrMsg = '_adm_txt_modules_zip_not_available';

        if (!$sErrMsg && $this->_isArchive($aFile['type']) && move_uploaded_file($aFile['tmp_name'], $sAbsolutePath)) {

            // extract uploaded zip package into tmp folder

            $oZip = new ZipArchive();
            if ($oZip->open($sAbsolutePath) !== TRUE)
                $sErrMsg = '_adm_txt_modules_cannot_unzip_package';

            if (!$sErrMsg) {
                $sPackageRootFolder = $oZip->numFiles > 0 ? $oZip->getNameIndex(0) : false;

                if (file_exists(CH_DIRECTORY_PATH_ROOT . 'tmp/' . $sPackageRootFolder)) // remove existing tmp folder with the same name
                    ch_rrmdir(CH_DIRECTORY_PATH_ROOT . 'tmp/' . $sPackageRootFolder);

                if ($sPackageRootFolder && !$oZip->extractTo(CH_DIRECTORY_PATH_ROOT . 'tmp/'))
                    $sErrMsg = '_adm_txt_modules_cannot_unzip_package';

                $oZip->close();
            }

            // upload files to the correct folder via FTP

            if (!$sErrMsg && $sPackageRootFolder) {

                $oFtp = new ChWsbFtp(!empty($sHost) ? $sHost : $_SERVER['HTTP_HOST'], $sLogin, $sPassword, $sPath);

                if (!$oFtp->connect())
                    $sErrMsg = '_adm_txt_modules_cannot_connect_to_ftp';

                if (!$sErrMsg && !$oFtp->isCheetah())
                    $sErrMsg = '_adm_txt_modules_destination_not_valid';

                if (!$sErrMsg) {
                    $sConfigPath = CH_DIRECTORY_PATH_ROOT . "tmp/" . $sPackageRootFolder . $this->_aTypesConfig[$sType]['configfile'];
                    if (file_exists($sConfigPath)) {
                        include($sConfigPath);
                        $sConfigVar = !empty($this->_aTypesConfig[$sType]['configvarindex']) ? ${$this->_aTypesConfig[$sType]['configvar']}[$this->_aTypesConfig[$sType]['configvarindex']] : ${$this->_aTypesConfig[$sType]['configvar']};
                        $sSubfolder = $this->_aTypesConfig[$sType]['subfolder'];
                        $sSubfolder = str_replace('{configvar}', $sConfigVar, $sSubfolder);
                        $sSubfolder = str_replace('{packagerootfolder}', $sPackageRootFolder, $sSubfolder);
                        if (!$oFtp->copy(CH_DIRECTORY_PATH_ROOT . "tmp/" . $sPackageRootFolder . '/', $this->_aTypesConfig[$sType]['folder'] . $sSubfolder))
                            $sErrMsg = '_adm_txt_modules_ftp_copy_failed';
                    } else {
                        $sErrMsg = '_adm_txt_modules_wrong_package_format';
                    }
                }

            } else {
                $sErrMsg = '_adm_txt_modules_cannot_unzip_package';
            }

            // remove temporary files
            ch_rrmdir(CH_DIRECTORY_PATH_ROOT . 'tmp/' . $sPackageRootFolder);
            unlink($sAbsolutePath);

        } else {
            $sErrMsg = '_adm_txt_modules_cannot_upload_package';
        }

        return $sErrMsg ? $sErrMsg : '_adm_txt_modules_success_upload';
    }
    function actionInstall($aDirectories)
    {
        return $this->_perform($aDirectories, 'install');
    }
    function actionUninstall($aDirectories)
    {
        return $this->_perform($aDirectories, 'uninstall');
    }
    function actionRecompile($aDirectories)
    {
        return $this->_perform($aDirectories, 'recompile');
    }
    function actionUpdate($aDirectories)
    {
        return $this->_perform($aDirectories, 'update');
    }
    function actionDelete($aDirectories, $sType = 'module')
    {
    	$sFtpHost = getParam('sys_ftp_host');
		if(empty($sFtpHost))
			$sFtpHost = $_SERVER['HTTP_HOST'];

        $oFtp = new ChWsbFtp($sFtpHost, getParam('sys_ftp_login'), getParam('sys_ftp_password'), getParam('sys_ftp_dir'));
        if (!$oFtp->connect())
            return '_adm_txt_modules_cannot_connect_to_ftp';

        $sDir = $this->_aTypesConfig[$sType]['folder'];
        foreach ($aDirectories as $sDirectory)
            if (!$oFtp->delete($sDir . $sDirectory))
                return '_adm_txt_modules_cannot_remove_package';

        return '_adm_txt_modules_success_delete';
    }
	function checkForUpdatesByPath($sPath)
    {
    	ch_import('ChWsbModuleDb');
    	$oModuleDb = new ChWsbModuleDb();
        $aModule = $oModuleDb->getModulesBy(array('type' => 'path', 'value' => $sPath));

        $aResult = self::checkForUpdates($aModule);
        $aResult['content'] = $this->_parseUpdate($aResult);
        return $aResult;
    }
    function downloadUpdate($sLink)
    {
    	$sName = time() . '.zip';
    	$sData = ch_file_get_contents($sLink);

		//--- write ZIP archive.
		$sTmpPath = CH_DIRECTORY_PATH_ROOT . 'tmp/';
		$sFilePath = $sTmpPath . $sName;
		if (!$rHandler = fopen($sFilePath, 'w'))
			return _t('_adm_txt_modules_cannot_download_package');

		if (!fwrite($rHandler, $sData))
			return _t('_adm_txt_modules_cannot_write_package');

    	fclose($rHandler);

    	//--- Unarchive package.
    	if(!class_exists('ZipArchive'))
            return _t('_adm_txt_modules_zip_not_available');

		$oZip = new ZipArchive();
		if($oZip->open($sFilePath) !== true)
        	return _t('_adm_txt_modules_cannot_unzip_package');

        $sPackageRootFolder = $oZip->numFiles > 0 ? $oZip->getNameIndex(0) : false;
		if($sPackageRootFolder && file_exists($sTmpPath . $sPackageRootFolder)) // remove existing tmp folder with the same name
        	ch_rrmdir($sTmpPath . $sPackageRootFolder);

		if($sPackageRootFolder && !$oZip->extractTo($sTmpPath))
        	return _t('_adm_txt_modules_cannot_unzip_package');

		$oZip->close();

		//--- Move unarchived package.
		$sHost = getParam('sys_ftp_host');
		$sLogin = getParam('sys_ftp_login');
		$sPassword = getParam('sys_ftp_password');
		$sPath = getParam('sys_ftp_dir');
		if(empty($sLogin) || empty($sPassword) || empty($sPath))
			return _t('_adm_txt_modules_no_ftp_info');

		ch_import('ChWsbFtp');
		$oFtp = new ChWsbFtp(!empty($sHost) ? $sHost : $_SERVER['HTTP_HOST'], $sLogin, $sPassword, $sPath);

		if(!$oFtp->connect())
        	return _t('_adm_txt_modules_cannot_connect_to_ftp');

		if(!$oFtp->isCheetah())
			return _t('_adm_txt_modules_destination_not_valid');

		$sConfigPath = $sTmpPath . $sPackageRootFolder . '/install/config.php';
		if(!file_exists($sConfigPath))
			return _t('_adm_txt_modules_wrong_package_format');

		include($sConfigPath);
		if(empty($aConfig) || empty($aConfig['home_dir']) || !$oFtp->copy($sTmpPath . $sPackageRootFolder . '/', 'modules/' . $aConfig['home_dir']))
			return _t('_adm_txt_modules_ftp_copy_failed');

        return true;
    }

    //--- Static methods ---//
    public static function checkForUpdates($aModule)
    {
    	if(empty($aModule['update_url']))
    		return array();

        $sData = ch_file_get_contents($aModule['update_url'], array(
            'uri' => $aModule['uri'],
            'path' => $aModule['path'],
            'version' => $aModule['version'],
            'domain' => $_SERVER['HTTP_HOST']
        ));

        $aValues = $aIndexes = array();
        $rParser = xml_parser_create('UTF-8');
        xml_parse_into_struct($rParser, $sData, $aValues, $aIndexes);
        xml_parser_free($rParser);

        $aInfo = array();
        if(isset($aIndexes['VERSION']))
            $aInfo['version'] = $aValues[$aIndexes['VERSION'][0]]['value'];
        if(isset($aIndexes['LINK']))
			$aInfo['link'] = $aValues[$aIndexes['LINK'][0]]['value'];
		if(isset($aIndexes['PACKAGE']))
			$aInfo['package'] = $aValues[$aIndexes['PACKAGE'][0]]['value'];

        return $aInfo;
    }

    //--- Protected methods ---//
    function _parseUpdate($aInfo) {
    	$bAvailable = !empty($aInfo) && is_array($aInfo);

    	return $GLOBALS['oAdmTemplate']->parseHtmlByName('modules_update.html', array(
    		'ch_if:show_available' => array(
    			'condition' => $bAvailable,
    			'content' => array(
					'text' => _t('_adm_txt_modules_update_text', empty($aInfo['version']) ? '' : $aInfo['version']),
		            'ch_if:show_update_view' => array(
		            	'condition' => !empty($aInfo['link']),
		            	'content' => array(
		            		'link' => !empty($aInfo['link']) ? $aInfo['link'] : '',
		            	)
		            ),
		            'ch_if:show_update_download' => array(
		            	'condition' => !empty($aInfo['package']),
		            	'content' => array(
		            		'js_object' => CH_WSB_ADM_MM_JS_NAME,
		            		'link' => !empty($aInfo['package']) ? $aInfo['package'] : '',
		            	)
		            )
				),
			),
			'ch_if:show_latest' => array(
            	'condition' => !$bAvailable,
                'content' => array()
			)
    	));
    }
    function _perform($aDirectories, $sOperation, $aParams = array())
    {

        // NOTE: Need to fix this. Check to see if mutipal modules are being installed. If so,
        // if membership module is one of them, move it to the last. This is to ensure it is
        // installed after any payment modules.

        // If installing more than one module at a time, see if membership module is being installed.
        if(count($aDirectories) > 1) {
            if (in_array('cheetah/membership/', $aDirectories)) {
                // It's there. See if payment module is being installed as well.
                if (in_array('cheetah/payment/', $aDirectories)) {
                    // It's there, Move it to the front of the array.
                    $sKey = array_search('cheetah/payment/', $aDirectories);
                    unset($aDirectories[$sKey]);
                    array_unshift($aDirectories , 'cheetah/payment/');
                } else {
                    // It's not there, add it to the front of the array.
                    // Only add it if it's not allready installed.
                    if( !ChWsbInstallerUtils::isModuleInstalled('payment') ) {
                        array_unshift($aDirectories , 'cheetah/payment/');
                    }
                }
            }
        }

        $sConfigFile = 'install/config.php';
        $sInstallerFile = 'install/installer.php';
        $sInstallerClass = $sOperation == 'update' ? 'Updater' : 'Installer';

        $aPlanks = array();
        foreach($aDirectories as $sDirectory) {
            $sPathConfig = CH_DIRECTORY_PATH_MODULES . $sDirectory . $sConfigFile;
            $sPathInstaller = CH_DIRECTORY_PATH_MODULES . $sDirectory . $sInstallerFile;
            if(file_exists($sPathConfig) && file_exists($sPathInstaller)) {
                include($sPathConfig);
                require_once($sPathInstaller);

                $sClassName = $aConfig['class_prefix'] . $sInstallerClass;
                $oInstaller = new $sClassName($aConfig);
                $aResult = $oInstaller->$sOperation($aParams);

                ch_import('ChWsbAlerts');
                $o = new ChWsbAlerts('module', $sOperation, 0, 0, array('uri' => $aConfig['home_uri'], 'config' => $aConfig, 'installer' => $oInstaller, 'res' => $aResult));
                $o->alert();

                if($aResult['result']) {
                    $aResult['e1'] = '';
                    $aResult['e2'] = '';
                } else {
                    $aResult['e1'] = 'modules-plank-switch-opened';
                    $aResult['e2'] = 'style="display: block;"';
                }

                if(!$aResult['result'] && empty($aResult['message']))
                   continue;
            } else
                $aResult = array(
                    'operation_title' => _t('_adm_txt_modules_process_operation_failed', $sOperation, $sDirectory),
                    'message' => ''
                );

            $aPlanks[] = array(
                'operation_title' => $aResult['operation_title'],
                'ch_if:operation_result_success' => array(
                    'condition' => $aResult['result'],
                    'content' => array()
                ),
                'ch_if:operation_result_failed' => array(
                    'condition' => !$aResult['result'],
                    'content' => array()
                ),
                'message' => $aResult['message'],
                'error_c1' => $aResult['e1'],
                'error_c2' => $aResult['e2'],

            );
        }

        return $GLOBALS['oAdmTemplate']->parseHtmlByName('modules_results.html', array(
            'ch_repeat:planks' => $aPlanks
        ));
    }
    function _isArchive($sType)
    {
        $bResult = false;
        switch($sType) {
            case 'application/zip':
            case 'application/x-zip-compressed':
                $bResult = true;
                break;
        }
        return $bResult;
    }
}
