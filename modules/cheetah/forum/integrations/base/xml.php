<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

/**
 *
 * Add xml contents to whole xml output
 * put xml content to $integration_xml variable
 *******************************************************************************/

global $site;
global $tmpl;
global $glHeader;
global $glFooter;
global $gConf;

if (isset($gConf['title']) && $gConf['title'])
    $glHeader = preg_replace('#<title>(.*)</title>#', "<title>" . str_replace(array('<![CDATA[', ']]>'), '', $gConf['title']) . "</title>", $glHeader);

$integration_xml .= '<url_cheetah>' . $site['url'] . '</url_cheetah>';
$integration_xml .= '<skin_cheetah>' . $tmpl . '</skin_cheetah>';
$integration_xml .= '<header><![CDATA[' . str_replace(array('<![CDATA[', ']]>'), '', $glHeader) . ']]></header>';
$integration_xml .= '<footer><![CDATA[' . str_replace(array('<![CDATA[', ']]>'), '', $glFooter) . ']]></footer>';
