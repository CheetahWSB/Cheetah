<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbFilesConfig.php');
require_once(CH_DIRECTORY_PATH_ROOT . "flash/modules/mp3/inc/constants.inc.php");

class ChSoundsConfig extends ChWsbFilesConfig
{
    /**
     * Constructor
     */
    function __construct (&$aModule)
    {
        parent::__construct($aModule);

        // only image files can added/removed here, changing list of sound files requires source code modification
        // image files support square resizing, just specify 'square' => true
        $this->aFilesConfig = array (
        	'browse' => array('postfix' => SCREENSHOT_EXT, 'fallback' => 'default.png', 'image' => true, 'w' => 240, 'h' => 240, 'square' => true),
        	'browse2x' => array('postfix' => '-2x' . SCREENSHOT_EXT, 'fallback' => 'default.png', 'image' => true, 'w' => 480, 'h' => 480, 'square' => true),
            'file' => array('postfix' => MP3_EXTENSION),
        );

        $this->aGlParams = array(
            'mode_top_index' => 'ch_sounds_mode_index',
            'category_auto_approve' => 'category_auto_activation_ch_sounds',
        );

        $this->initConfig();
    }

    function getFilesPath ()
    {
        return CH_DIRECTORY_PATH_ROOT . 'flash/modules/mp3/files/';
    }

    function getFilesUrl ()
    {
        return CH_WSB_URL_ROOT . 'flash/modules/mp3/files/';
    }
}
