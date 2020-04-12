<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_INC . 'admin.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'db.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'languages.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'profiles.inc.php');

/**
 * Get information about avaliable rooms in XML format.
 * @comment - Refreshed
 */

 function save($sSavedId, $sFilePath, $sTitle)
 {
    @copy($sFilePath, $sFilePath . ".tmp");
    $sFilePath .= ".tmp";
    $aRes = array('status' => FAILED_VAL, 'value' => "msgErrorSave");

    define ('CH_BOARD_PHOTOS_CAT', 'Board');
    define ('CH_BOARD_PHOTOS_TAG', 'Board');

    $aUser = getProfileInfo();
    $aFileInfo = array (
        'medTitle' => stripslashes($sTitle), 'medDesc' => stripslashes($sTitle),
        'medTags' => CH_BOARD_PHOTOS_TAG, 'Categories' => array(CH_BOARD_PHOTOS_CAT),
        'album' => str_replace('{nickname}', $aUser["NickName"], getParam('ch_photos_profile_album_name'))
    );

    if ($sSavedId > 0) {
        $iRet = ChWsbService::call('photos', 'perform_photo_replace', array($sFilePath, $aFileInfo, false, $sSavedId), 'Uploader');
        if ($iRet) {
            return array('status' => SUCCESS_VAL, 'value' => $sSavedId);
        }
    } else {
        $iRet = ChWsbService::call('photos', 'perform_photo_upload', array($sFilePath, $aFileInfo, false), 'Uploader');
        if ($iRet) {
            return array('status' => SUCCESS_VAL, 'value' => $iRet);
        }
    }

    return $aRes;
 }

 function getSavedBoardInfo($sId, $iBoardId)
 {
    global $aXmlTemplates;

    $aBoard = ChWsbService::call('photos', 'get_photo_array', array($iBoardId, 'original'), 'Search');
    if(count($aBoard)==0 || $sId != $aBoard["owner"])
        $sResult = parseXml($aXmlTemplates["result"], "msgSavedError", FAILED_VAL);
    else {
        $sResult = parseXml($aXmlTemplates["result"], $iBoardId, SUCCESS_VAL);
        $sResult .= parseXml($aXmlTemplates["savedBoard"], $aBoard["file"], $aBoard["title"]);
    }
    return $sResult;
 }
