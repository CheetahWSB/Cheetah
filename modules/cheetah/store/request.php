<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_INC . 'profiles.inc.php');

check_logged();

ch_import('ChWsbRequest');

class ChStoreRequest extends ChWsbRequest
{
    function __construct()
    {
        parent::__construct();
    }

    public static function processAsAction($aModule, &$aRequest, $sClass = "Module")
    {
        $sClassRequire = $aModule['class_prefix'] . $sClass;
        $oModule = ChWsbRequest::_require($aModule, $sClassRequire);
        $aVars = array ('BaseUri' => $oModule->_oConfig->getBaseUri());
        $GLOBALS['oTopMenu']->setCustomSubActions($aVars, 'ch_store_title', false);

        return ChWsbRequest::processAsAction($aModule, $aRequest, $sClass);
    }
}

ChStoreRequest::processAsAction($GLOBALS['aModule'], $GLOBALS['aRequest']);
