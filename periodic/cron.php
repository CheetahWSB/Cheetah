<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$GLOBALS['ch_profiler_disable'] = true;
define('CH_WSB_CRON_EXECUTE', '1');

$aPathInfo = pathinfo(__FILE__);
require_once ($aPathInfo['dirname'] . '/../inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'utils.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbDb.php');

function getRange($iLow, $iHigh, $iStep)
{
    $aResult = array();
    for ($i = $iLow; $i <= $iHigh && $iStep; $i += $iStep)
        $aResult[] = $i;
    return $aResult;
}

function getPeriod($sPeriod, $iLow, $iHigh)
{
    $aRes = array();
    $iStep = 1;
    $sErr = '';

    do {
        if ('' === $sPeriod) {
            $sErr = 'Variable sPeriod is emply';
            break;
        }

        $aParam = explode('/', $sPeriod);

        if (count($aParam) > 2) {
            $sErr = 'Error of format for string assigning period';
            break;
        }

        if (count($aParam) == 2 && is_numeric($aParam[1]))
            $iStep = $aParam[1];

        $sPeriod = $aParam[0];

        if ($sPeriod != '*') {
            $aParam = explode('-', $sPeriod);

            if (count($aParam) > 2) {
                $sErr = 'Error of format for string assigning period';
                break;
            }

            if (count($aParam) == 2)
                $aRes = getRange($aParam[0], $aParam[1], $iStep);
            else
                $aRes = explode(',', $sPeriod);
        } else
            $aRes = getRange($iLow, $iHigh, $iStep);
    } while(false);

    if ($sErr) {
        // show error or add to log
    }

    return $aRes;
}

function checkCronJob($sPeriods, $aDate = array())
{
    $aParam = explode(' ', preg_replace("{ +}", ' ', trim($sPeriods)));
    $bRes = true;

    if(empty($aDate))
        $aDate = getdate(time());

        //$aDate = getdate(strtotime('today midnight'));

    for ($i = 0; $i < count($aParam); $i++) {
        switch ($i) {
            case 0:
                $aRes = getPeriod($aParam[$i], 0, 59);
                $bRes = in_array($aDate['minutes'], $aRes);
                break;
            case 1:
                $aRes = getPeriod($aParam[$i], 0, 23);
                $bRes = in_array($aDate['hours'], $aRes);
                break;
            case 2:
                $aRes = getPeriod($aParam[$i], 1, 31);
                $bRes = in_array($aDate['mday'], $aRes);
                break;
            case 3:
                $aRes = getPeriod($aParam[$i], 1, 12);
                $bRes = in_array($aDate['mon'], $aRes);
                break;
            case 4:
                $aRes = getPeriod($aParam[$i], 0, 6);
                $bRes = in_array($aDate['wday'], $aRes);
                break;
        }

        if (!$bRes)
            break;
    }

    return $bRes;
}

function runJob($aJob)
{
    if(!empty($aJob['file']) && !empty($aJob['class']) && file_exists(CH_DIRECTORY_PATH_ROOT . $aJob['file'])) {
        if(!class_exists($aJob['class']))
            require_once(CH_DIRECTORY_PATH_ROOT . $aJob['file']);

        $oHandler = new $aJob['class']();
        $oHandler->processing();
    } else if(!empty($aJob['eval'])) {
        require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbService.php');
        eval($aJob['eval']);
    }
}

$oDb = ChWsbDb::getInstance();
$aJobs = $oDb->fromCache('sys_cron_jobs', 'getAll', 'SELECT * FROM `sys_cron_jobs`');

$iDate = time();
setParam('sys_cron_time', $iDate);

$aDate = getdate($iDate);
foreach($aJobs as $aRow) {
    if (checkCronJob($aRow['time'], $aDate))
        runJob($aRow);
}
