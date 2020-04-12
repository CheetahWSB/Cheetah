<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbDb');

/**
 * @see ChWsbSocialSharing
 */
class ChWsbSocialSharingQuery extends ChWsbDb
{

    function __construct()
    {
        parent::__construct();
    }

    function getActiveButtons ()
    {
        return $this->fromCache('sys_objects_social_sharing', 'getAll', 'SELECT * FROM `sys_objects_social_sharing` WHERE `active` = 1 ORDER BY `order` ASC');
    }

}
