<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_INC . "utils.inc.php");
require_once(CH_DIRECTORY_PATH_CLASSES . "ChWsbInstallerUtils.php");

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

function getBlockedUsers($sBlockerId)
{
    $aUsers = array();
    $rResult = getResult("SELECT `Profile` FROM `sys_block_list` WHERE `ID`='" . $sBlockerId . "'");
    while($aUser = $rResult->fetch())
        $aUsers[] = $aUser['Profile'];
    return $aUsers;
}
