<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( 'inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );

$_page['name_index']	= 7;

check_logged();

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = MainPageCode();

$_page['header'] = _t('_Empty');
$_page['header_text'] = _t('_Empty');

function MainPageCode()
{
    return MsgBox('Sorry, Under Development');
}

PageCode();
