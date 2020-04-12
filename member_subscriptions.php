<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( 'inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );

ch_import('ChWsbSubscription');

check_logged();
$oSubscription = ChWsbSubscription::getInstance();

// --------------- page components
$iIndex = 0;

$_page = array(
    'css_name' => '',
    'header' => _t('_sys_pcpt_my_subscriptions'),
    'header_text' => _t('_sys_bcpt_my_subscriptions'),
    'name_index' => $iIndex
);
$_page_cont[$iIndex]['page_main_code'] = $oSubscription->getMySubscriptions();

// --------------- [END] page components

PageCode();
// --------------- page components functions
