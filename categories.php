<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

define('CH_CATEG_ACTION_ALL', 'all');
define('CH_CATEG_ACTION_COMMON', 'common');
define('CH_CATEG_ACTION_USERS', 'users');
define('CH_CATEG_ACTION_CALENDAR', 'calendar');
define('CH_CATEG_ACTION_SEARCH', 'search');

require_once( 'inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );

ch_import('ChTemplCategories');
ch_import('ChWsbPageView');
ch_import('ChTemplCalendar');

$bAjaxMode = isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ? true : false;

function showCategories($aParam = array(), $iBoxId = 1, $sAction = '', $isBox = false, $bOrderPanel = false, $sTitle = '')
{
    $oCateg = new ChTemplCategories();

    $oCateg->getTagObjectConfig($aParam);

    if (empty($oCateg->aTagObjects)) {
        if ($isBox)
            return DesignBoxContent($sTitle, MsgBox(_t('_Empty')), 1);
        else
            return MsgBox(_t('_Empty'));
    }

    $aParam['type'] = isset($_GET['tags_mode']) && isset($oCateg->aTagObjects[$_GET['tags_mode']]) ? $_GET['tags_mode'] : $oCateg->getFirstObject();

    $sCode = $oCateg->display($aParam, $iBoxId, $sAction, $bOrderPanel, getParam('categ_show_columns'));

    if ($isBox) {
        $aCaptionMenu = $oCateg->getCategTopMenuHtml($aParam, $iBoxId, $sAction);
        $sCode = DesignBoxContent($sTitle, $sCode, 1, $aCaptionMenu);
        $sCode = '<div id="page_block_' . $iBoxId . '">' . $sCode . '<div class="clear_both"></div></div>';
        return $sCode;
    } else
        return array(
            $sCode,
            $oCateg->getCategTopMenu($aParam, $sAction),
            array(),
            ($sDate ? _t('_categ_by_day') . $sDate : '')
        );
}

class CategoriesCalendar extends ChTemplCalendar
{
    function __construct($iYear, $iMonth)
    {
        parent::__construct($iYear, $iMonth);
    }

    function display()
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
            FROM `sys_categories`
            WHERE `Date` >= TIMESTAMP(?)
            AND `Date` < TIMESTAMP(?)
            AND `Owner` <> 0 AND `Status` = 'active'",
            [
                "{$this->iYear}-{$this->iMonth}-1",
                "{$this->iNextYear}-{$this->iNextMonth}-1",
            ]
        );
    }

    function getBaseUri()
    {
        return CH_WSB_URL_ROOT . 'categories.php?action=calendar';
    }

    function getBrowseUri()
    {
        return CH_WSB_URL_ROOT . 'categories.php?action=calendar';
    }

    function getEntriesNames ()
    {
        return array(_t('_categ_single'), _t('_categ_plural'));
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

class CategoriesCalendarPage extends ChWsbPageView
{
    var $sPage;

    function __construct()
    {
        $this->sPage = 'categ_calendar';
        parent::__construct($this->sPage);
    }

    function getBlockCode_Calendar($iBlockId)
    {
        $sYear = isset($_GET['year']) ? (int)$_GET['year'] : '';
        $sMonth = isset($_GET['month']) ? (int)$_GET['month'] : '';
        $oCalendar = new CategoriesCalendar($sYear, $sMonth);

        return $oCalendar->display();
    }

    function getBlockCode_CategoriesDate($iBlockId)
    {
        if (isset($_GET['year']) && isset($_GET['month']) && isset($_GET['day'])) {
            $aParam = array(
                'pagination' => getParam('categ_perpage_browse'),
                'date' => array(
                    'year' => (int)$_GET['year'],
                    'month' => (int)$_GET['month'],
                    'day' => (int)$_GET['day']
                )
            );

            return showCategories($aParam, $iBlockId, CH_CATEG_ACTION_CALENDAR, false, false, _t('_categ_caption_calendar'));
        } else
            return MsgBox(_t('_Empty'));
    }
}

class CategoriesSearchPage extends ChWsbPageView
{
    var $aSearchForm;
    var $oForm;
    var $sPage;

    function __construct()
    {
        $this->sPage = 'categ_search';
        parent::__construct($this->sPage);

        ch_import('ChTemplFormView');
        $this->aSearchForm = array(
            'form_attrs' => array(
                'name'     => 'form_search_categories',
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
                    'caption' => _t('_categ_caption_keyword'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1, 100),
                        'error' => _t ('_categ_err_keyword'),
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
            'pagination' => getParam('categ_perpage_browse')
        );
        $sFilter = ch_get('filter');
        if ($sFilter !== false)
            $aParam['filter'] = process_db_input($sFilter);
        else if ($this->oForm->isSubmittedAndValid())
            $aParam['filter'] = $this->oForm->getCleanValue('Keyword');

        if (isset($aParam['filter']))
            return showCategories($aParam, $iBlockId, CH_CATEG_ACTION_SEARCH, false, false, _t('_categ_caption_founded_categ'));
        else
            return MsgBox(_t('_Empty'));
    }
}

function getPage_All()
{
    $aParam = array(
        'pagination' => getParam('categ_perpage_browse')
    );

    return showCategories($aParam, 1, CH_CATEG_ACTION_ALL, true, true, _t('_categ_caption_all'));
}

function getPage_Common()
{
    $aParam = array(
        'pagination' => getParam('categ_perpage_browse'),
        'common' => true,
    );

    return showCategories($aParam, 1, CH_CATEG_ACTION_COMMON, true, false, _t('_categ_caption_common'));
}

function getPage_Users()
{
    $aParam = array(
        'pagination' => getParam('categ_perpage_browse'),
        'common' => false
    );

    return showCategories($aParam, 1, CH_CATEG_ACTION_USERS, true, false, _t('_categ_caption_users'));
}

function getPage_Calendar()
{
    $oCalendarPage = new CategoriesCalendarPage();

    return $oCalendarPage->getCode();
}

function getPage_Search()
{
    $oSearchPage = new CategoriesSearchPage();

    return $oSearchPage->getCode();
}

$sAction = empty($_GET['action']) ? '' : $_GET['action'];
switch ($sAction) {
    case CH_CATEG_ACTION_COMMON:
        $sContent = getPage_Common();
        break;

    case CH_CATEG_ACTION_USERS:
        $sContent = getPage_Users();
        break;

    case CH_CATEG_ACTION_CALENDAR:
        $sContent = getPage_Calendar();
        break;

    case CH_CATEG_ACTION_SEARCH:
        $sContent = getPage_Search();
        break;

    default:
        $sContent = getPage_All();
}

if (!$bAjaxMode) {
    global $_page;
    global $_page_cont;
    $iIndex = 25;

    $_page['name_index']    = $iIndex;
    $_page['css_name']      = 'tags.css';

    $_page['header'] = _t('_Categories');
    $_page['header_text'] = _t('_Categories');
    $_page_cont[$iIndex]['page_main_code'] = $sContent;

    check_logged();
    PageCode();
} else
    echo $sContent;
