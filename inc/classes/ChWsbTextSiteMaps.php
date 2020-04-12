<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbSiteMaps');
ch_import('ChWsbPrivacy');

/**
 * Sitemaps generator for News
 */
class ChWsbTextSiteMaps extends ChWsbSiteMaps
{
    protected $_oModule;

    protected function __construct($aSystem, &$oModule)
    {
        parent::__construct($aSystem);

        $this->_oModule = $oModule;
        $this->_aQueryParts = array (
            'fields' => "`id`, `uri`, `when`", // fields list
            'field_date' => "when", // date field name
            'field_date_type' => "timestamp", // date field type
            'table' => "`" . $this->_oModule->_oConfig->getDbPrefix() . "entries`", // table name
            'join' => "", // join SQL part
            'where' => "AND `status` = '" . CH_TD_STATUS_ACTIVE . "'", // SQL condition, without WHERE
            'order' => " `when` ASC ", // SQL order, without ORDER BY
        );
    }

    protected function _genUrl ($a)
    {
        return CH_WSB_URL_ROOT . $this->_oModule->_oConfig->getBaseUri() . 'view/' . $a['uri'];
    }
}
