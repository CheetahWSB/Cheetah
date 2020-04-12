<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChBaseMenu');

class ChBaseMenuQlinks2 extends ChBaseMenu
{
    function __construct()
    {
        parent::__construct();
    }

    function getCode()
    {
        $this->getMenuInfo();
        return $GLOBALS['oSysTemplate']->parseHtmlByName('quick_links_list.html', array('content' => $this->genQuickLinks()));
    }

    function genQuickLinks()
    {
        $aQlinksUnits = array();
        foreach ($this->aTopMenu as $iItemID => $aItem) {
            if ($aItem['BQuickLink'] != '1' || $aItem['Type'] == 'system')
                continue;
            if (!$this->checkToShow($aItem))
                continue;

            list($aItem['Link']) = explode('|', $aItem['Link']);

            $aItem['Link']    = $this->replaceMetas($aItem['Link']);
            $aItem['Onclick'] = $this->replaceMetas($aItem['Onclick']);
            $aItem['Caption'] = $this->replaceMetas($aItem['Caption']);

            $sPicture = isset($aItem['Icon']) && !empty($aItem['Icon']) ? $aItem['Icon'] : $aItem['Picture'];
            if (!$sPicture && $aItem['Parent']) {
                $aItemRoot = $this->aTopMenu[$aItem['Parent']];
                $sPicture = isset($aItemRoot['Icon']) && !empty($aItemRoot['Icon']) ? $aItemRoot['Icon'] : $aItemRoot['Picture'];
            }

            $aQlinksUnits[_t($aItem['Caption'])] = $this->genQuickLink(_t($aItem['Caption']), $aItem['Link'], $aItem['Onclick'], $iItemID, $sPicture);
        }

        ksort($aQlinksUnits);
        return implode('', $aQlinksUnits);
    }

    function genQuickLink ($sText, $sLink, $sOnclick, $iItemID, $sPictureVal)
    {
        $sOnclick = $sOnclick ? (' onclick="' . $sOnclick . '" ') : '';

        if (strpos($sLink, 'http://') === false && strpos($sLink, 'https://') === false && !$sOnclick)
            $sLink = CH_WSB_URL_ROOT . $sLink;

        $sScriptAction = (!$sOnclick ? " onclick=\"window.open ('{$sLink}','_self');\" " : $sOnclick);

        $isFontIcon = (false === strpos ($sPictureVal, '.'));
        return $GLOBALS['oSysTemplate']->parseHtmlByName('quick_link.html', array(
            'ch_if:icon' => array(
                'condition' => !$isFontIcon,
                'content' => array('picture' => getTemplateIcon($sPictureVal), 'caption' => $sText, 'action' => $sScriptAction),
            ),
            'ch_if:texticon' => array(
                'condition' => $isFontIcon,
                'content' => array('picture' => $sPictureVal, 'caption' => $sText, 'action' => $sScriptAction),
            ),
            'action' => $sScriptAction,
            'caption' => $sText,
        ));
    }
}
