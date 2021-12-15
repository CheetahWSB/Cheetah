<?php

/**
 * Cheetah - Social Network Software Platform. Copyright (c) Dean J. Bassett Jr. - https://www.cheetahwsb.com
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once('inc/header.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'utils.inc.php');
require_once(CH_DIRECTORY_PATH_INC . 'design.inc.php');

ch_import('ChWsbPageView');

class ChMaintPageView extends ChWsbPageView
{
    function __construct()
    {
        parent::__construct('site_maintenance');
    }

    function getBlockCode_BlockOne()
    {
        return MsgBox(getParam('sys_maint_mode_msg'));
    }

}

$_page['name_index'] = 7;
$_page['header'] = 'Maintenance';

$_ni = $_page['name_index'];

$oEPV = new ChMaintPageView();
$_page_cont[$_ni]['page_main_code'] = $oEPV->getCode();

PageCode();
