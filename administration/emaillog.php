<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( '../inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'admin.inc.php' );

ch_import('ChWsbPaginate');
ch_import('ChWsbAdminIpBlockList');
ch_import('ChWsbCacheUtilities');

$logged['admin'] = member_auth(1, true, true);


$iNameIndex = 3;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array(),
    'js_name' => array(),
    'header' => _t('_adm_txt_emaillog'),
    'header_text' => _t('_adm_txt_emaillog'),
);

if (!isset($_GET['mode']))
    $sMode = '';
else
    $sMode = $_GET['mode'];

$aTopItems = array();
$aTopItems['log'] = array(
    'href' => 'emaillog.php',
    'title' => _t('_adm_txt_emaillog_log'),
    'active' => '' == $sMode ? 1 : 0
);
$aTopItems['settings'] = array(
    'href' => 'emaillog.php?mode=settings',
    'title' => _t('_adm_txt_emaillog_settings'),
    'active' => 'settings' == $sMode ? 1 : 0
);

//$_page['css_name'] = 'cache.css';
$_page_cont[$iNameIndex]['page_main_code'] = mainFunc();

PageCodeAdmin();

function mainFunc()
{

    global $sMode, $aTopItems;

    if($sMode == 'settings') {
      $s = getSettings();
    } else {
      $s = getLog();
    }

    return DesignBoxAdmin(_t('_adm_txt_emaillog'), $s, $aTopItems, '', 11);
}

function getLog() {

    global $oAdmTemplate;

    if('on' == getParam('email_log_emabled')) {
        $sQuery = "SELECT * FROM `sys_email_log` ORDER BY `timestamp` DESC";
        $aEmailLog = $GLOBALS['MySQL']->getAll($sQuery);

        if($aEmailLog) {
            foreach($aEmailLog as $id => $value) {
                $time = strtotime($value['timestamp']);
                $myFormatForView = getLocaleDate($time, CH_WSB_LOCALE_DATE);
                $aEmailLog[$id]['phptime'] = $myFormatForView;
                if($value['html'] == 'text') {
                    $aEmailLog[$id]['body'] = '<div style="padding: 20px;">' . nl2br($value['body']) . '</div>';
                }
                $aEmailLog[$id]['params'] = '<div style="padding: 20px;"><pre>' . print_r(unserialize($value['params']), true) . '</pre></div>';
                $aEmailLog[$id]['recipientinfo'] = '<div style="padding: 20px;"><pre>' . print_r(unserialize($value['recipientinfo']), true) . '</pre></div>';
                $aEmailLog[$id]['debug'] = '<div style="padding: 20px;"><pre>' . print_r(unserialize($value['debug']), true) . '</pre></div>';
            }
            $r = $oAdmTemplate->parseHtmlByName('emaillog.html', array(
                'ch_repeat:emaillog' => $aEmailLog,
                //'emailto' => $aEmailLog['email'],
            ));
        } else {
            $r =  MsgBox(_t('_Empty'));
        }
    } else {
        $r =  MsgBox(_t('_adm_txt_emaillog_not_enabled'));
    }

    return $r;
}

function getSettings() {
  ch_import('ChWsbAdminSettings');
  $oSettings = new ChWsbAdminSettings(28);

  $sResults = false;
  if (isset($_POST['save']) && isset($_POST['cat']))
      $sResult = $oSettings->saveChanges($_POST);

  $s = $oSettings->getForm();
  if ($sResult)
      $s = $sResult . $s;

  return DesignBoxAdmin(_t('_adm_txt_emaillog'), $s, '', '', 0);


  $r = 'Settings';
  return $r;
}
