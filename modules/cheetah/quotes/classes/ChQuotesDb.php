<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbModuleDb.php' );

define('CH_QUOTES_TABLE', 'ch_quotes_units');

/*
* Quotes module Data
*/
class ChQuotesDb extends ChWsbModuleDb
{
    var $_oConfig;
    /*
    * Constructor.
    */
    function __construct(&$oConfig)
    {
        parent::__construct();

        $this->_oConfig = $oConfig;
    }

    function getRandomQuote()
    {
        return $this->getRow("SELECT `Text`, `Author` FROM `" . CH_QUOTES_TABLE . "` ORDER BY RAND() LIMIT 1");
    }
    function getQuote($iID)
    {
        return $this->getRow("SELECT * FROM `" . CH_QUOTES_TABLE . "` WHERE `ID`= ? LIMIT 1", [$iID]);
    }
    function getAllQuotes()
    {
        return $this->getAll("SELECT * FROM `" . CH_QUOTES_TABLE . "`");
    }
    function deleteUnit($iID)
    {
        return $this->query("DELETE FROM `" . CH_QUOTES_TABLE . "` WHERE `ID`= ? LIMIT 1", [$iID]);
    }
}
