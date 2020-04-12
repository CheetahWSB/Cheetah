<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbDb');

/**
 * @see ChWsbExport
 */
class ChWsbExportQuery extends ChWsbDb
{
    protected $_aSystem;

    public function __construct ($aSystem)
    {
        parent::__construct();
        $this->_aSystem = $aSystem;
    }

    static public function getAllActiveSystemsFromCache ()
    {
        return $GLOBALS['MySQL']->fromCache('sys_objects_exports', 'getAllWithKey', 'SELECT * FROM `sys_objects_exports` WHERE `active` = 1 ORDER BY `order`', 'object');
    }
}
