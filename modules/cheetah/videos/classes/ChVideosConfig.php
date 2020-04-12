<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbFilesConfig.php');
require_once(CH_DIRECTORY_PATH_ROOT . "flash/modules/video/inc/constants.inc.php");

class ChVideosConfig extends ChWsbFilesConfig
{
    /**
     * Constructor
     */
    function __construct (&$aModule)
    {
        parent::__construct($aModule);

        // only image files can added/removed here, changing list of video files requires source code modification
        // image files support square resizing, just specify 'square' => true
        $this->aFilesConfig = array (
            'poster' => array('postfix' => IMAGE_EXTENSION, 'image' => true), // first image must be not square
            'browse' => array('postfix' => THUMB_FILE_NAME . IMAGE_EXTENSION, 'image' => true, 'w' => 240, 'h' => 240, 'square' => true),
            'browse2x' => array('postfix' => THUMB_FILE_NAME . '_2x' . IMAGE_EXTENSION, 'image' => true, 'w' => 480, 'h' => 480, 'square' => true),
            'main' => array('postfix' => FLV_EXTENSION),
            'mpg' => array('postfix' => '.mpg'),
            'file' => array('postfix' => MOBILE_EXTENSION),
            'm4v' => array('postfix' => M4V_EXTENSION),
        );

        $this->aGlParams = array(
            'mode_top_index' => 'ch_videos_mode_index',
            'category_auto_approve' => 'category_auto_activation_ch_videos',
        );

        $sProto = ch_proto();

        if(!defined("YOUTUBE_VIDEO_PLAYER"))
            define("YOUTUBE_VIDEO_PLAYER", '<iframe width="100%" height="315" src="' . $sProto . '://www.youtube-nocookie.com/embed/#video#?rel=0&amp;showinfo=0#autoplay#" frameborder="0" allowfullscreen></iframe>');

        if(!defined("YOUTUBE_VIDEO_EMBED"))
            define("YOUTUBE_VIDEO_EMBED", '<iframe width="560" height="315" src="' . $sProto . '://www.youtube-nocookie.com/embed/#video#?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>');

        $this->initConfig();
    }

    function getFilesPath ()
    {
        return CH_DIRECTORY_PATH_ROOT . 'flash/modules/video/files/';
    }

    function getFilesUrl ()
    {
        return CH_WSB_URL_ROOT . 'flash/modules/video/files/';
    }
}
