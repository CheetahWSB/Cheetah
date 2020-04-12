<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    if( isset($_REQUEST['gConf']) ) die; // globals hack prevention
    require_once ($gConf['dir']['layouts'] . 'base/params.php');

    $gConf['dir']['xsl'] = $gConf['dir']['layouts'] . 'uni/xsl/';	// xsl dir

    $gConf['url']['css'] = $gConf['url']['layouts'] . 'uni/css/';	// css url
    $gConf['url']['xsl'] = $gConf['url']['layouts'] . 'uni/xsl/';	// xsl url
