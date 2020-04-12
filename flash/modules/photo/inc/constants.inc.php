<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

if(!defined("IMAGE_EXTENSION")) define("IMAGE_EXTENSION", ".jpg");
if(!defined("THUMB_FILE_NAME")) define("THUMB_FILE_NAME", "_small");

$aInfo = array(
    'mode' => "as3",
    'title' => "Photo Shooter",
    'version' => "7.2.0000",
    'code' => "photo_7.2.0000",
    'author' => "Cheetah",
    'authorUrl' => "https://www.cheetahwsb.com"
);
$aModules = array(
    'shooter' => array(
        'caption' => 'Photo Shooter',
        'parameters' => array('id', 'extra'),
        'js' => array(),
        'inline' => true,
        'vResizable' => false,
        'hResizable' => false,
        'reloadable' => true,
        'layout' => array('top' => 0, 'left' => 0, 'width' => 400, 'height' => 300),
                                'minSize' => array('width' => 250, 'height' => 230),
        'div' => array(
            'style' => array('text-align' => 'center')
        )
    )
);
