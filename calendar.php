<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('./inc/header.inc.php');
require_once( CH_DIRECTORY_PATH_INC  . 'design.inc.php' );
require_once(CH_DIRECTORY_PATH_INC . 'admin.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'db.inc.php');

ch_import('ChWsbProfileFields');
ch_import('ChWsbProfilesController');
ch_import("ChTemplProfileView");
ch_import("ChTemplProfileView");
ch_import("ChTemplSearchProfile");

check_logged();

$_page['name_index'] = 7;
$_page['css_name']   = 'browse.css';

$_page['header'] = _t('_People_Calendar');
$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = getBlockCode_Results(100);

PageCode();

function getBlockCode_Results($iBlockID)
{
    $sAction = strip_tags($_GET['action']);
    switch ($sAction) {
        case 'browse':
            $sCode = getProfilesByDate($_GET['date']);
            break;
        default:
            $sCode = getCalendar();
    }
    return $sCode;
}

function getProfilesByDate ($sDate)
{
    $sDate = strip_tags($sDate);
    $aDateParams = explode('/', $sDate);
    $oSearch = new ChTemplSearchProfile('calendar', (int)$aDateParams[0], (int)$aDateParams[1], (int)$aDateParams[2]);
    $oSearch -> aConstants['linksTempl']['browseAll'] = 'calendar.php?';

    $sCode = $oSearch->displayResultBlock();
    return $oSearch->displaySearchBox('<div class="search_container">'
        . $sCode . '</div>', $oSearch->showPagination(false, false, false));
}

function getCalendar ()
{
    ch_import("ChTemplProfileGenerator");
    $oProfile = new ChTemplProfileGenerator(getLoggedId());
    $mSearchRes = $oProfile->GenProfilesCalendarBlock();
    list($sResults, $aDBTopMenu, $sPagination, $sTopFilter) = $mSearchRes;
    return DesignBoxContent(_t('_People_Calendar'), $sResults, 1);
}
