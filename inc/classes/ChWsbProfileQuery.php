<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbDb.php' );

class ChWsbProfileQuery extends ChWsbDb
{
    function __construct()
    {
        parent::__construct();
    }

    function getIdByEmail( $sEmail )
    {
        $sEmail = process_db_input($sEmail, CH_TAGS_STRIP);
        return $this -> getRow( "SELECT `ID` FROM " . CH_WSB_TABLE_PROFILES . " WHERE `Email` = ?", [$sEmail]);
    }

    function getIdByNickname( $sNickname )
    {
        $sNickname = process_db_input( $sNickname, CH_TAGS_STRIP );
        return $this -> getRow( "SELECT `ID` FROM " . CH_WSB_TABLE_PROFILES . " WHERE `NickName` = ?", [$sNickname]);
    }

    function getProfileDataById( $iID )
    {
        $iID = (int)$iID;
        return $this -> getRow( "SELECT * FROM " . CH_WSB_TABLE_PROFILES . " WHERE `ID` = ?", [$iID]);
    }

    function getNickName( $iID )
    {
        return $this -> getOne( "SELECT `NickName` FROM " . CH_WSB_TABLE_PROFILES . " WHERE `ID` = ?", [$iID]);
    }

}
