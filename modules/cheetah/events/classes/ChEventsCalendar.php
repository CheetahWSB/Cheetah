<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import ('ChWsbTwigCalendar');

class ChEventsCalendar extends ChWsbTwigCalendar
{
    var $oTemplate;

    function __construct ($iYear, $iMonth, &$oDb, &$oConfig, &$oTemplate)
    {
        parent::__construct($iYear, $iMonth, $oDb, $oConfig);
        $this->oTemplate = &$oTemplate;
    }

    function getEntriesNames ()
    {
        return array(_t('_ch_events_single'), _t('_ch_events_plural'));
    }

}
