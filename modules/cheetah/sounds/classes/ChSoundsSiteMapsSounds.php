<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbSiteMaps');
ch_import('ChWsbPrivacy');

/**
 * Sitemaps generator for Sounds
 */
class ChSoundsSiteMapsSounds extends ChWsbSiteMaps
{
    protected $_oModule;

    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);

        $this->_aQueryParts = array (
            'fields' => "`e`.`ID`, `e`.`Uri`, `e`.`Date`", // fields list
            'field_date' => "Date", // date field name
            'field_date_type' => "timestamp", // date field type
            'table' => "`RayMp3Files` AS `e`", // table name
            'join' => " INNER JOIN `sys_albums_objects` AS `o` ON (`o`.`id_object` = `e`.`ID`)
                        INNER JOIN `sys_albums` AS `a` ON (`a`.`Type` = 'ch_sounds' AND `a`.`Status` = 'active' AND `a`.`AllowAlbumView` = '" . CH_WSB_PG_ALL . "' AND `a`.`ID` = `o`.`id_album`)", // join SQL part
            'where' => "AND `e`.`Status` = 'approved'", // SQL condition, without WHERE
            'order' => " `e`.`Date` ASC ", // SQL order, without ORDER BY
        );

        $this->_oModule = ChWsbModule::getInstance('ChSoundsModule');
    }

    protected function _genUrl ($a)
    {
        return CH_WSB_URL_ROOT . $this->_oModule->_oConfig->getBaseUri() . 'view/' . $a['Uri'];
    }
}
