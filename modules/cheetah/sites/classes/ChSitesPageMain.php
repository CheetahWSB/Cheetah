<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbTwigPageMain');

class ChSitesPageMain extends ChWsbTwigPageMain
{
    var $_oSites;
    var $_oTemplate;
    var $_oConfig;
    var $_oDb;

    function __construct(&$oSites)
    {
        parent::__construct('ch_sites_main', $oSites);

        $this->_oSites = &$oSites;
        $this->_oTemplate = $oSites->_oTemplate;
        $this->_oConfig = $oSites->_oConfig;
        $this->_oDb = $oSites->_oDb;
    }

    function getBlockCode_ViewFeature()
    {
        ch_sites_import('SearchResult');
        $oSearchResult = new ChSitesSearchResult('featuredshort');

        if ($s = $oSearchResult->displayResultBlock(true, true))
            return $s;
        else
            return '';
    }

    function getBlockCode_ViewRecent()
    {
        ch_sites_import('SearchResult');
        $oSearchResult = new ChSitesSearchResult('featuredlast');

        if ($s = $oSearchResult->displayResultBlock())
            return $s;
        else
            return '';
    }

    function getBlockCode_ViewAll()
    {
        ch_sites_import('SearchResult');
        $oSearchResult = new ChSitesSearchResult('home');

        if ($s = $oSearchResult->displayResultBlock(true, true)) {
            return array(
                $s,
                array(
                    _t('RSS') => array(
                        'href' => $this->_oConfig->getBaseUri() . 'browse/all?rss=1',
                        'target' => '_blank',
                        'icon' => 'rss',
                    )
                ),
                array(),
                true
            );
        } else
            return MsgBox(_t('_Empty'));
    }

}
