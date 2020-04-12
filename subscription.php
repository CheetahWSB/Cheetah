<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'utils.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'languages.inc.php');

ch_import('ChWsbSubscription');

$oSubscription = ChWsbSubscription::getInstance();

$aResult = array();
if(isset($_POST['direction'])) {
    $sUnit = process_db_input($_POST['unit']);
    $sAction = process_db_input($_POST['action']);
    $iObjectId = (int)$_POST['object_id'];

    switch($_POST['direction']) {
        case 'subscribe':
            if(isset($_POST['user_id']) && (int)$_POST['user_id'] != 0)
                $aResult = $oSubscription->subscribeMember((int)$_POST['user_id'], $sUnit, $sAction, $iObjectId);
            else if(isset($_POST['user_name']) && isset($_POST['user_email']))
                $aResult = $oSubscription->subscribeVisitor($_POST['user_name'], $_POST['user_email'], $sUnit, $sAction, $iObjectId);
            break;

        case 'unsubscribe':
            if(isset($_POST['user_id']) && (int)$_POST['user_id'] != 0)
                $aResult = $oSubscription->unsubscribeMember((int)$_POST['user_id'], $sUnit, $sAction, $iObjectId);
            else if(isset($_POST['user_name']) && isset($_POST['user_email']))
                $aResult = $oSubscription->unsubscribeVisitor($_POST['user_name'], $_POST['user_email'], $sUnit, $sAction, $iObjectId);
            break;
    }

    header('Content-Type:text/javascript; charset=utf-8');
    echo json_encode($aResult);
}
else if(isset($_GET['sid'])) {
    $aResult = $oSubscription->unsubscribe(array('type' => 'sid', 'sid' => $_GET['sid']));
    if(isset($_GET['js']) && (int)$_GET['js'] == 1) {
    	header('Content-Type:text/javascript; charset=utf-8');
        echo json_encode($aResult);
        exit;
    }

    $_page['name_index'] = 0;
    $_page['header'] = $GLOBALS['site']['title'];
    $_page['header_text'] = $GLOBALS['site']['title'];
    $_page_cont[0]['page_main_code'] = MsgBox($aResult['message']);

    PageCode();
}
