<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once (CH_DIRECTORY_PATH_CLASSES . 'ChWsbDb.php');

/**
 * @see ChWsbSession
 */
class ChWsbSessionQuery extends ChWsbDb
{
    var $sTable;

    function __construct()
    {
        parent::__construct();

        $this->sTable = 'sys_sessions';
    }
    function getTableName()
    {
        return $this->sTable;
    }
    function exists($sId)
    {
        $aSession = $this->getRow("SELECT `id`, `user_id`, `data` FROM `" . $this->sTable . "` WHERE `id`= ? LIMIT 1", [$sId]);
        return !empty($aSession) ? $aSession : false;
    }
    function save($sId, $aSet)
    {
        $sSetClause = "`id`='" . $sId . "'";
        foreach($aSet as $sKey => $sValue)
            $sSetClause .= ", `" . $sKey . "`='" . $sValue . "'";
        $sSetClause .= ", `date`=UNIX_TIMESTAMP()";

        return (int)$this->query("REPLACE INTO `" . $this->sTable . "` SET " . $sSetClause) > 0;
    }
    function delete($sId)
    {
        return (int)$this->query("DELETE FROM `" . $this->sTable . "` WHERE `id`='" . $sId . "' LIMIT 1") > 0;
    }
    function deleteExpired()
    {
        $iRet = (int)$this->query("DELETE FROM `" . $this->sTable . "` WHERE `date`<(UNIX_TIMESTAMP()-" . CH_WSB_SESSION_LIFETIME . ")");
        $this->query("OPTIMIZE TABLE `" . $this->sTable . "`");
        return $iRet;
    }
}
