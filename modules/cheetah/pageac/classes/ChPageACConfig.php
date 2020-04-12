<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbConfig');

require_once( CH_DIRECTORY_PATH_INC . 'membership_levels.inc.php' );

class ChPageACConfig extends ChWsbConfig
{
    /**
     * Constructor
     */
    var $_aMemberships;
    function __construct($aModule)
    {
        parent::__construct($aModule);
        $this->_aMemberships = getMemberships();
    }
}
