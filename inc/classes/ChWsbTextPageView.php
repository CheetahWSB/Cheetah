<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');
ch_import('ChTemplConfig');

class ChWsbTextPageView extends ChWsbPageView
{
    var $_sPageName;
    var $_sName;
    var $_oObject;

    function __construct($sPageName, $sName, &$oObject)
    {
        parent::__construct($sPageName);

        $this->_sName = process_db_input($sName, CH_TAGS_STRIP);
        $this->_oObject = $oObject;
    }
    function getBlockCode_Content()
    {
        return $this->_oObject->getBlockView($this->_sName);
    }
    function getBlockCode_Comment()
    {
        return $this->_oObject->getBlockComment($this->_sName);
    }
    function getBlockCode_Vote()
    {
        $sContent = $this->_oObject->getBlockVote($this->_sName);
        return !empty($sContent) ? array($sContent, array(), array(), false) : '';
    }
    function getBlockCode_Info()
    {
        return $this->_oObject->getBlockInfo($this->_sName);
    }
    function getBlockCode_Action()
    {
        return $this->_oObject->getBlockAction($this->_sName);
    }
    function getBlockCode_SocialSharing()
    {
        return $this->_oObject->getBlockSocialSharing($this->_sName);
    }
}
