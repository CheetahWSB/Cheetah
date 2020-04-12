<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function post($sTable, $sId, $sAuthor, $sParent, $sMood, $sFileId)
{
    global $sIncPath;
    global $sModule;
    global $sHomeUrl;

    require($sIncPath . "content.inc.php");
    $sText = getEmbedCode($sModule, "player", array('id' => $sFileId, 'file' => TRUE_VAL));
    $sText = str_replace($sHomeUrl, "[ray_url]", $sText);
    $sSql = "INSERT INTO `" . $sTable . "`(`cmt_parent_id`, `cmt_object_id`, `cmt_author_id`, `cmt_text`, `cmt_mood`, `cmt_time`) VALUES('" . $sParent . "', '" . $sId . "', '" . $sAuthor . "', '" . $sText . "', '" . $sMood . "', NOW())";
    getResult($sSql);
    return getLastInsertId();
}
