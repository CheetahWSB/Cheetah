<?php

if (version_compare(PHP_VERSION, '5.4.0', '<')) 
    return "This version requires PHP 5.4.0 or newer";

$mixCheckResult = 'Update can not be applied';

if ('1.0.0.B1' == $this->oDb->getOne("SELECT `VALUE` FROM `sys_options` WHERE `Name` = 'sys_tmp_version'"))
    $mixCheckResult = true;

return $mixCheckResult;
