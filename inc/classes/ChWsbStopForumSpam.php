<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

/**
 * Spam detection based on username, email and ip powered by  Stop Forum Spam - http://www.stopforumspam.com/
 */
class ChWsbStopForumSpam
{
    var $_aKeys = array ('ip' => 1, 'email' => 1, 'username' => 1);

    /**
     * Check if user is spammer
     * @param $aValues - array with keys: ip, email, username
     * @param $sDesc - desctiption, for example: join
     * @return true - on positive detection, false - on error or no spammer detection
     */
    public function isSpammer ($aValues, $sDesc)
    {
        if (!getParam('sys_stopforumspam_enable'))
            return false;

        if (!$aValues || !is_array($aValues))
            return false;

        $aRequestParams = array ('f' => 'json');
        foreach ($this->_aKeys as $k => $b)
            if (isset($aValues[$k]))
                $aRequestParams[$k] = rawurlencode($aValues[$k]);

        $s = ch_file_get_contents('http://www.stopforumspam.com/api', $aRequestParams);
        if (!$s)
            return false;

        $aResult = json_decode($s, true);
        if (null === $aResult || !$aResult['success'])
            return false;

        foreach ($this->_aKeys as $k => $b) {
            if (isset($aResult[$k]) && $aResult[$k]['appears']) {
                $this->onPositiveDetection($sDesc);
                return true;
            }
        }

        return false;
    }

    /**
     * Submit spammer
     * @param @aValues - array with keys: ip, email, username
     * @return false - on error, or true - on success
     */
    public function submitSpammer ($aValues, $sEvidences = false)
    {
        if (!getParam('sys_stopforumspam_enable'))
            return false;

        $sKey = getParam('sys_stopforumspam_api_key');
        if (!$sKey)
            return false;

        $sData = 'api_key=' . $sKey . '&evidence=' . ($sEvidences ? rawurlencode($sEvidences) : 'spammer');
        foreach ($this->_aKeys as $k => $b)
            if (isset($aValues[$k]))
                $sData .= '&' . ('ip' == $k ? 'ip_addr' : $k) . '=' . rawurlencode($aValues[$k]);

        $fp = fsockopen("www.stopforumspam.com", 80);
        fputs($fp, "POST /add.php HTTP/1.1\n" );
        fputs($fp, "Host: www.stopforumspam.com\n" );
        fputs($fp, "Content-type: application/x-www-form-urlencoded\n" );
        fputs($fp, "Content-length: " . strlen($sData) . "\n" );
        fputs($fp, "Connection: close\n\n" );
        fputs($fp, $sData);
        fclose($fp);

        return true;
    }

    public function onPositiveDetection ($sExtraData = '')
    {
        $o = ch_instance('ChWsbDNSBlacklists');
        $o->onPositiveDetection (getVisitorIP(false), $sExtraData, 'stopforumspam');
    }
}
