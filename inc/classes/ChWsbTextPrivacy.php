<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPrivacy');

class ChWsbTextPrivacy extends ChWsbPrivacy
{
    var $_oModule;

    function __construct(&$oModule)
    {
        parent::__construct($oModule->_oDb->getPrefix() . 'entries', 'id', 'author_id');

        $this->_oModule = $oModule;
    }
}
