<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin_design.inc.php' );

$logged['admin'] = member_auth( 1, true, true );

$aProfile = getProfileInfo();

$iNameIndex = 0;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array(),
    'js_name' => array(),
    'header' => _t('_adm_page_cpt_fapps'),
    'header_text' => _t('_adm_box_cpt_fapps')
);
$_page_cont[$iNameIndex]['page_main_code'] = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => getApplicationContent(GLOBAL_MODULE, "admin", array("nick" => $aProfile['NickName'], "password" => $aProfile['Password']), true)));

PageCodeAdmin();
