<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChWmapPageEdit extends ChWsbPageView
{
    var $_oMain;
    var $_oTemplate;
    var $_oConfig;
    var $_oDb;
    var $_sUrlStart;
    var $_aLocation;

    function __construct(&$oModule, $aLocation)
    {
        $this->_oMain = &$oModule;
        $this->_oTemplate = $oModule->_oTemplate;
        $this->_oConfig = $oModule->_oConfig;
        $this->_oDb = $oModule->_oDb;
        $this->_aLocation = $aLocation;
        parent::__construct('ch_wmap_edit');
    }

    function getBlockCode_MapEdit()
    {
        return $this->_oMain->serviceEditLocation ($this->_aLocation['part'], $this->_aLocation['id']);
    }
}
