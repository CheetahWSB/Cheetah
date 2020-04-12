<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_events_import ('FormAdd');

class ChEventsFormEdit extends ChEventsFormAdd
{
    function __construct ($oMain, $iProfileId, $iEventId, &$aEvent)
    {
        parent::__construct ($oMain, $iProfileId, $iEventId, $aEvent['PrimPhoto']);

        $aFormInputsId = array (
            'ID' => array (
                'type' => 'hidden',
                'name' => 'ID',
                'value' => $iEventId,
            ),
        );

        ch_import('ChWsbCategories');
        $oCategories = new ChWsbCategories();
        $oCategories->getTagObjectConfig ();
        $this->aInputs['Categories'] = $oCategories->getGroupChooser ('ch_events', (int)$iProfileId, true, $aEvent['Categories']);

        $this->aInputs = array_merge($this->aInputs, $aFormInputsId);
    }

}
