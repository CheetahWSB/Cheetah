<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbFilesPageAlbumsOwner.php');

class ChPhotosPageAlbumsOwner extends ChWsbFilesPageAlbumsOwner
{
    function __construct(&$oShared, $aParams = array())
    {
        parent::__construct('ch_photos_albums_owner', $oShared, $aParams);
    }

    function getBlockCode_ProfilePhotos()
    {
        list($sParamName, $sParamValue, $sParamValue1, $sParamValue2, $sParamValue3) = $this->aAddParams;
        if($sParamValue != 'owner')
            return '';

        $oSearch = $this->getSearchObject();
        $oSearch->aCurrent['restriction']['album'] = array(
            'value'=>'', 'field'=>'Uri', 'operator'=>'=', 'paramName'=>'albumUri', 'table'=>'sys_albums'
        );

        $oSearch->aCurrent['restriction']['album_owner'] = array(
            'value'=>'', 'field'=>'Owner', 'operator'=>'=', 'paramName'=>'albumOwner', 'table'=>'sys_albums'
        );

        $sUri = ChWsbAlbums::getAbumUri($this->oConfig->getGlParam('profile_album_name'), $this->iOwnerId);
        $aParams = array('album' => $sUri, 'owner' => $this->iOwnerId);
        $aCustom = array(
            'per_page' => $this->oConfig->getGlParam('number_top'),
            'simple_paginate' => FALSE
        );
        $aHtml = $oSearch->getBrowseBlock($aParams, $aCustom);
        return array($aHtml['code'], $aHtml['menu_top'], $aHtml['menu_bottom'], '');
    }
}
