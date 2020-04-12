<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbTextSearchResult');

class ChFdbSearchResult extends ChWsbTextSearchResult
{
    function __construct($oModule = null)
    {
        $oModule = !empty($oModule) ? $oModule : ChWsbModule::getInstance('ChFdbModule');
        parent::__construct($oModule);

        $this->aCurrent['searchFields'] = array('caption', 'content', 'tags');
        unset($this->aCurrent['restriction']['category']);
    }
    function getAlterOrder()
    {
        return array('order' => 'ORDER BY `date` DESC');
    }
}
