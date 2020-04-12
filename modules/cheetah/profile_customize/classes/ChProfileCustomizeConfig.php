<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbConfig.php');

class ChProfileCustomizeConfig extends ChWsbConfig
{
    var $_oDb;
	var $_aJsClasses;
    var $_aJsObjects;

    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);

		$this->_aJsClasses = array('main' => 'ChProfileCustimizer');
        $this->_aJsObjects = array('main' => 'oCustomizer');
    }

    function init(&$oDb)
    {
        $this->_oDb = &$oDb;
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
