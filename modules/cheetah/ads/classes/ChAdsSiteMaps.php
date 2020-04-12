<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbSiteMaps');
ch_import('ChWsbPrivacy');

/**
 * Sitemaps generator for Ads
 */
class ChAdsSiteMaps extends ChWsbSiteMaps
{
    protected $_oModule;

    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);

        $this->_aQueryParts = array (
            'fields' => "`ID`, `EntryUri`, `DateTime`", // fields list
            'field_date' => "DateTime", // date field name
            'field_date_type' => "timestamp", // date field type
            'table' => "`ch_ads_main`", // table name
            'join' => "", // join SQL part
            'where' => "AND `Status` = 'active' AND `AllowView` = '" . CH_WSB_PG_ALL . "'", // SQL condition, without WHERE
            'order' => " `DateTime` ASC ", // SQL order, without ORDER BY
        );

        $this->_oModule = ChWsbModule::getInstance('ChAdsModule');
    }

    protected function _genUrl ($a)
    {
        return $this->_oModule->genUrl($a['ID'], $a['EntryUri']);
    }
}
