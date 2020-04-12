<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbDb');

/**
 * Database queries for member info objects.
 *
 * @see ChWsbMemberInfo
 */
class ChWsbMemberInfoQuery extends ChWsbDb
{
    protected $_aObject;

    public function __construct($aObject)
    {
        parent::__construct();
        $this->_aObject = $aObject;
    }

    static public function getMemberInfoObject($sObject)
    {
        $oDb     = ChWsbDb::getInstance();
        $sQuery  = "SELECT * FROM `sys_objects_member_info` WHERE `object` = ?";
        $aObject = $oDb->getRow($sQuery, [$sObject]);
        if (!$aObject || !is_array($aObject)) {
            return false;
        }

        return $aObject;
    }

    static public function getMemberInfoKeysByType($sType)
    {
        $oDb      = ChWsbDb::getInstance();
        $sQuery   = "SELECT * FROM `sys_objects_member_info` WHERE `type` = ? ORDER BY `title` ASC";
        $aObjects = $oDb->getPairs($sQuery, 'object', 'title', [$sType]);
        if (!$aObjects || !is_array($aObjects)) {
            return false;
        }

        foreach ($aObjects as $k => $v) {
            $aObjects[$k] = _t($v);
        }

        return $aObjects;
    }

}
