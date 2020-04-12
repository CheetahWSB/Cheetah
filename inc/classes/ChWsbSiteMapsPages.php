<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbSiteMaps');

/**
 * Sitemaps generator for pages created using admin page builder
 */
class ChWsbSiteMapsPages extends ChWsbSiteMaps
{
    protected $_bPermalinks = true;

    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);

        $this->_aQueryParts = array (
            'fields' => "`Name`, 0 AS `Date`", // fields list
            'field_date' => "Date", // date field name
            'field_date_type' => "timestamp", // date field type (or timestamp)
            'table' => "`sys_page_compose_pages`", // table name
            'join' => "", // join SQL part
            'where' => "AND `System` = 0", // SQL condition, without WHERE
            'order' => " `Order` ASC ", // SQL order, without ORDER BY
        );
    }

    protected function _genUrl ($a)
    {
        return CH_WSB_URL_ROOT . ($this->_bPermalinks ? 'page/' : 'viewPage.php?ID=') . rawurlencode($a['Name']);
    }
}
