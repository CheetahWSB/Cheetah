<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbConfig.php');

class ChSctrConfig extends ChWsbConfig
{
    var $_oDb;
    var $_oSession;
    var $_bEnabled;
    var $_sSessionKeyOpen;
    var $_sSessionKeyData;
    var $_sSessionDataDivider;
	var $_aJsClasses;
    var $_aJsObjects;

    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);

        $this->_sSessionKeyOpen = 'ch_sctr_open';
        $this->_sSessionKeyData = 'ch_sctr_data';
        $this->_sSessionDataDivider = '#';
        $this->_aJsClasses = array('main' => 'ChSctrMain');
        $this->_aJsObjects = array('main' => 'oChSctrMain');
    }

    function init(&$oDb)
    {
        $this->_oDb = &$oDb;
        $this->_oSession = ChWsbSession::getInstance();

        $this->_bEnabled = getParam('ch_sctr_enable') == 'on';
    }

    function isEnabled()
    {
    	global $oSysTemplate;
		return $this->_bEnabled && in_array($oSysTemplate->getCode(), array('uni', 'alt', 'evo'));
    }

	function getOpenKey()
    {
		return $this->_sSessionKeyOpen;
    }

    function isOpen()
    {
    	return (int)$this->_oSession->getValue($this->getOpenKey()) != 0;
    }

    function doOpen()
    {
    	$this->_oSession->setValue($this->getOpenKey(), 1);
    }

    function doClose()
    {
    	$this->_oSession->unsetValue($this->getOpenKey());
    	$this->cancelSession();
    }

	function getSessionKey()
    {
		return $this->_sSessionKeyData;
    }

    function isSession()
    {
		$sData = $this->_oSession->getValue($this->getSessionKey());
		return !empty($sData);
    }

    function getSessionData()
    {
		$sData = $this->_oSession->getValue($this->getSessionKey());
		return explode($this->_sSessionDataDivider, $sData);
    }

	function setSessionData($aData)
    {
    	$sData = implode($this->_sSessionDataDivider, $aData);
		$this->_oSession->setValue($this->getSessionKey(), $sData);
    }

	function cancelSession()
	{
	    $this->_oSession->unsetValue($this->getSessionKey());
	}

	function getJsClass($sType = 'main')
    {
        if(empty($sType))
            return $this->_aJsClasses;

        return $this->_aJsClasses[$sType];
    }

    function getJsObject($sType = 'main')
    {
        if(empty($sType))
            return $this->_aJsObjects;

        return $this->_aJsObjects[$sType];
    }
}
