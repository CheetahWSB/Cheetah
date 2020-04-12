<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChMbpPageJoin extends ChWsbPageView
{
	var $_oObject;

    function __construct(&$oObject)
    {
    	parent::__construct('ch_mbp_join');

    	$this->_oObject = $oObject;
    }

	function getBlockCode_Select()
    {
        return $this->_oObject->getSelectLevelBlock();
    }
}
