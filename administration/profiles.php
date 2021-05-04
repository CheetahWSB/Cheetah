<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../inc/header.inc.php' );

$GLOBALS['iAdminPage'] = 1;

require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'utils.inc.php' );

ch_import('ChTemplSearchResult');
ch_import('ChTemplBrowse');
ch_import('ChTemplTags');
ch_import('ChTemplFunctions');
ch_import('ChWsbAlerts');
ch_import('ChWsbEmailTemplates');

$sDefaultOverViewMode = strtolower(getParam('default_overview_mode'));
$sDefaultMemberMode = strtolower(getParam('default_view_mode'));
$iDefaultPerPage = (int)getParam('default_per_page');
if($sDefaultOverViewMode == 'quick links') $sDefaultOverViewMode = 'qlinks';

define('CH_WSB_ADM_MP_CTL', $sDefaultOverViewMode);
define('CH_WSB_ADM_MP_VIEW', $sDefaultMemberMode);
define('CH_WSB_ADM_MP_JS_NAME', 'oMP');
define('CH_WSB_ADM_MP_PER_PAGE', $iDefaultPerPage);
define('CH_WSB_ADM_MP_PER_PAGE_STEP', 16);

$logged['admin'] = member_auth( 1, true, true );

$sCtlType = isset($_POST['adm-mp-members-ctl-type']) && in_array($_POST['adm-mp-members-ctl-type'], array('qlinks', 'browse', 'calendar', 'tags', 'search', 'settings')) ? $_POST['adm-mp-members-ctl-type'] : CH_WSB_ADM_MP_CTL;
$aCtlType = array();

$sViewType = isset($_POST['adm-mp-members-view-type']) && in_array($_POST['adm-mp-members-view-type'], array('geeky', 'simple', 'extended')) ? $_POST['adm-mp-members-view-type'] : CH_WSB_ADM_MP_VIEW;

// If settings form was submitted, then set the ctl type to settings.
if (isset($_POST['save']) && isset($_POST['cat'])) {
    $sCtlType = 'settings';
}

//--- Process Actions ---//
if(isset($_POST['adm-mp-activate']) && (bool)$_POST['members']) {
    ch_admin_profile_change_status($_POST['members'], 'Active', TRUE);
    echo "<script>window.parent." . CH_WSB_ADM_MP_JS_NAME . ".reload();</script>";
    exit;
} else if(isset($_POST['adm-mp-deactivate']) && (bool)$_POST['members']) {
    ch_admin_profile_change_status($_POST['members'], 'Approval');

    echo "<script>window.parent." . CH_WSB_ADM_MP_JS_NAME . ".reload();</script>";
    exit;
} else if(isset($_POST['adm-mp-ban']) && (bool)$_POST['members']) {
	$iBanDuration = isset($_POST['adm-mp-members-ban-duration']) ? (int)$_POST['adm-mp-members-ban-duration'] : 0;

    foreach($_POST['members'] as $iId)
        ch_admin_profile_ban_control($iId, true, $iBanDuration);

    echo "<script>window.parent." . CH_WSB_ADM_MP_JS_NAME . ".reload();</script>";
    exit;
} else if(isset($_POST['adm-mp-unban']) && (bool)$_POST['members']) {
    ch_import('ChWsbForm');
    $oChecker = new ChWsbFormCheckerHelper();
    $GLOBALS['MySQL']->query("DELETE FROM `sys_admin_ban_list` WHERE `ProfID` IN ('" . implode("','", $oChecker->passInt($_POST['members'])) . "')");

    echo "<script>window.parent." . CH_WSB_ADM_MP_JS_NAME . ".reload();</script>";
    exit;

} else if(isset($_POST['adm-mp-set-admin']) && (bool)$_POST['members']) {
    $iIdCurr = getLoggedId();
    foreach($_POST['members'] as $iId) {
        $iId = (int)$iId;
        if ($iIdCurr != $iId)
            $GLOBALS['MySQL']->query("UPDATE `Profiles` SET `Role` = 3 WHERE `ID` = '$iId'");
    }

    echo "<script>window.parent." . CH_WSB_ADM_MP_JS_NAME . ".reload();</script>";
    exit;

  } else if(isset($_POST['adm-mp-unset-admin']) && (bool)$_POST['members']) {

      $iIdCurr = getLoggedId();
      foreach($_POST['members'] as $iId) {
          $iId = (int)$iId;
          if ($iIdCurr != $iId)
              $GLOBALS['MySQL']->query("UPDATE `Profiles` SET `Role` = 1 WHERE `ID` = '$iId'");
      }

      echo "<script>window.parent." . CH_WSB_ADM_MP_JS_NAME . ".reload();</script>";
      exit;

} else if(isset($_POST['adm-mp-members-list']) && $_POST['doSetMembership'] == 'yes') {

    $iIdCurr = getLoggedId();
    $aMembers = explode(',', $_POST['adm-mp-members-list']);
    $iMembershipDays = (int)$_POST['MembershipDays'];
    $bMembershipImmediately = $_POST['MembershipImmediately'] == 'on' ? true : false;
    foreach($aMembers as $value) {
        if((int)$value != $iIdCurr) {
            $bSave = setMembership((int)$value, (int)$_POST['MembershipID'], $iMembershipDays, $bMembershipImmediately);
        }
    }

} else if((isset($_POST['adm-mp-delete']) || isset($_POST['adm-mp-delete-spammer'])) && (bool)$_POST['members']) {
    $iIdCurr = getLoggedId();
    foreach($_POST['members'] as $iId) {
        $iId = (int)$iId;
        if ($iIdCurr != $iId)
            $bResult = profile_delete($iId, isset($_POST['adm-mp-delete-spammer']));
    }

    echo "<script>window.parent." . CH_WSB_ADM_MP_JS_NAME . ".reload();</script>";
    exit;
} else if(isset($_POST['adm-mp-confirm']) && (bool)$_POST['members']) {
    foreach($_POST['members'] as $iId)
        activation_mail((int)$iId, 0);

    echo "<script>alert('" . _t('_adm_txt_mp_activation_sent') . "')</script>";
    exit;
} else if(isset($_POST['action']) && $_POST['action'] == 'get_members') {
    $aParams = array();
    if(is_array($_POST['ctl_value']))
        foreach($_POST['ctl_value'] as $sValue) {
            $aValue = explode('=', $sValue);
            $aParams[$aValue[0]] = $aValue[1];
        }

    echo json_encode(array('code' => 0, 'content' => getMembers(array(
        'view_type' => $_POST['view_type'],
        'view_start' => (int)$_POST['view_start'],
        'view_per_page' => (int)$_POST['view_per_page'],
        'view_order' => $_POST['view_order'],
        'ctl_type' => $_POST['ctl_type'],
        'ctl_params' => $aParams
    ))));
    exit;
} else if(isset($_POST['action']) && $_POST['action'] == 'get_controls') {

    $sCtlType = process_db_input($_POST['ctl_type'], CH_TAGS_STRIP);
    $sMethodName = 'getBlock' . ucfirst($sCtlType);
    if(!function_exists($sMethodName)) {
        echo '{}';
        exit;
    }

    echo json_encode(array(
        'code' => 0,
        'content' => $oAdmTemplate->parseHtmlByName('mp_ctl_type_' . $sCtlType . '.html', $sMethodName($sCtlType))
    ));
    exit;
}

//--- Process Query String ---//
if(isset($_GET['action']) && $_GET['action'] == 'browse') {
    $aCtlType = array(
        'ctl_type' => 'qlinks',
        'ctl_params' => array(
            'by' => process_db_input(ch_get('by'), CH_TAGS_STRIP),
            'value' => process_db_input(ch_get('value'), CH_TAGS_STRIP)
        )
    );
}

$iNameIndex = 10;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('forms_adv.css', 'profiles.css'),
    'js_name' => array('profiles.js'),
    'header' => _t('_adm_page_cpt_manage_members')
);
$_page_cont[$iNameIndex] = array(
    'page_code_controls' => PageCodeControls($sCtlType),
    'page_code_members' => PageCodeMembers(!empty($aCtlType) ? $aCtlType : $sCtlType, $sViewType),
    'obj_name' => CH_WSB_ADM_MP_JS_NAME,
    'actions_url' => $GLOBALS['site']['url_admin'] . 'profiles.php',
    'sel_control' => $sCtlType,
    'sel_view' => $sViewType,
    'per_page' => CH_WSB_ADM_MP_PER_PAGE,
    'order_by' => ''
);

PageCodeAdmin();

function PageCodeControls($sDefault = CH_WSB_ADM_MP_CTL)
{
    global $oAdmTemplate;

    $aTopMenu = array(
        'ctl-type-qlinks' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . CH_WSB_ADM_MP_JS_NAME . '.changeTypeControl(this);', 'title' => _t('_adm_btn_mp_qlinks'), 'active' => $sDefault == 'qlinks' ? 1 : 0),
        //'ctl-type-browse' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . CH_WSB_ADM_MP_JS_NAME . '.changeTypeControl(this);', 'title' => _t('_adm_btn_mp_browse'), 'active' => $sDefault == 'browse' ? 1 : 0),
        //'ctl-type-calendar' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . CH_WSB_ADM_MP_JS_NAME . '.changeTypeControl(this);', 'title' => _t('_adm_btn_mp_calendar'), 'active' => $sDefault == 'calendar' ? 1 : 0),
        'ctl-type-tags' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . CH_WSB_ADM_MP_JS_NAME . '.changeTypeControl(this);', 'title' => _t('_adm_btn_mp_tags'), 'active' => $sDefault == 'tags' ? 1 : 0),
        'ctl-type-search' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . CH_WSB_ADM_MP_JS_NAME . '.changeTypeControl(this);', 'title' => _t('_adm_btn_mp_search'), 'active' => $sDefault == 'search' ? 1 : 0),
        'ctl-type-settings' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . CH_WSB_ADM_MP_JS_NAME . '.changeTypeControl(this);', 'title' => _t('_adm_btn_mp_settings'), 'active' => $sDefault == 'settings' ? 1 : 0)
    );

    $aParams = array_merge(
        getBlockQlinks($sDefault),
        getBlockBrowse($sDefault),
        getBlockCalendar($sDefault),
        getBlockTags($sDefault),
        getBlockSearch($sDefault),
        getBlockSettings($sDefault),
        array(
            'loading' => LoadingBox('adm-mp-controls-loading')
        )
    );
    return DesignBoxAdmin(_t('_adm_box_cpt_mp_controls'), $oAdmTemplate->parseHtmlByName('mp_controls.html', $aParams), $aTopMenu, '', 11);
}
function getBlockQlinks($sDefault)
{
    global $MySQL;

    $aResult = array();
    $sBaseUrl = $GLOBALS['site']['url_admin'] . 'profiles.php?type=qlinks&value=';

    $aItems = array();
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'all' AS `by`, 'all' AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE 1 AND (`Couple`='0' OR `Couple`>`ID`)"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'status' AS `by`, `Status` AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE 1 AND (`Couple`='0' OR `Couple`>`ID`) GROUP BY `Status`"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'featured' AS `by`, 'featured' AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE `Featured`='1'"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'banned' AS `by`, 'banned' AS `value`, COUNT(`ProfID`) AS `count` FROM `sys_admin_ban_list` WHERE `Time`='0' OR (`Time`<>'0' AND DATE_ADD(`DateTime`, INTERVAL `Time` HOUR)>NOW())"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'membership' AS `by`, `tl`.`Name` AS `value`, COUNT(`tlm`.`IDMember`) AS `count` FROM `sys_acl_levels` AS `tl` LEFT JOIN `sys_acl_levels_members` AS `tlm` ON `tl`.`ID`=`tlm`.`IDLevel` WHERE `tl`.`Active`='yes' AND (`tl`.`Purchasable`='yes' OR `tl`.`Name`='Promotion') AND `tlm`.`DateStarts` < NOW() AND (`tlm`.`DateExpires`>NOW() || ISNULL(`tlm`.`DateExpires`)) GROUP BY `tl`.`ID`"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'sex' AS `by`, `Sex` AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE NOT ISNULL(`Sex`) AND `Sex` <> '' AND `Couple` = 0 GROUP BY `Sex`"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'type' AS `by`, 'single' AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE `Couple`='0'"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'type' AS `by`, 'couple' AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE `Couple`<>'0' AND `Couple`>`ID`"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'role' AS `by`, 'admins' AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE `Role` & " . CH_WSB_ROLE_ADMIN . ""));

    foreach($aItems as $aItem)
        $aResult[] = array('link' => 'javascript:void(0)', 'on_click' => 'javascript:' . CH_WSB_ADM_MP_JS_NAME . '.changeFilterQlinks(\'' . strtolower($aItem['by']) . '\', \'' . strtolower($aItem['value']) . '\')', 'title' => _t('_adm_txt_mp_' . strtolower($aItem['value'])), 'count' => $aItem['count']);

    return array(
        'styles_qlinks' => $sDefault != 'qlinks' ? "display: none;" : "",
        'ch_repeat:content_qlinks' => $aResult
    );
}
function getBlockBrowse($sDefault)
{
    return array(
        'styles_browse' => $sDefault != 'browse' ? "display: none;" : "",
        'content_browse' => ''
    );
}
function getBlockCalendar($sDefault)
{
    return array(
        'styles_calendar' => $sDefault != 'calendar' ? "display: none;" : "",
        'content_calendar' => ''
    );
}
function getBlockTags($sDefault)
{
    $oTags = new ChTemplTags();
    $oTags->setTemplateContent('<span class="one_tag" style="font-size:__tagSize__px;"><a href="javascript:void(0)" onclick="javascript:__tagHref__" title="__countCapt__: __countNum__">__tag__</a></span>');

    $aTags = $oTags->getTagList(array('type' => 'profile'));
    return array(
        'styles_tags' => $sDefault != 'tags' ? "display: none;" : "",
        'content_tags' => $oTags->getTagsView($aTags, CH_WSB_ADM_MP_JS_NAME . '.changeFilterTags(\'{tag}\')')
    );
}
function getBlockSearch($sDefault)
{
    $aForm = array(
        'form_attrs' => array(
            'id' => 'adm-mp-search',
            'action' => $GLOBALS['site']['url_admin'] . 'profiles.php',
            'method' => 'post',
            'enctype' => 'multipart/form-data',
    		'onsubmit' => 'javascript:' . CH_WSB_ADM_MP_JS_NAME . '.changeFilterSearch(); return false;'
        ),
        'inputs' => array (
            'adm-mp-filter' => array(
                'type' => 'text',
                'name' => 'adm-mp-filter',
                'caption' => _t('_adm_txt_mp_filter'),
                'value' => '',
            ),
            'search' => array(
                'type' => 'button',
                'name' => 'search',
                'value' => _t('_adm_btn_mp_search'),
                'attrs' => array(
                    'onclick' => 'javascript:' . CH_WSB_ADM_MP_JS_NAME . '.changeFilterSearch()'
                )
            ),
        )
    );

    $oForm = new ChTemplFormView($aForm);
    return array(
        'styles_search' => $sDefault != 'search' ? "display: none;" : "",
        'content_search' => $oForm->getCode()
    );
}

function getBlockSettings($sDefault)
{
  ch_import('ChWsbAdminSettings');
  $oSettings = new ChWsbAdminSettings(18);

  if (isset($_POST['save']) && isset($_POST['cat'])) {
      $sResult = $oSettings->saveChanges($_POST);
  }

  $s = $oSettings->getForm();
  if ($sResult)
      $s = $sResult . $s;

  return array(
      'styles_settings' => $sDefault != 'settings' ? "display: none;" : "",
      'content_settings' => $s
  );
}


function PageCodeMembers($sDefaultCtl = CH_WSB_ADM_MP_CTL, $sDefaultView = CH_WSB_ADM_MP_VIEW)
{
    $aTopMenu = array(
        'view-type-simple' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . CH_WSB_ADM_MP_JS_NAME . '.changeTypeView(this);', 'title' => _t('_adm_btn_mp_simple'), 'active' => $sDefaultView == 'simple' ? 1 : 0),
        'view-type-extended' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . CH_WSB_ADM_MP_JS_NAME . '.changeTypeView(this);', 'title' => _t('_adm_btn_mp_extended'), 'active' => $sDefaultView == 'extended' ? 1 : 0),
        'view-type-geeky' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . CH_WSB_ADM_MP_JS_NAME . '.changeTypeView(this);', 'title' => _t('_adm_btn_mp_geeky'), 'active' => $sDefaultView == 'geeky' ? 1 : 0)
    );

    $oPaginate = new ChWsbPaginate(array(
        'per_page' => CH_WSB_ADM_MP_PER_PAGE,
        'per_page_step' => CH_WSB_ADM_MP_PER_PAGE_STEP,
        'on_change_per_page' => CH_WSB_ADM_MP_JS_NAME . '.changePerPage(this);'
    ));

    $sSelected1 = '';
    $sSelected2 = '';
    $sSelected3 = '';
    $sSelected4 = '';

    $sSelected = getParam('default_order_by');
    if($sSelected == 'None') $sSelected1 = 'selected="selected"';
    if($sSelected == 'User Name') $sSelected2 = 'selected="selected"';
    if($sSelected == 'Last Join') $sSelected3 = 'selected="selected"';
    if($sSelected == 'Last Activity') $sSelected4 = 'selected="selected"';

    $sTopControls = $GLOBALS['oAdmTemplate']->parseHtmlByName('mp_members_top_controls.html', array(
        'change_order' => CH_WSB_ADM_MP_JS_NAME . '.changeOrder(this);',
        'reload' => CH_WSB_ADM_MP_JS_NAME . '.reloadSelected();',
        'selected1' => $sSelected1,
        'selected2' => $sSelected2,
        'selected3' => $sSelected3,
        'selected4' => $sSelected4,
        'per_page' => $oPaginate->getPages(),
    ));

    $aMemberships = getMemberships();
    $sMbrOptions = '';
    foreach($aMemberships as $iId => $sName) {
        if($iId != 1) {
            $sMbrOptions .= '<option value="' . $iId . '">' . $sName . '</option>';
        }
    }

    $aResult = array(
        'action_url' => $GLOBALS['site']['url_admin'] . 'profiles.php',
        'ctl_type' => is_array($sDefaultCtl) && !empty($sDefaultCtl) ? $sDefaultCtl['ctl_type'] : $sDefaultCtl,
        'view_type' => $sDefaultView,
        'top_controls' => $sTopControls,
        'mbr_options' => $sMbrOptions,
        'loading' => LoadingBox('adm-mp-members-loading')
    );

    foreach(array('simple', 'extended', 'geeky') as $sType)
        if($sType == $sDefaultView) {
            $aParams = array('view_type' => $sType);
            if(is_array($sDefaultCtl) && !empty($sDefaultCtl))
                $aParams = array_merge($aParams, $sDefaultCtl);

            $aResult = array_merge($aResult, array('style_' . $sType => '', 'content_' . $sType => getMembers($aParams)));
        } else
            $aResult = array_merge($aResult, array('style_' . $sType => 'display: none;', 'content_' . $sType => ''));

    return DesignBoxAdmin(_t('_adm_box_cpt_mp_members'), $GLOBALS['oAdmTemplate']->parseHtmlByName('mp_members.html', $aResult), $aTopMenu);
}

function getMembers($aParams)
{
    if(!isset($aParams['view_order']) || empty($aParams['view_order'])) {
        $sSelected = getParam('default_order_by');
        $aParams['view_order'] = 'ID';
        if($sSelected == 'None') $aParams['view_order'] = 'ID';
        if($sSelected == 'User Name') $aParams['view_order'] = 'NickName';
        if($sSelected == 'Last Join') $aParams['view_order'] = 'DateReg DESC';
        if($sSelected == 'Last Activity') $aParams['view_order'] = 'DateLastNav DESC';
    }

    if(!isset($aParams['view_start']) || empty($aParams['view_start']))
        $aParams['view_start'] = 0;

    if(!isset($aParams['view_per_page']) || empty($aParams['view_per_page']))
        $aParams['view_per_page'] = CH_WSB_ADM_MP_PER_PAGE;

    $aParams['view_order_way'] = 'ASC';
    if(!isset($aParams['view_order']) || empty($aParams['view_order']))
        $aParams['view_order'] = 'ID';
    else {
        $aOrder = explode(' ', $aParams['view_order']);
        if(count($aOrder) > 1) {
            $aParams['view_order'] = $aOrder[0];
            $aParams['view_order_way'] = $aOrder[1];
        }
    }

    $sDateFormat = getLocaleFormat(CH_WSB_LOCALE_DATE, CH_WSB_LOCALE_DB);

    $sSelectClause = $sJoinClause = $sWhereClause = $sGroupClause = '';
    if(isset($aParams['ctl_type'])) {
        switch ($aParams['ctl_type']) {
            case 'qlinks':
                switch ($aParams['ctl_params']['by']) {
                    case 'status':
                        $sWhereClause .= " AND `tp`.`Status`='" . ucfirst($aParams['ctl_params']['value']) . "'";
                        break;
                    case 'featured':
                        $sWhereClause .= " AND `tp`.`Featured`='1'";
                        break;
                    case 'banned':
                        $sWhereClause .= " AND (`tbl`.`Time`='0' OR (`tbl`.`Time`<>'0' AND DATE_ADD(`tbl`.`DateTime`, INTERVAL `tbl`.`Time` HOUR)>NOW()))";
                        break;
                    case 'type':
                        $sWhereClause .= $aParams['ctl_params']['value'] == 'single' ? " AND `tp`.`Couple`='0'" : " AND `tp`.`Couple`<>'0' AND `tp`.`Couple`>`tp`.`ID`";
                        break;
                    case 'role':
                        $iRole = CH_WSB_ROLE_MEMBER;
                        if ($aParams['ctl_params']['value'] == 'admins') {
                            $iRole = CH_WSB_ROLE_ADMIN;
                        }

                        $sWhereClause .= " AND `tp`.`Role` & " . $iRole . "";
                        break;
                    case 'sex':
                        $sWhereClause .= " AND LOWER(`tp`.`Sex`)='" . strtolower($aParams['ctl_params']['value']) . "' AND `tp`.`Couple` = 0 ";
                        break;
                    case 'membership':
                        $sWhereClause .= " AND LOWER(`tl`.`Name`)='" . strtolower($aParams['ctl_params']['value']) . "'";
                        break;
                }
                break;

            case 'tags':
                $sWhereClause .= " AND `tp`.`Tags` LIKE '%" . $aParams['ctl_params']['value'] . "%'";
                break;

            case 'search':
                $sWhereClause .= " AND (
                `tp`.`ID` LIKE '%" . $aParams['ctl_params']['value'] . "%' OR
                `tp`.`NickName` LIKE '%" . $aParams['ctl_params']['value'] . "%' OR
                `tp`.`Email` LIKE '%" . $aParams['ctl_params']['value'] . "%' OR
                `tp`.`DescriptionMe` LIKE '%" . $aParams['ctl_params']['value'] . "%' OR
                `tp`.`Tags` LIKE '%" . $aParams['ctl_params']['value'] . "%'
            )";
                break;

            case 'settings':
                //$sWhereClause .= " AND `tp`.`Tags` LIKE '%" . $aParams['ctl_params']['value'] . "%'";
                break;
        }
    }

    //--- Get Paginate ---//
    $oPaginate = new ChWsbPaginate(array(
        'start' => $aParams['view_start'],
        'count' => (int)db_value("SELECT COUNT(`tp`.`ID`) FROM `Profiles` AS `tp` LEFT JOIN `sys_admin_ban_list` AS `tbl` ON `tp`.`ID`=`tbl`.`ProfID` LEFT JOIN `sys_acl_levels_members` AS `tlm` ON `tp`.`ID`=`tlm`.`IDMember` AND `tlm`.`DateStarts` < NOW() AND (`tlm`.`DateExpires`>NOW() || ISNULL(`tlm`.`DateExpires`)) LEFT JOIN `sys_acl_levels` AS `tl` ON `tlm`.`IDLevel`=`tl`.`ID` " . $sJoinClause . " WHERE 1 AND (`tp`.`Couple`=0 OR `tp`.`Couple`>`tp`.`ID`)" . $sWhereClause),
        'per_page' => $aParams['view_per_page'],
        'page_url' => $GLOBALS['site']['url_admin'] . 'profiles.php?start={start}',
        'on_change_page' => CH_WSB_ADM_MP_JS_NAME . '.changePage({start})'
    ));
    $sPaginate = $oPaginate->getPaginate();

    //--- Get Controls ---//
    $GLOBALS['oAdmTemplate']->addJsTranslation(array('_adm_btn_mp_ban_duration'));

    $aButtons = array(
        'adm-mp-activate' => _t('_adm_btn_mp_activate'),
        'adm-mp-deactivate' => _t('_adm_btn_mp_deactivate'),
        'adm-mp-ban' => array(
            'type' => 'submit',
            'name' => 'adm-mp-ban',
            'value' => _t('_adm_btn_mp_ban'),
            'onclick' => 'onclick="javascript: return ' . CH_WSB_ADM_MP_JS_NAME . '.actionBan(this);"'
        ),
        'adm-mp-unban' => _t('_adm_btn_mp_unban'),
        'adm-mp-confirm' => _t('_adm_btn_mp_confirm'),
        'adm-mp-delete' => _t('_adm_btn_mp_delete'),
        'adm-mp-delete-spammer' => _t('_adm_btn_mp_delete_spammer'),
        'adm-mp-set-admin' => _t('_adm_btn_mp_set_admin'),
        'adm-mp-unset-admin' => _t('_adm_btn_mp_unset_admin'),
        'adm-mp-set-mbrship' => array(
            'type' => 'submit',
            'name' => 'adm-mp-set-mbrship',
            'value' => _t('_adm_btn_mp_set_mbrship'),
            'onclick' => 'onclick="javascript: return checkChecked(\'adm-mp-members-form\');"'
        ),
    );
    $sControls = ChTemplSearchResult::showAdminActionsPanel('adm-mp-members-' . $aParams['view_type'], $aButtons, 'members');

    //--- Get Items ---//
    $sQuery = "
        SELECT
            `tp`.`ID` as `id`,
            `tp`.`NickName` AS `username`,
            `tp`.`Sex` AS `sex`,
            `tp`.`DateOfBirth` AS `date_of_birth`,
            `tp`.`Country` AS `country`,
            `tp`.`City` AS `city`,
            `tp`.`DescriptionMe` AS `description`,
            `tp`.`Email` AS `email`,
            `tp`.`CurrentPageTitle` AS `pagetitle`,
            DATE_FORMAT(`tp`.`DateReg`,  '" . $sDateFormat . "' ) AS `registration`,
            DATE_FORMAT(`tp`.`DateLastLogin`,  '" . $sDateFormat . "' ) AS `last_login`,
            DATE_FORMAT(`tp`.`DateLastNav`,  '" . $sDateFormat . "' ) AS `last_activity`,
            `tp`.`Status` AS `status`,
            IF(`tbl`.`Time`='0' OR DATE_ADD(`tbl`.`DateTime`, INTERVAL `tbl`.`Time` HOUR)>NOW(), 1, 0) AS `banned`,
            `tl`.`ID` AS `ml_id`,
            IF(ISNULL(`tl`.`Name`),'', `tl`.`Name`) AS `ml_name`
            " . $sSelectClause . "
        FROM `Profiles` AS `tp`
        LEFT JOIN `sys_admin_ban_list` AS `tbl` ON `tp`.`ID`=`tbl`.`ProfID`
        LEFT JOIN `sys_acl_levels_members` AS `tlm` ON `tp`.`ID`=`tlm`.`IDMember` AND `tlm`.`DateStarts` < NOW() AND (`tlm`.`DateExpires`>NOW() || ISNULL(`tlm`.`DateExpires`))
        LEFT JOIN `sys_acl_levels` AS `tl` ON `tlm`.`IDLevel`=`tl`.`ID`
        " . $sJoinClause . "
        WHERE
            1 AND (`tp`.`Couple`=0 OR `tp`.`Couple`>`tp`.`ID`)" . $sWhereClause . "
        " . $sGroupClause . "
        ORDER BY `tp`.`" . $aParams['view_order'] . "` " . $aParams['view_order_way'] . "
        LIMIT " . $aParams['view_start'] . ", " . $aParams['view_per_page'];
    $aProfiles = $GLOBALS['MySQL']->getAll($sQuery);

    //--- Display ---//
    $sFunction = 'getMembers' . ucfirst($aParams['view_type']);
    return $sFunction($aProfiles, $sPaginate, $sControls);
}

function getMembersGeeky($aProfiles, $sPaginate, $sControls)
{
    $iEmailLength = 20;
    $aItems = array();
    foreach($aProfiles as $aProfile){
        $sEmail = ( mb_strlen($aProfile['email']) > $iEmailLength ) ? mb_substr($aProfile['email'], 0, $iEmailLength) . '...' : $aProfile['email'];

        $aItems[$aProfile['id']] = array(
            'id' => $aProfile['id'],
            'username' => getNickName($aProfile['id']),
            'email' => $sEmail,
            'full_email' => $aProfile['email'],
            'page' => $aProfile['pagetitle'],
            'edit_link' => $GLOBALS['site']['url'] . 'pedit.php?ID=' . $aProfile['id'],
            'edit_class' => (int)$aProfile['banned'] == 1 ? 'adm-mp-banned' : ($aProfile['status'] != 'Active' ? 'adm-mp-inactive' : 'adm-mp-active'),
            'registration' => $aProfile['registration'],
            'last_activity' => $aProfile['last_activity'],
            'status' => $aProfile['status'],
            'ml_id' => !empty($aProfile['ml_id']) ? (int)$aProfile['ml_id'] : 2,
            'ml_name' => !empty($aProfile['ml_name']) ? $aProfile['ml_name'] : 'Standard'
        );
    }

    return $GLOBALS['oAdmTemplate']->parseHtmlByName('mp_members_geeky.html', array(
        'ch_repeat:items' => array_values($aItems),
        'paginate' => $sPaginate,
        'control' => $sControls
    ));
}
function getMembersSimple($aProfiles, $sPaginate, $sControls)
{
    $aItems = array();
    foreach($aProfiles as $aProfile)
        $aItems[$aProfile['id']] = array(
            'id' => $aProfile['id'],
            'thumbnail' => get_member_thumbnail($aProfile['id'], 'none'),
            'edit_link' => $GLOBALS['site']['url'] . 'pedit.php?ID=' . $aProfile['id'],
            'edit_class' => (int)$aProfile['banned'] == 1 ? 'adm-mp-banned' : ($aProfile['status'] != 'Active' ? 'adm-mp-inactive' : 'adm-mp-active'),
            'edit_width' => defined('CH_AVA_W') ? CH_AVA_W : 70,
            'username' => getNickName($aProfile['id']),
            'info' => $GLOBALS['oFunctions']->getUserInfo($aProfile['id'])
        );

    return $GLOBALS['oAdmTemplate']->parseHtmlByName('mp_members_simple.html', array(
        'ch_repeat:items' => array_values($aItems),
        'paginate' => $sPaginate,
        'control' => $sControls
    ));
}
function getMembersExtended($aProfiles, $sPaginate, $sControls)
{
    $aItems = array();
    foreach($aProfiles as $aProfile)
        $aItems[$aProfile['id']] = array(
            'id' => $aProfile['id'],
            'thumbnail' => get_member_thumbnail($aProfile['id'], 'none'),
            'edit_link' => $GLOBALS['site']['url'] . 'pedit.php?ID=' . $aProfile['id'],
            'edit_class' => (int)$aProfile['banned'] == 1 ? 'adm-mp-banned' : ($aProfile['status'] != 'Active' ? 'adm-mp-inactive' : 'adm-mp-active'),
            'username' => getNickName($aProfile['id']),
            'info' => $GLOBALS['oFunctions']->getUserInfo($aProfile['id']),
            'description' => strmaxtextlen($aProfile['description'], 130),
        );
    return $GLOBALS['oAdmTemplate']->parseHtmlByName('mp_members_extended.html', array(
        'ch_repeat:items' => array_values($aItems),
        'paginate' => $sPaginate,
        'control' => $sControls
    ));
}
