<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbConnectDb');

class ChFaceBookConnectDb extends ChWsbConnectDb
{
    /**
     * Constructor.
     */
    function __construct(&$oConfig)
    {
        parent::__construct($oConfig);
    }

    /**
     * Process big number
     *
     * @param $mValue mixed
     * @return integer
     */
    function _processBigNumber($mValue)
    {
        return preg_replace('/[^0-9]/', '', $mValue);
    }

    /**
     * Check fb profile id
     *
     * @param $iFbUid integer
     * @return integer
     */
    function getProfileId($iFbUid)
    {
        $iFbUidCopy = (int) $iFbUid;
        $iFbUid = $this -> _processBigNumber($iFbUid);


        //-- handle 64 bit number on 32bit system ( will need remove it in a feature version)--//
        if($iFbUidCopy != $iFbUid) {
            //update id
            $sQuery = "UPDATE `{$this -> sTablePrefix}accounts` SET `fb_profile` = '{$iFbUid}'
                WHERE `fb_profile` = '{$iFbUidCopy}'";

            $this -> query($sQuery);
        }
        //--

        //-- new auth method --//
        $sQuery = "SELECT `id_profile` FROM `{$this -> sTablePrefix}accounts` WHERE
            `fb_profile` = '{$iFbUid}' LIMIT 1";

        $iProfileId = $this -> getOne($sQuery);
        //--

        return $iProfileId;
    }

    /**
     *  Save new Fb uid
     *
     * @param $iProfileId integer
     * @param $iFbUid integer
     * @return void
     */
    function saveRemoteId($iProfileId, $iFbUid)
    {
        $iFbUid = $this -> _processBigNumber($iFbUid);
        $iProfileId = (int) $iProfileId;

        $sQuery = "REPLACE INTO `{$this -> sTablePrefix}accounts`
                    SET `id_profile` = {$iProfileId}, `fb_profile` = '{$iFbUid}'";

        $this -> query($sQuery);
    }

    /**
     * Delete Fb's uid
     *
     * @param $iProfileId integer
     * @return void
     */
    function deleteRemoteAccount($iProfileId)
    {
        $iProfileId = (int) $iProfileId;
        $sQuery = "DELETE FROM `{$this -> sTablePrefix}accounts`
            WHERE `id_profile` = {$iProfileId}";

        $this -> query($sQuery);
    }
}
