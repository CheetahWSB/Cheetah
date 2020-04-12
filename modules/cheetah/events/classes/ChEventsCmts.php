<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChTemplCmtsView');

class ChEventsCmts extends ChTemplCmtsView
{
    /**
     * Constructor
     */
    function __construct($sSystem, $iId)
    {
        parent::__construct($sSystem, $iId);
    }

    function getMain()
    {
        $aPathInfo = pathinfo(__FILE__);
        require_once ($aPathInfo['dirname'] . '/ChEventsSearchResult.php');
        return (new ChEventsSearchResult())->getMain();
    }

    function getBaseUrl()
    {
    	$oMain = $this->getMain();
    	$aEntry = $oMain->_oDb->getEntryById($this->getId());
    	if(empty($aEntry) || !is_array($aEntry))
    		return '';

    	return CH_WSB_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'view/' . $aEntry['EntryUri'];
    }

    function isPostReplyAllowed ($isPerformAction = false)
    {
        if (!parent::isPostReplyAllowed($isPerformAction))
            return false;

        $oMain = $this->getMain();
        $aEvent = $oMain->_oDb->getEntryByIdAndOwner($this->getId (), 0, true);
        return $oMain->isAllowedComments($aEvent);
    }

    function isEditAllowedAll ()
    {
        $oMain = $this->getMain();
        $aEvent = $oMain->_oDb->getEntryByIdAndOwner($this->getId (), 0, true);
        if ($oMain->isAllowedCreatorCommentsDeleteAndEdit ($aEvent))
            return true;
        return parent::isEditAllowedAll ();
    }

    function isRemoveAllowedAll ()
    {
        $oMain = $this->getMain();
        $aEvent = $oMain->_oDb->getEntryByIdAndOwner($this->getId (), 0, true);
        if ($oMain->isAllowedCreatorCommentsDeleteAndEdit ($aEvent))
            return true;
        return parent::isRemoveAllowedAll ();
    }
}
