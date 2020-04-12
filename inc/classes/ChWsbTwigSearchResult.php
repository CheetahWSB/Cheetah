<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChTemplSearchResult');

/**
 * Base data search class for modules like events/groups/store
 */
class ChWsbTwigSearchResult extends ChTemplSearchResult
{
    var $oVotingView = null;
    var $iRate = 1;
    var $sBrowseUrl;
    var $isError;
    var $aCurrent = array ();
    var $aGlParamsSettings = array();
    var $sProfileCatType;
    var $sUnitTemplate = 'unit';
    var $sFilterName = 'unit';

    function __construct()
    {
        parent::__construct();
    }

    function getMain()
    {
        // override this to return main module class
    }

    function displaySearchUnit ($aData)
    {
        $oMain = $this->getMain();
        return $oMain->_oTemplate->unit($aData, $this->sUnitTemplate, $this->oVotingView);
    }

    function showPagination($aParams = array())
    {
        $sUrlAdmin = isset($aParams['url_admin']) && !empty($aParams['url_admin']) ? $aParams['url_admin'] : false;

        $oMain = $this->getMain();
        $oConfig = $oMain->_oConfig;
        ch_import('ChWsbPaginate');
        $sUrlStart = CH_WSB_URL_ROOT . $oConfig->getBaseUri() . ($sUrlAdmin ? $sUrlAdmin : $this->sBrowseUrl);
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');

        $oPaginate = new ChWsbPaginate(array(
            'page_url' => $sUrlStart . 'page={page}&per_page={per_page}' . (false !== ch_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . ch_get($this->sFilterName) : ''),
            'count' => $this->aCurrent['paginate']['totalNum'],
            'per_page' => $this->aCurrent['paginate']['perPage'],
            'page' => $this->aCurrent['paginate']['page'],
            'on_change_per_page' => "document.location='" . $sUrlStart . "page=1&per_page=' + this.value + '" . (false !== ch_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . ch_get($this->sFilterName) ."';": "';"),
        ));

        return '<div class="clear_both"></div>'.$oPaginate->getPaginate();
    }

    function setPublicUnitsOnly($isPublic)
    {
        $this->aCurrent['restriction']['public']['value'] = $isPublic ? CH_WSB_PG_ALL : false;
    }

    function showPaginationAjax($sBlockId)
    {
        $oMain = $this->getMain();
        $oConfig = $oMain->_oConfig;
        ch_import('ChWsbPaginate');
        $sUrlStart = CH_WSB_URL_ROOT . $oConfig->getBaseUri() . $this->sBrowseUrl;
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');

        $oPaginate = new ChWsbPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $this->aCurrent['paginate']['totalNum'],
            'per_page' => $this->aCurrent['paginate']['perPage'],
            'page' => $this->aCurrent['paginate']['page'],
            'on_change_page' => "getHtmlData('{$sBlockId}', '{$sUrlStart}page={page}&per_page={per_page}&block={$sBlockId}" . (false !== ch_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . ch_get($this->sFilterName) : '') . "');",
        ));

        return $oPaginate->getSimplePaginate(false, -1, -1, false);
    }

    function rss ()
    {
        $this->setPublicUnitsOnly(true);
        return parent::rss();
    }

    function getRssUnitImage (&$a, $sField)
    {
        $aImage = array ('ID' => $a['author_id'], 'Avatar' => $a[$sField]);
        $aImage = ChWsbService::call('photos', 'get_image', array($aImage, 'browse'), 'Search');

        return $aImage['no_image'] ? '' : $aImage['file'];
    }
}
