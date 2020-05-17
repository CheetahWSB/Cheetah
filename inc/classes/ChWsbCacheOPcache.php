<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbCache');

class ChWsbCacheOPcache extends ChWsbCache
{
    //var $iTTL = 3600;
    //var $iStoreFlag = 0;
    //var $oOPcache = null;

    var $bOpcacheGetConfigurationAva = false;
    var $bOpcacheGetStatusAva = false;
    var $bOpcacheGetConfigurationDisabled = true;
    var $bOpcacheGetStatusDisabled = true;

    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();


        //if (class_exists('OPcache')) {
        //    $this->oOPcache = new OPcache();
            //if (!$this->oOPcache->connect (getParam('sys_cache_OPcache_host'), getParam('sys_cache_OPcache_port')))
            //    $this->oOPcache = null;
        //}

        $this -> bOpcacheGetConfigurationAva = function_exists('opcache_get_configuration');
        $this -> bOpcacheGetStatusAva = function_exists('opcache_get_status');

        $s = ini_get('disable_functions');

        if(strpos($s, 'opcache_get_status') === false) {
            $this -> bOpcacheGetStatusDisabled = false;
        } else {
            $this -> bOpcacheGetStatusDisabled = true;
        }

        if(strpos($s, 'opcache_get_configuration') === false) {
            $this -> bOpcacheGetConfigurationDisabled = false;
        } else {
            $this -> bOpcacheGetConfigurationDisabled = true;
        }

        //echo $s;
        //exit;

    }

    /**
     * Get data from cache server
     *
     * @param  string $sKey - file name
     * @param  int    $iTTL - time to live
     * @return the    data is got from cache.
     */
    function getData($sKey, $iTTL = false)
    {
        //$mixedData = $this->oOPcache->get($sKey);
        //return false === $mixedData ? null : $mixedData;
    }

    /**
     * Save data in cache server
     *
     * @param  string  $sKey      - file name
     * @param  mixed   $mixedData - the data to be cached in the file
     * @param  int     $iTTL      - time to live
     * @return boolean result of operation.
     */
    function setData($sKey, $mixedData, $iTTL = false)
    {
        //return $this->oOPcache->set($sKey, $mixedData, $this->iStoreFlag, false === $iTTL ? $this->iTTL : $iTTL);
    }

    /**
     * Delete cache from cache server
     *
     * @param  string $sKey - file name
     * @return result of the operation
     */
    function delData($sKey)
    {
        //$this->oOPcache->delete($sKey);
        //return true;
    }

    /**
     * Check if OPcache is available
     * @return boolean
     */
    function isAvailable()
    {

        //$r = @opcache_get_status();
        //echo '<pre>' . print_r($r, true) . '</pre>';
        //exit;

        return $this -> bOpcacheGetConfigurationAva;
    }

    /**
     * Check if OPcache extension is loaded
     * @return boolean
     */
    function isInstalled()
    {
        if($this -> bOpcacheGetConfigurationAva) {
            $r = @opcache_get_configuration();
            return $r['directives']['opcache.enable'];
        }
    }

    /**
     * remove all data from cache by key prefix
     * @return true on success
     */
    function removeAllByPrefix ($s)
    {
        // not implemented for current cache
        return false;
    }

    /**
     * get size of cached data by name prefix
     */
    function getSizeByPrefix ($s)
    {
        // not implemented for current cache
        return false;
    }
}
