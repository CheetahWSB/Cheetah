<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbSiteMaps');

/**
 * Sitemaps generator for system pages
 */
class ChWsbSiteMapsSystem extends ChWsbSiteMaps
{
    protected $_aPages = array (
        array('page' => 'about_us.php'),
        array('page' => 'advice.php'),
        array('page' => 'contact.php'),
        array('page' => 'faq.php'),
        array('page' => 'forgot.php'),
        array('page' => 'help.php'),
        array('page' => 'join.php'),
        array('page' => 'privacy.php'),
        array('page' => 'search_home.php'),
        array('page' => 'services.php'),
        array('page' => 'terms_of_use.php'),
    );

    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
    }

    protected function _genUrl ($a)
    {
        return CH_WSB_URL_ROOT . $a['page'];
    }

    protected function _getCount ()
    {
        return count($this->_aPages);
    }

    protected function _getRecords ($iStart)
    {
        return array_slice($this->_aPages, $iStart, CH_SITE_MAPS_URLS_PER_FILE);
    }
}
