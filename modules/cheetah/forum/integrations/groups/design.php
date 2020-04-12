<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

// select menu items and set title header

require_once(CH_DIRECTORY_PATH_INC . 'db.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'params.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbPageView.php');

$aForum = array ();
if (isset($_GET['action']) && 'goto' == $_GET['action'] && $_GET['forum_id']) {
    $aForum = $GLOBALS['f']->fdb->getForumByUri (process_db_input(rawurldecode($_GET['forum_id'])));
    $GLOBALS['oTopMenu']->setCustomVar('ch_groups_view_uri', $aForum['forum_uri']);
    $GLOBALS['oTopMenu']->setCustomSubHeader($aForum['forum_title']);
} elseif (isset($_GET['action']) && 'goto' == $_GET['action'] && $_GET['topic_id']) {
    $aTopic = $GLOBALS['f']->fdb->getTopicByUri (process_db_input(rawurldecode($_GET['topic_id'])));
    $aForum = $GLOBALS['f']->fdb->getForum ($aTopic['forum_id']);
    $GLOBALS['oTopMenu']->setCustomVar('ch_groups_view_uri', $aTopic['forum_uri']);
    $GLOBALS['oTopMenu']->setCustomSubHeader($aTopic['forum_title']);
} else {
    $GLOBALS['oTopMenu']->setCustomVar('ch_groups_view_uri', '../');
}

if ((isset($_GET['action']) && 'goto' == $_GET['action'] && $_GET['forum_id']) || (isset($_GET['action']) && 'goto' == $_GET['action'] && $_GET['topic_id'])) {
    $oModuleMain = ChWsbModule::getInstance('ChGroupsModule');
    if ($oModuleMain && $aForum) {
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(CH_WSB_URL_ROOT . $oModuleMain->_oConfig->getBaseUri() . 'view/' . $aForum['forum_uri']);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_ch_groups') => CH_WSB_URL_ROOT . $oModuleMain->_oConfig->getBaseUri() . 'home/',
            $aForum['forum_title'] => CH_WSB_URL_ROOT . $oModuleMain->_oConfig->getBaseUri() . 'view/' . $aForum['forum_uri'],
            _t('_ch_groups_menu_view_forum') => '',
        ));
    }
}

// use default cheetah design

$aPathInfo = pathinfo(__FILE__);
require_once ($aPathInfo['dirname'] . '/../base/design.php');

// do not show forum index page - always select Groups category at least

if (!isset($_GET['action']) && !isset($_POST['action'])) {
    $_GET['action'] = 'goto';
    $_GET['cat_id'] = 'Groups';
}
