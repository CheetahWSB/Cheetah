<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPrivacy');

class ChBlogsPrivacy extends ChWsbPrivacy
{
    /**
    * Constructor
    */
    function __construct(&$oModule)
    {
        parent::__construct('ch_blogs_posts', 'PostID', 'OwnerID');
    }

    /**
    * Get database field name for action.
    *
    * @param string $sAction action name.
    * @return string with field name.
    */
    function getFieldAction($sAction)
    {
        return 'allow' . str_replace(' ', '', ucwords(str_replace('_', ' ', $sAction)));
    }
}
