<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbFilesPageAlbumsOwner.php');

class ChVideosPageAlbumsOwner extends ChWsbFilesPageAlbumsOwner
{
    function __construct (&$oShared, $aParams = array())
    {
        parent::__construct('ch_videos_albums_owner', $oShared, $aParams);
    }
}
