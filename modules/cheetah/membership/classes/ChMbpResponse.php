<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

class ChMbpResponse extends ChWsbAlertsResponse
{
	var $_oModule;

	function response($oAlert)
	{
            $sMethod = 'processAlert' . str_replace(' ', '', ucwords(str_replace(array('_', '-'), array(' ', ' '), $oAlert->sUnit . '_' . $oAlert->sAction)));
            if(!method_exists($this, $sMethod))
                return;

            $this->_oModule = ChWsbModule::getInstance('ChMbpModule');

            $this->$sMethod($oAlert);
	}

	protected function processAlertSystemPageOutput($oAlert)
	{
            if($oAlert->aExtras['page_name'] != 'join')
                return;

            if(!$this->_oModule->_oConfig->isDisableFreeJoin())
                return;

            ch_import('PageJoin', $this->_oModule->_aModule);
            $oPage = new ChMbpPageJoin($this->_oModule);

            $oAlert->aExtras['page_code'] = $oPage->getCode();
	}

	protected function processAlertProfileShowJoinForm($oAlert)
	{
            if(!$this->_oModule->_oConfig->isDisableFreeJoin())
                return;

            list($oAlert->aExtras['sCode']) = $this->_oModule->getSelectLevelBlock(true);
	}
}
