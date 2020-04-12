<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChTemplProfileView');

class ChWsbProfilePrivatePageView extends ChTemplProfileView
{
    function __construct(&$oPr, &$aSite, &$aDir)
    {
        $this->oProfileGen = &$oPr;
        $this->aConfSite = $aSite;
        $this->aConfDir  = $aDir;
        ChWsbPageView::__construct('profile_private');
    }

    function getBlockCode_PrivacyExplain()
    {
        return array(_t('_sys_profile_private_text'));
    }
}
