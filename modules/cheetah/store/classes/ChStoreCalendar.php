<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import ('ChWsbTwigCalendar');

class ChStoreCalendar extends ChWsbTwigCalendar
{
    function __construct ($iYear, $iMonth, &$oDb, &$oConfig, &$oTemplate)
    {
        parent::__construct($iYear, $iMonth, $oDb, $oConfig);
    }

    function getEntriesNames ()
    {
        return array(_t('_ch_store_products_single'), _t('_ch_store_products_plural'));
    }

}
