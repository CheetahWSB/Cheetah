<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

chdir ('..');

require_once ('./inc/header.inc.php');

require_once ($gConf['dir']['classes'].'ChJsGzipLoader.php');

$aJsGzip = array ('ChError.js', 'ChXmlRequest.js', 'ChXslTransform.js', 'util.js', 'ChHistory.js', 'ChForum.js', 'ChAdmin.js', 'ChLogin.js', 'ChEditor.js');
new ChJsGzipLoader ('ja', $aJsGzip, $gConf['dir']['js'], $gConf['dir']['cache']);

//new ChJsGzipLoader ('d', $gConf['dir']['base'] . 'js/', '', $gConf['dir']['cache']);
