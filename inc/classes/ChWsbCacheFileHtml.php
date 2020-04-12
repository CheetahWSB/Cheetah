<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbCacheFile.php');

class ChWsbCacheFileHtml extends ChWsbCacheFile
{
    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all data from the cache file.
     *
     * @param  string $sKey - file name
     * @param  int    $iTTL - time to live
     * @return the    data is got from cache.
     */
    function getData($sKey, $iTTL = false)
    {
        if(!file_exists($this->sPath . $sKey))
            return null;

        if ($iTTL > 0 && $this->_removeFileIfTtlExpired ($this->sPath . $sKey, $iTTL))
            return null;

        return file_get_contents($this->sPath . $sKey);
    }

    /**
     * Get full path to cache file
     */
    function getDataFilePath($sKey, $iTTL = false)
    {
        if (!file_exists($this->sPath . $sKey))
            return null;

        if ($iTTL > 0 && $this->_removeFileIfTtlExpired ($this->sPath . $sKey, $iTTL))
            return null;

        return $this->sPath . $sKey;
    }

    /**
     * Save all data in cache file.
     *
     * @param  string  $sKey      - file name
     * @param  mixed   $mixedData - the data to be cached in the file
     * @param  int     $iTTL      - time to live
     * @return boolean result of operation.
     */
    function setData($sKey, $mixedData, $iTTL = false)
    {
        if(file_exists($this->sPath . $sKey) && !is_writable($this->sPath . $sKey))
           return false;

        if(!($rHandler = fopen($this->sPath . $sKey, 'w')))
           return false;

        fwrite($rHandler, $mixedData);
        fclose($rHandler);
        @chmod($this->sPath . $sKey, 0666);

        return true;
    }
}
