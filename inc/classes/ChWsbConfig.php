<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_INC . 'utils.inc.php');

ch_import('ChWsbPermalinks');

/**
 * Base class for Config classes in modules engine.
 *
 * The object of the class contains different basic configuration settings which are necessary for all modules.
 *
 *
 * Example of usage:
 * @see any module included in the default Cheetah's package.
 *
 *
 * Static Methods:
 *
 * Get an instance of a module's class.
 * @see ChWsbModule::getInstance($sClassName)
 *
 *
 * Memberships/ACL:
 * Doesn't depend on user's membership.
 *
 *
 * Alerts:
 * no alerts available
 *
 */

class ChWsbConfig
{
    var $_iId;

    var $_sVendor;

    var $_sClassPrefix;

    var $_sDbPrefix;

    var $_sDirectory;

    var $_sUri;

    var $_sHomePath;

    var $_sClassPath;

    var $_sHomeUrl;

    var $_sBaseUri;

    /**
     * constructor
     */
    function __construct($aModule)
    {
        $this->_iId = empty($aModule['id']) ? 0 : (int)$aModule['id'];
        $this->_sVendor = $aModule['vendor'];
        $this->_sClassPrefix = $aModule['class_prefix'];
        $this->_sDbPrefix = $aModule['db_prefix'];

        $this->_sDirectory = $aModule['path'];
        $this->_sHomePath = CH_DIRECTORY_PATH_MODULES . $this->_sDirectory;
        $this->_sClassPath = $this->_sHomePath . 'classes/';

        $this->_sUri = $aModule['uri'];
        $this->_sHomeUrl = CH_WSB_URL_MODULES . $this->_sDirectory;

        $oPermalinks = new ChWsbPermalinks();
        $this->_sBaseUri = $oPermalinks->permalink('modules/?r=' . $this->_sUri . '/');
    }
    function getId()
    {
        return $this->_iId;
    }
    function getClassPrefix()
    {
        return $this->_sClassPrefix;
    }
    function getDbPrefix()
    {
        return $this->_sDbPrefix;
    }
    function getHomePath()
    {
        return $this->_sHomePath;
    }
    function getClassPath()
    {
        return $this->_sClassPath;
    }
    /**
     * Get unique URI.
     *
     * @return string with unique URI.
     */
    function getUri()
    {
        return $this->_sUri;
    }
    /**
     * Get base URI which depends on the Permalinks mechanism.
     *
     * example /modules/?r=module_uri or /m/module_uri
     * @return string with base URI.
     */
    function getBaseUri()
    {
        return $this->_sBaseUri;

    }
    /**
     * Get full URL.
     *
     * @return string with full URL.
     */
    function getHomeUrl()
    {
        return $this->_sHomeUrl;
    }
}
