<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_INC . 'db.inc.php');

define('SHARED_SITES_TABLE', 'sys_shared_sites');

function ch_pre_rawurlencode($sURL) {
    $sEncoded = '';
    $iLength = mb_strlen($sURL);
    for ($i = 0 ; $i < $iLength ; $i++) {
        $c = mb_substr($sURL, $i, 1);
        if (ord($c[0]) >= 0 && ord($c[0]) <= 127)
            $sEncoded .= $c;
        else
            $sEncoded .= rawurlencode(mb_substr($sURL, $i, 1));
    }
    return $sEncoded;
}

function getSitesArray ($sLink)
{
    $aSites = $GLOBALS['MySQL']->fromCache ('sys_shared_sites', 'getAllWithKey', "SELECT `ID` as `id`, `URL` as `url`, `Icon` as `icon`, `Name` FROM `" . SHARED_SITES_TABLE . "`", 'Name');

    $sLink = rawurlencode(ch_pre_rawurlencode($sLink));

    foreach ($aSites as $sKey => $aValue)
        $aSites[$sKey]['url'] .= $sLink;

    return $aSites;
}

function getSitesHtml ($sLink, $sTitle = false)
{
    if (!$sTitle)
        $sTitle = _t('_Share');
    $aSitesPrepare = getSitesArray ($sLink);
    $sIconsUrl = getTemplateIcon('digg.png');
    $sIconsUrl = str_replace('digg.png', '', $sIconsUrl);
    $aSites = array ();
    foreach ($aSitesPrepare as $k => $r) {
        $aSites[] = array (
            'icon' => $sIconsUrl . $r['icon'],
            'name' => $k,
            'url' => $r['url'],
        );
    }

    $aVarsContent = array (
        'ch_repeat:sites' => $aSites,
    );
    $aVarsPopup = array (
        'title' => $sTitle,
        'content' => $GLOBALS['oSysTemplate']->parseHtmlByName('popup_share.html', $aVarsContent),
    );
    return $GLOBALS['oFunctions']->transBox($GLOBALS['oSysTemplate']->parseHtmlByName('popup.html', $aVarsPopup), true);
}
