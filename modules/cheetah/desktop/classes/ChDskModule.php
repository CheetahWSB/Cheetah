<?php

/**
 * This work, "Cheetah - https://cheetah.deanbassett.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbModule.php');

class ChDskModule extends ChWsbModule
{
    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);
    }

    function serviceGetFileUrl()
    {
        return CH_WSB_URL_MODULES . 'cheetah/desktop/file/desktop.air';
    }
}
