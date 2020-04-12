<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbModuleDb.php' );

define('CH_PROFILE_CUSTOM_TABLE_PREFIX', 'ch_profile_custom');

class ChProfileCustomizeDb extends ChWsbModuleDb
{
    var $_oConfig;
    /*
     * Constructor.
     */
    function __construct(&$oConfig)
    {
        parent::__construct();

        $this->_oConfig = $oConfig;
    }

    function getProfileByUserId($iUserId)
    {
        return $this->getRow("SELECT * FROM `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_main` WHERE `user_id` = ? LIMIT 1", [$iUserId]);
    }

    function getProfileTmpByUserId($iUserId)
    {
        $aStyle = $this->getProfileByUserId($iUserId);

        if (!empty($aStyle))
            return unserialize($aStyle['tmp']);

        return array();
    }

    function getProfileCssByUserId($iUserId)
    {
        $aStyle = $this->getProfileByUserId($iUserId);

        if (!empty($aStyle))
            return unserialize($aStyle['css']);

        return '';
    }

    function updateProfileByUserId($iUserId, $sStyle, $sType)
    {
        // check exist user
        $aRow = $this->getProfileByUserId($iUserId);
        if (empty($aRow))
            return $this->query("INSERT INTO `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_main` (`user_id`, `$sType`) VALUES($iUserId, '$sStyle')");
        else
            return $this->query("UPDATE `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_main` SET `$sType` = '$sStyle' WHERE `user_id` = $iUserId LIMIT 1");
    }

    function saveProfileByUserId($iUserId)
    {
        return $this->query("UPDATE `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_main` SET `css` = `tmp` WHERE `user_id` = $iUserId LIMIT 1");
    }

    function updateProfileTmpByUserId($iUserId, $aTmp)
    {
        return $this->updateProfileByUserId($iUserId, serialize($aTmp), 'tmp');
    }

    function updateProfileCssByUserId($iUserId, $aCss)
    {
        return $this->updateProfileByUserId($iUserId, serialize($aCss), 'css');
    }

    function resetProfileStyleByUserId($iUserId)
    {
        return $this->query("DELETE FROM `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_main` WHERE `user_id` = $iUserId");
    }

    function getUnits()
    {
        $aResult = array();
        $aRows = $this->getAll("SELECT `name`, `caption`, `css_name`, `type` FROM `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_units`");

        foreach ($aRows as $aValue) {
            $aResult[$aValue['type']][$aValue['name']] = array(
                'name' => $aValue['caption'],
                'css_name' => $aValue['css_name']
            );
        }

        return $aResult;
    }

    function getUnitById($iUnitId)
    {
        return $this->getRow("SELECT * FROM `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_units` WHERE `id` = ? LIMIT 1", [$iUnitId]);
    }

    function deleteUnit($iUnitId)
    {
        return $this->query("DELETE FROM `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_units` WHERE `id` = $iUnitId");
    }

    function getAllThemesByUserId($iUserId)
    {
        return $this->getAll("SELECT * FROM `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_themes` WHERE `ownerid` = $iUserId ORDER BY `id`");
    }

    function getSharedThemes()
    {
        return $this->getAll("SELECT * FROM `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_themes` WHERE `ownerid` = 0 ORDER BY `id`");
    }

    function getThemeByName($sName)
    {
        return $this->getRow("SELECT * FROM `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_themes` WHERE `name` = ? LIMIT 1", [$sName]);
    }

    function getThemeById($iThemeId)
    {
        return $this->getRow("SELECT * FROM `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_themes` WHERE `id` = ? LIMIT 1", [$iThemeId]);
    }

    function getThemeStyle($iThemeId)
    {
        if ((int)$iThemeId) {
            $aTheme = $this->getRow("SELECT * FROM `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_themes` WHERE `id` = ? LIMIT 1", [$iThemeId]);

            if (!empty($aTheme))
                return unserialize($aTheme['css']);
        }

        return array();
    }

    function addTheme($sName, $iOwnerId, $sCss)
    {
        if ($this->query("INSERT INTO `" . CH_PROFILE_CUSTOM_TABLE_PREFIX .
                "_themes` (`name`, `ownerid`, `css`) VALUES('$sName', $iOwnerId, '$sCss')"))
            return $this->lastId();

        return -1;
    }

    function deleteTheme($iThemeId)
    {
        return $this->query("DELETE FROM `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_themes` WHERE `id` = $iThemeId");
    }

    function addImage($sExt)
    {
        if (strlen($sExt) > 0 && $this->query("INSERT INTO `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_images` (`ext`, `count`) VALUES('$sExt', 1)"))
            return $this->lastId() . '.' . $sExt;

        return '';
    }

    function copyImage($sFileName)
    {
        if (strlen($sFileName) > 0) {
            $sId = basename($sFileName, '.' . pathinfo($sFileName, PATHINFO_EXTENSION));
            return strlen($sId) > 0 ? $this->query("UPDATE `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_images` SET `count` = `count` +  1 WHERE `id` = $sId") : 0;
        }

        return 0;
    }

    function deleteImage($sFileName)
    {
        $sResult = true;

        if (strlen($sFileName) > 0) {
            $sId = basename($sFileName, '.' . pathinfo($sFileName, PATHINFO_EXTENSION));
            if (strlen($sId) > 0 && $this->query("UPDATE `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_images` SET `count` = `count` -  1 WHERE `id` = $sId")) {
                $aRow = $this->getRow("SELECT * FROM `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_images` WHERE `id` = ? LIMIT 1", [$sId]);
                if ($aRow['count'] < 1)
                    $this->query("DELETE FROM `" . CH_PROFILE_CUSTOM_TABLE_PREFIX . "_images` WHERE `id` = $sId");
                else
                    $sResult = false;
            }
        }

        return $sResult;
    }

    function getSettingsCategory()
    {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Profile Customizer' LIMIT 1");
    }
}
