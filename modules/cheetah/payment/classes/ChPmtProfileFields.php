<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import("ChWsbProfileFields");

class ChPmtProfileFields extends ChWsbProfileFields {
	var $_oMain;

	function __construct($iAreaID, $oMain = null) {
		parent::__construct($iAreaID);

		$this->_oMain = $oMain;
	}

	function getFormJoin($aParams)
	{
		$aForm = parent::getFormJoin($aParams);
		$aForm['form_attrs']['action'] = CH_WSB_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'join';

		return $aForm;
	}

}
?>
