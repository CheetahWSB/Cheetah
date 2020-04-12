<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    ch_import('ChTemplMenuSimple');

    /**
     * Bottom menu
     *
     * Related classes:
     *  @see ChBaseMenuBottom - bottom menu base representation
     *  @see ChTemplMenuBottom - bottom menu template representation
     *
     * Table structure - `sys_menu_bottom`;
     *
     * Memberships/ACL:
     * no levels
     *
     * Alerts:
     * no alerts
     */
    class ChWsbMenuBottom extends ChTemplMenuSimple
    {
        function __construct()
        {
            parent::__construct();

            $this->sName = 'bottom';
            $this->sDbTable = 'sys_menu_bottom';
            $this->sCacheKey = 'sys_menu_bottom';
        }
    }
