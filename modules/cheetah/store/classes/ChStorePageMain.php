<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbTwigPageMain');

class ChStorePageMain extends ChWsbTwigPageMain
{
    function __construct(&$oMain)
    {
        $this->sSearchResultClassName = 'ChStoreSearchResult';
        $this->sFilterName = 'ch_store_filter';
        parent::__construct('ch_store_main', $oMain);
    }

    function getBlockCode_LatestFeaturedProduct()
    {
        $aDataEntry = $this->oDb->getLatestFeaturedItem ();
        if (!$aDataEntry)
            return false;

        $aAuthor = getProfileInfo($aDataEntry['author_id']);

        $sImageUrl = '';
        $sImageTitle = '';
        $a = array ('ID' => $aDataEntry['author_id'], 'Avatar' => $aDataEntry['thumb']);
        $aImage = ChWsbService::call('photos', 'get_image', array($a, 'file'), 'Search');

        ch_store_import('Voting');
        $oRating = new ChStoreVoting ('ch_store', $aDataEntry['id']);

        $aVars = array (
            'ch_if:image' => array (
                'condition' => !$aImage['no_image'] && $aImage['file'],
                'content' => array (
                    'image_url' => !$aImage['no_image'] && $aImage['file'] ? $aImage['file'] : '',
                    'image_title' => !$aImage['no_image'] && $aImage['title'] ? $aImage['title'] : '',
                    'product_url' => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                ),
            ),
            'product_url' => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
            'product_title' => $aDataEntry['title'],
            'author_title' => _t('_From'),
            'author_username' => getNickName($aAuthor['ID']),
            'author_url' => getProfileLink($aAuthor['ID']),
            'rating' => $oRating->isEnabled() ? $oRating->getJustVotingElement (true, $aDataEntry['id']) : '',
            'created' => defineTimeInterval($aDataEntry['created']),
            'price_range' => $this->oMain->_formatPriceRange($aDataEntry),
        );
        return $this->oTemplate->parseHtmlByName('latest_featured_product', $aVars);
    }

    function getBlockCode_Recent()
    {
        return $this->ajaxBrowse('recent', $this->oDb->getParam('ch_store_perpage_main_recent'));
    }
}
