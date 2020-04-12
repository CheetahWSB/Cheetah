<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbFilesPageAlbumsOwner.php');

class ChFilesPageAlbumsOwner extends ChWsbFilesPageAlbumsOwner
{
    function __construct(&$oShared, $aParams = array())
    {
        parent::__construct('ch_files_albums_owner', $oShared, $aParams);
    }

    function getBlockCode_Favorited ($aParams = array())
    {
    	list($sCode, $aTopMenu, $aBottomMenu) = parent::getBlockCode_Favorited(array(
    		'unit_css_class' => false
    	));

    	return array($sCode, $aTopMenu, $aBottomMenu, '');
    }
}
