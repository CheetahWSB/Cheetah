<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModule');
ch_import('ChTemplCmtsView');

class ChWsbTextCmts extends ChTemplCmtsView
{
    var $_oModule;

    function __construct($sSystem, $iId, $iInit = 1)
    {
        parent::__construct($sSystem, $iId, $iInit);

        $this->_oModule = null;
    }

    function getBaseUrl()
    {
    	$aEntry = $this->_oModule->_oDb->getEntries(array('sample_type' => 'id', 'id' => $this->getId()));
    	if(empty($aEntry) || !is_array($aEntry))
    		return '';

    	return CH_WSB_URL_ROOT . $this->_oModule->_oConfig->getBaseUri() . 'view/' . $aEntry['uri'];
    }

    /**
     * get full comments block with initializations
     */
    function getCommentsShort($sType)
    {
        return array(
            'cmt_actions' => $this->getActions(0, $sType),
            'cmt_object' => $this->getId(),
            'cmt_addon' => $this->getCmtsInit()
        );
    }
}
