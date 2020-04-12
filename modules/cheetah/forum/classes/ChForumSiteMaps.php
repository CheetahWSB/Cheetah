<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbSiteMaps');

define('CH_ORCA_INTEGRATION', 'cheetah');

require_once(CH_DIRECTORY_PATH_ROOT . 'modules/cheetah/forum/inc/header.inc.php');

/**
 * Sitemaps generator for Forum
 */
class ChForumSiteMaps extends ChWsbSiteMaps
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);

        $this->_aQueryParts = array (
            'fields' => "`t`.`topic_id`, `t`.`topic_uri`, `t`.`last_post_when`", // fields list
            'field_date' => "last_post_when", // date field name
            'field_date_type' => "timestamp", // date field type
            'table' => "`ch_forum_topic` AS `t`", // table name
            'join' => " INNER JOIN `ch_forum` AS `f` ON (`f`.`forum_id` = `t`.`forum_id` AND `f`.`forum_type` = 'public')", // join SQL part
            'where' => "AND `t`.`topic_hidden` = 0 AND `t`.`topic_posts` > 0", // SQL condition, without WHERE
            'order' => " `t`.`last_post_when` ASC ", // SQL order, without ORDER BY
        );
    }

    protected function _genUrl ($a)
    {
        return CH_WSB_URL_ROOT . 'forum/' . sprintf($GLOBALS['gConf']['rewrite']['topic'], $a['topic_uri']);
    }

}
