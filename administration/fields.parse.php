<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

define ('CH_SECURITY_EXCEPTIONS', true);
$aChSecurityExceptions = array ();
$aChSecurityExceptions[] = 'POST.Check';
$aChSecurityExceptions[] = 'REQUEST.Check';
$aChSecurityExceptions[] = 'POST.Values';
$aChSecurityExceptions[] = 'REQUEST.Values';

require_once( '../inc/header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbPFM.php' );

send_headers_page_changed();

$logged['admin'] = member_auth( 1, true, true );

$sAction = ch_get('action');
switch(true) {
    case 'getArea' == $sAction:
        genAreaJSON((int)ch_get('id'));
        break;
    case 'createNewBlock' == $sAction:
        createNewBlock();
        break;
    case 'createNewItem' == $sAction:
        createNewItem();
        break;
    case 'savePositions' == $sAction:
        savePositions((int)ch_get('id'));
        break;
    case 'loadEditForm' == $sAction:
    	header('Content-Type: text/html; charset=utf-8');
        showEditForm((int)ch_get('id'), (int)ch_get('area'));
        break;
    case 'dummy' == $sAction:
        echo 'Dummy!';
        break;
    case true == ch_get('action-save'):
    case 'Save' == $sAction:
        saveItem((int)ch_get('area'), $_POST);
        break;
    case true == ch_get('action-delete'):
    case 'Delete' == $sAction:
        deleteItem((int)ch_get('id'), (int)ch_get('area'));
        break;
}

function createNewBlock()
{
    $oFields = new ChWsbPFM( 1 );
    $iNewID = $oFields -> createNewBlock();
    header('Content-Type:text/javascript');
    echo '{"id":' . $iNewID . '}';
}

function createNewItem()
{
    $oFields = new ChWsbPFM( 1 );
    $iNewID = $oFields -> createNewField();

    header('Content-Type:text/javascript');
    echo '{"id":' . $iNewID . '}';
}

function genAreaJSON( $iAreaID )
{
    $oFields = new ChWsbPFM( $iAreaID );

    header('Content-Type:text/javascript; charset=utf-8');
    echo $oFields -> genJSON();
}

function savePositions( $iAreaID )
{
    $oFields = new ChWsbPFM( $iAreaID );

    header( 'Content-Type:text/javascript' );
    $oFields -> savePositions( $_POST );

    $oCacher = new ChWsbPFMCacher();
    $oCacher -> createCache();
}

function saveItem( $iAreaID, $aData )
{
    $oFields = new ChWsbPFM( $iAreaID );
    $oFields -> saveItem( $_POST );

    $oCacher = new ChWsbPFMCacher();
    $oCacher -> createCache();
}

function deleteItem( $iItemID, $iAreaID )
{
    $oFields = new ChWsbPFM( $iAreaID );
    $oFields -> deleteItem( $iItemID );

    $oCacher = new ChWsbPFMCacher();
    $oCacher -> createCache();
}

function showEditForm( $iItemID, $iAreaID )
{
    $oFields = new ChWsbPFM( $iAreaID );

    ob_start();
    ?>
    <form name="fieldEditForm" method="post" action="<?=$GLOBALS['site']['url_admin'] . 'fields.parse.php'; ?>" target="fieldFormSubmit" onsubmit="clearFormErrors( this )">
        <div class="edit_item_table_cont">
            <?=$oFields -> genFieldEditForm( $iItemID ); ?>
        </div>
    </form>

    <iframe name="fieldFormSubmit" style="display:none;"></iframe>
    <?php
    $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => ob_get_clean()));

    echo PopupBox('pf_edit_popup', _t('_adm_fields_box_cpt_field'), $sResult);
}
