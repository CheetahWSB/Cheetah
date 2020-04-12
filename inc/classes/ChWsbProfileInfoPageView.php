<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import ('ChTemplProfileView');
ch_import ('ChTemplProfileGenerator');

class ChWsbProfileInfoPageView extends ChTemplProfileView
{
    // contain informaion about viewed profile ;
    var $aMemberInfo = array();
    // logged member ID ;
    var $iMemberID;
    var $oProfilePV;

    /**
     * Class constructor ;
     */
    function __construct( $sPageName, &$aMemberInfo )
    {
        global $site, $dir;

        $this->oProfileGen = new ChTemplProfileGenerator( $aMemberInfo['ID'] );
        $this->aConfSite = $site;
        $this->aConfDir  = $dir;
        ChWsbPageView::__construct($sPageName);

        $this->iMemberID  = getLoggedId();
        $this->aMemberInfo = &$aMemberInfo;
    }

    /**
     * Function will generate profile's  general information ;
     *
     * @return : (text) - html presentation data;
     */
    function getBlockCode_GeneralInfo($iBlockID)
    {
        return $this -> getBlockCode_PFBlock($iBlockID, 17);
    }

    /**
     * Function will generate profile's additional information ;
     *
     * @return : (text) - html presentation data;
     */
    function getBlockCode_AdditionalInfo($iBlockID)
    {
        return $this -> getBlockCode_PFBlock($iBlockID, 20);
    }

}
