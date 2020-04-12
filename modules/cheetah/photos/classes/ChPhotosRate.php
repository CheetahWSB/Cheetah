<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbFilesRate');

require_once('ChPhotosSearch.php');

class ChPhotosRate extends ChWsbFilesRate
{
    function __construct()
    {
        $oMedia = new ChPhotosSearch();
        parent::__construct('ch_photos', $oMedia);
    }

    function getRateFile(&$aData)
    {
        $aImg = $this->oMedia->serviceGetPhotoArray($aData[0]['id'], 'file');
        $iImgWidth = (int)getParam($this->sType . '_file_width');

        $aFile = array(
            'fileBody' => $aImg['file'],
            'infoWidth' => $iImgWidth > 0 ? $iImgWidth + 2: ''
        );

        return $this->oMedia->oTemplate->parseHtmlByName('rate_object_file.html', $aFile);
    }
}
