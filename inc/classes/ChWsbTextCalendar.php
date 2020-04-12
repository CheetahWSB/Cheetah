<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChTemplCalendar');

class ChWsbTextCalendar extends ChTemplCalendar
{
    var $_oDb;
    var $_oConfig;
    var $sCssPrefix;

    var $iBlockID = 0;
    var $sDynamicUrl = '';

    function __construct($iYear, $iMonth, &$oDb, &$oConfig)
    {
        parent::__construct($iYear, $iMonth);

        $this->_oDb = &$oDb;
        $this->_oConfig = &$oConfig;

        $this->sCssPrefix = '';
    }
    /**
     * return records for current month
     */
    function getData ()
    {
        return $this->_oDb->getByMonth($this->iYear, $this->iMonth, $this->iNextYear, $this->iNextMonth);
    }

    /**
     * return html for data unit for some day.
     */
    function getUnit (&$aData)
    {
        $sUrl = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'];
        return '<div class="' . $this->sCssPrefix . '-calendar-unit"><a href="' . $sUrl . '" title="' . $aData['caption'] . '">' . $aData['caption'] . '</a></div>';
    }

    /**
     * return base calendar url
     */
    function getBaseUri ()
    {
        return CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . "calendar/";
    }

    function getBrowseUri ()
    {
        return CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'calendar/';
    }

    function getEntriesNames ()
    {
        $sModuleUri = $this->_oConfig->getUri();
        return array(_t('_' . $sModuleUri . '_entry_single'), _t('_' . $sModuleUri . '_entry_plural'));
    }

    function getMonthUrl ($isNextMoths, $isMiniMode = false)
    {
        if ($isMiniMode && $this->iBlockID && $this->sDynamicUrl)
            return "javascript:loadDynamicBlock('" . $this->iBlockID . "', '" . ch_append_url_params($this->sDynamicUrl, 'date=' . ($isNextMoths ? "{$this->iNextYear}/{$this->iNextMonth}" : "{$this->iPrevYear}/{$this->iPrevMonth}")) . "');";
        else
            return parent::getMonthUrl ($isNextMoths, $isMiniMode);
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
