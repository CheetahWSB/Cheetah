<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbDb.php' );

class ChCRSSDb extends ChWsbDb
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

    function insertProfileRSS($_iProfileID, $sNewUrl, $sNewDesc, $iQuantity)
    {
        if ($sNewUrl != '' && $sNewDesc != '') {
            $sStatus = (getParam('crss_AutoApprove_RSS') == 'on') ? 'active' : 'passive';

            $sRSSSQL = "
                INSERT INTO `ch_crss_main` SET
                `ProfileID`='{$_iProfileID}',
                `RSSUrl`='{$sNewUrl}',
                `Quantity`='{$iQuantity}',
                `Description`='{$sNewDesc}',
                `Status`='{$sStatus}'
            ";
            return $this->query($sRSSSQL);
        }
    }

    function updateProfileRSS($_iProfileID, $sNewUrl, $iOldID)
    {
        if ($iOldID != '' && $sNewUrl != '') {
            $sStatus = (getParam('crss_AutoApprove_RSS') == 'on') ? 'active' : 'passive';

            $sRSSSQL = "
                UPDATE `ch_crss_main` SET
                `RSSUrl`='{$sNewUrl}',
                `Status`='{$sStatus}'
                WHERE
                `ProfileID`='{$_iProfileID}' AND `ID`='{$iOldID}'
            ";
            return $this->query($sRSSSQL);
        }
    }

    function deleteProfileRSS($_iProfileID, $iOldID)
    {
        if ($iOldID != '') {
            $sRSSSQL = "
                DELETE FROM `ch_crss_main`
                WHERE `ProfileID`='{$_iProfileID}' AND `ID`='{$iOldID}'
            ";
            return $this->query($sRSSSQL);
        }
    }

    function getProfileRSS($_iProfileID)
    {
        $sMemberRSSSQL = "SELECT * FROM `ch_crss_main` WHERE `ProfileID`='{$_iProfileID}'";

        $aRSSInfos = array();

        $aRSSInfo = $this->getFirstRow($sMemberRSSSQL);
        while($aRSSInfo) {
            $aRSSInfos[] = $aRSSInfo;
            $aRSSInfo = $this->getNextRow();
        }

        return $aRSSInfos;
    }

    function getActiveProfileRSS($_iProfileID)
    {
        $sMemberRSSSQL = "SELECT * FROM `ch_crss_main` WHERE `ProfileID`='{$_iProfileID}' AND `Status`='active'";

        $aRSSInfos = array();

        $aRSSInfo = $this->getFirstRow($sMemberRSSSQL);
        while($aRSSInfo) {
            $aRSSInfos[] = $aRSSInfo;
            $aRSSInfo = $this->getNextRow();
        }

        return $aRSSInfos;
    }
}
