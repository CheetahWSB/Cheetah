<?php
/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('./../inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'admin.inc.php');
require_once(CH_DIRECTORY_PATH_ROOT . 'upgrade/classes/ChWsbUpgradeController.php');
require_once(CH_DIRECTORY_PATH_ROOT . 'upgrade/classes/ChWsbUpgradeUtil.php');
require_once(CH_DIRECTORY_PATH_ROOT . 'upgrade/classes/ChWsbUpgradeDb.php');
require_once(CH_DIRECTORY_PATH_ROOT . 'upgrade/classes/ChWsbUpdaterCron.php');

define('CH_UPGRADE_DIR_UPGRADES', CH_DIRECTORY_PATH_ROOT . 'upgrade/files/');
define('CH_UPGRADE_DIR_TEMPLATES', CH_DIRECTORY_PATH_ROOT . 'upgrade/templates/');

$logged['admin'] = member_auth(1, true, true);

$sFolder = $_REQUEST['folder'];
$sCheck = $_REQUEST['check'];

include(CH_UPGRADE_DIR_TEMPLATES . '_header.php');

echo $GLOBALS['oSysTemplate']->addCss('common.css', true);

$oController = new ChWsbUpgradeController();

$bUpdaterSupported = (int)getParam('sys_updater_supported') > 0 ? true : false;
//var_dump($bUpdaterSupported);

if (!$sFolder)
    if (!$sCheck) {
        $oController->showAvailableUpgrades();
    } else {
        // Check for updates.
        $oCheck = new ChWsbUpdaterCron();
        $bResult = $oCheck->chCheckForUpdates();
        if ($bResult) {
            echo '<div style="padding: 5px;">Update check complete. Updates found.</div>';
        } else {
            echo '<div style="padding: 5px;">Update check complete. No updates found.</div>';
        }
    } else
    $oController->runUpgrade($sFolder);

if (!$sFolder) {
    $iUpdates = $oController->getUpgradeCount();
    //$iUpdates = 0;
    $sCheckUrl = CH_WSB_URL_ROOT . 'upgrade/?check=true';
    if (!$iUpdates && !$sCheck) {
        $sCheckButton = '<button class="ch-btn ch-btn-small" type="submit" name="adm-mp-deactivate" value="Back" onclick="location.href = \'' . $sCheckUrl . '\';">Check for Updates</button>';
    } else {
        $sCheckButton = '';
    }
    $sAdminUrl = CH_WSB_URL_ADMIN;
    echo <<<CODE
<div class="admin-actions-buttons button_box clearfix">
    <button class="ch-btn ch-btn-small" type="submit" name="adm-mp-deactivate" value="Back" onclick="location.href = '$sAdminUrl';">Back to Admin</button>
    $sCheckButton
</div>
CODE;
}

include(CH_UPGRADE_DIR_TEMPLATES . '_footer.php');
