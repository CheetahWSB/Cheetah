<?php

if (version_compare(PHP_VERSION, '5.4.0', '<'))
    return "This version requires PHP 5.4.0 or newer";

$mixCheckResult = 'Update can not be applied';

$sCurVer = $this->oDb->getOne("SELECT `VALUE` FROM `sys_options` WHERE `Name` = 'sys_tmp_version'");

if ('1.1.0' == $sCurVer) $mixCheckResult = true;

// sys_tmp_version was not updated in previous final. My Mistake.
// So it could still be 1.0.0 even if they are running 1.1.0
// So do an additional check for something else that was changed in version 1.1.0
// to see if 1.1.0 is actually installed.
if ($mixCheckResult !== true) {
    $sExtraCheck = $this->oDb->getOne("SELECT `VALUE` FROM `sys_options` WHERE `Name` = 'usex264'");
    if ('on' == $sExtraCheck) $mixCheckResult = true;
}

return $mixCheckResult;
