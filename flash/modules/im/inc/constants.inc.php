<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

if(!defined("USER_STATUS_ONLINE")) define("USER_STATUS_ONLINE", "online");
if(!defined("USER_STATUS_OFFLINE")) define("USER_STATUS_OFFLINE", "offline");

$aInfo = array(
    'mode' => "as3",
    'title' => "Messenger",
    'version' => "7.2.0000",
    'code' => "im_7.2.0000",
    'author' => "Cheetah",
    'authorUrl' => "https://www.cheetahwsb.com/"
);
$aModules = array(
    'user' => array(
        'caption' => 'Messenger',
        'parameters' => array('sender', 'password', 'recipient'),
        'js' => array(),
        'inline' => false,
        'vResizable' => true,
        'hResizable' => true,
        'layout' => array('top' => 0, 'left' => 0, 'width' => 550, 'height' => 450),
                                'minSize' => array('width' => 550, 'height' => 400),
        'reloadable' => true,
        'div' => array()
    )
);
