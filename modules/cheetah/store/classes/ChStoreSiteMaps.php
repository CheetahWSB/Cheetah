<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbSiteMaps');
ch_import('ChWsbPrivacy');

/**
 * Sitemaps generator for Store
 */
class ChStoreSiteMaps extends ChWsbSiteMaps
{
    protected $_oModule;

    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);

        $this->_aQueryParts = array (
            'fields' => "`id`, `uri`, `created`", // fields list
            'field_date' => "created", // date field name
            'field_date_type' => "timestamp", // date field type
            'table' => "`ch_store_products`", // table name
            'join' => "", // join SQL part
            'where' => "AND `status` = 'approved' AND `allow_view_product_to` = '" . CH_WSB_PG_ALL . "'", // SQL condition, without WHERE
            'order' => " `created` ASC ", // SQL order, without ORDER BY
        );

        $this->_oModule = ChWsbModule::getInstance('ChStoreModule');
    }

    protected function _genUrl ($a)
    {
        return CH_WSB_URL_ROOT . $this->_oModule->_oConfig->getBaseUri() . 'view/' . $a['uri'];
    }
}
