<?php

if (version_compare(PHP_VERSION, '5.4.0', '<'))
    return "This version requires PHP 5.4.0 or newer";

$mixCheckResult = 'Update can not be applied';

$sCurVer = $this->oDb->getOne("SELECT `VALUE` FROM `sys_options` WHERE `Name` = 'sys_tmp_version'");

//if ('1.2.0' == $sCurVer) $mixCheckResult = true;
if (version_compare($sCurVer, '1.3.0', '<')) {
    if (version_compare($sCurVer, '1.2.0', '>=')) {
        $mixCheckResult = true;
    }
}
return $mixCheckResult;
