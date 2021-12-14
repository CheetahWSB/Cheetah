<?php

/**
 * Cheetah - Social Network Software Platform. Copyright (c) Dean J. Bassett Jr. - https://www.cheetahwsb.com
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'utils.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');

require_once(CH_DIRECTORY_PATH_PLUGINS . 'phpotp/rfc6238.php');

ch_import('ChWsbPageView');

class ChTwoFactorAuthPageView extends ChWsbPageView
{
    public $sMode;
    public $iMemberID;

    public function __construct()
    {
        parent::__construct('two_factor_auth');
        $this->sMode = $_GET['mode'];
        $this->iMemberID = getLoggedId();
    }

    public function getBlockCode_GetCode()
    {
        switch ($this->sMode) {
            case 'popup':

                $_page['header'] = $_page['header_text'] = 'Url QR Code';

                $iQr = (int) $_GET['qr'];

                switch ($iQr) {
                    case 1:
                        $sCode = '<div class="ch-def-margin text-center ch-2fa-width-250px">QR Link to Google Authenticator on Google Play</div>';
                        $sCode .= '<div class="ch-def-margin text-center"><img src="' . CH_WSB_URL_ROOT . 'media/images/2FA/google-play-googleauth.png"></div>';
                        break;
                    case 2:
                        $sCode = '<div class="ch-def-margin text-center ch-2fa-width-250px">QR Link to Google Authenticator on Apple App Store</div>';
                        $sCode .= '<div class="ch-def-margin text-center"><img src="' . CH_WSB_URL_ROOT . 'media/images/2FA/apple-store-googleauth.png"></div>';
                        break;
                    case 3:
                        $sCode = '<div class="ch-def-margin text-center ch-2fa-width-250px">QR Link to Microsoft Authenticator on Google Play</div>';
                        $sCode .= '<div class="ch-def-margin text-center"><img src="' . CH_WSB_URL_ROOT . 'media/images/2FA/google-play-microsoftauth.png"></div>';
                        break;
                    case 4:
                        $sCode = '<div class="ch-def-margin text-center ch-2fa-width-250px">QR Link to Microsoft Authenticator on Apple App Store</div>';
                        $sCode .= '<div class="ch-def-margin text-center"><img src="' . CH_WSB_URL_ROOT . 'media/images/2FA/apple-store-microsoftauth.png"></div>';
                        break;
                    case 5:
                        $sCode = '<div class="ch-def-margin text-center ch-2fa-width-250px">QR Link to 2FA Authenticator on Google Play</div>';
                        $sCode .= '<div class="ch-def-margin text-center"><img src="' . CH_WSB_URL_ROOT . 'media/images/2FA/google-play-2fa-auth.png"></div>';
                        break;
                    case 6:
                        $sCode = '<div class="ch-def-margin text-center ch-2fa-width-250px">QR Link to 2FA Authenticator on Apple App Store</div>';
                        $sCode .= '<div class="ch-def-margin text-center"><img src="' . CH_WSB_URL_ROOT . 'media/images/2FA/apple-store-2fa-auth.png"></div>';
                        break;
                    case 7:
                        $sCode = '<div class="ch-def-margin text-center ch-2fa-width-250px">QR Link to LastPass Authenticator on Google Play</div>';
                        $sCode .= '<div class="ch-def-margin text-center"><img src="' . CH_WSB_URL_ROOT . 'media/images/2FA/google-play-lastpass.png"></div>';
                        break;
                    case 8:
                        $sCode = '<div class="ch-def-margin text-center ch-2fa-width-250px">QR Link to LastPass Authenticator on Apple App Store</div>';
                        $sCode .= '<div class="ch-def-margin text-center"><img src="' . CH_WSB_URL_ROOT . 'media/images/2FA/apple-store-lastpass.png"></div>';
                        break;
                    default:
                        $sCode .= '<div class="ch-def-margin text-center">Error occured loading QR code image file.</div>';
                }

                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                    header('Content-type:text/html;charset=utf-8');
                    echo $GLOBALS['oFunctions']->popupBox('explanation_popup', $_page['header'], $sCode);
                    exit;
                }

                break;
            case 'setup':
                $sReferer = str_replace(CH_WSB_URL_ROOT, '', $_SERVER['HTTP_REFERER']);
                //echo $sReferer;
                //exit;
                // Create the database entry in sys_2fa_data for this member.
                $sSecret = strtoupper(md5($this->iMemberID));
                $sBackupHash = strtoupper(hash('sha512', microtime()));
                $sBackupKeys = '';
                for ($x = 0; $x < 125; $x += 8) {
                    $a = substr($sBackupHash, $x, 8);
                    $sBackupKeys .= substr($a, 0, 4) . ' ' . substr($a, 3, 4) . ',';
                }
                $sBackupKeys = rtrim($sBackupKeys, ',');
                $GLOBALS['MySQL']->query("INSERT IGNORE INTO `sys_2fa_data` (`memberid`, `enabled`, `secretkey`, `backupkeys`) VALUES ('$this->iMemberID', '1', '$sSecret', '$sBackupKeys')");
                $sNick = getUsername($this->iMemberID);
                $aSiteUrl = parse_url($GLOBALS['site']['url']);
                // Set the 2FA cookie now. Otherwise member will be prompted for code before setup is complete.
                $sPath = isset($aUrl['path']) && !empty($aSiteUrl['path']) ? $aSiteUrl['path'] : '/';
                $sHost = '';
                setcookie("memberTFA", $this->iMemberID, time() + 24 * 60 * 60 * 365, $sPath, $sHost);
                $sDomain = $aSiteUrl['host'];
                $sSiteTitle = getParam('site_title');
                $sSiteTitle = str_replace(' ', '-', $sSiteTitle); // Authy has been known to have problems with spaces. Remove them.
                $sEncodedSecret = substr(Base32Static::encode($sSecret, false), 0, 16);
                $sQrCodeUrl = TokenAuth6238::getBarCodeUrl($sNick, $sDomain, $sEncodedSecret, $sSiteTitle);

                $aCode = array(
                    'qrcodeurl' => $sQrCodeUrl,
                    'secret' => $sEncodedSecret,
                    'msg1' => _t('_two_factor_auth_setup_msg1', CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=apps&ar=' . (int) $_GET['ar']),
                    'msg2' => _t('_two_factor_auth_setup_msg2'),
                    'msg3' => _t('_two_factor_auth_setup_msg3'),
                    'ch_if:show_next' => array(
                        'condition' => ((int) $_GET['ar'] == 1) ? true : false,
                        'content' => array(
                            'nexturl' => CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=sbcodes&ar=' . (int) $_GET['ar']
                        )
                    ),
                    'ch_if:show_back' => array(
                        'condition' => ((int) $_GET['ar'] != 1) ? true : false,
                        'content' => array(
                            'backurl' => CH_WSB_URL_ROOT . 'member.php'
                        )
                    )
                );
                $sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('two_factor_setup.html', $aCode);
                return DesignBoxContent(_t('_two_factor_auth_setup'), $sRet, 1);
                break;
            case 'sbcodes':
                $aMessages = array(
                    _t('_two_factor_auth_sbcodes_msg1')
                );
                $sReferer = str_replace(CH_WSB_URL_ROOT, '', $_SERVER['HTTP_REFERER']);
                // Shows existing backup codes.
                $sBackupKeys = $GLOBALS['MySQL']->getOne("SELECT `backupkeys` FROM `sys_2fa_data` WHERE `memberid` = '$this->iMemberID'");
                $aBackupKeys = explode(',', $sBackupKeys);
                $aCodes = array();
                foreach ($aBackupKeys as $id => $value) {
                    $aCodes[] = array(
                        'code' => $value
                    );
                }
                $aTemplateKeys = array(
                    'ch_repeat:codes' => $aCodes,
                    'ch_if:show_back' => array(
                        'condition' => ((int) $_GET['ar'] != 1) ? true : false,
                        'content' => array(
                            'newcodes' => CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=cbcodes',
                            'backurl' => CH_WSB_URL_ROOT . 'member.php'
                        )
                    ),
                    'ch_if:show_next' => array(
                        'condition' => ((int) $_GET['ar'] == 1) ? true : false,
                        'content' => array(
                            'nexturl' => CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=finish'
                        )
                    ),
                    'ch_if:show_msg' => array(
                        'condition' => isset($_GET['sm']),
                        'content' => array(
                            'msg' => $aMessages[(int) $_GET['sm']]
                        )
                    )
                );

                $sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('two_factor_codes.html', $aTemplateKeys);
                return DesignBoxContent(_t('_two_factor_auth_backup_codes'), $sRet, 1);
                break;
            case 'cbcodes':
                // Creates new backup codes.
                $sBackupHash = strtoupper(hash('sha512', microtime()));
                $sBackupKeys = '';
                for ($x = 0; $x < 125; $x += 8) {
                    $a = substr($sBackupHash, $x, 8);
                    $sBackupKeys .= substr($a, 0, 4) . ' ' . substr($a, 3, 4) . ',';
                }
                $sBackupKeys = rtrim($sBackupKeys, ',');
                $GLOBALS['MySQL']->query("UPDATE `sys_2fa_data` SET `backupkeys` = '$sBackupKeys' WHERE `memberid` = '$this->iMemberID'");
                // Redirect. Show the member the new codes.
                header('Location: ' . CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=sbcodes&sm=0');
                exit;
                break;
            case 'apps':
                // Shows a list of Android and IOS authenticator apps.
                if((int) $_GET['ar'] == 0) {
                    $sBackUrl = CH_WSB_URL_ROOT . 'member.php';
                } else {
                    $sBackUrl = CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=setup&ar=1';
                }
                $aTemplateKeys = array(
                    'backurl' => $sBackUrl
                );
                $sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('two_factor_apps.html', $aTemplateKeys);
                return DesignBoxContent(_t('_two_factor_auth_apps'), $sRet, 1);
                break;

            case 'enable':
                // Enables 2fa for member. If it's not already setup then redirec to setup.
                $iSetup = (int) $GLOBALS['MySQL']->getOne("SELECT `id` FROM `sys_2fa_data` WHERE `memberid` = '$this->iMemberID'");
                if ($iSetup) {
                    $GLOBALS['MySQL']->query("UPDATE `sys_2fa_data` SET `enabled` = 1 WHERE `memberid` = '$this->iMemberID'");
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                    exit;
                } else {
                    header('Location: ' . CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=setup&ar=1');
                    exit;
                }
                break;

            case 'disable':
                // Disables 2fa for member unless site admin requires it.
                if (!getParam('two_factor_auth_required')) {
                    $GLOBALS['MySQL']->query("UPDATE `sys_2fa_data` SET `enabled` = 0 WHERE `memberid` = '$this->iMemberID'");
                }
                if ($_SERVER['HTTP_REFERER']) {
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                } else {
                    header('Location: ' . CH_WSB_URL_ROOT . 'member.php');
                }
                exit;
                break;
            case 'status':
                // Show a start page with current backup codes left, enable and disable and get new codes button.
                $iEnabled = (int) $GLOBALS['MySQL']->getOne("SELECT `enabled` FROM `sys_2fa_data` WHERE `memberid` = '$this->iMemberID'");
                //$iEnabled = 0;  // Test. Force status disabled.
                $sTwoFactorAuthStatus = $iEnabled ? _t('_two_factor_auth_status_enabled') : _t('_two_factor_auth_status_disabled');
                if (getParam('two_factor_auth_required')) {
                    $sDisableStyle = 'none';
                } else {
                    $sDisableStyle = 'inline';
                }
                if ($iEnabled) {
                    $sBackupKeys = $GLOBALS['MySQL']->getOne("SELECT `backupkeys` FROM `sys_2fa_data` WHERE `memberid` = '$this->iMemberID'");
                    $aBackupKeys = explode(',', $sBackupKeys);
                    $aCodes = array();
                    foreach ($aBackupKeys as $id => $value) {
                        $aCodes[] = array(
                            'code' => $value
                        );
                    }

                    $aTemplateKeys = array(
                        'status' => _t('_two_factor_auth_status_msg', $sTwoFactorAuthStatus),
                        'backurl' => CH_WSB_URL_ROOT . 'member.php',
                        'ch_if:show_codes' => array(
                            'condition' => $iEnabled ? true : false,
                            'content' => array(
                                'codesmsg' => _t('_two_factor_auth_status_codes_msg', count($aBackupKeys)),
                                'ch_repeat:codes' => $aCodes
                            )
                        ),
                        'ch_if:show_enable' => array(
                            'condition' => false,
                            'content' => array(
                                'enableurl' => CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=enable'
                            )
                        ),
                        'ch_if:show_disable' => array(
                            'condition' => true,
                            'content' => array(
                                'enableurl' => '',
                                'disable_style' => $sDisableStyle,
                                'disableurl' => CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=disable',
                                'newcodesurl' => CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=cbcodes'
                            )
                        )
                    );
                } else {
                    $aTemplateKeys = array(
                        'status' => _t('_two_factor_auth_status_msg', $sTwoFactorAuthStatus),
                        'backurl' => CH_WSB_URL_ROOT . 'member.php',
                        'ch_if:show_codes' => array(
                            'condition' => $iEnabled ? true : false,
                            'content' => array()
                        ),
                        'ch_if:show_enable' => array(
                            'condition' => true,
                            'content' => array(
                                'enableurl' => CH_WSB_URL_ROOT . 'two_factor_auth.php?mode=enable'
                            )
                        ),
                        'ch_if:show_disable' => array(
                            'condition' => false,
                            'content' => array(
                                'enableurl' => '',
                                'disableurl' => '',
                                'newcodesurl' => ''
                            )
                        )
                    );
                }

                $sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('two_factor_status.html', $aTemplateKeys);
                return DesignBoxContent(_t('_two_factor_auth_status'), $sRet, 1);
                break;
            case 'finish':
                $aTemplateKeys = array(
                    'msg1' => _t('_two_factor_auth_finish_msg1', getParam('site_title')),
                    'finishurl' => CH_WSB_URL_ROOT . 'member.php'
                );

                $sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('two_factor_finish.html', $aTemplateKeys);
                return DesignBoxContent(_t('_two_factor_auth_finished'), $sRet, 1);

                break;
            default:
                $aForm = array(
                    'form_attrs' => array(
                        'id' => 'two-factor-auth-form',
                        'name' => 'two-factor-auth-form',
                        'action' => CH_WSB_URL_ROOT . 'two_factor_auth.php',
                        'method' => 'post',
                        'enctype' => 'multipart/form-data'
                    ),
                    'params' => array(
                        'db' => array(
                            'submit_name' => 'Submit'
                        )
                    ),
                    'inputs' => array(
                        'code' => array(
                            'type' => 'text',
                            'name' => 'code',
                            'caption' => _t('_two_factor_auth_enter_code'),
                            'value' => '',
                            'required' => true,

                            // checker params
                            'checker' => array(
                                'func' => 'length',
                                'error' => _t('_two_factor_auth_length_error'),
                                'params' => array(
                                    6,
                                    9
                                )
                            )
                        ),
                        'Submit' => array(
                            'type' => 'submit',
                            'name' => 'Submit',
                            'value' => _t('_two_factor_auth_submit')
                        )
                    )
                );

                $oForm = new ChTemplFormView($aForm);
                $oForm->initChecker();
                if ($oForm->isSubmittedAndValid()) {
                    $sRet = '';
                    // Check the passed code to see if it's valid.
                    // Convert to uppercase incase member has entered a backup key in lowercase.
                    $sCode = strtoupper($oForm->getCleanValue('code'));
                    // Remove any spaces from the code.
                    $sCode = str_replace(' ', '', trim($sCode));
                    if (strlen($sCode) == 8) {
                        // Member has entered a backup key.
                        $sBackupKeys = $GLOBALS['MySQL']->getOne("SELECT `backupkeys` FROM `sys_2fa_data` WHERE `memberid` = '$this->iMemberID'");
                        $aBackupKeys = explode(',', $sBackupKeys);
                        // See if entered backup key exists in array
                        $iFound = 0;
                        foreach ($aBackupKeys as $id => $value) {
                            $sKey = str_replace(' ', '', $value);
                            if ($sCode == $sKey) {
                                $iFound = $id + 1;
                            }
                        }
                        if ($iFound) {
                            // Backup code has been used. Need to remove the entered code from the array and update the database.
                            // Then redirect member to after login redirect url.
                            unset($aBackupKeys[$iFound - 1]);
                            $sBackupKeys = implode(',', $aBackupKeys);
                            $GLOBALS['MySQL']->query("UPDATE `sys_2fa_data` SET `backupkeys` = '$sBackupKeys' WHERE `memberid` = '$this->iMemberID'");
                            //$sRet = '<div class="ch-def-padding">' . _t('_two_factor_auth_valid_code') . '</div>';
                            header('Location: ' . CH_WSB_URL_ROOT . 'member.php');
                            exit;
                        } else {
                            // Invalid Code entered. Display error and redisplay form.
                            $aTemplateKeys = array(
                                'showerror' => 'true',
                                'error' => _t('_two_factor_auth_invalid_code'),
                                'form' => $oForm->getCode()
                            );
                            $sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('two_factor_prompt.html', $aTemplateKeys);
                        }
                    } else {
                        // Member has entered an authenticator key.
                        $sSecretDb = $GLOBALS['MySQL']->getOne("SELECT `secretkey` FROM `sys_2fa_data` WHERE `memberid` = '$this->iMemberID'");
                        $sSecret = substr(Base32Static::encode($sSecretDb, false), 0, 16);
                        if (TokenAuth6238::verify($sSecret, $sCode)) {
                            // If code is valid, redirect member to after login redirect url.
                            $aUrl = parse_url($GLOBALS['site']['url']);
                            $sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';
                            $sHost = '';
                            setcookie("memberTFA", $this->iMemberID, time() + 24 * 60 * 60 * 365, $sPath, $sHost);
                            header('Location: ' . CH_WSB_URL_ROOT . 'member.php');
                            exit;
                        } else {
                            // Invalid Code entered. Display error and redisplay form.
                            $aTemplateKeys = array(
                                'showerror' => 'true',
                                'error' => _t('_two_factor_auth_invalid_code'),
                                'form' => $oForm->getCode()
                            );
                            $sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('two_factor_prompt.html', $aTemplateKeys);
                        }
                    }
                } else {
                    $aTemplateKeys = array(
                        'showerror' => 'false',
                        'form' => $oForm->getCode()
                    );
                    $sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('two_factor_prompt.html', $aTemplateKeys);
                }
                return DesignBoxContent(_t('_two_factor_auth_getcode'), $sRet, 1);
        }
    }
}

$_page['name_index'] = 7;
$_page['header'] = 'Two Factor Auth';

$_ni = $_page['name_index'];

$oEPV = new ChTwoFactorAuthPageView();
$_page_cont[$_ni]['page_main_code'] = $oEPV->getCode();

PageCode();
