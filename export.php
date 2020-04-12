<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( 'inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'utils.inc.php' );

check_logged();

ch_import ('ChWsbExport');

if ('popup' === ch_get('action')) {
    echo PopupBox('ch_profile_export', _t('_adm_txt_langs_export'), $GLOBALS['oSysTemplate']->parseHtmlByName('export.html', array(
        'content' => $GLOBALS['oFunctions']->loadingBoxInline(),
        'profile_id' => (int)ch_get('profile_id'),
    )));
}
else {
    $mixedRes = false;
    if (0 === strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
        $iProfileId = isAdmin() && (int)ch_get('profile_id') ? (int)ch_get('profile_id') : getLoggedId();
        $mixedRes = ChWsbExport::generateAllExports ($iProfileId);
    }

    header('Content-Type: text/html; charset=utf-8');
    if (true === $mixedRes) {
        $aProfile = getProfileInfo($iProfileId);
        echo json_encode(array('err' => 0, 'msg' => _t('_sys_export_success', $aProfile['Email'])));
    }
    else {
        echo json_encode(array('err' => 1, 'msg' => $mixedRes ? $mixedRes : _t('_Error occured')));
    }
}
