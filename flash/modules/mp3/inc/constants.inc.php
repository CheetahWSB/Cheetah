<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

if(!defined("TEMP_FILE_NAME")) define("TEMP_FILE_NAME", "_temp");
if(!defined("MP3_EXTENSION")) define("MP3_EXTENSION", ".mp3");
if(!defined("SCREENSHOT_EXT")) define("SCREENSHOT_EXT", ".jpg");

if(!defined("STATUS_APPROVED")) define("STATUS_APPROVED", "approved");
if(!defined("STATUS_DISAPPROVED")) define("STATUS_DISAPPROVED", "disapproved");
if(!defined("STATUS_PENDING")) define("STATUS_PENDING", "pending");
if(!defined("STATUS_PROCESSING")) define("STATUS_PROCESSING", "processing");
if(!defined("STATUS_FAILED")) define("STATUS_FAILED", "failed");

$aInfo = array(
    'mode' => "as3",
    'title' => "Music Player",
    'version' => "7.2.0000",
    'code' => "mp3_7.2.0000",
    'author' => "Cheetah",
    'authorUrl' => "https://www.cheetahwsb.com"
);
$aModules = array(
    'player' => array(
        'caption' => 'Music Player',
        'parameters' => array('id', 'user', 'password'),
        'js' => array(),
        'inline' => true,
        'vResizable' => false,
        'hResizable' => false,
        'reloadable' => true,
        'layout' => array('top' => 0, 'left' => 0, 'width' => "100%", 'height' => 350),
                                   'minSize' => array('width' => 340, 'height' => 350),
        'div' => array()
    ),
    'recorder' => array(
        'caption' => 'Music Recorder',
        'parameters' => array('user', 'password', 'extra'),
        'js' => array(),
        'inline' => true,
        'vResizable' => false,
        'hResizable' => false,
        'reloadable' => false,
        'layout' => array('top' => 0, 'left' => 0, 'width' => "100%", 'height' => 300),
                                   'minSize' => array('width' => 340, 'height' => 300),
        'div' => array()
    )
);
