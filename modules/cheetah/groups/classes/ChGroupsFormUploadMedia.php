<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_groups_import ('FormEdit');

class ChGroupsFormUploadMedia extends ChGroupsFormEdit
{
    function __construct ($oMain, $iProfileId, $iEntryId, &$aDataEntry, $sMedia, $aMediaFields)
    {
        parent::__construct ($oMain, $iProfileId, $iEntryId, $aDataEntry);

        foreach ($this->_aMedia as $k => $a) {
            if ($k == $sMedia)
                continue;
            unset($this->_aMedia[$k]);
        }

        array_push($aMediaFields, 'Submit', 'id');

        foreach ($this->aInputs as $k => $a) {
            if (in_array($k, $aMediaFields))
                continue;
            unset($this->aInputs[$k]);
        }
    }

}
