<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( CH_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $aModule['class_prefix'] . 'Module.php');

global $_page;
global $_page_cont;

$iId = isset($_COOKIE['memberID']) ? (int)$_COOKIE['memberID'] : 0;
$_page['name_index']	= 57;
$_page['css_name']		= 'main.css';

// --------------- page variables and login

check_logged();

$_page['header'] = _t( "_chat_page_rules_caption" );
$_page['header_text'] = _t( "_chat_page_rules_caption" );

// --------------- page components

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = PageCompMainCode();

// --------------- [END] page components

PageCode();

// --------------- page components functions

/**
 * page code function
 */
function PageCompMainCode()
{
    return DesignBoxContent( _t( "_chat_page_rules_caption" ), '<div class="dbContent">' . _t( "_chat_rules" ) . '</div>', $GLOBALS['oTemplConfig'] -> PageCompThird_db_num);
}
