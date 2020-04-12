<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbFilesConfig.php');

class ChPhotosConfig extends ChWsbFilesConfig
{
    /**
     * Constructor
     */
    function __construct (&$aModule)
    {
        parent::__construct($aModule);

        $this->aFilesConfig = array (
            'icon' => array('postfix' => '_ri.jpg', 'size_def' => 32, 'square' => true),
            'thumb' => array('postfix' => '_rt.jpg', 'size_def' => 64, 'square' => true),
            'browse' => array('postfix' => '_t.jpg', 'size_def' => 240, 'square' => true),
        	'browse2x' => array('postfix' => '_t_2x.jpg', 'size_def' => 480, 'square' => true),
            'file' => array('postfix' => '_m.jpg', 'size_def' => 600),
            'original' => array('postfix' => '.{ext}'),
        );

        $this->aGlParams = array(
                'auto_activation' => 'ch_photos_activation',
                'mode_top_index' => 'ch_photos_mode_index',
                'category_auto_approve' => 'category_auto_activation_ch_photos',
        );

        $this->aDefaultAlbums[] = 'profile_cover_album_name';

        if(!defined("FLICKR_PHOTO_RSS"))
            define("FLICKR_PHOTO_RSS", "https://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key=#api_key#&photo_id=#photo#");
        if(!defined("FLICKR_PHOTO_URL"))
            define("FLICKR_PHOTO_URL", "https://farm#farm#.static.flickr.com/#server#/#id#_#secret##mode#.#ext#");

        $this->initConfig();
    }
}
