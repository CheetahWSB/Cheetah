<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_INC . 'admin_design.inc.php');

ch_import('Module', $aModule);

global $_page;
global $_page_cont;

$iIndex = 9;
$_page['name_index'] = $iIndex;
$_page['header'] = _t('_ch_pageac');

if(!@isAdmin()) {
    send_headers_page_changed();
    login_form("", 1);
    exit;
}

$oModule = new ChPageACModule($aModule);

$_page_cont[$iIndex]['page_main_code'] = $oModule->_oTemplate->getTabs();

PageCodeAdmin();
