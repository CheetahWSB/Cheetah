<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChTemplCmtsView');

class ChWsbCmtsProfile extends ChTemplCmtsView
{
    /**
     * Constructor
     */
    function __construct($sSystem, $iId, $iInit = 1)
    {
        parent::__construct($sSystem, $iId, $iInit);
    }

	function getBaseUrl()
    {
    	$aEntry = getProfileInfo($this->getId());
    	if(empty($aEntry) || !is_array($aEntry))
    		return '';

    	return getProfileLink($aEntry['ID']);
    }

    function isRemoveAllowedAll()
    {
        if($this->_iId == $this->_getAuthorId() && getParam('enable_cmts_profile_delete') == 'on')
           return true;

        return parent::isRemoveAllowedAll();
    }
}
