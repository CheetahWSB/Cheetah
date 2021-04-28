<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModule');
ch_import('ChWsbPaginate');
ch_import('ChWsbAlerts');

require_once (CH_DIRECTORY_PATH_PLUGINS . "phpmailer/class.phpmailer.php");
require_once (CH_DIRECTORY_PATH_PLUGINS . "phpmailer/class.smtp.php");

class ChSMTPModule extends ChWsbModule
{
    function __construct(&$aModule)
    {
        parent::__construct($aModule);
    }

    function serviceSend ($sRecipientEmail, $sMailSubject, $sMailBody, $sMailHeader, $sMailParameters, $isHtml, $aRecipientInfo = array())
    {
        $iRet = true;

        if ($sRecipientEmail) {

            $mail = new PHPMailer();

            if ('on' == getParam('ch_smtp_on'))
                $mail->IsSMTP();
            //$mail->SMTPDebug = 2;

            $mail->CharSet = 'utf8';

            // smtp server auth or not
            $mail->SMTPAuth = 'on' == getParam('ch_smtp_auth') ? true : false;

            // from settings, smtp server secure ssl/tls
            $sParamSecure = getParam('ch_smtp_secure');
            if ('SSL' == $sParamSecure || 'TLS' == $sParamSecure) {
                $mail->SMTPSecure = strtolower($sParamSecure);

                if ('on' == getParam('ch_smtp_allow_selfsigned')) {
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                }
            }

            // from settings, smtp server
            $sParamHost = getParam('ch_smtp_host');
            if ($sParamHost)
                $mail->Host = $sParamHost;

            // smtp port 25, 465
            $sParamPort = getParam('ch_smtp_port');
            if ((int)$sParamPort > 0)
                $mail->Port = $sParamPort;

            // from settings, username and passord of smtp server
            $mail->Username = getParam ('ch_smtp_username');
            $mail->Password = getParam ('ch_smtp_password');

            $sParamSender = trim(getParam('ch_smtp_from_email'));
            if ($sParamSender)
                $mail->From = $sParamSender;
            else
                // Deano. Boonex had this set to below value. $sSenderEmail is not assigned anywhere, so it is empty.
                // I changed it to default to the site email address.
                //$mail->From = $sSenderEmail;
                $mail->From = getParam('site_email');

            // get site name or some other name as sender's name
            $mail->FromName   = getParam ('ch_smtp_from_name');
            // Deano. Boonex did not check to see if this value is empty. Change
            // to default it to the sites name if it is empty.
            if(!$mail->FromName) $mail->FromName = getParam ('site_title');

            $mail->Subject    = $sMailSubject;
            if ($isHtml) {
                $mail->Body       = $sMailBody;
                $mail->AltBody    = $isHtml ? strip_tags($sMailBody) : $sMailBody;
            } else {
                $mail->Body = $sMailBody;
            }

            $mail->WordWrap   = 50; // set word wrap

            $mail->AddAddress($sRecipientEmail);

            // get attachments from attach directory
            if ('on' == getParam ('ch_smtp_send_attachments')) {
                if ($h = opendir(CH_DIRECTORY_PATH_MODULES . "cheetah/smtpmailer/data/attach/")) {
                    while (false !== ($sFile = readdir($h))) {
                        if ($sFile == "." || $sFile == ".." || $sFile[0] == ".") continue;
                        $mail->AddAttachment(CH_DIRECTORY_PATH_MODULES . "cheetah/smtpmailer/data/attach/" . $sFile, $sFile);
                    }
                    closedir($h);
                }
            }

            $mail->IsHTML($isHtml ? true : false);

            $iRet = $mail->Send();
            if (!$iRet)
                $this->log("Mailer Error ($sRecipientEmail): " . $mail->ErrorInfo);

        }

        //--- create system event [begin]
        ch_import('ChWsbAlerts');
        $aAlertData = array(
            'email'     => $sRecipientEmail,
            'subject'   => $sMailSubject,
            'body'      => $sMailBody,
            'header'    => $sMailHeader,
            'params'    => $sMailParameters,
            'html'      => $isHtml,
        );

        $oZ = new ChWsbAlerts('profile', 'send_mail', $aRecipientInfo ? $aRecipientInfo['ID'] : 0, '', $aAlertData);
        $oZ -> alert();
        //--- create system event [ end ]

        return $iRet;
    }

    function actionAdministration ()
    {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $iId = $this->_oDb->getSettingsCategory();
        if(empty($iId)) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_ch_smtp_administration'));
            return;
        }

        ch_import('ChWsbAdminSettings');

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) {
            $oSettings = new ChWsbAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new ChWsbAdminSettings($iId);
        $sResult = $oSettings->getForm();

        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;

        $aVars = array (
            'content' => $sResult,
        );
        echo $this->_oTemplate->adminBlock ($this->_oTemplate->parseHtmlByName('default_padding', $aVars), _t('_ch_smtp_administration'));

        $aVars = array (
            'content' => _t('_ch_smtp_help_text')
        );
        echo $this->_oTemplate->adminBlock ($this->_oTemplate->parseHtmlByName('default_padding', $aVars), _t('_ch_smtp_help'));

        $aVars = array (
            'content' => $this->formTester(),
        );
        echo $this->_oTemplate->adminBlock ($this->_oTemplate->parseHtmlByName('default_padding', $aVars), _t('_ch_smtp_tester'));

        $this->_oTemplate->addCssAdmin ('forms_adv.css');
        $this->_oTemplate->pageCodeAdmin (_t('_ch_smtp_administration'));
    }

    function formTester()
    {
        $sMsg  = '';
        if ($_POST['tester_submit']) {

            $sRecipient = process_pass_data($_POST['recipient']);
            $sSubj = process_pass_data($_POST['subject']);
            $sBody = process_pass_data($_POST['body']);
            $isHTML = $_POST['html'] == 'on' ? true : false;

            if (sendMail($sRecipient, $sSubj, $sBody, 0, array(), $isHTML ? 'html' : ''))
                $sMsg = MsgBox(_t('_ch_smtp_send_ok'));
            else
                $sMsg = MsgBox(_t('_ch_smtp_send_fail'));
        }

        $aForm = array(
            'form_attrs' => array(
                'action' => CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/',
                'method'   => 'post',
            ),
            'inputs' => array (
                'header' => array(
                    'type' => 'block_header',
                    'caption' => _t('_ch_smtp_tester'),
                ),
                'recipient' => array(
                    'type' => 'text',
                    'name' => 'recipient',
                    'caption' => _t('_ch_smtp_recipient'),
                    'value' => '',
                ),
                'subject' => array(
                    'type' => 'text',
                    'name' => 'subject',
                    'caption' => _t('_ch_smtp_subject'),
                    'value' => '',
                ),
                'body' => array(
                    'type' => 'textarea',
                    'name' => 'body',
                    'caption' => _t('_ch_smtp_body'),
                    'value' => '',
                ),
                'html' => array(
                    'type' => 'checkbox',
                    'name' => 'html',
                    'caption' => _t('_ch_smtp_is_html'),
                    'checked' => false,
                ),
                'Submit' => array(
                    'type' => 'submit',
                    'name' => 'tester_submit',
                    'value' => _t("_Submit"),
                ),
            )
        );

        ch_import('ChTemplFormView');
        $oForm = new ChTemplFormView($aForm);
        return $sMsg . $oForm->getCode();
    }

    function isAdmin ()
    {
        return $GLOBALS['logged']['admin'] ? true : false;
    }

    function log ($s)
    {
        $fn = CH_DIRECTORY_PATH_MODULES . "cheetah/smtpmailer/data/logs/log.log";
        $f = @fopen ($fn, 'a');
        if (!$f) return;
        fwrite ($f, date(DATE_RFC822) . "\t" . $s . "\n");
        fclose ($f);
    }
}
