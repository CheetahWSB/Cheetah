<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbDb');

/**
 * Database queries for captcha objects.
 * @see ChWsbCaptcha
 */
class ChWsbCaptchaQuery extends ChWsbDb
{
    protected $_aObject;

    public function __construct($aObject)
    {
        parent::__construct();
        $this->_aObject = $aObject;
    }

    static public function getCaptchaObject ($sObject)
    {
        $oDb = $GLOBALS['MySQL'];
        $sQuery = "SELECT * FROM `sys_objects_captcha` WHERE `object` = ?";
        $aObject = $oDb->getRow($sQuery, [$sObject]);
        if (!$aObject || !is_array($aObject))
            return false;

        return $aObject;
    }

}
