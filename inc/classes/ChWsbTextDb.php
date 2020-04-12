<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModuleDb');

class ChWsbTextDb extends ChWsbModuleDb
{
    var $_oConfig;

    function __construct(&$oConfig)
    {
        parent::__construct($oConfig);

        $this->_oConfig = &$oConfig;
    }
    /**
     * Get entries.
     */
    function getEntries($aParams = array())
    {
        $sMethod = 'getAll';
        $sSelectClause = $sWhereClause = $sOrderClause = $sLimitClause = "";
        switch($aParams['sample_type']) {
            case 'id':
                $sMethod = 'getRow';
                $sWhereClause = " AND `te`.`id`='" . $aParams['id'] . "'";
                $sOrderClause = "`te`.`when` DESC";
                $sLimitClause = "LIMIT 1";
                break;
            case 'uri':
                $sMethod = 'getRow';
                $sWhereClause = " AND `te`.`uri`='" . $aParams['uri'] . "'";
                $sOrderClause = "`te`.`when` DESC";
                $sLimitClause = "LIMIT 1";
                break;
            case 'view':
                $sWhereClause = " AND `te`.`uri`='" . $aParams['uri'] . "' AND `te`.`status`='" . CH_TD_STATUS_ACTIVE . "'";
                $sOrderClause = "`te`.`when` DESC";
                $sLimitClause = "LIMIT 1";
                break;
            case 'search_unit':
                $sWhereClause = " AND `te`.`uri`='" . $aParams['uri'] . "'";
                $sOrderClause = "`te`.`when` DESC";
                $sLimitClause = "LIMIT 1";
                break;
            case 'archive':
                $sWhereClause = " AND `te`.`status`='" . CH_TD_STATUS_ACTIVE . "'";
                $sOrderClause = "`te`.`when` DESC";
                $sLimitClause = "LIMIT " . (int)$aParams['start'] . ', ' . (int)$aParams['count'];
                break;
            case 'featured':
                $sWhereClause = " AND `te`.`status`='" . CH_TD_STATUS_ACTIVE . "' AND `te`.`featured`='1'";
                $sOrderClause = "`te`.`when` DESC";
                $sLimitClause = "LIMIT " . (int)$aParams['start'] . ', ' . (int)$aParams['count'];
                break;
            case 'top_rated':
                $sWhereClause = " AND `te`.`status`='" . CH_TD_STATUS_ACTIVE . "'";
                $sOrderClause = "`te`.`rate` DESC";
                $sLimitClause = "LIMIT " . (int)$aParams['start'] . ', ' . (int)$aParams['count'];
                break;
            case 'popular':
                $sWhereClause = " AND `te`.`status`='" . CH_TD_STATUS_ACTIVE . "'";
                $sOrderClause = "`te`.`view_count` DESC";
                $sLimitClause = "LIMIT " . (int)$aParams['start'] . ', ' . (int)$aParams['count'];
                break;
            case 'admin':
                $sWhereClause = !empty($aParams['filter_value']) ? " AND (`caption` LIKE '%" . $aParams['filter_value'] . "%' OR `content` LIKE '%" . $aParams['filter_value'] . "%' OR `tags` LIKE '%" . $aParams['filter_value'] . "%')" : "";
                $sOrderClause = "`te`.`when` DESC";
                $sLimitClause = "LIMIT " . $aParams['start'] . ', ' . $aParams['count'];
                break;
            case 'all':
                $sWhereClause = " AND `te`.`status`='" . CH_TD_STATUS_ACTIVE . "'";
                $sOrderClause = "`te`.`when` DESC";
                break;
        }
        $sSql = "SELECT
                   " . $sSelectClause . "
                   `te`.`id` AS `id`,
                   `te`.`caption` AS `caption`,
                   `te`.`snippet` AS `snippet`,
                   `te`.`content` AS `content`,
                   `te`.`when` AS `when_uts`,
                   DATE_FORMAT(FROM_UNIXTIME(`te`.`when`), '%Y-%m-%d %H:%i') AS `when`,
                   DATE_FORMAT(FROM_UNIXTIME(`te`.`when`), '" . $this->_oConfig->getDateFormat() . "') AS `when_uf`,
                   UNIX_TIMESTAMP() - `te`.`when` AS `ago`,
                   `te`.`uri` AS `uri`,
                   `te`.`tags` AS `tags`,
                   `te`.`categories` AS `categories`,
                   `te`.`comment` AS `comment`,
                   `te`.`vote` AS `vote`,
                   `te`.`date` AS `date`,
                   `te`.`status` AS `status`,
                   `te`.`featured` AS `featured`,
                   `te`.`cmts_count` AS `cmts_count`
                FROM `" . $this->_sPrefix . "entries` AS `te`
                WHERE 1 " . $sWhereClause . "
                ORDER BY " . $sOrderClause . " " . $sLimitClause;
        $aResult = $this->$sMethod($sSql);

        if(!in_array($aParams['sample_type'], array('id', 'uri', 'view'))) {
            $iSnippetLen = $this->_oConfig->getSnippetLength();

            for($i = 0; $i < count($aResult); $i++)
                $aResult[$i]['content'] = mb_substr(str_replace(array('&nbsp;', '&lt;', '&gt;'), array(' ', '', ''), strip_tags($aResult[$i]['snippet'])), 0, $iSnippetLen);
        }

        return $aResult;
    }
    /**
     * Delete entries.
     *
     * @param  integer/array $mixed ID or an array of ID-s.
     * @return boolean       result of operation.
     */
    function deleteEntries($mixed)
    {
        if(!is_array($mixed))
            $mixed = array($mixed);

        $sSql = "DELETE FROM `" . $this->_sPrefix . "entries` WHERE `id` IN ('" . implode("', '", $mixed) . "')";
        return $this->query($sSql) > 0;
    }
    /**
     * Update entries.
     *
     * @param  integer/array $mixed   ID or an array of ID-s.
     * @param  array         $aValues key/value pears to be saved in the DB.
     * @return boolean       result of operation.
     */
    function updateEntry($mixed, $aValues)
    {
        if(!is_array($mixed))
            $mixed = array($mixed);

        $sSql = "";
        foreach($aValues as $sKey => $sValue)
           $sSql .= "`" . $sKey . "`='" . $sValue . "', ";
        $sSql = "UPDATE `" . $this->_sPrefix . "entries` SET " . substr($sSql, 0, -2) . " WHERE `id` IN ('" . implode("', '", $mixed) . "')";
        return $this->query($sSql) > 0;
    }
    function getCount($aParams = array())
    {
        if(!isset($aParams['sample_type']))
            $aParams['sample_type'] = '';

        switch($aParams['sample_type']) {
            case 'featured':
                $sWhereClause = "`status`='" . CH_TD_STATUS_ACTIVE . "' AND `featured`='1'";
                break;
            case 'admin':
                $sWhereClause = !empty($aParams['filter_value']) ? "(`caption` LIKE '%" . $aParams['filter_value'] . "%' OR `content` LIKE '%" . $aParams['filter_value'] . "%' OR `tags` LIKE '%" . $aParams['filter_value'] . "%')" : "1";
                break;
            default:
                $sWhereClause = "`status`='" . CH_TD_STATUS_ACTIVE . "'";
                break;
        }
        $sSql = "SELECT COUNT(`id`) FROM `" . $this->_sPrefix . "entries` WHERE " . $sWhereClause . " LIMIT 1";
        return (int)$this->getOne($sSql);
    }
    function getByMonth($iYear, $iMonth, $iNextYear, $iNextMonth)
    {
        $sSql = "SELECT
               *,
               DAYOFMONTH(FROM_UNIXTIME(`when`)) AS `Day`
            FROM `" . $this->_sPrefix . "entries`
            WHERE `when` >= UNIX_TIMESTAMP('" . $iYear . "-" . $iMonth . "-1') AND `when` < UNIX_TIMESTAMP('" . $iNextYear . "-" . $iNextMonth . "-1') AND `status` = '0'";
        return $this->getAll($sSql);
    }
    function publish(&$aIds)
    {
        $aIds = $this->getColumn("SELECT
                `id`
            FROM `" . $this->_sPrefix . "entries`
            WHERE `status`='" . CH_TD_STATUS_PENDING . "' AND `when`<=UNIX_TIMESTAMP()");
        if(empty($aIds))
            return false;

        $iStatus = $this->_oConfig->isAutoapprove() ? CH_TD_STATUS_ACTIVE : CH_TD_STATUS_INACTIVE;
        return (int)$this->query("UPDATE `" . $this->_sPrefix . "entries`
            SET `status`='" . $iStatus . "'
            WHERE `id` IN ('" . implode("','", $aIds) . "')") > 0;
    }
}
