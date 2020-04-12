<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../../../inc/header.inc.php' );
require_once(CH_DIRECTORY_PATH_INC . 'admin.inc.php');

ch_import('ChWsbModuleDb');
require_once( CH_DIRECTORY_PATH_MODULES . 'cheetah/ads/classes/ChAdsModule.php');

// --------------- page variables and login

$_page['name_index'] 	= 151;

check_logged();

$oModuleDb = new ChWsbModuleDb();
$aModule = $oModuleDb->getModuleByUri('ads');

$oAds = new ChAdsModule($aModule);
$oAds->sCurrBrowsedFile = ch_html_attribute($_SERVER['PHP_SELF']);
$_page['header'] = $oAds->GetHeaderString();
$_page['header_text'] = $oAds->GetHeaderString();

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = PageCompAds($oAds);

$oAds->_oTemplate->addCss(array('ads.css', 'categories.css', 'entry_view.css'));

function PageCompAds($oAds)
{
    $sRetHtml = '';

    $sRetHtml .= $oAds->PrintCommandForms();

    if ($_REQUEST) {

        if (false !== ch_get('action')) {

            switch(ch_get('action')) {
                case '3':
                    echo $oAds->actionSearch();exit;
                    break;
                case '2':
                    $iClassifiedSubID = (int)ch_get('FilterSubCat');
                    $sRetHtml .= $oAds->PrintSubRecords($iClassifiedSubID);
                    break;
                case '1':
                    $iClassifiedID = (int)ch_get('FilterCat');
                    $sRetHtml .= $oAds->PrintAllSubRecords($iClassifiedID);
                    break;
                case 'report':
                    $iCommentID = (int)ch_get('commentID');
                    print $oAds->GenReportSubmitForm($iCommentID);
                    exit();
                case 'post_report':
                    print $oAds->ActionReportSubmit();
                    exit();

                case 'show_calendar':
                    $sRetHtml .= $oAds->GenAdsCalendar();
                    break;
                case 'show_calendar_ads':
                    $sRetHtml .= $oAds->GenAdsByDate();
                    break;

                case 'show_featured':
                    $sRetHtml .= $oAds->GenAllAds('featured');
                    break;

                case 'show_categories':
                    $sRetHtml .= $oAds->genCategoriesBlock();
                    break;

                case 'show_all_ads':
                    $sRetHtml .= $oAds->GenAllAds();
                    break;
                case 'show_popular':
                    $sRetHtml .= $oAds->GenAllAds('popular');
                    break;
                case 'show_top_rated':
                    $sRetHtml .= $oAds->GenAllAds('top');
                    break;

                case 'my_page':
                    $sRetHtml .= $oAds->GenMyPageAdmin();
                    break;

                case 'tags':
                    $sRetHtml .= $oAds->GenTagsPage();
                    break;

            }

        } elseif ((false !== ch_get('bClassifiedID') && (int)ch_get('bClassifiedID') > 0) || (false !== ch_get('catUri') && ch_get('catUri')!='')) {
            $iClassifiedID = ((int)ch_get('bClassifiedID') > 0) ? (int)ch_get('bClassifiedID') : (int)db_value("SELECT `ID` FROM `{$oAds->_oConfig->sSQLCatTable}` WHERE `CEntryUri`='" . process_db_input(ch_get('catUri'), CH_TAGS_STRIP) . "' LIMIT 1");
            if ($iClassifiedID > 0) {
                $sRetHtml .= $oAds->PrintAllSubRecords($iClassifiedID);
            }
        } elseif ((false !== ch_get('bSubClassifiedID') && (int)ch_get('bSubClassifiedID') > 0) || (false !== ch_get('scatUri') && ch_get('scatUri')!='')) {
            $iSubClassifiedID = ((int)ch_get('bSubClassifiedID') > 0) ? (int)ch_get('bSubClassifiedID') : (int)db_value("SELECT `ID` FROM `{$oAds->_oConfig->sSQLSubcatTable}` WHERE `SEntryUri`='" . process_db_input(ch_get('scatUri'), CH_TAGS_STRIP) . "' LIMIT 1");
            if ($iSubClassifiedID > 0) {
                $sRetHtml .= $oAds->PrintSubRecords($iSubClassifiedID);
            }
        } elseif ((false !== ch_get('ShowAdvertisementID') && (int)ch_get('ShowAdvertisementID')>0) || (false !== ch_get('entryUri') && ch_get('entryUri')!='')) {
            $iID = ((int)ch_get('ShowAdvertisementID') > 0) ? (int)ch_get('ShowAdvertisementID') : (int)db_value("SELECT `ID` FROM `{$oAds->_oConfig->sSQLPostsTable}` WHERE `EntryUri`='" . process_db_input(ch_get('entryUri'), CH_TAGS_STRIP) . "' LIMIT 1");
            $oAds->ActionPrintAdvertisement($iID);

            ch_import('PageView', $oAds->_aModule);
            $oAPV = new ChAdsPageView($oAds, $iID);
            $sRetHtml .= $oAPV->getCode();

        } elseif (false !== ch_get('UsersOtherListing')) {
            $iProfileID = (int)ch_get('IDProfile');
            if ($iProfileID > -1) {
                $sRetHtml .= $oAds->getMemberAds($iProfileID);
            }
        }
        //non safe functions
        elseif (false !== ch_get('DeleteAdvertisementID')) {
            $id = (int)ch_get('DeleteAdvertisementID');
            if ($id > 0) {
                $sRetHtml .= $oAds->ActionDeleteAdvertisement($id);
                $sRetHtml .= $oAds->GenMyPageAdmin('manage');
            }
        } elseif (false !== ch_get('ActivateAdvertisementID')) {
            $iAdID = (int)ch_get('ActivateAdvertisementID');
            if ($iAdID > 0 && ($oAds->bAdminMode || isModerator($oAds->_iVisitorID))) {
                $sStatus = ch_get('ActType') == 'active' ? 'active' : 'inactive';
                $oAds->_oDb->setPostStatus($iAdID, $sStatus);
                $oAds->ActionPrintAdvertisement($iAdID);

                ch_import('PageView', $oAds->_aModule);
                $oAPV = new ChAdsPageView($oAds, $iAdID);
                $sRetHtml .= $oAPV->getCode();
            }
        } elseif (false !== ch_get('BuyNow')) {
            $advId = (int)ch_get('IDAdv');
            if ($advId > 0) {
                $sRetHtml .= $oAds->ActionBuyAdvertisement($advId);
            }
        } elseif (false !== ch_get('BuySendNow')) {
            $advId = (int)ch_get('IDAdv');
            if ($advId > 0) {
                $sRetHtml .= $oAds->ActionBuySendMailAdvertisement($advId);
            }
        } else {
            $sRetHtml .= $oAds->getAdsMainPage();
        }
    } else {
        $sRetHtml .= $oAds->getAdsMainPage();
    }

    return $sRetHtml;
}

if ($oAds->_iVisitorID) {
    $aOpt = array('only_menu' => 1);
    $GLOBALS['oTopMenu']->setCustomSubActions($aOpt, 'ch_ads', true);
}

PageCode($oAds->_oTemplate);
