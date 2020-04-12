<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'utils.inc.php' );
ch_import('ChWsbAdminSettings');

$logged['admin'] = member_auth( 1, true, true );

$mixedCategory = 0;
if(ch_get('cat') !== false)
    $mixedCategory = ch_get('cat');

$oSettings = new ChWsbAdminSettings($mixedCategory);

//--- Process submit ---//
$sResult = '';
if(isset($_POST['save']) && isset($_POST['cat'])) {
    $sResult = $oSettings->saveChanges($_POST);
}

$iNameIndex = 3;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('forms_adv.css', 'settings.css'),
    'header' => $oSettings->getTitle(),
);
$_page_cont[$iNameIndex]['page_main_code'] = DesignBoxAdmin(_t('_adm_page_cpt_settings'), $sResult . $oSettings->getForm(), '', '', 11);
if (26 == $mixedCategory) {
    define('CH_PROMO_CODE', adm_hosting_promo());
}

PageCodeAdmin();
