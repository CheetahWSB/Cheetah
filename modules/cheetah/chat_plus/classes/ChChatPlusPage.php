<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChChatPlusPage extends ChWsbPageView
{
    function __construct($sPageName)
    {
        parent::__construct($sPageName);
    }

    function getChatBlockMenu($iBlockID, $aMenu)
    {
        if (!$aMenu || !($oModule = ChWsbModule::getInstance('ChChatPlusModule')))
            return '';

        reset($aMenu);
        $sTitle = key($aMenu);
        $a = current($aMenu);

        return $oModule->_oTemplate->parseHtmlByName('chat_block_menu.html', array(
            'block_id' => $iBlockID,
            'href' => $a['href'],
            'target' => isset($a['target']) ? $a['target'] : '',
            'title' => $sTitle,
        ));
    }
}
