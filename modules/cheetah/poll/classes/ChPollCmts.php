<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChTemplCmtsView');

class ChPollCmts extends ChTemplCmtsView
{
	var $_oModule;

    /**
     * Constructor
     */
    function __construct($sSystem, $iId)
    {
        parent::__construct($sSystem, $iId);

        $this->_oModule = ChWsbModule::getInstance('ChPollModule');
    }

	function getBaseUrl()
    {
    	$aEntry = $this->_oModule->_oDb->getPollInfo($this->getId());
    	if(empty($aEntry) || !is_array($aEntry))
    		return '';

    	$aEntry = array_shift($aEntry);
    	if(empty($aEntry) || !is_array($aEntry))
    		return '';

    	return CH_WSB_URL_ROOT . $this->_oModule->_oConfig->getBaseUri() . '&action=show_poll_info&id=' . $aEntry['id_poll'];
    }
}
