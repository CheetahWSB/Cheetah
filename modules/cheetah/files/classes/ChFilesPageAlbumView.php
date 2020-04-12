<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

require_once('ChFilesCmtsAlbums.php');

class ChFilesPageAlbumView extends ChWsbPageView
{
    var $aInfo;
    var $iProfileId;

    var $oTemplate;
    var $oConfig;
    var $oDb;
    var $oModule;

    var $sBrowseCode;

    function __construct($oModule, $aInfo, $sBrowseCode = '')
    {
        parent::__construct('ch_files_album_view');
        $this->aInfo = $aInfo;
        $this->iProfileId = $oModule->_iProfileId;

        $this->oModule = $oModule;
        $this->oConfig = $oModule->_oConfig;
        $this->oDb = $oModule->_oDb;
        $this->oTemplate = $oModule->_oTemplate;

        $this->sBrowseCode = $sBrowseCode;

        if(!empty($aInfo['Caption'])) {
        	$GLOBALS['oTopMenu']->setCustomSubHeader(_t('_sys_album_x', $aInfo['Caption']));
            $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/album/' . $aInfo['Uri'] . '/owner/' . getUsername($aInfo['Owner']));
        }
    }

    function getBlockCode_Objects($iBlockId)
    {
        if(!empty($this->sBrowseCode))
            return $this->sBrowseCode;

        $sClassName = $this->oConfig->getClassPrefix() . 'Search';
        ch_import('Search', $this->oModule->_aModule);
        $oSearch = new $sClassName('album');
        $aParams = array('album' => $this->aInfo['Uri'], 'owner' => getUsername($this->aInfo['Owner']));
        $aCustom = array(
            'enable_center' => true,
            'per_page' => $this->oConfig->getGlParam('number_view_album'),
            'sorting' => 'album_order'
        );
        $aHtml = $oSearch->getBrowseBlock($aParams, $aCustom);
        $iCount = $oSearch->aCurrent['paginate']['totalNum'];
        $sPaginate = '';
        if ($iCount > $oSearch->aCurrent['paginate']['perPage']) {
            $sLink = $this->oConfig->getBaseUri() . 'browse/album/' . $aParams['album'] . '/owner/' . $aParams['owner'];
            $oPaginate = new ChWsbPaginate(array(
                'page_url' => $sLink . '&page={page}&per_page={per_page}',
                'count' => $iCount,
                'per_page' => $oSearch->aCurrent['paginate']['perPage'],
                'page' => $oSearch->aCurrent['paginate']['page'],
                'on_change_per_page' => 'document.location=\'' . CH_WSB_URL_ROOT . $sLink . '&page=1&per_page=\' + this.value;'
            ));
            $sPaginate = $oPaginate->getPaginate();
        }

        if(empty($aHtml['code']))
            $aHtml['code'] = MsgBox(_t('_Empty'));

        return DesignBoxContent(_t('_' . $this->oConfig->getMainPrefix() . '_browse_by_album', $this->aInfo['Caption']), $aHtml['code'], 1, '', $sPaginate);
    }

    function getBlockCode_Actions()
    {
        return $this->oModule->getBlockActionsAlbum($this->aInfo);
    }

    function getBlockCode_Author()
    {
        $aOwner = array('medProfId' => $this->aInfo['Owner'], 'NickName' => getNickName($this->aInfo['Owner']));
        return $this->oTemplate->getFileAuthor($aOwner);
    }

	function getBlockCode_Info()
    {
        return $this->oTemplate->getAlbumInfo($this->aInfo);
    }

    function getBlockCode_Comments()
    {
        $this->oTemplate->addCss('cmts.css');

        $oCmtsView = new ChFilesCmtsAlbums($this->oConfig->getMainPrefix() . '_albums', $this->aInfo['ID']);
        if(!$oCmtsView->isEnabled())
        	return '';

        return $oCmtsView->getCommentsFirst();
    }
}
