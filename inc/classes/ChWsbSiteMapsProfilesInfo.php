<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbSiteMaps');
ch_import('ChWsbPrivacy');

/**
 * Sitemaps generator for Profile Info Pages
 */
class ChWsbSiteMapsProfilesInfo extends ChWsbSiteMaps
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);

        $this->_aQueryParts = array (
            'fields' => "`ID`, `DateLastEdit`", // fields list
            'field_date' => "DateLastEdit", // date field name
            'field_date_type' => "datetime", // date field type (or timestamp)
            'table' => "`Profiles`", // table name
            'join' => "", // join SQL part
            'where' => "AND `Profiles`.`Status` = 'Active' AND `allow_view_to` = '" . CH_WSB_PG_ALL . "' AND (`Profiles`.`Couple` = 0 OR `Profiles`.`Couple` > `Profiles`.`ID`)", // SQL condition, without WHERE
            'order' => " `DateLastNav` ASC ", // SQL order, without ORDER BY
        );
    }

    protected function _genUrl ($a)
    {
        return CH_WSB_URL_ROOT . 'profile_info.php?ID=' . $a['ID'];
    }
}
