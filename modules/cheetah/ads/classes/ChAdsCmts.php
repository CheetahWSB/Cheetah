<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChTemplCmtsView');

class ChAdsCmts extends ChTemplCmtsView
{
	var $_oModule;

    /**
     * Constructor
     */
    function __construct($sSystem, $iId)
    {
        parent::__construct($sSystem, $iId);

        $this->_oModule = ChWsbModule::getInstance('ChAdsModule');
    }

    function getBaseUrl()
    {
    	$aEntry = $this->_oModule->_oDb->getAdInfo($this->getId());
    	if(empty($aEntry) || !is_array($aEntry))
    		return '';

    	return $this->_oModule->genUrl($aEntry['ID'], $aEntry['EntryUri'], 'entry');
    }

    function isPostReplyAllowed($isPerformAction = false)
    {
        if (!parent::isPostReplyAllowed($isPerformAction))
            return false;

        $oMain = ChWsbModule::getInstance('ChAdsModule');
        $aAdPost = $oMain->_oDb->getAdInfo($this->getId());
        return $oMain->isAllowedComments($aAdPost);
    }
}
