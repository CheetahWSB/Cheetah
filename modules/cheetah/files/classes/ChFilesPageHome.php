<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbFilesPageHome');

class ChFilesPageHome extends ChWsbFilesPageHome
{
    function __construct (&$oShared)
    {
        parent::__construct($oShared);
    }

    function getBlockCode_Featured ()
    {
        return $this->getBlockCode_Special();
    }

    function getBlockCode_Top ()
    {
        $this->oSearch->clearFilters(array('activeStatus', 'allow_view', 'album_status', 'albumType', 'ownerStatus'), array('albumsObjects', 'albums', 'icon'));
        $this->oSearch->aCurrent['paginate']['perPage'] = (int)$this->oConfig->getGlParam('number_top');
        $this->oSearch->aCurrent['sorting'] = 'top';
        $this->oSearch->aCurrent['view'] = 'short';
        $sCode = $this->oSearch->displayResultBlock();
        if ($this->oSearch->aCurrent['paginate']['totalNum'] > 0)
            return $sCode;
    }

    function wrapUnits($sCode, $bCenter = false, $bIndent = false, $sIndent = 'default_padding.html')
    {
        return parent::wrapUnits($sCode, $bCenter, $bIndent, $sIndent);
    }
}
