<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbRequest.php' );
if(isset($aRequest[0]) && substr($aRequest[0], 0, 4) == 'act_') {
    $aRequest[0] = str_replace('act_', '', $aRequest[0]);
    echo ChWsbRequest::processAsAction($aModule, $aRequest);
} else
    ChWsbRequest::processAsFile($aModule, $aRequest);
