<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( 'inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'utils.inc.php' );

ch_import ('ChWsbPageView');

class ChWsbPageSearchMain extends ChWsbPageView
{
    function __construct()
    {
        parent::__construct('search_home');
    }

    function getBlockCode_Keyword()
    {
        $a = array(
            'form_attrs' => array(
               'id' => 'searchForm',
               'action' => CH_WSB_URL_ROOT . 'searchKeyword.php',
               'method' => 'get',
            ),
            'inputs' => array(
                'keyword' => array(
                    'type' => 'text',
                    'name' => 'keyword',
                    'caption' => _t('_Keyword'),
                ),
                'search' => array(
                    'type' => 'submit',
                    'name' => 'search',
                    'value' => _t('_Search'),
                ),
            ),
        );

        $oForm = new ChTemplFormView($a);
        return array($oForm->getCode(), array(), array(), false);
    }

    function getBlockCode_People()
    {
        $iMemberId = getLoggedId();

        $aProfile = $iMemberId ? getProfileInfo($iMemberId) : array();

        // default params for search form
        $aDefaultParams = array(
            'LookingFor'  => $aProfile['Sex']        ? $aProfile['Sex']           : 'male',
            'Sex'         => $aProfile['LookingFor'] ? $aProfile['LookingFor']    : 'female',
            'Country'     => $aProfile['Country']    ? $aProfile['Country']       : getParam('default_country'),
            'DateOfBirth' => getParam('search_start_age') . '-' . getParam('search_end_age'),
        );

        ch_import('ChWsbProfileFields');
        $oPF = new ChWsbProfileFields(9);
        $a = array('default_params' => $aDefaultParams);
        return array($oPF->getFormCode($a), array(), array(), false);
    }

    function getBlockCode_History()
    {
        return MsgBox('Under Development');
    }
}

$_page['name_index'] = 81;

check_logged();

$_page['header'] = _t('_sys_search_main_title');
$_page['header_text'] = _t('_sys_search_main_title');
$_page['css_name'] = 'search.css';

$oPage = new ChWsbPageSearchMain();

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = $oPage -> getCode();

PageCode();
