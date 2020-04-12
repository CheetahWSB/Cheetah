<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( 'inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'profiles.inc.php' );

// --------------- page variables and login

$_page['name_index'] 	= 34;
$_page['css_name']		= 'unregister.css';

$logged['member'] = member_auth(0);

$_page['header'] = _t("_Delete account");
$_page['header_text'] = _t("_Delete account");

// --------------- page components

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = PageCompPageMainCode();

// --------------- [END] page components

PageCode();

// --------------- page components functions

/**
 * page code function
 */
function PageCompPageMainCode()
{
    $iUserId = getLoggedId();

    if($_POST['DELETE']) {
        profile_delete($iUserId);
        ch_logout();
        return MsgBox(_t("_DELETE_SUCCESS"));
    }

    $aForm = array(
        'form_attrs' => array (
            'action' =>  CH_WSB_URL_ROOT . 'unregister.php',
            'method' => 'post',
            'name' => 'form_unregister'
        ),

        'inputs' => array(
            'delete' => array (
                'type'     => 'hidden',
                'name'     => 'DELETE',
                'value'    => '1',
            ),
            'info' => array(
                'type' => 'custom',
                'content' => _t("_DELETE_TEXT"),
                'colspan' => true
            ),
            'submit' => array (
                'type'     => 'submit',
                'name'     => 'submit',
                'value'    => _t("_Delete account"),
            ),
        ),
    );
    $oForm = new ChTemplFormView($aForm);

    $GLOBALS['oTopMenu']->setCurrentProfileID($iUserId);
    return $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array('content' => $oForm->getCode()));
}
