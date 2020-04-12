<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

/**
 * Spam detection based on URIs in the message
 */
class ChWsbDNSURIBlacklists
{
    var $aZonesUrls = array (
        2 => "http://www.surbl.org/tld/two-level-tlds",
        3 => "http://www.surbl.org/tld/three-level-tlds",
    );

    var $oDb;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aZonesUrls = array ( // for some reason original urls are restricted to fetch, so copies are created locally
            2 => CH_DIRECTORY_PATH_ROOT . "two-level-tlds",
            3 => CH_DIRECTORY_PATH_ROOT . "three-level-tlds",
        );

        $this->oDb = &$GLOBALS['MySQL'];
        $this->initZones();
    }

    public function isSpam ($s)
    {
        $aURIs = $this->parseUrls ($s);
        if (!$aURIs)
            return false;

        $aURIs = $this->validateUrls ($aURIs);
        if (!$aURIs)
            return false;

        $o = ch_instance('ChWsbDNSBlacklists');
        foreach ($aURIs as $sURI) {
            if (CH_WSB_DNSBL_POSITIVE == $o->dnsbl_lookup_uri ($sURI))
                return true;
        }

        return false;
    }

    public function parseUrls (&$s)
    {
        $aMatches = array ();
        if (!preg_match_all("!(https?|ftp|gopher|telnet|file|notes|ms-help):[/\\\\]+([\w\d\.-]*)!", $s, $aMatches))
            return false;

        if (!$aMatches || !isset($aMatches[2]) || !$aMatches[2])
            return false;

        $aUrlsUniq = array ();
        foreach ($aMatches[2] as $sUrl) {
            if (isset($aUrlsUniq[$sUrl]))
                continue;
            $aUrlsUniq[$sUrl] = $sUrl;
        }

        return $aUrlsUniq;
    }

    public function validateUrls ($aUrlsUniq)
    {
        $aUrls = array ();
        foreach ($aUrlsUniq as $sUrl) {

            if (0 === strncasecmp('www.', $sUrl, 4))
                $sUrl = substr($sUrl, 4);

            $aZones = explode ('.', $sUrl);
            $iLevels = count($aZones);

            if ($iLevels <= 2) {

                $aUrls[] = $sUrl;

            } elseif (3 == $iLevels) {

                if ($this->isDbZoneMatch (2, $aZones[1] . '.' . $aZones[2]))
                    $aUrls[] = $sUrl;
                else
                    $aUrls[] = $aZones[1] . '.' . $aZones[2];

            } else {

                $iExt = count($aZones) - 1;
                $iDom = $iExt - 1;
                $iSubDom = $iExt - 2;
                $iSubSubDom = $iExt - 3;

                if ($this->isDbZoneMatch (3, $aZones[$iSubDom] . '.' . $aZones[$iDom] . '.' . $aZones[$iExt])) {
                    $aUrls[] = $aZones[$iSubSubDom] . '.' . $aZones[$iSubDom] . '.' . $aZones[$iDom] . '.' . $aZones[$iExt];
                } else {

                    if ($this->isDbZoneMatch (2, $aZones[$iDom] . '.' . $aZones[$iExt]))
                        $aUrls[] = $aZones[$iSubDom] . '.' . $aZones[$iDom] . '.' . $aZones[$iExt];
                    else
                        $aUrls[] = $aZones[$iDom] . '.' . $aZones[$iExt];
                }
            }

        }

        $aUrlsUniq = array ();
        foreach ($aUrls as $sUrl) {
            if (isset($aUrlsUniq[$sUrl]))
                continue;
            $aUrlsUniq[$sUrl] = $sUrl;
        }

        return array_values($aUrlsUniq);
    }

    public function onPositiveDetection ($sExtraData = '')
    {
        $o = ch_instance('ChWsbDNSBlacklists');
        $o->onPositiveDetection (getVisitorIP(false), $sExtraData, 'dnsbluri');
    }

    /*************** private function ***************/

    private function isDbZoneMatch ($iLevel, $sZone)
    {
        $sZone = process_db_input($sZone, CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION);
        return $this->oDb->getOne("SELECT `level` FROM `sys_dnsbluri_zones` WHERE `level` = $iLevel AND `zone` = '$sZone' LIMIT 1") ? true : false;
    }

    private function initZones()
    {
        if (0 == $this->oDb->getOne("SELECT COUNT(*) FROM `sys_dnsbluri_zones`")) {

            $this->oDb->query("TRUNCATE TABLE `sys_dnsbluri_zones`");

            foreach ($this->aZonesUrls as $iLevel => $sUrl) {
                $f = fopen ($sUrl, 'r');
                if (!$f)
                    return false;
                while (!feof($f)) {
                    $sZone = fgets($f);
                    $sZone = trim($sZone);
                    if ($sZone)
                        $this->oDb->query("INSERT INTO `sys_dnsbluri_zones` SET `level` = $iLevel, `zone` = '" . process_db_input($sZone, CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION) . "'");
                }
                fclose($f);
            }
        }

        return true;
    }

}
