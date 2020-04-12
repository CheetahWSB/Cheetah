<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbFilesTemplate');
ch_import('ChTemplVotingView');

class ChFilesTemplate extends ChWsbFilesTemplate
{
    /**
     * Constructor
     */
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);
    }

    function getFileViewArea ($aInfo)
    {
    }

    function getBasicFileInfoForm (&$aInfo, $sUrlPref = '')
    {
        $aForm = parent::getBasicFileInfoForm($aInfo, $sUrlPref);

        if(!empty($aInfo['albumCaption']) && !empty($aInfo['albumUri']))
            $aForm['album'] = array(
                'type' => 'value',
                'value' => getLink($aInfo['albumCaption'], $sUrlPref . 'browse/album/' . $aInfo['albumUri'] . '/owner/' . getUsername($aInfo['medProfId'])),
                'caption' => _t('_ch_files_album')
            );

        return $aForm;
    }
}
