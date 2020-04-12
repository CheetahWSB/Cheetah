<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

class ChPfwLog
{
	protected $sLogDateFormat;
	protected $sPath;

	protected $bLogInfo;
	protected $sFileInfo;

	protected $bLogError;
	protected $sFileError;

	protected function __construct()
	{
		$oMain = ChWsbModule::getInstance('ChPfwModule');

		$this->sLogDateFormat = 'm.d.y H:i:s';
		$this->sPath = $oMain->_oConfig->getLogPath();

		$this->bLogInfo = $oMain->_oConfig->isLog('info');
		$this->sFileInfo = $this->bLogInfo ? $oMain->_oConfig->getLogFile('info') : '';

		$this->bLogError = $oMain->_oConfig->isLog('error');
		$this->sFileError = $this->bLogError ? $oMain->_oConfig->getLogFile('error') : '';
	}

	public static function getInstance()
    {
        if(!isset($GLOBALS['chWsbClasses']['ChPfwLog']))
            $GLOBALS['chWsbClasses']['ChPfwLog'] = new ChPfwLog();

        return $GLOBALS['chWsbClasses']['ChPfwLog'];
    }

	public function logInfo() {
		if(!$this->bLogInfo)
			return;

		$sFile = $this->sFileInfo;
		$sMessage = "--- Info: {date}";

		$this->_log($sFile, $sMessage);

		$aArgs = func_get_args();
		foreach($aArgs as $mixedArg)
			$this->_log($sFile, $mixedArg);

		$this->_log($sFile, $sMessage . "\n");
	}

	public function logError() {
		if(!$this->bLogError)
			return;

		$sFile = $this->sFileError;
		$sMessage = "--- Error Occured: {date}";

		$this->_log($sFile, $sMessage);

		$aArgs = func_get_args();
		foreach($aArgs as $mixedArg)
			$this->_log($sFile, $mixedArg);

		$this->_log($sFile, $sMessage . "\n");
	}

	protected function _log($sFile, $mixedValue)
	{
	    $rHandle = fopen($this->sPath . $sFile, 'a');
	    if(!$rHandle)
	    	return;

        if(is_array($mixedValue) || is_object($mixedValue)) {
            ob_start();
            print_r($mixedValue);
            $sValue = ob_get_contents();
            ob_end_clean();
            fwrite($rHandle, "$sValue\n");
        }
        else {
        	$mixedValue = str_replace('{date}', date($this->sLogDateFormat), $mixedValue);

            fwrite($rHandle, "$mixedValue\n");
        }

		fclose($rHandle);
	}
}
