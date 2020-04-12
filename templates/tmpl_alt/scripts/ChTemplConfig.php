<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( CH_DIRECTORY_PATH_BASE . 'scripts/ChBaseConfig.php' );

/***
 template variables
***/

// path to the images used in the template
$site['images']	= $site['url'] . "templates/tmpl_{$GLOBALS['tmpl']}/images/";
$site['zodiac']	= $site['url'] . "templates/base/images/zodiac/";
$site['icons']	= $site['images'] . "icons/";
$site['css_dir']= "templates/tmpl_{$GLOBALS['tmpl']}/css/";

class ChTemplConfig extends ChBaseConfig
{
    function __construct($site)
    {
        ChBaseConfig::__construct($site);
    }
}
