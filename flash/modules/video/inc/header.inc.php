<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

if(empty($GLOBALS['sModule'])) $GLOBALS['sModule'] = "video";
$GLOBALS['sModuleUrl'] = $GLOBALS['sModulesUrl'] . $GLOBALS['sModule'] . "/";
$GLOBALS['sFilesDir'] = "files/";
$GLOBALS['sFilesUrl'] = $GLOBALS['sModuleUrl'] . $GLOBALS['sFilesDir'];
$GLOBALS['sFilesPath'] = $GLOBALS['sModulesPath'] . $GLOBALS['sModule'] . "/" . $GLOBALS['sFilesDir'];
$GLOBALS['sServerApp'] = "video";
$GLOBALS['sStreamsFolder'] = "streams/";
$GLOBALS['aConvertTmpls'] = array(
    "playX264" => $GLOBALS['sFfmpegPath'] . " -y -i #input# -b:v #bitrate#k -vcodec libx264 -s #size# #audio_options# #output#",
    "play" => $GLOBALS['sFfmpegPath'] . " -y -i #input# -r 25 -b:v #bitrate#k -s #size# #audio_options# #output#",
    "image" => $GLOBALS['sFfmpegPath'] . " -y -i #input# #size# -ss #second# -vframes 1 -an -f image2 #output#",
);
