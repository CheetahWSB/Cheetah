<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    require_once( CH_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $aModule['class_prefix'] . 'Module.php');
    ch_import('ChWsbPageView');

    $oSpy = new ChSpyModule($aModule);

    // ** init some needed variables ;

    global $_page;
    global $_page_cont;

    //-- Define activity type --//;
    $sActivityType = '';
    if(isset($_GET['spy_type']) ) {
        switch($_GET['spy_type']) {
            case 'profiles_activity' :
                $sActivityType = 'profiles_activity';
                break;

            case 'content_activity' :
                $sActivityType = 'content_activity';
                break;
        }
    }

    $iIndex = 0;
    $sPageCaption = _t('_ch_spy_notifications');

    $GLOBALS['oTopMenu']->setCurrentProfileID($oSpy->iMemberId);

    $_page['name_index']	= $iIndex;
    $_page['header']        = $sPageCaption ;
    $_page['header_text']   = $sPageCaption ;
    $_page['css_name']   = 'spy.css';
    $_page_cont[$iIndex]['page_main_code'] = $oSpy->getActivityPage($oSpy->iMemberId, $sActivityType);

    PageCode($oSpy -> _oTemplate);
