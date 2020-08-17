<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'admin.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'protected.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'db.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'languages.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'prof.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'banners.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'params.inc.php');
require_once(CH_DIRECTORY_PATH_CLASSES . 'ChRSS.php');

require_once(CH_DIRECTORY_PATH_ROOT . "templates/tmpl_{$tmpl}/scripts/ChTemplMenu.php");
require_once(CH_DIRECTORY_PATH_ROOT . "templates/tmpl_{$tmpl}/scripts/ChTemplFunctions.php");

$db_color_index = 0;

$_page['js'] = 1;

/**
 * Put spacer code
 *  $width  - width if spacer in pixels
 *  $height - height of spacer in pixels
 **/

function spacer($width, $height)
{
    return '<img src="' . CH_WSB_URL_ROOT . 'templates/base/images/spacer.gif" width="' . $width . '" height="' . $height . '" alt="" />';
}

/**
 * Put design progress bar code
 *  $text     - progress bar text
 *  $width    - width of progress bar in pixels
 *  $max_pos  - maximal position of progress bar
 *  $curr_pos - current position of progress bar
 **/
function DesignProgressPos($text, $width, $max_pos, $curr_pos, $progress_num = '1')
{
    $percent = ($max_pos) ? $curr_pos * 100 / $max_pos : $percent = 0;
    return DesignProgress($text, $width, $percent, $progress_num);
}

/**
 * Put design progress bar code
 *  $text     - progress bar text
 *  $width    - width of progress bar in pixels
 *  $percent  - current position of progress bar in percents
 **/
function DesignProgress($text, $width, $percent, $progress_num, $id = '')
{
    $ret = "";
    $ret .= '<div class="rate_block" style="width:' . $width . 'px;">';
    $ret .= '<div class="rate_text"' . ($id ? " id=\"{$id}_text\"" : '') . '>';
    $ret .= $text;
    $ret .= '</div>';
    $ret .= '<div class="rate_scale"' . ($id ? " id=\"{$id}_scale\"" : '') . '>';
    $ret .= '<div class="rate_bar" ' . ($id ? "id=\"{$id}_bar\"" : '') . ' style="width:' . round($percent) . '%;"></div>';
    $ret .= '</div>';
    $ret .= '</div>';

    return $ret;
}

/**
 * Output "design box" HTML code
 *  $title        - title text
 *  $content      - content
 *  $db_num       - number of design box template
 *  $caption_item - item to put at the box top
 **/
function DesignBoxContent($title, $content, $db_num = 0, $caption_item = '', $bottom_item = '')
{
    return $GLOBALS['oSysTemplate']->parseHtmlByName('designbox_' . (int)$db_num . '.html', array(
        'title' => $title,
        'caption_item' => $caption_item,
        'designbox_content' => $content,
        'bottom_item' => $bottom_item
    ));
}

/**
 * Output code for the page
 **/
function PageCode($oTemplate = null)
{
    chPageCode($oTemplate);
}

/**
 * Use this function in pages if you want to not cache it.
 **/
function send_headers_page_changed()
{
    $now = gmdate('D, d M Y H:i:s') . ' GMT';

    header("Expires: $now");
    header("Last-Modified: $now");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
}

/**
 * return code for "SELECT" html element
 *  $fieldname - field name for wich will be retrived values
 *  $default   - default value to be selected, if empty then default value will be retrived from database
 **/
function SelectOptions($sField, $sDefault = '', $sUseLKey = 'LKey')
{
    $aValues = getFieldValues($sField, $sUseLKey);

    $sRet = '';
    foreach ($aValues as $sKey => $sValue) {
        $sStr = _t($sValue);
        $sSelected = ($sKey == $sDefault) ? 'selected="selected"' : '';
        $sRet .= "<option value=\"$sKey\" $sSelected>$sStr</option>\n";
    }

    return $sRet;
}

function getFieldValues($sField, $sUseLKey = 'LKey')
{
    global $aPreValues;

    $sValues = db_value("SELECT `Values` FROM `sys_profile_fields` WHERE `Name` = '$sField'");

    if (substr($sValues, 0, 2) == '#!') {
        //predefined list
        $sKey = substr($sValues, 2);

        $aValues = array();

        $aMyPreValues = $aPreValues[$sKey];
        if (!$aMyPreValues) {
            return $aValues;
        }

        foreach ($aMyPreValues as $sVal => $aVal) {
            $sMyUseLKey = $sUseLKey;
            if (!isset($aMyPreValues[$sVal][$sUseLKey])) {
                $sMyUseLKey = 'LKey';
            }

            $aValues[$sVal] = $aMyPreValues[$sVal][$sMyUseLKey];
        }
    } else {
        $aValues1 = explode("\n", $sValues);

        $aValues = array();
        foreach ($aValues1 as $iKey => $sValue) {
            $aValues[$sValue] = "_$sValue";
        }
    }

    return $aValues;
}

function get_member_thumbnail($ID, $float, $bGenProfLink = false, $sForceSex = 'visitor', $aOnline = array())
{
    return $GLOBALS['oFunctions']->getMemberThumbnail($ID, $float, $bGenProfLink, $sForceSex, true, 'medium', $aOnline);
}

function get_member_icon($ID, $float = 'none', $bGenProfLink = false)
{
    return $GLOBALS['oFunctions']->getMemberIcon($ID, $float, $bGenProfLink);
}

function MsgBox($sText, $iTimer = 0)
{
    return $GLOBALS['oFunctions'] -> msgBox($sText, $iTimer);
}

function AdvMsgBox($sText, $aOptions = array())
{
    return $GLOBALS['oFunctions'] -> advMsgBox($sText, $aOptions);
}

function LoadingBox($sName)
{
    return $GLOBALS['oFunctions'] -> loadingBox($sName);
}
function PopupBox($sName, $sTitle, $sContent, $aActions = array())
{
    return $GLOBALS['oFunctions'] -> popupBox($sName, $sTitle, $sContent, $aActions);
}
function getTemplateIcon($sFileName)
{
    return $GLOBALS['oFunctions']->getTemplateIcon($sFileName);
}

function getTemplateImage($sFileName)
{
    return $GLOBALS['oFunctions']->getTemplateImage($sFileName);
}

function getVersionComment()
{
    global $site;
    $aVer = explode('.', $site['ver']);

    // version output made for debug possibilities.
    // randomizing made for security issues. do not change it...
    $aVerR[0] = $aVer[0];
    $aVerR[1] = rand(0, 100);
    $aVerR[2] = $aVer[1];
    $aVerR[3] = rand(0, 100);
    $aVerR[4] = $site['build'];

    //remove leading zeros
    while ($aVerR[4][0] === '0') {
        $aVerR[4] = substr($aVerR[4], 1);
    }

    return '<!-- ' . implode(' ', $aVerR) . ' -->';
}

// ----------------------------------- site statistick functions --------------------------------------//

function getSiteStatUser()
{
    global $aStat;
    $aStat = getSiteStatArray();

    $sCode  = '<div class="siteStatMain">';

    foreach ($aStat as $aVal) {
        $sCode .= $GLOBALS['oFunctions']->getSiteStatBody($aVal);
    }

    $sCode .= '<div class="clear_both"></div></div>';

    return $sCode;
}

function genAjaxyPopupJS($iTargetID, $sDivID = 'ajaxy_popup_result_div', $sRedirect = '')
{
    $iProcessTime = 1000;

    if ($sRedirect) {
        $sRedirect = "window.location = '$sRedirect';";
    }

    $sJQueryJS = <<<EOF
<script type="text/javascript">

setTimeout( function(){
    $('#{$sDivID}_{$iTargetID}').show({$iProcessTime})
    setTimeout( function(){
        $('#{$sDivID}_{$iTargetID}').hide({$iProcessTime});
        $sRedirect
    }, 3000);
}, 500);

</script>
EOF;
    return $sJQueryJS;
}

function getBlockWidth($iAllWidth, $iUnitWidth, $iNumElements)
{
    $iAllowed = $iNumElements * $iUnitWidth;
    if ($iAllowed > $iAllWidth) {
        $iMax = (int)floor($iAllWidth / $iUnitWidth);
        $iAllowed = $iMax*$iUnitWidth;
    }
    return $iAllowed;
}

function getMemberJoinFormCode($sParams = '')
{
    if (getParam('reg_by_inv_only') == 'on' && getID($_COOKIE['idFriend']) == 0) {
        return MsgBox(_t('_registration by invitation only'));
    }

    $sCodeBefore = '';
    $sCodeAfter = '';

    ch_import("ChWsbJoinProcessor");
    $oJoin = new ChWsbJoinProcessor();
    $sCode = $oJoin->process();

    ch_import('ChWsbAlerts');
    $oAlert = new ChWsbAlerts('profile', 'show_join_form', 0, 0, array('oJoin' => $oJoin, 'sParams' => &$sParams, 'sCustomHtmlBefore' => &$sCodeBefore, 'sCustomHtmlAfter' => &$sCodeAfter, 'sCode' => &$sCode));
    $oAlert->alert();

    $sAuthCode = getMemberAuthCode('_sys_auth_join_with');

    $sAction = 'join';
    return $GLOBALS['oSysTemplate']->parseHtmlByName('login_join_form.html', array(
        'action' => $sAction,
        'ch_if:show_auth' => array(
            'condition' => !empty($sAuthCode),
            'content' => array(
                'auth' => $sAuthCode
            )
        ),
        'custom_code_before' => $sCodeBefore,
        'form' => $sCode,
        'custom_code_after' => $sCodeAfter,
        'ch_if:show_text' => array(
            'condition' => false,
            'content' => array(
                'action' => $sAction,
                'text' => _t('_join_form_note', CH_WSB_URL_ROOT)
            )
        )
    ));
}

function getMemberLoginFormCode($sID = 'member_login_form', $sParams = '')
{
    $aForm = array(
        'form_attrs' => array(
            'id' => $sID,
            'action' => CH_WSB_URL_ROOT . 'member.php',
            'method' => 'post',
            'onsubmit' => "validateLoginForm(this); return false;",
        ),
        'inputs' => array(
            'nickname' => array(
                'type' => 'text',
                'name' => 'ID',
                'caption' => _t('_NickName'),
            ),
            'password' => array(
                'type' => 'password',
                'name' => 'Password',
                'caption' => _t('_Password'),
            ),
            'rememberme' => array(
                'type' => 'hidden',
                'name' => 'rememberMe',
                'value' => 'on',
            ),
            'relocate' => array(
                'type' => 'hidden',
                'name' => 'relocate',
                'value'=> isset($_REQUEST['relocate']) ? $_REQUEST['relocate'] : CH_WSB_URL_ROOT . 'member.php',
            ),
            'LogIn' => array(
                'type' => 'submit',
                'name' => 'LogIn',
                'caption' => '',
                'value' => _t('_Login'),
            ),
            'forgot' => array(
                'type' => 'custom',
                'colspan' => '2',
                'tr_attrs' => array(
                    'class' => 'ch-form-element-forgot'
                ),
                'content' => '<a href="' . CH_WSB_URL_ROOT . 'forgot.php">' . _t('_forgot_your_password') . '?</a>',
            )
        ),
    );

    $oForm = new ChTemplFormView($aForm);


    ch_import('ChWsbAlerts');
    $sCustomHtmlBefore = '';
    $sCustomHtmlAfter = '';
    $oAlert = new ChWsbAlerts('profile', 'show_login_form', 0, 0, array('oForm' => $oForm, 'sParams' => &$sParams, 'sCustomHtmlBefore' => &$sCustomHtmlBefore, 'sCustomHtmlAfter' => &$sCustomHtmlAfter));
    $oAlert->alert();

    $sAuthCode = getMemberAuthCode('_sys_auth_login_with');

    $sAction = 'login';
    return $GLOBALS['oSysTemplate']->parseHtmlByName('login_join_form.html', array(
        'action' => $sAction,
        'ch_if:show_auth' => array(
            'condition' => !empty($sAuthCode) && false === strpos($sParams, 'disable_external_auth'),
            'content' => array(
                'auth' => $sAuthCode
            )
        ),
        'custom_code_before' => $sCustomHtmlBefore,
        'form' => $oForm->getCode(),
        'custom_code_after' => $sCustomHtmlAfter,
        'ch_if:show_text' => array(
            'condition' => strpos($sParams, 'no_join_text') === false,
            'content' => array(
                'action' => $sAction,
                'text' => _t('_login_form_description2join', CH_WSB_URL_ROOT)
            )
        )
    ));
}

function getMemberAuthCode($sTitleKey = '')
{
    $aAuthTypes = $GLOBALS['MySQL']-> fromCache('sys_objects_auths', 'getAll', 'SELECT * FROM `sys_objects_auths`');
    if (empty($aAuthTypes) || !is_array($aAuthTypes)) {
        return '';
    }

    $aTmplButtons = array();
    foreach ($aAuthTypes as $iKey => $aItems) {
        $sTitle = _t($aItems['Title']);

        $aTmplButtons[] = array(
            'href' => !empty($aItems['Link']) ? CH_WSB_URL_ROOT . $aItems['Link'] : 'javascript:void(0)',
            'ch_if:show_onclick' => array(
                'condition' => !empty($aItems['OnClick']),
                'content' => array(
                    'onclick' => 'javascript:' . $aItems['OnClick']
                )
            ),
            'ch_if:show_icon' => array(
                'condition' => !empty($aItems['Icon']),
                'content' => array(
                    'icon' => $aItems['Icon']
                )
            ),
            'title' => !empty($sTitleKey) ? _t($sTitleKey, $sTitle) : $sTitle
        );
    }

    return $GLOBALS['oSysTemplate']->parseHtmlByName('login_join_auth.html', array(
        'ch_repeat:buttons' => $aTmplButtons
    ));
}

ch_import('ChWsbAlerts');
$oZ = new ChWsbAlerts('system', 'design_included', 0);
$oZ->alert();
