<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    require_once( 'inc/header.inc.php' );
    require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
    require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbPageView.php' );

    ch_import( 'ChTemplProfileView' );

    $sPageCaption = _t( '_Profile info' );

    $_page['name_index'] 	= 7;
    $_page['header'] 		= $sPageCaption;
    $_page['header_text'] 	= $sPageCaption;
    $_page['css_name']		= 'profile_view.css';

    //-- init some needed variables --//;

    $iViewedID = false != ch_get('ID') ? (int) ch_get('ID') : 0;
    if (!$iViewedID) {
        $iViewedID = getLoggedId();
    }

    // check profile membership, status, privacy and if it is exists
    ch_check_profile_visibility($iViewedID, getLoggedId());

    $GLOBALS['oTopMenu'] -> setCurrentProfileID($iViewedID);

    // fill array with all profile informaion
    $aMemberInfo  = getProfileInfo($iViewedID);

    // build page;
    $_ni = $_page['name_index'];

    // prepare all needed keys ;
    $aMemberInfo['anonym_mode'] 	= $oTemplConfig -> bAnonymousMode;
    $aMemberInfo['member_pass'] 	= $aMemberInfo['Password'];
    $aMemberInfo['member_id'] 		= $aMemberInfo['ID'];

    $aMemberInfo['url'] = CH_WSB_URL_ROOT;

    ch_import('ChWsbProfileInfoPageView');
    $oProfileInfo = new ChWsbProfileInfoPageView('profile_info', $aMemberInfo);
    $sOutputHtml  = $oProfileInfo->getCode();

    $_page_cont[$_ni]['page_main_code'] = $sOutputHtml;

    PageCode();
