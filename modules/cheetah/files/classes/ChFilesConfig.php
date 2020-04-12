<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbFilesConfig.php');

class ChFilesConfig extends ChWsbFilesConfig
{
    var $_oDb;
    var $_aMimeTypes;
    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);

        $this->aFilesConfig = array (
            'original' => array('postfix' => '_{ext}'),
        );

        $this->aGlParams = array(
            'auto_activation' => 'ch_files_activation',
            'mode_top_index' => 'ch_files_mode_index',
            'category_auto_approve' => 'category_auto_activation_ch_files',
            'browse_width' => 'ch_files_thumb_width',
        );

        $this->_aMimeTypes = array();

        $this->initConfig();
    }

    function init(&$oDb)
    {
        $this->_oDb = $oDb;

        $this->_aMimeTypes = $this->_oDb->getTypeToIconArray();
    }

    function getMimeTypeIcon($sType)
    {
        if(isset($this->_aMimeTypes[$sType]))
            return $this->_aMimeTypes[$sType];

        return 'default.png';
    }
}
