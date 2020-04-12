<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChBaseMenuService');

/**
* @see ChBaseMenuService;
*/
class ChTemplMenuService extends ChBaseMenuService
{
    /**
    * Class constructor;
    */
    function __construct()
    {
        parent::__construct();
    }

	function getItems()
	{
		$sContent = parent::getItems();

		return $GLOBALS['oSysTemplate']->parseHtmlByContent($sContent, array(
			'ch_if:show_profile_link' => array(
				'condition' => $this->aMenuInfo['memberID'] != 0,
				'content' => array(
					'link' => getProfileLink($this->aMenuInfo['memberID']),
					'title' => getNickName($this->aMenuInfo['memberID'])
				)
			)
		));
	}
}
