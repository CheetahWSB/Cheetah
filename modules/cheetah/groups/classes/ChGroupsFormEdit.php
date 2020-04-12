<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_groups_import ('FormAdd');

class ChGroupsFormEdit extends ChGroupsFormAdd
{
    function __construct ($oMain, $iProfileId, $iEntryId, &$aDataEntry)
    {
        parent::__construct ($oMain, $iProfileId, $iEntryId, $aDataEntry['thumb']);

        $aFormInputsId = array (
            'id' => array (
                'type' => 'hidden',
                'name' => 'id',
                'value' => $iEntryId,
            ),
        );

        ch_import('ChWsbCategories');
        $oCategories = new ChWsbCategories();
        $oCategories->getTagObjectConfig ();
        $this->aInputs['categories'] = $oCategories->getGroupChooser ('ch_groups', (int)$iProfileId, true, $aDataEntry['categories']);

        $this->aInputs = array_merge($this->aInputs, $aFormInputsId);
    }

}
