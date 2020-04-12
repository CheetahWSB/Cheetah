<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

// generate custom $glHeader and $glFooter variables here

// ******************* include cheetah header/footer [begin]

check_logged();

require_once(CH_DIRECTORY_PATH_INC . 'db.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'params.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbPageView.php');


class ChWsbOrcaForumsTemplate extends ChWsbTemplate
{
    function __construct($sRootPath = CH_DIRECTORY_PATH_ROOT, $sRootUrl = CH_WSB_URL_ROOT)
    {
        parent::__construct($sRootPath, $sRootUrl);
        $this->addLocation('ChWsbOrcaForums', $GLOBALS['gConf']['dir']['base'], $GLOBALS['gConf']['url']['base']);
    }
}

class ChWsbOrcaForumsIndex extends ChWsbPageView
{
    var $sMarker = '-=++=-';

    function __construct()
    {
        parent::__construct('forums_index');
    }

    function getBlockCode_FullIndex()
    {
        return $this->sMarker;
    }
}

class ChWsbOrcaForumsHome extends ChWsbPageView
{
    var $sMarker = '-=++=-';

    function __construct()
    {
        parent::__construct('forums_home');
    }

    function getBlockCode_Search()
    {
        $oTemplate = new ChWsbOrcaForumsTemplate();
        $aVars = array(
            'base_url_forum' => $GLOBALS['gConf']['url']['base'],
        );
        return array($oTemplate->parseHtmlByName('search_block.html', $aVars));
    }

    function getBlockCode_ShortIndex()
    {
        global $gConf;

        $s = '<div class="forums_index_short">';
        $ac = $GLOBALS['f']->fdb->getCategs();
        foreach ($ac as $c) {
            $s .= '<div class="forums_index_short_cat ch-def-font-large"><a href="' . $gConf['url']['base'] . sprintf($gConf['rewrite']['cat'], $c['cat_uri'], 0) . '" onclick="return f.selectForumIndex(\'' . $c['cat_uri'] . '\')">'. $c['cat_name'] .'</a></div>';
            $af = $GLOBALS['f']->fdb->getForumsByCatUri (filter_to_db($c['cat_uri']));
            foreach ($af as $ff)
                $s .= '<div class="forums_index_short_forum ch-def-padding-sec-left"><a href="' . $gConf['url']['base'] . sprintf($gConf['rewrite']['forum'], $ff['forum_uri'], 0) . '" onclick="return f.selectForum(\'' . $ff['forum_uri'] . '\', 0)">' . $ff['forum_title'] . '</a></div>';
        }
        $s .= '</div>';
        return array($s);
    }

    function getBlockCode_RecentTopics()
    {
        return $this->sMarker;
    }
}


global $_page, $glHeader, $glFooter, $logged, $_ni;

$GLOBALS['name_index'] = $_page['name_index'] = 55;

$_page['header'] = $gConf['def_title'];
$_page['header_text'] = $gConf['def_title'];

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = '-=++=-';

global $gConf;

$sCssPathUrl = ch_ltrim_str($gConf['url']['css'], CH_WSB_URL_ROOT);
$sCssPathDir = ch_ltrim_str("{$gConf['dir']['layouts']}{$gConf['skin']}/css/", CH_DIRECTORY_PATH_ROOT);
$GLOBALS['oSysTemplate']->addCss ("{$sCssPathDir}|{$sCssPathUrl}|main.css");

$sJsPathUrl = ch_ltrim_str($gConf['url']['js'], CH_WSB_URL_ROOT);
$sJsPathDir = ch_ltrim_str($gConf['dir']['js'], CH_DIRECTORY_PATH_ROOT);
$GLOBALS['oSysTemplate']->addJs (array(
    'history.js',
    "{$sJsPathDir}|{$sJsPathUrl}|util.js",
    "{$sJsPathDir}|{$sJsPathUrl}|ChError.js",
    "{$sJsPathDir}|{$sJsPathUrl}|ChXmlRequest.js",
    "{$sJsPathDir}|{$sJsPathUrl}|ChXslTransform.js",
    "{$sJsPathDir}|{$sJsPathUrl}|ChForum.js",
    "{$sJsPathDir}|{$sJsPathUrl}|ChHistory.js",
    "{$sJsPathDir}|{$sJsPathUrl}|ChLogin.js",
    "{$sJsPathDir}|{$sJsPathUrl}|ChAdmin.js",
));

$GLOBALS['ChWsbTemplateInjections']['page_'.$_ni]['injection_body'][] = array('type' => 'text', 'data' => 'id="body" onload="if(!document.body) { document.body = document.getElementById(\'body\'); }; h = new ChHistory(\'' . $gConf['url']['base'] .  '\'); document.h = h; return h.init(\'h\'); "');

if (CH_ORCA_INTEGRATION == 'cheetah') {
    $aVars = array ('ForumBaseUrl' => $gConf['url']['base']);
    $GLOBALS['oTopMenu']->setCustomSubActions($aVars, 'ch_forum_title', false);
}

if (isLogged()) {
    ch_import('ChWsbEditor');
    $oEditor = ChWsbEditor::getObjectInstance();
    $sEditorId = isset($_REQUEST['new_topic']) ? '#tinyEditor' : '#fakeEditor';
    if ($oEditor) {
        if ('sys_tinymce' == $oEditor->getObjectName())
            $oEditor->setCustomConf('setup :
function(ed) {
    ed.on("init", function(e) {
        if ("undefined" === typeof(glOrcaSettings))
            glOrcaSettings = tinyMCE.activeEditor["settings"];
        orcaInitInstance(ed);
    });
},');
        $sEditor .= $oEditor->attachEditor ($sEditorId, CH_EDITOR_FULL) . '<div id="fakeEditor" style="display:none;"></div>';
    }
}


// add css from pages
$sAction = ch_get('action');
if ('goto' == $sAction && isset($_GET['index'])) {
    $o = new ChWsbOrcaForumsIndex();
    $o->getCode();
}
elseif (!$sAction) {
    $o = new ChWsbOrcaForumsHome();
    $o->getCode();
}


ob_start();
PageCode();
$sCheetahDesign = ob_get_clean();

$iPos = strpos($sCheetahDesign, '-=++=-');
$glHeader = substr ($sCheetahDesign, 0, $iPos) . $sEditor;
$glFooter = substr ($sCheetahDesign, $iPos + 6 - strlen($sCheetahDesign));
$glIndexBegin = '';
$glIndexEnd = '';

// ******************* include cheetah header/footer [ end ]

