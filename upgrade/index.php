<?php
/**
 * Copyright (c) Cheetah Pty Limited - https://www.cheetahwsb.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('./../inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_ROOT . 'upgrade/classes/ChWsbUpgradeController.php');
require_once(CH_DIRECTORY_PATH_ROOT . 'upgrade/classes/ChWsbUpgradeUtil.php');
require_once(CH_DIRECTORY_PATH_ROOT . 'upgrade/classes/ChWsbUpgradeDb.php');

define ('CH_UPGRADE_DIR_UPGRADES', CH_DIRECTORY_PATH_ROOT . 'upgrade/files/');
define ('CH_UPGRADE_DIR_TEMPLATES', CH_DIRECTORY_PATH_ROOT . 'upgrade/templates/');

$sFolder = $_REQUEST['folder'];

include (CH_UPGRADE_DIR_TEMPLATES . '_header.php');

$oController = new ChWsbUpgradeController ();

if (!$sFolder)
    $oController->showAvailableUpgrades();
else
    $oController->runUpgrade($sFolder);

include (CH_UPGRADE_DIR_TEMPLATES . '_footer.php');
