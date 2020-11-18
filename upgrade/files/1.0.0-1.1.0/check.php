<?php

if (version_compare(PHP_VERSION, '5.4.0', '<'))
    return "This version requires PHP 5.4.0 or newer";

$mixCheckResult = 'Update can not be applied';

$sCurVer = $this->oDb->getOne("SELECT `VALUE` FROM `sys_options` WHERE `Name` = 'sys_tmp_version'");

if ('1.0.0' == $sCurVer) $mixCheckResult = true;

return $mixCheckResult;
