<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbTextModule');

require_once('ChNewsCalendar.php');
require_once('ChNewsCmts.php');
require_once('ChNewsVoting.php');
require_once('ChNewsSearchResult.php');
require_once('ChNewsData.php');

/**
 * News module by Cheetah
 *
 * This module is needed to manage site news.
 *
 *
 * Profile's Wall:
 * no spy events
 *
 *
 *
 * Spy:
 * no spy events
 *
 *
 *
 * Memberships/ACL:
 * Doesn't depend on user's membership.
 *
 *
 *
 * Service methods:
 *
 * Get post block.
 * @see ChNewsModule::servicePostBlock
 * ChWsbService::call('news', 'post_block');
 * @note is needed for internal usage.
 *
 * Get edit block.
 * @see ChNewsModule::serviceEditBlock
 * ChWsbService::call('news', 'edit_block', array($mixed));
 * @note is needed for internal usage.
 *
 * Get administration block.
 * @see ChNewsModule::serviceAdminBlock
 * ChWsbService::call('news', 'admin_block', array($iStart, $iPerPage, $sFilterValue));
 * @note is needed for internal usage.
 *
 * Get block with all news ordered by the time of posting.
 * @see ChNewsModule::serviceArchiveBlock
 * ChWsbService::call('news', 'archive_block', array($iStart, $iPerPage));
 * @note is needed for internal usage.
 *
 * Get block with news marked as featured.
 * @see ChNewsModule::serviceFeaturedBlock
 * ChWsbService::call('news', 'featured_block', array($iStart, $iPerPage));
 * @note is needed for internal usage.
 *
 * Get block with news ordered by their rating.
 * @see ChNewsModule::serviceTopRatedBlock
 * ChWsbService::call('news', 'top_rated_block', array($iStart, $iPerPage));
 * @note is needed for internal usage.
 *
 * Get block with all news ordered by their popularity(number of views).
 * @see ChNewsModule::servicePopularBlock
 * ChWsbService::call('news', 'popular_block', array($iStart, $iPerPage));
 * @note is needed for internal usage.
 *
 *
 * Alerts:
 * Alerts type/unit - 'news'
 * The following alerts are rised
 *
 *  post - news is added
 *      $iObjectId - news id
 *      $iSenderId - admin's id
 *
 *  edit - news was modified
 *      $iObjectId - news id
 *      $iSenderId - admin's id
 *
 *  featured - news was marked as featured
 *      $iObjectId - news id
 *      $iSenderId - admin's id
 *
 *  publish - news was published
 *      $iObjectId - news id
 *      $iSenderId - admin's id
 *
 *  unpublish - news was unpublished
 *      $iObjectId - news id
 *      $iSenderId - admin's id
 *
 *  delete - news was deleted
 *      $iObjectId - news id
 *      $iSenderId - admin's id
 *
 *  RSS Feeds.
 *  Standard feed as provided in the boonex version
 *  m/news/act_rss/
 *
 *  New feeds provided by cheetah modification.
 *  0 defaults to the news module setting for The number of items shown in the RSS feed.
 *  or set 0 to the max number of items desired.
 *  m/news/act_rss/0/archive/   Same as the standard above, except you can specifiy item count.
 *  m/news/act_rss/0/featured/
 *  m/news/act_rss/0/top_rated/
 *  m/news/act_rss/0/popular/
 *
 */
class ChNewsModule extends ChWsbTextModule
{
    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);

        //--- Define Membership Actions ---//
        defineMembershipActions(array('news delete'), 'ACTION_ID_');
    }

    /**
     * Service methods
     */
    function serviceNewsRss($iLength = 0, $sType = 'archive')
    {
        return $this->actionRss($iLength, $sType);
    }

    /**
     * Action methods
     */
    function actionGetNews($sSampleType = 'all', $iStart = 0, $iPerPage = 0)
    {
        return $this->actionGetEntries($sSampleType, $iStart, $iPerPage);
    }

    /**
     * Private methods.
     */
    function _createObjectCalendar($iYear, $iMonth)
    {
        return new ChNewsCalendar($iYear, $iMonth, $this->_oDb, $this->_oConfig);
    }
    function _createObjectCmts($iId)
    {
        return new ChNewsCmts($this->_oConfig->getCommentsSystemName(), $iId);
    }
    function _createObjectVoting($iId)
    {
        return new ChNewsVoting($this->_oConfig->getVotesSystemName(), $iId);
    }
    function _isDeleteAllowed($bPerform = false)
    {
        if(!isLogged())
            return false;

        if(isAdmin())
            return true;

        $aCheckResult = checkAction(getLoggedId(), ACTION_ID_NEWS_DELETE, $bPerform);
        return $aCheckResult[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }
}
