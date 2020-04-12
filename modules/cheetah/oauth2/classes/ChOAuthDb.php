<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModuleDb');

class ChOAuthDb extends ChWsbModuleDb
{
    var $_oConfig;

    function __construct(&$oConfig)
    {
        parent::__construct();
        $this->_oConfig = $oConfig;
    }

    function getClients()
    {
        return $this->getAll("SELECT * FROM `ch_oauth_clients` ORDER BY `title`");
    }

    function getClientTitle($sClientId)
    {
        return $this->getOne("SELECT `title` FROM `ch_oauth_clients` WHERE `client_id` = ? LIMIT 1", [$sClientId]);
    }

    function getSavedProfile($iProfileId)
    {
        return $this->getOne("SELECT `user_id` FROM `ch_oauth_refresh_tokens` WHERE `user_id` = ? LIMIT 1", [$iProfileId]);
    }

    function getSettingsCategory()
    {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'OAuth2 Server' LIMIT 1");
    }

    function deleteClients($aClients)
    {
        foreach ($aClients as $sClientId)
            $this->query("DELETE FROM `ch_oauth_clients` WHERE `client_id` = '" . process_db_input($sClientId) . "'");
    }
}
