<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( './inc/header.inc.php' );
require_once( './inc/db.inc.php' );
require_once( './inc/profiles.inc.php' );

$aPredefinedRssFeeds = array (
    'cheetah_news' => 'https://www.cheetahwsb.com/m/news/act_rss',
    'cheetah_version' => 'https://www.cheetahwsb.com/version.rss',
    'cheetah_market' => 'https://www.cheetahwsb.com/m/market/act_rss',
    'cheetah_market_lang_files' => 'https://www.cheetahwsb.com/m/market/act_rss/language',
    'cheetah_market_templates' => 'https://www.cheetahwsb.com/m/market/act_rss/template',
    'cheetah_market_featured' => 'https://www.cheetahwsb.com/m/market/act_rss/featured',
);

if (isset($aPredefinedRssFeeds[$_GET['ID']])) {

    $sCont = $aPredefinedRssFeeds[$_GET['ID']];

} elseif (0 === strncmp('forum|', $_GET['ID'], 6)) {

    $a = explode('|', $_GET['ID']);
    if (!is_array($a) || 3 != count($a))
        exit;

    $sCont = CH_WSB_URL_ROOT . $a[0] . '/' . $a[1] . '/rss/forum/' . $a[2] . '.htm';

} else {

    $sQuery = "SELECT `Content` FROM `sys_page_compose` WHERE `ID` = " . (int)$_GET['ID'];
    $sCont = db_value( $sQuery );

    if( !$sCont )
        exit;
}

list( $sUrl ) = explode( '#', $sCont );
$sUrl = str_replace( '{SiteUrl}', $site['url'], $sUrl );

$iMemID = (int)$_GET['member'];
if( $iMemID ) {
    $aMember = getProfileInfo( $iMemID );
    $sUrl = str_replace( '{NickName}', $aMember['NickName'], $sUrl );
}

header( 'Content-Type: text/xml' );
echo ch_file_get_contents(defined('CH_PROFILER') && CH_PROFILER && 0 === strncmp($site['url'], $sUrl, strlen($site['url'])) ? ch_append_url_params($sUrl, 'ch_profiler_disable=1') : $sUrl);
