<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( 'header.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( CH_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbTags.php' );

function explodeTags( $text )
{
    //$text = preg_replace( '/[^a-zA-Z0-9_\'-]/', ' ', $text );

    $aTags = preg_split( '/[' . CH_WSB_TAGS_DIVIDER . ']/', $text, 0, PREG_SPLIT_NO_EMPTY );

    foreach( $aTags as $iInd => $sTag ) {
        if( strlen( $sTag ) < 3 )
            unset( $aTags[$iInd] );
        else
            $aTags[$iInd] = trim(mb_strtolower( $sTag , 'UTF-8'));
    }
    $aTags = array_unique( $aTags );
    $sTagsNotParsed = getParam( 'tags_non_parsable' );
    $aTagsNotParsed = preg_split( '/[' . CH_WSB_TAGS_DIVIDER . ']/', $sTagsNotParsed, 0, PREG_SPLIT_NO_EMPTY );

    $aTags = array_diff( $aTags, $aTagsNotParsed ); //drop non parsable tags

    return $aTags;
}

function storeTags( $iID, $sTags, $sType )
{
    $aTags = explodeTags( $sTags );
    db_res( "DELETE FROM `sys_tags` WHERE `ID` = ? AND `Type` = ?", [$iID, $sType]); //re-store if exist

    foreach( $aTags as $sTag ) {
        $sTag = addslashes( $sTag );
        db_res( "INSERT INTO `sys_tags` VALUES ( ?, ?, ?, CURRENT_TIMESTAMP )", [$sTag, $iID, $sType]);
    }
}

function reparseObjTags( $sType, $iID )
{
    $oTags = new ChWsbTags();
    $oTags->reparseObjTags($sType, $iID);
}
