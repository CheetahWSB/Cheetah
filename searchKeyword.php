<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'languages.inc.php');

ch_import('ChWsbSearch');
ch_import('ChTemplFormView');

check_logged();
$member['ID'] = getLoggedId();

$_page['name_index'] = 81;
$_page['css_name'] = array('searchKeyword.css', 'plugins/fancybox/|jquery.fancybox.css');
$_page['js_name'] = array('plugins/fancybox/|jquery.fancybox.js');

$_page['header'] = _t( "_Search" );
$_page['header_text'] = _t("_Search");

ob_start();
?>
<script language="javascript">
    function toggleState(sItem) {
        $('#'+sItem).toggleClass('ch-btn-disabled', !$('#'+sItem).hasClass('ch-btn-disabled'));
    }
    function serializeButtons() {
        var sQuery = '';
        $('button', '#buttonArea').each(function(index) {
            if (!$(this).hasClass('ch-btn-disabled')) {
                sQuery += '&section[]=' + $(this).prop('id');
            }
        });
        return sQuery;
    }
    $(document).ready(function() {
        $('#searchForm').bind('submit', function() {
            ch_loading('searchForm', true);
            var sQuery = $('input', '#searchForm').serialize();
            sQuery += serializeButtons();
            $.post('searchKeywordContent.php', sQuery, function(data) {
                    $('#searchArea').html(data);
                    ch_loading('searchForm', false);
            });
            return false;
        });
    });
</script>
<?php
$sCode = '';
$_page['extra_js'] = ob_get_clean();

$_ni = $_page['name_index'];

$oZ = new ChWsbSearch();
if (ch_get('keyword')) {
    $sCode = $oZ->response();
    if (mb_strlen($sCode) == 0)
        $sCode = $oZ->getEmptyResult();
}

$sForm = getSearchForm();
$sSearchArea = '<div id="searchArea">'.$sCode.'</div>';

$_page_cont[$_ni]['page_main_code'] = $sForm . $sSearchArea;

$aVars = array ();
$GLOBALS['oTopMenu']->setCustomSubActions($aVars, '');

PageCode();

function getSearchForm ()
{
    $aList = $GLOBALS['MySQL']->fromCache('sys_objects_search', 'getAllWithKey',
           'SELECT `ID` as `id`,
                   `Title` as `title`,
                   `ClassName` as `class`,
                   `ClassPath` as `file`,
                   `ObjectName`
            FROM `sys_objects_search`', 'ObjectName'
    );
    $aValues = array();
    foreach ($aList as $sKey => $aValue) {
        $aValues[$sKey] = _t($aValue['title']);
        if (!class_exists($aValue['class'])) {
            $sPath = CH_DIRECTORY_PATH_ROOT . str_replace('{tmpl}', $GLOBALS['tmpl'], $aValue['file']);
            require_once($sPath);
        }
        $oClass = new $aValue['class']();
        $oClass->addCustomParts();
    }

    $sButtons = '<div id="buttonArea" class="ch-btn-area clearfix">';
    foreach ($aList as $sKey => $aValue) {
        $sClassAdd = '';
        if (isset($_GET['type']))
            $sClassAdd = $_GET['type'] == $sKey ? '' : 'ch-btn-disabled';
        $sButtons .= '<button class="ch-btn ch-btn-centered ' . $sClassAdd . '" type="button" id="' . $sKey . '" onclick="toggleState(\'' . $sKey . '\');">' . _t($aValue['title']) . '</button>';
    }
    $sButtons .= '</div>';

    $aForm = array(
        'form_attrs' => array(
           'id' => 'searchForm',
           'action' => '',
           'method' => 'post',
           'onsubmit' => '',
        ),
        'inputs' => array(
            'keyword' => array(
                'type' => 'text',
                'name' => 'keyword',
                'caption' => _t('_Keyword')
            ),
            'search' => array(
                'type' => 'submit',
                'name' => 'search',
                'value' => _t('_Search')
            )
        )
    );

    $oForm = new ChTemplFormView($aForm);
    $sFormVal = $oForm->getCode();

    return DesignBoxContent(_t( "_Search" ), $sButtons . $sFormVal, 11);
}
