<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbInstaller');

class ChZIPInstaller extends ChWsbInstaller
{
    function __construct($aConfig)
    {
        parent::__construct($aConfig);
    }

    function install($aParams)
    {
        $aResult = parent::install($aParams);

        $s = $this->_readFromUrl("http://ws.geonames.org/postalCodeCountryInfo?");
        $a = $this->_getCountriesArray ($s);
        if (count($a)) {
            db_res ("TRUNCATE TABLE `ch_zip_countries_geonames`");
            foreach ($a as $sCountry)
                db_res ("INSERT INTO `ch_zip_countries_geonames` VALUES ('$sCountry')");
        } else {
            return array('code' => CH_WSB_INSTALLER_FAILED, 'content' => 'Network error - can not get list of countries');
        }

        return $aResult;
    }

    function uninstall($aParams)
    {
        return parent::uninstall($aParams);
    }

    function _getCountriesArray (&$s)
    {
        if (!preg_match_all('/<countryCode>(.*)<\/countryCode>/', $s, $m)) {
            return array ();
        }
        return array_unique($m[1]);
    }

    function _readFromUrl ($sUrl)
    {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $sUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            $s = curl_exec($curl);
            curl_close($curl);
            if (true === $s)
                $s = '';
        } else {
            $s = @file_get_contents($sUrl);
        }
        return $s;
    }
}
