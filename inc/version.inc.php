<?php

/**
 * Cheetah - Social Network Software Platform. Copyright (c) Dean J. Bassett Jr. - https://www.cheetahwsb.com
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

// Version can be in one of 2 different formats.
// This one used 99% of the time is in YY.MM.DD format.
// And this one used only when more than one update is released in a single day is in YY.MM.DD.HH.MM format.
// More than one update in a single day would be very rare. Not likley to happen.
// Both formats use short form. No 0 padding on values below 10.
// Both formats are also based on the Gregorian calendar.
$site['version'] = '21.12.1';
$site['modifier'] = ''; // Set to Dev, Alpha, Beta, RC1, ect as needed.

// This adds a version number to the end of loaded JS and CSS files.
// Helps with installs and upgrades when browsers cache the data.
// The different version number forces the browser to reload the files.
$iIncrement = 0; // Add a manual increment value if needed to the timestamp generated from the version.
$aVersion = explode('.', $site['version']);
if((int)$aVersion[3] != 0) {
    $sTimeStamp = strtotime($aVersion[0] . "-" . $aVersion[1] . "-" . $aVersion[2] . " " . $aVersion[3] . ":" . $aVersion[4]);
} else {
    $sTimeStamp = strtotime($aVersion[0] . "-" . $aVersion[1] . "-" . $aVersion[2] . " 00:00");
}
define('CH_WSB_CSS_JS_VER', $sTimeStamp+(int)$iIncrement);  // Unix timestamp when this version was released.

header('X-Powered-By: Cheetah ' . $site['version'], false);
