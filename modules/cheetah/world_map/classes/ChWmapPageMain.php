<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChWmapPageMain extends ChWsbPageView
{
    var $_oMain;
    var $_oTemplate;
    var $_oConfig;
    var $_oDb;

    function __construct(&$oModule)
    {
        $this->_oMain = &$oModule;
        $this->_oTemplate = $oModule->_oTemplate;
        $this->_oConfig = $oModule->_oConfig;
        $this->_oDb = $oModule->_oDb;
        parent::__construct('ch_wmap');
    }

    function getBlockCode_Map()
    {
        $fLat = false;
        $fLng = false;
        $iZoom = false;
        $sParts = '';
        return $this->_oMain->serviceSeparatePageBlock ($fLat, $fLng, $iZoom, $sParts);
    }
}
