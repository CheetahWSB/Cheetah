<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    ch_import ('ChTemplCalendar');

    class ChPollCalendar extends ChTemplCalendar
    {
        var $oDb, $oTemplate, $oConfig;

        var $sActionViewResult = 'view_calendar/';
        var $sActionBase       = 'calendar/';

        function __construct ($iYear, $iMonth, &$oDb, &$oTemplate, &$oConfig)
        {
            parent::__construct($iYear, $iMonth);
            $this->oDb = &$oDb;
            $this->oTemplate = &$oTemplate;
            $this->oConfig = &$oConfig;
        }

        function getData ()
        {
            return $this->oDb->getPollsByMonth ($this->iYear, $this->iMonth, $this->iNextYear, $this->iNextMonth);
        }

        function getBaseUri ()
        {
            return CH_WSB_URL_ROOT . $this -> oConfig->getBaseUri() . $this ->sActionBase;
        }

        function getBrowseUri ()
        {
            return CH_WSB_URL_ROOT . $this -> oConfig->getBaseUri() . $this -> sActionViewResult;
        }

        function getEntriesNames ()
        {
            return array(_t('_ch_poll'), _t('_ch_polls'));
        }
    }
