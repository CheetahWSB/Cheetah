<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbCache');

class ChWsbCacheAPC extends ChWsbCache
{
    var $iTTL = 3600;

    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get data from shared memory cache
     *
     * @param  string $sKey - file name
     * @param  int    $iTTL - time to live
     * @return the    data is got from cache.
     */
    function getData($sKey, $iTTL = false)
    {
        $isSucess = false;
        $mixedData = apc_fetch ($sKey, $isSucess);
        if (!$isSucess)
            return null;

        return $mixedData;
    }
    /**
     * Save data in shared memory cache
     *
     * @param  string  $sKey      - file name
     * @param  mixed   $mixedData - the data to be cached in the file
     * @param  int     $iTTL      - time to live
     * @return boolean result of operation.
     */
    function setData($sKey, $mixedData, $iTTL = false)
    {
        return apc_store ($sKey, $mixedData, false === $iTTL ? $this->iTTL : $iTTL);
    }

    /**
     * Delete cache from shared memory
     *
     * @param  string $sKey - file name
     * @return result of the operation
     */
    function delData($sKey)
    {
        $isSucess = false;
        apc_fetch ($sKey, $isSucess);
        if (!$isSucess)
            return true;

        return apc_delete($sKey);
    }

    /**
     * Check if apc cache functions are available
     * @return boolean
     */
    function isAvailable()
    {
        return function_exists('apc_store');
    }

    /**
     * Check if apc extension is loaded
     * @return boolean
     */
    function isInstalled()
    {
        return extension_loaded('apc');
    }

    /**
     * remove all data from cache by key prefix
     * @return true on success
     */
    function removeAllByPrefix ($s)
    {
        $l = strlen($s);
        $aKeys = apc_cache_info('user');
        if (isset($aKeys['cache_list']) && is_array($aKeys['cache_list'])) {
            foreach ($aKeys['cache_list'] as $r) {
                $sKey = $r['info'];
                if (0 === strncmp($sKey, $s, $l))
                    $this->delData($sKey);
            }
        }
        return true;
    }

    /**
     * get size of cached data by name prefix
     */
    function getSizeByPrefix ($s)
    {
        $iSize = 0;
        $l = strlen($s);
        $aKeys = apc_cache_info('user');
        if (isset($aKeys['cache_list']) && is_array($aKeys['cache_list'])) {
            foreach ($aKeys['cache_list'] as $r) {
                $sKey = $r['info'];
                if (0 === strncmp($sKey, $s, $l))
                    $iSize += $r['mem_size'];
            }
        }
        return $iSize;
    }
}
