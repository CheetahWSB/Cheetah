<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');

class ChAvaExport extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'ch_avatar_images' => '`author_id` = {profile_id}',
        );
        $this->_sFilesBaseDir = 'modules/cheetah/avatar/data/images/';
        $this->_aTablesWithFiles = array(
            'ch_avatar_images' => array( // table name
                'id' => array ( // field name
                    // prefixes & extensions
                    '.jpg',
                    'b.jpg',
                    'i.jpg'
                ),
            ),
        );
    }
}
