<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbTextModule');

require_once('ChArlCalendar.php');
require_once('ChArlCmts.php');
require_once('ChArlVoting.php');
require_once('ChArlSearchResult.php');
require_once('ChArlData.php');

/**
 * Articles module by Cheetah
 *
 * This module is needed to manage site articles.
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
 * @see ChArlModule::servicePostBlock
 * ChWsbService::call('articles', 'post_block');
 * @note is needed for internal usage.
 *
 * Get edit block.
 * @see ChArlModule::serviceEditBlock
 * ChWsbService::call('articles', 'edit_block', array($mixed));
 * @note is needed for internal usage.
 *
 * Get administration block.
 * @see ChArlModule::serviceAdminBlock
 * ChWsbService::call('articles', 'admin_block', array($iStart, $iPerPage, $sFilterValue));
 * @note is needed for internal usage.
 *
 * Get block with all articles ordered by the time of posting.
 * @see ChArlModule::serviceArchiveBlock
 * ChWsbService::call('articles', 'archive_block', array($iStart, $iPerPage));
 * @note is needed for internal usage.
 *
 * Get block with articles marked as featured.
 * @see ChArlModule::serviceFeaturedBlock
 * ChWsbService::call('articles', 'featured_block', array($iStart, $iPerPage));
 * @note is needed for internal usage.
 *
 * Get block with articles ordered by their rating.
 * @see ChArlModule::serviceTopRatedBlock
 * ChWsbService::call('articles', 'top_rated_block', array($iStart, $iPerPage));
 * @note is needed for internal usage.
 *
 * Get block with all articles ordered by their popularity(number of views).
 * @see ChArlModule::servicePopularBlock
 * ChWsbService::call('articles', 'popular_block', array($iStart, $iPerPage));
 * @note is needed for internal usage.
 *
 *
 * Alerts:
 * Alerts type/unit - 'articles'
 * The following alerts are rised
 *
 *  post - article is added
 *      $iObjectId - article id
 *      $iSenderId - admin's id
 *
 *  edit - article was modified
 *      $iObjectId - article id
 *      $iSenderId - admin's id
 *
 *  featured - article was marked as featured
 *      $iObjectId - article id
 *      $iSenderId - admin's id
 *
 *  publish - article was published
 *      $iObjectId - article id
 *      $iSenderId - admin's id
 *
 *  unpublish - article was unpublished
 *      $iObjectId - article id
 *      $iSenderId - admin's id
 *
 *  delete - article was deleted
 *      $iObjectId - article id
 *      $iSenderId - admin's id
 *
 */
class ChArlModule extends ChWsbTextModule
{
    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);

        //--- Define Membership Actions ---//
        defineMembershipActions(array('articles delete'), 'ACTION_ID_');
    }

    /**
     * Service methods
     */
    function serviceArticlesRss($iLength = 0)
    {
        return $this->actionRss($iLength);
    }

    /**
     * Action methods
     */
    function actionGetArticles($sSampleType = 'all', $iStart = 0, $iPerPage = 0)
    {
        return $this->actionGetEntries($sSampleType, $iStart, $iPerPage);
    }

    /**
     * Private methods.
     */
    function _createObjectCalendar($iYear, $iMonth)
    {
        return new ChArlCalendar($iYear, $iMonth, $this->_oDb, $this->_oConfig);
    }
    function _createObjectCmts($iId)
    {
        return new ChArlCmts($this->_oConfig->getCommentsSystemName(), $iId);
    }
    function _createObjectVoting($iId)
    {
        return new ChArlVoting($this->_oConfig->getVotesSystemName(), $iId);
    }
    function _isDeleteAllowed($bPerform = false)
    {
        if(!isLogged())
            return false;

        if(isAdmin())
            return true;

        $aCheckResult = checkAction(getLoggedId(), ACTION_ID_ARTICLES_DELETE, $bPerform);
        return $aCheckResult[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }
}
