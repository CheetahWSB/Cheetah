<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'params.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'utils.inc.php');

ch_import('ChWsbPrivacy');
ch_import('ChWsbUserStatusView');
ch_import('ChWsbSubscription');

$iMemberId  = getLoggedId();

if (!isset($_GET['ID']) && !(int)$_GET['ID'])
    exit;

$iProfId = (int)$_GET['ID'];
$aProfileInfo = getProfileInfo($iProfId);

$sProfLink = '<a href="' . getProfileLink($iProfId) . '">' . getNickName($aProfileInfo['ID']) . '</a> ';

$oUserStatus = new ChWsbUserStatusView();
$sUserIcon = $oUserStatus->getStatusIcon($iProfId);
$sUserStatus = $oUserStatus->getStatus($iProfId);

$aUnit = array(
    'status_icon' => $sUserIcon,
    'profile_status' => _t('_prof_status', $sProfLink, $sUserStatus),
    'profile_status_message' => $aProfileInfo['status_message'],
    'profile_actions' => $oFunctions->getProfileViewActions($iProfId, true),
);

header('Content-type:text/html;charset=utf-8');
echo $oFunctions->transBox($oSysTemplate->parseHtmlByName('short_profile_info.html', $aUnit) . $sAddon);
