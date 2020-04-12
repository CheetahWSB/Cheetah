<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChTemplCmtsView');

class ChBlogsCmts extends ChTemplCmtsView
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
        require_once ($aPathInfo['dirname'] . '/ChBlogsSearchUnit.php');
        return (new ChBlogsSearchUnit())->getBlogsMain();
    }

    function getBaseUrl()
    {
    	$oMain = $this->getMain();
    	$aEntry = $oMain->_oDb->getPostInfo($this->getId());
    	if(empty($aEntry) || !is_array($aEntry))
    		return '';

    	return $oMain->genUrl($aEntry['ID'], $aEntry['PostUri'], 'entry');
    }

    function isPostReplyAllowed($isPerformAction = false)
    {
        if (!parent::isPostReplyAllowed($isPerformAction))
            return false;

        $oMain = $this->getMain();
        $aBlogPost = $oMain->_oDb->getPostInfo($this->getId(), 0, true);
        return $oMain->isAllowedComments($aBlogPost);
    }

    function isEditAllowedAll()
    {
        $oMain = $this->getMain();
        $aBlogPost = $oMain->_oDb->getPostInfo($this->getId(), 0, true);
        if ($oMain->isAllowedCreatorCommentsDeleteAndEdit($aBlogPost))
            return true;
        return parent::isEditAllowedAll();
    }

    function isRemoveAllowedAll()
    {
        $oMain = $this->getMain();
        $aBlogPost = $oMain->_oDb->getPostInfo($this->getId(), 0, true);
        if ($oMain->isAllowedCreatorCommentsDeleteAndEdit($aBlogPost))
            return true;
        return parent::isRemoveAllowedAll();
    }
}
