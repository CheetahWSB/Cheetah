<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbDb');

class ChWsbPaymentsQuery extends ChWsbDb
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getObjects()
    {
        $aObjects = $this->getAll("SELECT * FROM `sys_objects_payments` WHERE 1");
        if(empty($aObjects) || !is_array($aObjects))
            return array();

        return $aObjects;
    }
}
