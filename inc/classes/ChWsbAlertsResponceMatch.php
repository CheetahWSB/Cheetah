<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( CH_DIRECTORY_PATH_INC . "match.inc.php");
ch_import('ChWsbAlerts');
ch_import('ChWsbDb');
ch_import('ChWsbEmailTemplates');

class ChWsbAlertsResponceMatch extends ChWsbAlertsResponse
{
    function response($oAlert)
    {
        $iRecipientId = $oAlert->iObject;

        if ($oAlert->sUnit == 'profile') {
            switch ($oAlert->sAction) {
                case 'join':
                case 'edit':
                    $this->_checkProfileMatch($iRecipientId, $oAlert->sAction);
                    break;

                case 'change_status':
                    $this->_profileChangeStatus();
                    break;

                case 'delete':
                    $this->_profileDelete($iRecipientId);
                    break;
            }
        }
    }

    function _checkProfileMatch($iProfileId, $sAction)
    {
        if (!getParam('enable_match'))
            return;

        $aProfile = getProfileInfo($iProfileId);

        if ($aProfile['Status'] == 'Active' && ($aProfile['UpdateMatch'] || $sAction == 'join')) {
            $oDb = ChWsbDb::getInstance();

            // clear field "UpdateMatch"
            $oDb->query("UPDATE `Profiles` SET `UpdateMatch` = 0 WHERE `ID`= $iProfileId");

            // clear cache
            $oDb->query("DELETE FROM `sys_profiles_match`");

            // get send mails
            $aSendMails = $oDb->getRow("SELECT `profiles_match` FROM `sys_profiles_match_mails` WHERE `profile_id` = ?", [$iProfileId]);
            $aSend = !empty($aSendMails) ? unserialize($aSendMails['profiles_match']) : array();

            $aProfiles = getMatchProfiles($iProfileId);
            foreach ($aProfiles as $iProfId) {
                if (isset($aSend[(int)$iProfId]))
                    continue;

                $aProfile = getProfileInfo($iProfId);
                if (1 != $aProfile['EmailNotify'] || 'Unconfirmed' == $aProfile['Status'])
                    continue;

                $oEmailTemplate = new ChWsbEmailTemplates();
                $aMessage = $oEmailTemplate->parseTemplate('t_CupidMail', array(
                    'StrID' => $iProfId,
                    'MatchProfileLink' => getProfileLink($iProfileId)
                ), $iProfId);

                if (!empty($aProfile) && $aProfile['Status'] == 'Active')
                    $oDb->query("INSERT INTO `sys_sbs_queue`(`email`, `subject`, `body`) VALUES('" . $aProfile['Email'] . "', '" . process_db_input($aMessage['subject'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION) . "', '" . process_db_input($aMessage['body'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION) . "')");

                $aSend[(int)$iProfId] = 0;
            }

            if (empty($aSendMails))
                $oDb->query("INSERT INTO `sys_profiles_match_mails`(`profile_id`, `profiles_match`) VALUES($iProfileId, '" . serialize($aSend) . "')");
            else
                $oDb->query("UPDATE `sys_profiles_match_mails` SET `profiles_match` = '" . serialize($aSend) . "' WHERE `profile_id` = $iProfileId");
        }
    }

    function _profileDelete($iProfileId)
    {
        $oDb = ChWsbDb::getInstance();

        $oDb->query("DELETE FROM `sys_profiles_match`");
        $oDb->query("DELETE FROM `sys_profiles_match_mails` WHERE `profile_id` = $iProfileId");
    }

    function _profileChangeStatus()
    {
        $oDb = ChWsbDb::getInstance();
        $oDb->query("DELETE FROM `sys_profiles_match`");
    }
}
