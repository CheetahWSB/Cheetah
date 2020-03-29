<?php

/**
 * This work, "Cheetah - https://cheetah.deanbassett.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( CH_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $aModule['class_prefix'] . 'Module.php');

global $_page;
global $_page_cont;

$iId = ( isset($_COOKIE['memberID']) && ($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) ) ? (int) $_COOKIE['memberID'] : 0;
$iIndex = 57;

$_page['name_index']	= $iIndex;
$_page['css_name']		= 'main.css';

$_page['header'] = _t('_board_page_caption');
$_page['header_text'] = _t('_board_box_caption', $site['title']);

$oBoard = new ChBoardModule($aModule);
$_page_cont[$iIndex]['page_main_code'] = $oBoard->getContent($iId, $aRequest[0]);

PageCode($oBoard->_oTemplate);
