<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChSitesPageHon extends ChWsbPageView
{
    var $_oSites;
    var $_oTemplate;
    var $_oDb;

    function __construct(&$oSites)
    {
        parent::__construct('ch_sites_hon');

        $this->_oSites = &$oSites;
        $this->_oTemplate = $oSites->_oTemplate;
        $this->_oDb = $oSites->_oDb;
    }

    function getBlockCode_ViewPreviously()
    {
        ch_sites_import('SearchResult');
        $oSearchResult = new ChSitesSearchResult('hon_prev_rate');
        $oSearchResult->sUnitTemplate = 'block_prev_hon';

        if ($s = $oSearchResult->displayResultBlock())
            return $s;
        else
            return MsgBox(_t('_Empty'));
    }

    function getBlockCode_ViewRate()
    {
        ch_sites_import('SearchResult');
        $oSearchResult = new ChSitesSearchResult('hon_rate');
        $oSearchResult->sUnitName = 'hon';
        $oSearchResult->sUnitTemplate = 'block_hon';

        if ($s = $oSearchResult->displayResultBlock())
            return $s;
        else
            return MsgBox(_t('_Empty'));
    }
}
