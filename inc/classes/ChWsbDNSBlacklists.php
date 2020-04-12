<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

define('CH_WSB_DNSBL_NEGATIVE', 0);   // negative
define('CH_WSB_DNSBL_POSITIVE', 1);   // positive match
define('CH_WSB_DNSBL_FAILURE', 2);    // generic failure, not enabled or configured

// Types of queries for dnsbl_lookup_ip() and dnsbl_lookup_domain()
define('CH_WSB_DNSBL_ANYPOSTV_RETFIRST', 0);   // Any positive from chain, stop and return first
define('CH_WSB_DNSBL_ANYPOSTV_RETEVERY', 1);   // Any positive, check all and return every positive
define('CH_WSB_DNSBL_ALLPOSTV_RETEVERY', 2);   // All must check positive, return every positive

define('CH_WSB_DNSBL_MATCH_ANY', "any");

define('CH_WSB_DNSBL_CHAIN_SPAMMERS', "spammers");
define('CH_WSB_DNSBL_CHAIN_WHITELIST', "whitelist");
define('CH_WSB_DNSBL_CHAIN_URIDNS', "uridns");

/**
 *  Spam detection based on spammer IP
 *
 *
 * Example of usage:
 *
 *  if (DNSBL_POSITIVE == $o->dnsbl_lookup_ip(DNSBL_CHAIN_SPAMMERS, $sCurIP) && DNSBL_POSITIVE != $o->dnsbl_lookup_ip(DNSBL_CHAIN_WHITELIST, $sCurIP))
 *  {
 *    // positive detection - block this ip
 *  }
 *  // continue script execution
 *
 *
 *  There is more handy function available:
 *  @see ch_is_ip_dns_blacklisted
 */
class ChWsbDNSBlacklists
{
    private $aChains = array ();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initChains();
    }

    public function dnsbl_lookup_ip($mixedChain, $sIp, $querymode = CH_WSB_DNSBL_ANYPOSTV_RETFIRST)
    {
        $lookupkey = $this->ipreverse($sIp);
        if (false === $lookupkey)
            return CH_WSB_DNSBL_FAILURE;	// unable to prepare lookup string from address

        if (is_array($mixedChain))
            $aChain = $mixedChain;
        else
            $aChain = &$this->aChains[$mixedChain];
        return $this->dnsbl_lookup($aChain, $lookupkey, $querymode);
    }

    public function dnsbl_lookup_uri($sUri, $mixedChain = CH_WSB_DNSBL_CHAIN_URIDNS, $querymode = CH_WSB_DNSBL_ANYPOSTV_RETFIRST)
    {
        if (!$sUri)
            return CH_WSB_DNSBL_FAILURE;

        if (is_array($mixedChain))
            $aChain = $mixedChain;
        else
            $aChain = &$this->aChains[$mixedChain];
        return $this->dnsbl_lookup($aChain, $sUri, $querymode);
    }

    public function onPositiveDetection ($sIP, $sExtraData = '', $sType = 'dnsbl')
    {
        $iIP = sprintf("%u", ip2long($sIP));
        $iMemberId = getLoggedId();
        $sExtraData = process_db_input($sExtraData);
        return $GLOBALS['MySQL']->query("INSERT INTO `sys_antispam_block_log` SET `ip` = '$iIP', `member_id` = '$iMemberId', `type` = '$sType', `extra` = '$sExtraData', `added` = " . time());
    }

    public function clearCache ()
    {
        $GLOBALS['MySQL']->cleanCache('sys_dnsbl_'.CH_WSB_DNSBL_CHAIN_SPAMMERS);
        $GLOBALS['MySQL']->cleanCache('sys_dnsbl_'.CH_WSB_DNSBL_CHAIN_WHITELIST);
    }

    /*************** private function ***************/

    private function dnsbl_lookup(&$zones, $key, $querymode)
    {
        $numpositive = 0;
        $numservers = count ($zones);
        $servers = $zones;

        if (!$servers)
            return CH_WSB_DNSBL_FAILURE; // no servers defined

        if (($querymode!=CH_WSB_DNSBL_ANYPOSTV_RETFIRST) && ($querymode!=CH_WSB_DNSBL_ANYPOSTV_RETEVERY)
             && ($querymode!=CH_WSB_DNSBL_ALLPOSTV_RETEVERY))
             return CH_WSB_DNSBL_FAILURE;	// invalid querymode

        foreach ($servers as $r) {
            $resultaddr = gethostbyname ($key . "." . $r['zonedomain']);

            if ($resultaddr && $resultaddr != $key . "." . $r['zonedomain']) {
                // we got some result from the DNS query, not NXDOMAIN. should we consider 'positive'?
                $postvresp = $r['postvresp'];	// check positive match criteria
                if (
                    CH_WSB_DNSBL_MATCH_ANY == $postvresp ||
                    (preg_match("/^\d+\.\d+\.\d+\.\d+$/", $postvresp) && $resultaddr == $postvresp) ||
                    (is_numeric($postvresp) && (ip2long($resultaddr) & $postvresp))
                ) {
                    $numpositive++;
                    if ($querymode == CH_WSB_DNSBL_ANYPOSTV_RETFIRST)
                        return CH_WSB_DNSBL_POSITIVE;	// found one positive, returning single
                }
            }
        }
        // all servers were queried
        if ($numpositive == $numservers)
            return CH_WSB_DNSBL_POSITIVE;
        else if (($querymode == CH_WSB_DNSBL_ANYPOSTV_RETEVERY) && ($numpositive > 0))
            return CH_WSB_DNSBL_POSITIVE;
        else
            return CH_WSB_DNSBL_NEGATIVE;
    }

    private function ipreverse ($sIp)
    {
        if (!preg_match ('/(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})/', $sIp, $m))
            return false;

        return "{$m[4]}.{$m[3]}.{$m[2]}.{$m[1]}";
    }

    private function initChains()
    {
        if (!isset($GLOBALS['ch_dol_dnsbl_'.CH_WSB_DNSBL_CHAIN_SPAMMERS]))
            $GLOBALS['ch_dol_dnsbl_'.CH_WSB_DNSBL_CHAIN_SPAMMERS] = $GLOBALS['MySQL']->fromCache('sys_dnsbl_'.CH_WSB_DNSBL_CHAIN_SPAMMERS, 'getAll', "SELECT `zonedomain`, `postvresp` FROM `sys_dnsbl_rules` WHERE `chain` = '".CH_WSB_DNSBL_CHAIN_SPAMMERS."' AND `active` = 1");

        if (!isset($GLOBALS['ch_dol_dnsbl_'.CH_WSB_DNSBL_CHAIN_WHITELIST]))
            $GLOBALS['ch_dol_dnsbl_'.CH_WSB_DNSBL_CHAIN_WHITELIST] = $GLOBALS['MySQL']->fromCache('sys_dnsbl_'.CH_WSB_DNSBL_CHAIN_WHITELIST, 'getAll', "SELECT `zonedomain`, `postvresp` FROM `sys_dnsbl_rules` WHERE `chain` = '".CH_WSB_DNSBL_CHAIN_WHITELIST."' AND `active` = 1");

        if (!isset($GLOBALS['ch_dol_dnsbl_'.CH_WSB_DNSBL_CHAIN_URIDNS]))
            $GLOBALS['ch_dol_dnsbl_'.CH_WSB_DNSBL_CHAIN_URIDNS] = $GLOBALS['MySQL']->fromCache('sys_dnsbl_'.CH_WSB_DNSBL_CHAIN_URIDNS, 'getAll', "SELECT `zonedomain`, `postvresp` FROM `sys_dnsbl_rules` WHERE `chain` = '".CH_WSB_DNSBL_CHAIN_URIDNS."' AND `active` = 1");

        $this->aChains[CH_WSB_DNSBL_CHAIN_SPAMMERS] = &$GLOBALS['ch_dol_dnsbl_'.CH_WSB_DNSBL_CHAIN_SPAMMERS];
        $this->aChains[CH_WSB_DNSBL_CHAIN_WHITELIST] = &$GLOBALS['ch_dol_dnsbl_'.CH_WSB_DNSBL_CHAIN_WHITELIST];
        $this->aChains[CH_WSB_DNSBL_CHAIN_URIDNS] = &$GLOBALS['ch_dol_dnsbl_'.CH_WSB_DNSBL_CHAIN_URIDNS];

    }

}
