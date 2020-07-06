<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

define('CH_TAGS_ACTION_HOME', 'home');
define('CH_TAGS_ACTION_ALL', 'all');
define('CH_TAGS_ACTION_POPULAR', 'popular');
define('CH_TAGS_ACTION_CALENDAR', 'calendar');
define('CH_TAGS_ACTION_SEARCH', 'search');

define('CH_TAGS_BOX_DISIGN', 1);
define('CH_TAGS_BOX_INT_MENU', 2);

require_once( 'inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );

ch_import('ChTemplTags');
ch_import('ChWsbPageView');
ch_import('ChTemplCalendar');

$bAjaxMode = isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ? true : false;

function showTags($aParam = array(), $iBoxId = 1, $sAction = '', $iBox = 0, $sTitle = '')
{
    $oTags = new ChTemplTags();

    $oTags->getTagObjectConfig($aParam);

    if (empty($oTags->aTagObjects)) {
        if ($iBox & CH_TAGS_BOX_DISIGN)
            return DesignBoxContent($sTitle, MsgBox(_t('_Empty')), 1);
        else
            return MsgBox(_t('_Empty'));
    }

    $aParam['type'] = isset($_GET['tags_mode']) && isset($oTags->aTagObjects[$_GET['tags_mode']]) ? $_GET['tags_mode'] : $oTags->getFirstObject();

    $sCode = '';
    if ($iBox & CH_TAGS_BOX_INT_MENU)
        $sCode .= $oTags->getTagsInternalMenuHtml($aParam, $iBoxId, $sAction);
    $sCode .= $oTags->display($aParam, $iBoxId, $sAction);

    if ($iBox & CH_TAGS_BOX_DISIGN) {
        $aCaptionMenu = $iBox & CH_TAGS_BOX_INT_MENU ? '' : $oTags->getTagsTopMenuHtml($aParam, $iBoxId, $sAction);
        $sCode = DesignBoxContent($sTitle, $sCode, 1, $aCaptionMenu);
        $sCode = '<div id="page_block_' . $iBoxId . '">' . $sCode . '<div class="clear_both"></div></div>';
        return $sCode;
    } else
        return array(
            $sCode,
            ($iBox & CH_TAGS_BOX_INT_MENU ? '' : $oTags->getTagsTopMenu($aParam, $sAction)),
            array(),
            ($sDate ? _t('_tags_by_day') . $sDate : '')
        );
}

class TagsCalendar extends ChTemplCalendar
{
    function __construct($iYear, $iMonth)
    {
        parent::__construct($iYear, $iMonth);
    }

    function display($isMiniMode = false)
    {
        $sTopControls = $GLOBALS['oSysTemplate']->parseHtmlByName('calendar_top_controls.html', array(
            'month_prev_url' => $this->getBaseUri () . "&year={$this->iPrevYear}&month={$this->iPrevMonth}",
            'month_next_url' => $this->getBaseUri () . "&year={$this->iNextYear}&month={$this->iNextMonth}",
            'month_current' => $this->getTitle(),
        ));

        $sHtml = $GLOBALS['oSysTemplate']->parseHtmlByName('calendar.html', array (
        	'top_controls' => $sTopControls,
            'ch_repeat:week_names' => $this->_getWeekNames (),
            'ch_repeat:calendar_row' => $this->_getCalendar (),
        	'bottom_controls' => $sTopControls,
        ));
        $sHtml = preg_replace ('#<ch_repeat:events>.*?</ch_repeat:events>#s', '', $sHtml);
        $GLOBALS['oSysTemplate']->addCss('calendar.css');
        return $sHtml;
    }

    function getData()
    {
        $oDb = ChWsbDb::getInstance();

        return $oDb->getAll("SELECT *, DAYOFMONTH(`Date`) AS `Day`
            FROM `sys_tags`
            WHERE `Date` >= TIMESTAMP(?) AND `Date` < TIMESTAMP(?)",
            [
                "{$this->iYear}-{$this->iMonth}-1",
                "{$this->iNextYear}-{$this->iNextMonth}-1"
            ]
        );
    }

    function getBaseUri()
    {
        return CH_WSB_URL_ROOT . 'tags.php?action=calendar';
    }

    function getBrowseUri()
    {
        return CH_WSB_URL_ROOT . 'tags.php?action=calendar';
    }

    function getEntriesNames ()
    {
        return array(_t('_tags_single'), _t('_tags_plural'));
    }

    function _getCalendar ()
    {
        $sBrowseUri = $this->getBrowseUri();
        list ($sEntriesSingle, $sEntriesMul) = $this->getEntriesNames ();

        $this->_getCalendarGrid($aCalendarGrid);
        $aRet = array ();
        for ($i = 0; $i < 6; $i++) {

            $aRow = array ('ch_repeat:cell');
            $isRowEmpty = true;

            for ($j = $this->iWeekStart; $j < $this->iWeekEnd; $j++) {

                $aCell = array ();

                if ($aCalendarGrid[$i][$j]['today']) {
                    $aCell['class'] = 'sys_cal_cell sys_cal_today';
                    $aCell['day'] = $aCalendarGrid[$i][$j]['day'];
                    $aCell['ch_if:num'] = array ('condition' => $aCalendarGrid[$i][$j]['num'], 'content' => array(
                        'num' => $aCalendarGrid[$i][$j]['num'],
                        'href' => $sBrowseUri . '&year=' . $this->iYear . '&month=' . $this->iMonth . '&day=' . $aCell['day'],
                        'entries' => 1 == $aCalendarGrid[$i][$j]['num'] ? $sEntriesSingle : $sEntriesMul,
                    ));
                    $isRowEmpty = false;
                } elseif (isset($aCalendarGrid[$i][$j]['day'])) {
                    $aCell['class'] = 'sys_cal_cell';
                    $aCell['day'] = $aCalendarGrid[$i][$j]['day'];
                    $aCell['ch_if:num'] = array ('condition' => $aCalendarGrid[$i][$j]['num'], 'content' => array(
                        'num' => $aCalendarGrid[$i][$j]['num'],
                        'href' => $sBrowseUri . '&year=' . $this->iYear . '&month=' . $this->iMonth . '&day=' . $aCell['day'],
                        'entries' => 1 == $aCalendarGrid[$i][$j]['num'] ? $sEntriesSingle : $sEntriesMul,
                    ));
                    $isRowEmpty = false;
                } else {
                    $aCell['class'] = 'sys_cal_cell_blank';
                    $aCell['day'] = '';
                    $aCell['ch_if:num'] = array ('condition' => false, 'content' => array(
                        'num' => '',
                        'href' => '',
                        'entries' => '',
                    ));
                }

                if ($aCell)
                    $aRow['ch_repeat:cell'][] = $aCell;
            }

            if ($aRow['ch_repeat:cell'] && !$isRowEmpty) {
                $aRet[] = $aRow;
            }
        }
        return $aRet;
    }

}

class TagsHomePage extends ChWsbPageView
{
    var $sPage;

    function __construct()
    {
        $this->sPage = 'tags_home';
        parent::__construct($this->sPage);
    }

    function getBlockCode_Recent($iBlockId)
    {
        $aParam = array(
            'orderby' => 'recent',
            'limit' => getParam('tags_show_limit'),
        );

        return showTags($aParam, $iBlockId, CH_TAGS_ACTION_HOME, CH_TAGS_BOX_INT_MENU, _t('_tags_recent'));
    }

    function getBlockCode_Popular($iBlockId)
    {
        $aParam = array(
            'orderby' => 'popular',
            'limit' => getParam('tags_show_limit')
        );

        return showTags($aParam, $iBlockId, CH_TAGS_ACTION_HOME, 0, _t('_tags_popular'));
    }
}

class TagsCalendarPage extends ChWsbPageView
{
    var $sPage;

    function __construct()
    {
        $this->sPage = 'tags_calendar';
        parent::__construct($this->sPage);
    }

    function getBlockCode_Calendar($iBlockId)
    {
        $sYear = isset($_GET['year']) ? (int)$_GET['year'] : '';
        $sMonth = isset($_GET['month']) ? (int)$_GET['month'] : '';
        $oCalendar = new TagsCalendar($sYear, $sMonth);

        return $oCalendar->display();
    }

    function getBlockCode_TagsDate($iBlockId)
    {
        if (isset($_GET['year']) && isset($_GET['month']) && isset($_GET['day'])) {
            $aParam = array(
                'pagination' => getParam('tags_perpage_browse'),
                'date' => array(
                    'year' => (int)$_GET['year'],
                    'month' => (int)$_GET['month'],
                    'day' => (int)$_GET['day']
                )
            );

            return showTags($aParam, $iBlockId, CH_TAGS_ACTION_CALENDAR, 0, _t('_tags_by_day'));
        } else
            return MsgBox(_t('_Empty'));
    }
}

class TagsSearchPage extends ChWsbPageView
{
    var $aSearchForm;
    var $oForm;
    var $sPage;

    function __construct()
    {
        $this->sPage = 'tags_search';
        parent::__construct($this->sPage);

        ch_import('ChTemplFormView');
        $this->aSearchForm = array(
            'form_attrs' => array(
                'name'     => 'form_search_tags',
                'action'   => '',
                'method'   => 'post',
            ),

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
            ),

            'inputs' => array(
                'Keyword' => array(
                    'type' => 'text',
                    'name' => 'Keyword',
                    'caption' => _t('_tags_caption_keyword'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1, 100),
                        'error' => _t ('_tags_err_keyword'),
                    ),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                    'colspan' => true,
                ),
            ),
        );

        $this->oForm = new ChTemplFormView($this->aSearchForm);
        $this->oForm->initChecker();
    }

    function getBlockCode_Form()
    {
        return $GLOBALS['oSysTemplate']->parseHtmlByName('search_tags_box.html', array('form' => $this->oForm->getCode()));
    }

    function getBlockCode_Founded($iBlockId)
    {
        $aParam = array(
            'pagination' => getParam('tags_perpage_browse')
        );

        $sFilter = ch_get('filter');
        if ($sFilter !== false)
            $aParam['filter'] = process_db_input($sFilter);
        else if ($this->oForm->isSubmittedAndValid())
            $aParam['filter'] = $this->oForm->getCleanValue('Keyword');

        if (isset($aParam['filter']))
            return showTags($aParam, $iBlockId, CH_TAGS_ACTION_SEARCH, 0, _t('_tags_founded_tags'));
        else
            return MsgBox(_t('_Empty'));
    }
}

function getPage_Home()
{
    $oHomePage = new TagsHomePage();

    return $oHomePage->getCode();
}

function getPage_All()
{
    $aParam = array(
        'pagination' => getParam('tags_perpage_browse')
    );

    return showTags($aParam, 1, CH_TAGS_ACTION_ALL, CH_TAGS_BOX_DISIGN, _t('_all_tags'));
}

function getPage_Popular()
{
    $aParam = array(
        'orderby' => 'popular',
        'limit' => getParam('tags_show_limit')
    );

    return showTags($aParam, 2, CH_TAGS_ACTION_POPULAR, CH_TAGS_BOX_DISIGN, _t('_popular_tags'));
}

function getPage_Calendar()
{
    $oCalendarPage = new TagsCalendarPage();

    return $oCalendarPage->getCode();
}

function getPage_Search()
{
    $oSearchPage = new TagsSearchPage();

    return $oSearchPage->getCode();
}

$sAction = empty($_GET['action']) ? '' : $_GET['action'];
switch ($sAction) {
    case CH_TAGS_ACTION_POPULAR:
        $sContent = getPage_Popular();
        break;

    case CH_TAGS_ACTION_ALL:
        $sContent = getPage_All();
        break;

    case CH_TAGS_ACTION_CALENDAR:
        $sContent = getPage_Calendar();
        break;

    case CH_TAGS_ACTION_SEARCH:
        $sContent = getPage_Search();
        break;

    default:
        $sContent = getPage_Home();
}

if (!$bAjaxMode) {
    global $_page;
    global $_page_cont;
    $iIndex = 25;

    $_page['name_index']    = $iIndex;
    $_page['css_name']      = 'tags.css';

    $_page['header'] = _t('_Tags');
    $_page['header_text'] = _t('_Tags');
    $_page_cont[$iIndex]['page_main_code'] = $sContent;

    check_logged();
    PageCode();
} else
    echo $sContent;
