<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$sFile = "files/" . (int)$_GET['id'] . ".jpg";
if(!file_exists($sFile))
    $sFile = "files/default.png";

header("Content-type: image/jpeg");
readfile($sFile);
