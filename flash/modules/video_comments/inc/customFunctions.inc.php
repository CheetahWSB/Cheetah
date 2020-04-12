<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function post($sSystem, $iId, $iCmtAuthorId, $iCmtParentId, $iMood, $sFileId)
{
    global $sIncPath;
    global $sModule;
    global $sHomeUrl;

    $iId = (int)$iId;
    $iCmtParentId = (int)$iCmtParentId;
    $iMood = (int)$iMood;

    ch_import ('ChWsbCmts');
    $oCmts = ChWsbCmts::getObjectInstance($sSystem, $iId);
    if (!$oCmts)
        return 0;

    $sText = '<iframe width="100%" height="240" src="[ray_url]modules/video_comments/embed.php?id=' . $sFileId . '" frameborder="0" allowfullscreen></iframe>';

    $mixedOverrideResult = null;
    $oAlert = new ChWsbAlerts('ch_video_comments', 'post', $sFileId, getLoggedId(), array(
        'override' => &$mixedOverrideResult,
        'text' => &$sText,
        'file_id' => &$sFileId,
        'object_id' => &$iId,
        'author' => &$iCmtAuthorId,
        'parent_id' => &$iCmtParentId,
        'mood' => &$iMood,
    ));
    $oAlert->alert();

    if (null !== $mixedOverrideResult)
        return $mixedOverrideResult;

    $iCmtNewId = $oCmts->_oQuery->addComment ($iId, $iCmtParentId, $iCmtAuthorId, $sText, $iMood);

    if(false === $iCmtNewId)
        return 0;

    ch_import('ChWsbAlerts');
    $oZ = new ChWsbAlerts($sSystem, 'commentPost', $oCmts->getId(), $oCmts->_getAuthorId(), array('comment_id' => $iCmtNewId, 'comment_author_id' => $iCmtAuthorId));
    $oZ->alert();

    $oCmts->_triggerComment();

    return $iCmtNewId;
}

function deleteFileByCommentId($iCommentId)
{
    global $sModule;
    $sDBModule = DB_PREFIX . ucfirst($sModule);

    $iId = (int)getValue("SELECT `ID` FROM `" . $sDBModule . "Files` WHERE `Description`='" . $iCommentId . "' LIMIT 1");
    if($iId > 0)
        _deleteFile($iId);
}
