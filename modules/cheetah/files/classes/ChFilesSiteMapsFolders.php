<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbSiteMaps');
ch_import('ChWsbPrivacy');

/**
 * Sitemaps generator for File Folders
 */
class ChFilesSiteMapsFolders extends ChWsbSiteMaps
{
    protected $_oModule;

    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);

        $this->_aQueryParts = array (
            'fields' => "`ID`, `Uri`, `Date`, `Owner`", // fields list
            'field_date' => "Date", // date field name
            'field_date_type' => "timestamp", // date field type
            'table' => "`sys_albums`", // table name
            'join' => "", // join SQL part
            'where' => "AND `Type` = 'ch_files' AND `Status` = 'active' AND `ObjCount` > 0 AND `AllowAlbumView` = '" . CH_WSB_PG_ALL . "'", // SQL condition, without WHERE
            'order' => " `Date` ASC ", // SQL order, without ORDER BY
        );

        $this->_oModule = ChWsbModule::getInstance('ChFilesModule');
    }

    protected function _genUrl ($a)
    {
        return CH_WSB_URL_ROOT . $this->_oModule->_oConfig->getBaseUri() . 'browse/album/' . $a['Uri'] . '/owner/' . rawurlencode(getUsername($a['Owner']));
    }
}
