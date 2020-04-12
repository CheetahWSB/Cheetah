<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

/**
 * Spam detection based on the message content and logged in user
 */
class ChWsbAkismet
{
    var $oAkismet = null;

    /**
     * Constructor
     */
    public function __construct($iProfileID = 0)
    {
        $sKey = getParam('sys_akismet_api_key');
        if ($sKey) {
            require_once (CH_DIRECTORY_PATH_PLUGINS . 'akismet/Akismet.class.php');
            $this->oAkismet = new Akismet(CH_WSB_URL_ROOT, $sKey);
            $aProfile = getProfileInfo($iProfileID);
            if ($aProfile) {
                $this->oAkismet->setCommentAuthor($aProfile['NickName']);
                $this->oAkismet->setCommentAuthorEmail($aProfile['Email']);
                $this->oAkismet->setCommentAuthorURL(getProfileLink($aProfile['ID']));
            }
        }
    }

    public function isSpam ($s, $sPermalink = false)
    {
        if (!$this->oAkismet)
            return false;

        $this->oAkismet->setCommentContent($s);
        if ($sPermalink)
            $this->oAkismet->setPermalink($sPermalink);

        return $this->oAkismet->isCommentSpam();
    }

    public function onPositiveDetection ($sExtraData = '')
    {
        $o = ch_instance('ChWsbDNSBlacklists');
        $o->onPositiveDetection (getVisitorIP(), $sExtraData, 'akismet');
    }
}
