<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');

class ChProfileCustomizeExport extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'ch_profile_custom_main' => '`user_id` = {profile_id}',
            'ch_profile_custom_themes' => '`ownerid` = {profile_id}',
        );
        $this->_sFilesBaseDir = 'modules/cheetah/profile_customize/data/images/';
        $this->_aTablesWithFiles = array(
            'ch_profile_custom_main' => array( // table name
                'css' => array ( // field name
                    // prefixes & extensions
                    '' => '',
                    's_' => '',
                ),
            ),
        );
    }

    protected function _getFilePath($sTableName, $sField, $sFileName, $sPrefix, $sExt)
    {
        if (!($a = @unserialize($sFileName)))
            return false;

        $sImg = false;
        foreach ($a as $aa) {
            foreach ($aa as $r) {
                if (isset($r['image'])) {
                    $sImg = $r['image'];
                    break;
                }
            }
        }

        return $this->_sFilesBaseDir . $sPrefix . $sImg . $sExt;
    }
}
