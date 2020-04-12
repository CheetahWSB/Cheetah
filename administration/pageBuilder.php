<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

define('CH_SECURITY_EXCEPTIONS', true);
$aChSecurityExceptions = array(
    'POST.Content',
    'REQUEST.Content',
);

require_once( '../inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'images.inc.php' );
ch_import('ChWsbPageViewAdmin');

$logged['admin'] = member_auth( 1, true, true );

$GLOBALS['oAdmTemplate']->addJsTranslation(array(
    '_adm_btn_Column', '_Are_you_sure', '_Empty'
));

$oPVAdm = new ChWsbPageViewAdmin( 'sys_page_compose', 'sys_page_compose.inc' );
