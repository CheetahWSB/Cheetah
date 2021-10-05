
-- update module version

-- Bug in previous version where the version info is not properly set. Need to fix it if it was not manually fixed.
UPDATE `sys_modules` SET `version` = '1.1.0' WHERE `uri` = 'smtpmailer' AND `version` = '1.010';

-- Now update it to the new version.
UPDATE `sys_modules` SET `version` = '1.2.0' WHERE `uri` = 'smtpmailer' AND `version` = '1.1.0';
