<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModuleDb');
ch_import('ChWsbIO');

class ChWsbInstallerUtils extends ChWsbIO
{
    function __construct()
    {
        parent::__construct();
    }

    function isXsltEnabled()
    {
        if (((int)phpversion()) >= 5) {
            if (class_exists ('DOMDocument') && class_exists ('XsltProcessor'))
                return true;
        } else {
            if (function_exists('domxml_xslt_stylesheet_file'))
                return true;
            elseif (function_exists ('xslt_create'))
                return true;
        }
        return false;
    }

    function isAllowUrlInclude()
    {
        $sAllowUrlInclude = (int)ini_get('allow_url_include');
        return !($sAllowUrlInclude == 0);
    }

    public static function isModuleInstalled($sUri)
    {
        $oModuleDb = new ChWsbModuleDb();
        return $oModuleDb->isModule($sUri);
    }
}
