<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbConfig');

class ChAdsConfig extends ChWsbConfig
{
    var $_iAnimationSpeed;

    var $bUseFriendlyLinks;
    var $bAdminMode;
    var $sCurrBrowsedFile;
    var $sSpacerPath;

    // SQL tables

    var $sSQLPostsTable;
    var $sSQLPostsMediaTable;
    var $sSQLCatTable;
    var $sSQLSubcatTable;

    var $_sCommentSystemName;

    /*
    * Constructor.
    */
    function __construct($aModule)
    {
        parent::__construct($aModule);

        $this->_iAnimationSpeed = 'normal';

        $this->sSpacerPath = getTemplateIcon('spacer.gif');

        $this->sSQLPostsTable = 'ch_ads_main';
        $this->sSQLPostsMediaTable = 'ch_ads_main_media';
        $this->sSQLCatTable = 'ch_ads_category';
        $this->sSQLSubcatTable = 'ch_ads_category_subs';

        $this->_sCommentSystemName = "ads";
    }

    function getAnimationSpeed()
    {
        return $this->_iAnimationSpeed;
    }

    function getCommentSystemName()
    {
        return $this->_sCommentSystemName;
    }
}
