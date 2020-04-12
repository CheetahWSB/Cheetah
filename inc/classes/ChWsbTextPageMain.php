<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChWsbTextPageMain extends ChWsbPageView
{
    var $_sPageName;
    var $_oObject;

    function __construct($sPageName, &$oObject)
    {
        parent::__construct($sPageName);

        $this->_oObject = $oObject;
    }
    function getBlockCode_Featured()
    {
        return array($this->_oObject->serviceFeaturedBlock(), array(), array(), true);
    }
    function getBlockCode_Latest()
    {
        $sUri = $this->_oObject->_oConfig->getUri();
        $sBaseUri = $this->_oObject->_oConfig->getBaseUri();
        $aTopMenu = array(
            'get-rss' => array('href' => CH_WSB_URL_ROOT . $sBaseUri . 'act_rss/', 'target' => '_blank', 'title' => _t('_' . $sUri . '_get_rss'), 'icon' => 'rss'),
        );

        return array($this->_oObject->serviceArchiveBlock(), $aTopMenu, array(), true, 'getBlockCaptionMenu');
    }
    function getBlockCode_Categories($iBlockId)
    {
        return array($this->_oObject->serviceCategoriesBlock($iBlockId), array(), array(), true);
    }
    function getBlockCode_Tags($iBlockId)
    {
        return array($this->_oObject->serviceTagsBlock($iBlockId), array(), array(), true);
    }
    function getBlockCode_Calendar($iBlockId)
    {
        return array($this->_oObject->serviceGetCalendarBlock($iBlockId, array('mini_mode' => true)), array(), array(), true);
    }
}
