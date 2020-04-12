<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbTwigPageMain');

class ChGroupsPageMain extends ChWsbTwigPageMain
{
    function __construct(&$oMain)
    {
        $this->sSearchResultClassName = 'ChGroupsSearchResult';
        $this->sFilterName = 'ch_groups_filter';
        parent::__construct('ch_groups_main', $oMain);
    }

    function getBlockCode_LatestFeaturedGroup()
    {
        $aDataEntry = $this->oDb->getLatestFeaturedItem ();
        if (!$aDataEntry)
            return false;

        $aAuthor = getProfileInfo($aDataEntry['author_id']);

        $sImageUrl = '';
        $sImageTitle = '';
        $a = array ('ID' => $aDataEntry['author_id'], 'Avatar' => $aDataEntry['thumb']);
        $aImage = ChWsbService::call('photos', 'get_image', array($a, 'file'), 'Search');

        ch_groups_import('Voting');
        $oRating = new ChGroupsVoting ('ch_groups', $aDataEntry['id']);

        $aVars = array (
            'ch_if:image' => array (
                'condition' => !$aImage['no_image'] && $aImage['file'],
                'content' => array (
                    'image_url' => !$aImage['no_image'] && $aImage['file'] ? $aImage['file'] : '',
                    'image_title' => !$aImage['no_image'] && $aImage['title'] ? $aImage['title'] : '',
                    'group_url' => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                ),
            ),
            'group_url' => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
            'group_title' => $aDataEntry['title'],
            'author_title' => _t('_From'),
            'author_username' => getNickName($aAuthor['ID']),
            'author_url' => getProfileLink($aAuthor['ID']),
            'rating' => $oRating->isEnabled() ? $oRating->getJustVotingElement (true, $aDataEntry['id']) : '',
            'fans_count' => $aDataEntry['fans_count'],
            'country_city' => $this->oMain->_formatLocation($aDataEntry, false, true),
        );
        return $this->oTemplate->parseHtmlByName('latest_featured_group', $aVars);
    }

    function getBlockCode_Recent()
    {
        return $this->ajaxBrowse('recent', $this->oDb->getParam('ch_groups_perpage_main_recent'));
    }
}
