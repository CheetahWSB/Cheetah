<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin_design.inc.php' );

require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbAdminTools.php' );

$logged['admin'] = member_auth( 1, true, true );

$oAdmTools = new ChWsbAdminTools();
function PageCompAdmTools($oAdmTools)
{
    $sRetHtml = $oAdmTools->GenCommonCode();

    switch (ch_get('action')) {
        case 'perm_table':
            $sRetHtml .= $oAdmTools->GenPermTable();
            break;
        case 'main_params':
            $sRetHtml .= $oAdmTools->GenMainParamsTable();
            break;
        case 'main_page':
            $sRetHtml .= $oAdmTools->GenTabbedPage();
            break;
        default:
            $sRetHtml .= $oAdmTools->GenTabbedPage();
            break;
    }

    return $sRetHtml;
}

$iNameIndex = 9;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('forms_adv.css'),
    'header' => _t('_adm_admtools_title'),
    'header_text' => _t('_adm_admtools_title')
);
$_page_cont[$iNameIndex]['page_main_code'] = PageCompAdmTools($oAdmTools);

PageCodeAdmin();
