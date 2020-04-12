<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../../../inc/header.inc.php' );
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'admin.inc.php');

//require_once( CH_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $aModule['class_prefix'] . 'Module.php');
ch_import('ChWsbModuleDb');
require_once( CH_DIRECTORY_PATH_MODULES . 'cheetah/ads/classes/ChAdsModule.php');

// --------------- page variables and login
$_page['name_index'] 	= 151;

$oModuleDb = new ChWsbModuleDb();
$aModule = $oModuleDb->getModuleByUri('ads');

$oAds = new ChAdsModule($aModule);
$oAds->sCurrBrowsedFile = $oAds->sHomeUrl . 'classifieds.php';
$_page['header'] = $oAds->GetHeaderString();
$_page['header_text'] = $oAds->GetHeaderString();

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = PageCompAds($oAds);

$oAds->_oTemplate->addCss(array('ads.css', 'categories.css'));

function PageCompAds($oAds)
{
    $sRetHtml = '';

    $sRetHtml .= $oAds->PrintCommandForms();

    if ($_REQUEST) {
        if (false !== ch_get('tag')) {
            $sTag = uri2title(process_db_input(ch_get('tag'), CH_TAGS_STRIP));
            $sRetHtml .= $oAds->PrintAdvertisementsByTag($sTag);
        }
    }

    return $sRetHtml;
}

PageCode($oAds->_oTemplate);
