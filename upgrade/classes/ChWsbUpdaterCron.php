<?php

/**
 * Cheetah - Social Network Software Platform. Copyright (c) Dean J. Bassett Jr. - https://www.cheetahwsb.com
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_INC . 'db.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'classes/ChWsbCron.php');

class ChWsbUpdaterCron extends ChWsbCron
{
    // This function handles checking for, downloading and notifying of any updates found.
    public function processing()
    {
        $bUpdates = $this->chCheckForUpdates('cron');
    }

    public function chCheckForUpdates($sMode = '')
    {
        set_time_limit(36000);
        ignore_user_abort();

        $bUpdaterSupported = (int)getParam('sys_updater_supported') > 0 ? true : false;

        $sCurrentVersion = $GLOBALS['site']['ver'] . '.' . $GLOBALS['site']['build'];
        $sFrom = '';
        $sTo = '';
        $bUpdates = false;
        $sVersionCombined = '';
        // Eventually the versions.txt will be downloaded from github. But for now, use my website downloads url.
        //$sVerFileUrl = 'https://raw.githubusercontent.com/CheetahWSB/Cheetah/master/upgrade/versions.txt';
        $sVerFileUrl = 'https://www.cheetahwsb.com/downloads/versions.txt';
        // Make sure the remote file exists. If not, return false.
        $handle = fopen($sVerFileUrl, 'r');
        $sVerFileUrl = (!$handle) ? false : $sVerFileUrl;
        if($sVerFileUrl === false) return false;
        
        file_put_contents(CH_DIRECTORY_PATH_TMP . 'versions.txt', ch_file_get_contents($sVerFileUrl));
        $aVersions = file(CH_DIRECTORY_PATH_TMP . 'versions.txt');
        foreach ($aVersions as $aVersion) {
            $aSplit = explode('-', $aVersion);
            if (trim($aSplit[0]) == $sCurrentVersion) {
                $sVersionCombined = trim($aVersion);
                $sFrom = trim($aSplit[0]);
                $sTo = trim($aSplit[1]);
                break;
            }
        }
        if($bUpdaterSupported) {
            if ($sTo != '') {
                // Eventually the zip files will be downloaded from github. But for now, use my website downloads url.
                //$sDownloadUrl = 'https://raw.githubusercontent.com/CheetahWSB/Cheetah/master/upgrade/files/' . $sVersionCombined . '/zips/';
                $sDownloadUrl = 'https://www.cheetahwsb.com/downloads/';
                $sFile = 'Cheetah-' . $sVersionCombined . '-ai.zip';
                file_put_contents(CH_DIRECTORY_PATH_TMP . $sFile, ch_file_get_contents($sDownloadUrl . $sFile));
                $zip = new ZipArchive();
                $res = $zip->open(CH_DIRECTORY_PATH_TMP . $sFile);
                if ($res === true) {
                    $zip->extractTo(CH_DIRECTORY_PATH_ROOT);
                    $zip->close();
                }
                unlink(CH_DIRECTORY_PATH_TMP . $sFile);
                // zip downloaded and extracted. Now inform admin that new version is available to install.
                if ($sMode == 'cron') {
                    // run by cron, so send email message to administrator.
                    $aAdmins = $GLOBALS['MySQL']->getAll("SELECT * FROM `Profiles` WHERE `Role`&" . CH_WSB_ROLE_ADMIN . "<>0 AND `EmailNotify`='1'");
                    $iCounter = 0;
                    $oEmailTemplate = new ChWsbEmailTemplates();
                    $aEmailParams = array(
                        'MessageText' => _t('_adm_updates_msg_main2', 1),
                        'ViewLink' => CH_WSB_URL_ADMIN . 'upgrade/index.php'
                    );
                    foreach ($aAdmins as $aAdmin) {
                        $aMail = $oEmailTemplate->parseTemplate('t_AdminUpdates', $aEmailParams, $aAdmin['ID']);
                        if (sendMail($aAdmin['Email'], $aMail['subject'], $aMail['body'], $aAdmin['ID'], array(), 'html', false, true)) {
                            $iCounter++;
                        }
                    }                
                } else {
                    $bUpdates = true;
                }
            }
        } else {
            // Updater not supported. Inform user if update is available and provide link to page to download it.
            if ($sTo != '') {
                $sDownloadUrl = 'https://www.cheetahwsb.com/downloads/';
                $sFile = 'Cheetah-' . $sVersionCombined . '-mi.zip';
                echo 'The website needs to be running PHP as FPM for the updater to properly work.<br>';
                echo 'FPM also must be configured to run as the same user that uploaded the website.<br>';
                echo 'In it\'s current configuration, your server does not support the new updater.<br><br>';
                echo 'Any new updates will have to be installed manually until you resolved this problem.<br><br>';
                echo 'You can download the latest updates with instructions from the Cheetah website.<br>';
                echo '<a href="https://www.cheetahwsb.com/page/downloads">https://www.cheetahwsb.com/page/downloads</a><br><br>';

                $bUpdates = true;
            }
        }
        return $bUpdates;
    }
}
