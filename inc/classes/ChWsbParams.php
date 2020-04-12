<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbCacheFile.php');

class ChWsbParams
{
    /**
     * @var ChWsbDb
     */
    public $_oDb;
    public $_oCache;
    public $_sCacheFile;
    public $_aParams;

    /**
     * constructor
     */
    function __construct($oDb)
    {
        global $site;

        $this->_oDb = $oDb;
        $this->_sCacheFile = 'sys_options_' . md5($site['ver'] . $site['build'] . $site['url']) . '.php';

        // feel free to change to another cache system if you are sure that it is available
        $this->_oCache = new ChWsbCacheFile();
        $this->_aParams = $this->_oCache->getData($this->_sCacheFile);

        if (empty($this->_aParams) && $this->_oDb != null)
            $this->cache();
    }

    function isInCache($sKey)
    {
        return isset($this->_aParams[$sKey]);
    }

    function get($sKey, $bFromCache = true)
    {
        if (!$sKey)
            return false;
        if ($bFromCache && $this->isInCache($sKey))
           return $this->_aParams[$sKey];
        else
           return $this->_oDb->getOne("SELECT `VALUE` FROM `sys_options` WHERE `Name`= ? LIMIT 1", [$sKey]);
    }

    function set($sKey, $mixedValue)
    {
        //--- Update Database ---//
        $this->_oDb->query("UPDATE `sys_options` SET `VALUE`= ? WHERE `Name`= ? LIMIT 1", [$mixedValue, $sKey]);

        //--- Update Cache ---//
        $this->cache();

        // set param alert
        $oAlert = new ChWsbAlerts('system', 'set_param', 0, 0, array('name' => $sKey, 'value' => $mixedValue));
        $oAlert->alert();
    }

    function cache()
    {
        $this->_aParams = $this->_oDb->getPairs("SELECT `Name`, `VALUE` FROM `sys_options`", "Name", "VALUE");
        if (empty($this->_aParams)) {
            $this->_aParams = array ();
            return false;
        }

        return $this->_oCache->setData($this->_sCacheFile, $this->_aParams);
    }

    function clearCache()
    {
        $this->_oCache->delData($this->_sCacheFile);
    }
}
