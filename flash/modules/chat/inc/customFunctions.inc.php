<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_INC . "utils.inc.php");
require_once(CH_DIRECTORY_PATH_INC . "membership_levels.inc.php");
require_once(CH_DIRECTORY_PATH_CLASSES . "ChWsbInstallerUtils.php");

function rzGetMembershipId($sUserId)
{
    $aMembership = getMemberMembershipInfo_current($sUserId);
    return $aMembership["ID"];
}

function rzGetMemberships()
{
    $aMemberships = array();
    $rResult = getResult("SELECT * FROM `sys_acl_levels`");
	$iCount = $rResult->rowCount();
    for($i=0; $i<$iCount; $i++) {
        $aMembership = $rResult->fetch();
        $aMemberships[$aMembership["ID"]] = $aMembership["Name"];
    }
    return $aMemberships;
}

function getAdminIds()
{
    $rResult = getResult("SELECT `ID` FROM `Profiles` WHERE (`Role` & 2)");
    $aIds = array();
	$iCount = $rResult->rowCount();
    for($i=0; $i<$iCount; $i++) {
        $aId = $rResult->fetch();
        $aIds[] = (int)$aId['ID'];
    }
    return $aIds;
}

function isUserAdmin($iId)
{
    $aIds = getAdminIds();
    return in_array((int)$iId, $aIds);
}

function getUserVideoLink()
{
    global $sModulesUrl;
    if(ChWsbInstallerUtils::isModuleInstalled("videos"))
        return $sModulesUrl . "video/videoslink.php?id=#user#";
    return "";
}

function getUserMusicLink()
{
    global $sModulesUrl;
    if(ChWsbInstallerUtils::isModuleInstalled("sounds"))
        return $sModulesUrl . "mp3/soundslink.php?id=#user#";
    return "";
}

function blockUser($iUserId, $iBlockedId, $bBlock)
{
    ch_import('ChWsbAlerts');

    if($bBlock) {
        getResult("REPLACE INTO `sys_block_list` SET `ID` = '" . $iUserId . "', `Profile` = '" . $iBlockedId . "'");
        $oZ = new ChWsbAlerts('block', 'add', $iBlockedId, $iUserId);
    } else {
        getResult("DELETE FROM `sys_block_list` WHERE `ID` = '" . $iUserId . "' AND `Profile` = '" . $iBlockedId . "'");
        $oZ = new ChWsbAlerts('block', 'delete', $iBlockedId, $iUserId);
    }
    $oZ->alert();
}

function getBlockingList($sId, $bBlocking)
{
    $sSelectField = $bBlocking ? "ID" : "Profile";
    $sWhereField = $bBlocking ? "Profile" : "ID";

    $sType = getValue("SELECT `Type` FROM `" . MODULE_DB_PREFIX . "Profiles` WHERE `ID`='" . $sId . "' LIMIT 1");
    if(empty($sType))
        $sType = CHAT_TYPE_FULL;
    $aAllTypes = array(CHAT_TYPE_FULL, CHAT_TYPE_MODER, CHAT_TYPE_ADMIN);
    $iTypeIndex = array_search($sType, $aAllTypes);
    if($bBlocking)
        array_splice($aAllTypes, 0, $iTypeIndex);
    else
        array_splice($aAllTypes, $iTypeIndex+1, count($aAllTypes)-$iTypeIndex-1);
    $sTypes = count($aAllTypes) > 0 ? " AND `profiles`.`Type` IN ('" . implode("','", $aAllTypes) . "')" : "";
    $rResult = getResult("SELECT `blocked`.`" . $sSelectField . "` AS `Member` FROM `sys_block_list` AS `blocked` LEFT JOIN `" . MODULE_DB_PREFIX . "Profiles` AS `profiles` ON `blocked`.`" . $sSelectField . "`=`profiles`.`ID` WHERE `blocked`.`" . $sWhereField . "`='" . $sId . "'" . $sTypes);

    $aUsers = array();
	$iCount = $rResult->rowCount();
    for($i=0; $i<$iCount; $i++) {
        $aBlocked = $rResult->fetch();
        $aUsers[] = $aBlocked["Member"];
    }
    return $aUsers;
}
