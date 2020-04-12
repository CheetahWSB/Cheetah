<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( CH_DIRECTORY_PATH_CLASSES . 'ChWsbAlerts.php' );

class ChWsbTagParseResponse extends ChWsbAlertsResponse
{
    var $aParseList = array(
        'tag' => array(
            'class' => 'ChWsbTags',
            'file' => 'inc/classes/ChWsbTags.php',
            'method' => 'reparseObjTags({sType}, {iId})'
        ),
        'category' => array(
            'class' => 'ChWsbCategories',
            'file' => 'inc/classes/ChWsbCategories.php',
            'method' => 'reparseObjTags({sType}, {iId})'
        )
    );

    var $aCurrent = array();

    function response ($oTag)
    {
        foreach ($this->aParseList as $sKey => $aValue) {
            if (!class_exists($aValue['class']))
               require_once(CH_DIRECTORY_PATH_ROOT . $aValue['file']);
            $oParse = new $aValue['class']();
            $sMethod = $aValue['method'];

            $sMethod = str_replace('{sType}', "'".$oTag->sUnit."'", $sMethod);
            $sMethod = str_replace('{iId}', $oTag->iObject, $sMethod);
            $sMethod = str_replace('{iId}', $oTag->iObject, $sMethod);
            $sFullComm = '$oParse->'.$sMethod.'; ';
            eval($sFullComm);
        }
    }
}
