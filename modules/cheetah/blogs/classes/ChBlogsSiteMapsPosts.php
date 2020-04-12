<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbSiteMaps');
ch_import('ChWsbPrivacy');

/**
 * Sitemaps generator for Blog Posts
 */
class ChBlogsSiteMapsPosts extends ChWsbSiteMaps
{
    protected $_oModule;

    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);

        $this->_aQueryParts = array (
            'fields' => "`PostID`, `PostUri`, `PostDate`", // fields list
            'field_date' => "PostDate", // date field name
            'field_date_type' => "timestamp", // date field type
            'table' => "`ch_blogs_posts`", // table name
            'join' => "", // join SQL part
            'where' => "AND `PostStatus` = 'approval' AND `allowView` = '" . CH_WSB_PG_ALL . "'", // SQL condition, without WHERE
            'order' => " `PostDate` ASC ", // SQL order, without ORDER BY
        );

        $this->_oModule = ChWsbModule::getInstance('ChBlogsModule');
    }

    protected function _genUrl ($a)
    {
        return $this->_oModule->genUrl($a['PostID'], $a['PostUri']);
    }
}
