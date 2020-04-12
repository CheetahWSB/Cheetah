<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

if( isset($_REQUEST['gConf']) ) die; // globals hack prevention

$gConf['dir']['xsl'] = $gConf['dir']['layouts'] . 'base/xsl/';	// xsl dir

$gConf['url']['icon'] = $gConf['url']['layouts'] . 'base/icons/';	// icons url
$gConf['url']['img'] = $gConf['url']['layouts'] . 'base/img/';	// img url
$gConf['url']['css'] = $gConf['url']['layouts']  . 'base/css/';	// css url
$gConf['url']['xsl'] = $gConf['url']['layouts'] . 'base/xsl/';	// xsl url
