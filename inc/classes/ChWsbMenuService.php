<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    ch_import('ChTemplMenuSimple');

    /**
     * Service menu
     *
     * Related classes:
     *  @see ChBaseMenuService - service menu base representation
     *  @see ChTemplMenuService - service menu template representation
     *
     * Table structure - `sys_menu_service`;
     *
     * Memberships/ACL:
     * no levels
     *
     * Alerts:
     * no alerts
     */
    class ChWsbMenuService extends ChTemplMenuSimple
    {
        function __construct()
        {
            parent::__construct();

            $this->sName = 'service';
            $this->sDbTable = 'sys_menu_service';
            $this->sCacheKey = 'sys_menu_service';
        }
    }
