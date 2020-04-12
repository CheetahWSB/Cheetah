<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbTextConfig');

class ChNewsConfig extends ChWsbTextConfig
{
    function __construct($aModule)
    {
        parent::__construct($aModule);
    }
    function init(&$oDb)
    {
        parent::init($oDb);

        $sUri = $this->getUri();
        $sName = 'ch_' . $sUri;

        $this->_bAutoapprove = $this->_oDb->getParam('news_autoapprove') == 'on';
        $this->_bComments = $this->_oDb->getParam('news_comments') == 'on';
        $this->_sCommentsSystemName = $sName;
        $this->_bVotes = $this->_oDb->getParam('news_votes') == 'on';
        $this->_sVotesSystemName = $sName;
        $this->_sViewsSystemName = $sName;
        $this->_sSubscriptionsSystemName = $sName;
        $this->_sActionsViewSystemName = $sName;
        $this->_sCategoriesSystemName = $sName;
        $this->_sTagsSystemName = $sName;
        $this->_sAlertsSystemName = $sName;
        $this->_sSearchSystemName = $sName;
        $this->_sDateFormat = getLocaleFormat(CH_WSB_LOCALE_DATE_SHORT, CH_WSB_LOCALE_DB);
        $this->_sAnimationEffect = 'fade';
        $this->_iAnimationSpeed = 'slow';
        $this->_iIndexNumber = (int)$this->_oDb->getParam('news_index_number');
        $this->_iMemberNumber = (int)$this->_oDb->getParam('news_member_number');
        $this->_iSnippetLength = 1000;
        $this->_iPerPage = (int)$this->_oDb->getParam('news_per_page');
        $this->_sSystemPrefix = 'news';
        $this->_aJsClasses = array('main' => 'ChNewsMain');
        $this->_aJsObjects = array('main' => 'oNewsMain');
        $this->_iRssLength = (int)$this->_oDb->getParam('news_rss_length');
    }
}
