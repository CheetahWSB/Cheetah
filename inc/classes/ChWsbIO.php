<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

class ChWsbIO
{
    // constructor
    function __construct() {}

    function isExecutable($sFile)
    {
        clearstatcache();

        $aPathInfo = pathinfo(__FILE__);
        $sFile = $aPathInfo['dirname'] . '/../../' . $sFile;

        return (is_file($sFile) && is_executable($sFile));
    }

    function isWritable($sFile, $sPrePath = '/../../')
    {
        clearstatcache();

        $aPathInfo = pathinfo(__FILE__);
        $sFile = $aPathInfo['dirname'] . '/../../' . $sFile;

        return is_readable($sFile) && is_writable($sFile);
    }

    function getPermissions($sFileName)
    {
        $sPath = $GLOBALS['logged']['admin'] == true ? CH_DIRECTORY_PATH_ROOT : '../';

        clearstatcache();
        $hPerms = @fileperms($sPath . $sFileName);
        if($hPerms == false) return false;
        $sRet = substr( decoct( $hPerms ), -3 );
        return $sRet;
    }
}
