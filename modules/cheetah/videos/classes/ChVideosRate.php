<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbFilesRate');

require_once('ChVideosSearch.php');

class ChVideosRate extends ChWsbFilesRate
{
    function __construct()
    {
        $oMedia = new ChVideosSearch();
        $oMedia->aCurrent['ownFields'][] = 'Video';
        $oMedia->aCurrent['ownFields'][] = 'Source';

        parent::__construct('ch_videos', $oMedia);
    }

    function getRateFile(&$aData)
    {
        return $this->oMedia->oTemplate->getFileConcept($aData[0]['id'], array('ext'=>$aData[0]['Video'], 'source'=>$aData[0]['Source']));
    }
}
