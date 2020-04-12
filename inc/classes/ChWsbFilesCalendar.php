<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChTemplCalendar');

class ChWsbFilesCalendar extends ChTemplCalendar
{
    var $iBlockID = 0;
    var $sDynamicUrl = '';

    function __construct($iYear, $iMonth, &$oDb, &$oTemplate, &$oConfig)
    {
        parent::__construct($iYear, $iMonth);
        $this->oDb = &$oDb;
        $this->oTemplate = &$oTemplate;
        $this->oConfig = &$oConfig;
    }

    function getData()
    {
        return $this->oDb->getFilesByMonth($this->iYear, $this->iMonth, $this->iNextYear, $this->iNextMonth);
    }

    function getUnit(&$aData)
    {
    }

    function getBaseUri()
    {
        return CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . "calendar/";
    }

    function getBrowseUri()
    {
        return CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . "browse/calendar/";
    }

    function getEntriesNames()
    {
        return array(
            _t('_ch_' . $this->oConfig->getUri() . '_single'),
            _t('_ch_' . $this->oConfig->getUri() . '_plural')
        );
    }

    function getMonthUrl($isNextMoths, $isMiniMode = false)
    {
        if ($isMiniMode && $this->iBlockID && $this->sDynamicUrl) {
            return "javascript:loadDynamicBlock('" . $this->iBlockID . "', '" . ch_append_url_params($this->sDynamicUrl,
                'date=' . ($isNextMoths ? "{$this->iNextYear}/{$this->iNextMonth}" : "{$this->iPrevYear}/{$this->iPrevMonth}")) . "');";
        } else {
            return parent::getMonthUrl($isNextMoths, $isMiniMode);
        }
    }

    function setBlockId($iBlockID)
    {
        $this->iBlockID = $iBlockID;
    }

    function setDynamicUrl($s)
    {
        $this->sDynamicUrl = $s;
    }
}
