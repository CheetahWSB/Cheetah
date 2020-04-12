<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChTemplCalendar');

class ChSitesCalendar extends ChTemplCalendar
{
    var $_oDb, $_oTemplate, $_oConfig;

    function __construct($iYear, $iMonth, $oSites)
    {
        parent::__construct($iYear, $iMonth);
        $this->_oDb = $oSites->_oDb;
        $this->_oTemplate = $oSites->_oTemplate;
        $this->_oConfig = $oSites->_oConfig;
    }

    /**
     * return records for current month, there is mandatory field `Day` - a day for current row
     * use the following class variables to pass to your database query
     * $this->iYear, $this->iMonth, $this->iNextYear, $this->iNextMonth
     *
     * for example:
     *
     * return $db->getAll ("
     *  SELECT *, DAYOFMONTH(FROM_UNIXTIME(`EventStart`)) AS `Day`
     *  FROM `my_table`
     *  WHERE `Date` >= UNIX_TIMESTAMP('{$this->iYear}-{$this->iMonth}-1') AND `Date` < UNIX_TIMESTAMP('{$this->iNextYear}-{$this->iNextMonth}-1') AND `Status` = 'approved'");
     *
     */
    function getData()
    {
        return $this->_oDb->getSitesByMonth($this->iYear, $this->iMonth, $this->iNextYear, $this->iNextMonth);
    }

    /**
     * return base calendar url
     * year and month will be added to this url automatically
     * so if your base url is /m/some_module/calendar/, it will be transormed to
     * /m/some_module/calendar/YEAR/MONTH, like /m/some_module/calendar/2009/3
     */
    function getBaseUri()
    {
        return CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . "calendar/";
    }

    function getBrowseUri ()
    {
        return CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/calendar/";
    }

    function getEntriesNames ()
    {
        return array(_t('_ch_sites_single'), _t('_ch_sites_plural'));
    }
}
