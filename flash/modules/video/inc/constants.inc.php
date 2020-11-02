<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

if(!defined("TEMP_FILE_NAME")) define("TEMP_FILE_NAME", "_temp");
if(!defined("THUMB_FILE_NAME")) define("THUMB_FILE_NAME", "_small");
if(!defined("MOBILE_EXTENSION")) define("MOBILE_EXTENSION", ".mp4");
if(!defined("FLV_EXTENSION")) define("FLV_EXTENSION", ".flv");
if(!defined("M4V_EXTENSION")) define("M4V_EXTENSION", ".m4v");
if(!defined("IMAGE_EXTENSION")) define("IMAGE_EXTENSION", ".jpg");
if(!defined("VIDEO_SIZE_4_3")) define("VIDEO_SIZE_4_3", "480x360");
if(!defined("VIDEO_SIZE_16_9")) define("VIDEO_SIZE_16_9", "640x360");
if(!defined("VIDEO_SIZE_9_16")) define("VIDEO_SIZE_9_16", "360x640");

if(!defined("STATUS_APPROVED")) define("STATUS_APPROVED", "approved");
if(!defined("STATUS_DISAPPROVED")) define("STATUS_DISAPPROVED", "disapproved");
if(!defined("STATUS_PENDING")) define("STATUS_PENDING", "pending");
if(!defined("STATUS_PROCESSING")) define("STATUS_PROCESSING", "processing");
if(!defined("STATUS_FAILED")) define("STATUS_FAILED", "failed");
if(!defined("STATUS_ONHOLD")) define("STATUS_ONHOLD", "onhold");

$aInfo = array(
    'mode' => "as3",
    'title' => "Video Player",
    'version' => "7.2.0000",
    'code' => "video_7.2.0000",
    'author' => "Cheetah",
    'authorUrl' => "https://www.cheetahwsb.com"
);
$aModules = array(
    'player' => array(
        'caption' => 'Video Player',
        'parameters' => array('id', 'user', 'password'),
        'js' => array(),
        'inline' => true,
        'vResizable' => false,
        'hResizable' => false,
        'reloadable' => true,
        'layout' => array('top' => 0, 'left' => 0, 'width' => "100%", 'height' => 400),
                                'minSize' => array('width' => 350, 'height' => 400),
        'div' => array()
    ),
    'recorder' => array(
        'caption' => 'Video Recorder',
        'parameters' => array('user', 'password', 'extra'),
        'js' => array(),
        'inline' => true,
        'vResizable' => false,
        'hResizable' => false,
        'reloadable' => true,
        'layout' => array('top' => 0, 'left' => 0, 'width' => "100%", 'height' => 400),
                                'minSize' => array('width' => 350, 'height' => 400),
        'div' => array()
    )
);
