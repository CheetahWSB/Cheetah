<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('../../../inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'utils.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'admin_design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'admin.inc.php');

//require_once( CH_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $aModule['class_prefix'] . 'Module.php');
ch_import('ChWsbModuleDb');
require_once( CH_DIRECTORY_PATH_MODULES . 'cheetah/ads/classes/ChAdsModule.php');

$logged['admin'] = member_auth( 1, true, true );

$iNameIndex = 9;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('common.css', 'forms_adv.css'),
    'js_name' => array('jquery.simple.tree.js'),
    'header' => _t('_ch_ads_Manage_ads'),
    'header_text' => _t('_ch_ads_Manage_ads')
);
$_page_cont[$iNameIndex]['page_main_code'] = PageCompAds();
PageCodeAdmin();

function PageCompAds()
{
    $oModuleDb = new ChWsbModuleDb();
    $aModule = $oModuleDb->getModuleByUri('ads');

    $oAds = new ChAdsModule($aModule);
    $oAds->sCurrBrowsedFile = 'post_mod_ads.php';
    $oAds->bAdminMode = true;

    $sCss = $oAds->_oTemplate->addCss(array('ads.css'), true);
    $sResult = $sCss . $oAds->PrintCommandForms();

    if ($_REQUEST) {
        if (false !== ch_get('action')) {
            if ((int)ch_get('action')==3) {
                $sResult .= $oAds->PrintFilterForm();
                $sResult .= $oAds->actionSearch();
                return $sResult;
            } elseif ((int)ch_get('action')==2) {
                $iClassifiedSubID = (int)ch_get('FilterSubCat');
                $sResult .= $oAds->PrintSubRecords($iClassifiedSubID);
                return $sResult;
            } elseif ((int)ch_get('action')==1) {
                $iClassifiedID = (int)ch_get('FilterCat');
                $sResult .= $oAds->PrintAllSubRecords($iClassifiedID);
                return $sResult;
            } elseif (ch_get('action')=='add_sub_category') {
                $sCatID = (int)ch_get('id');
                $iCatID = ($sCatID) ? $sCatID : 0;
                header('Content-Type: text/html; charset=utf-8');
                echo $oAds->getAddSubcatForm($iCatID);
                exit;
            } elseif (ch_get('action')=='category_manager') {
            	header('Content-Type: text/html; charset=utf-8');
                echo $oAds->getCategoryManager();
                exit;
            }
        } elseif (false !== ch_get('bClassifiedID')) {
            $iClassifiedID = (int)ch_get('bClassifiedID');
            if ($iClassifiedID > 0) {
                $sResult .= $oAds->PrintAllSubRecords($iClassifiedID);
                $sResult .= $oAds->PrintBackLink();
                return $sResult;
            }
        } elseif (false !== ch_get('bSubClassifiedID')) {
            $iSubClassifiedID = (int)ch_get('bSubClassifiedID');
            if ($iSubClassifiedID > 0) {
                $sResult .= $oAds->PrintSubRecords($iSubClassifiedID);
                $sResult .= $oAds->PrintBackLink();
                return $sResult;
            }
        } elseif (false !== ch_get('DeleteAdvertisementID')) {
            $id = (int)ch_get('DeleteAdvertisementID');
            if ($id > 0) {
                $sResult .= $oAds->ActionDeleteAdvertisement($id);
            }
        } elseif (false !== ch_get('ActivateAdvertisementID')) {
            $iAdID = (int)ch_get('ActivateAdvertisementID');
            if ($iAdID > 0) {
                $oAds->_oDb->setPostStatus($iAdID, 'active');
            }
        }
        if (false !== ch_get('UpdatedAdvertisementID')) {
            $id = (int)ch_get('UpdatedAdvertisementID');
            if ($id > 0) {
                if (false !== ch_get('DeletedPictureID') && (int)ch_get('DeletedPictureID')>0) {
                    //delete a pic
                    $sResult .= $oAds->ActionDeletePicture();
                    $sResult .= $oAds->PrintEditForm($id);
                } else {
                    $sResult .= $oAds->ActionUpdateAdvertisementID($id);
                }
            }
            return;
        } elseif (false !== ch_get('EditAdvertisementID')) {
            if (((int)ch_get('EditAdvertisementID')) > 0) {
                $sResult .= $oAds->PrintEditForm((int)ch_get('EditAdvertisementID'));
                $sResult .= $oAds->PrintBackLink();
                return $sResult;
            }
        } elseif (false !== ch_get('ShowAdvertisementID')) {
            if (ch_get('ShowAdvertisementID') > 0) {
                $sResult .= $oAds->ActionPrintAdvertisement((int)ch_get('ShowAdvertisementID'));
                $sResult .= $oAds->PrintBackLink();
                return $sResult;
            }
        } elseif (false !== ch_get('BuyNow')) {
            $iAdID = (int)ch_get('IDAdv');
            if ($iAdID > 0) {
                $sResult .= $oAds->ActionBuyAdvertisement($iAdID);
                return $sResult;
            }
        } elseif (false !== ch_get('BuySendNow')) {
            $iAdID = (int)ch_get('IDAdv');
            if ($iAdID > 0) {
                $sResult .= $oAds->ActionBuySendMailAdvertisement($iAdID);
                return $sResult;
            }
        } elseif (false !== ch_get('UsersOtherListing')) {
            $iProfileID = (int)ch_get('IDProfile');
            if ($iProfileID > -1) {
                $sResult .= $oAds->PrintMyAds($iProfileID);
                return $sResult;
            }
        }
    }

    $sResult .= $oAds->GenAdminTabbedPage();
    return $sResult;
}
