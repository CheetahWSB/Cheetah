<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbTextModule');

require_once('ChFdbCmts.php');
require_once('ChFdbVoting.php');
require_once('ChFdbSearchResult.php');
require_once('ChFdbPrivacy.php');
require_once('ChFdbData.php');

/**
 * Feedback module by Cheetah
 *
 * This module is needed to manage user's feedback on the site.
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
 * @see ChFdbModule::servicePostBlock
 * ChWsbService::call('feedback', 'post_block');
 * @note is needed for internal usage.
 *
 * Get edit block.
 * @see ChFdbModule::serviceEditBlock
 * ChWsbService::call('feedback', 'edit_block', array($mixed));
 * @note is needed for internal usage.
 *
 * Get administration block.
 * @see ChFdbModule::serviceAdminBlock
 * ChWsbService::call('feedback', 'admin_block', array($iStart, $iPerPage, $sFilterValue));
 * @note is needed for internal usage.
 *
 * Get block with all feedback from current user.
 * @see ChFdbModule::serviceMyBlock
 * ChWsbService::call('feedback', 'my_block', array($iStart, $iPerPage));
 * @note is needed for internal usage.
 *
 * Get block with all feedback ordered by the time of posting.
 * @see ChFdbModule::serviceArchiveBlock
 * ChWsbService::call('feedback', 'archive_block', array($iStart, $iPerPage));
 * @note is needed for internal usage.
 *
 *
 * Alerts:
 * Alerts type/unit - 'feedback'
 * The following alerts are rised
 *
 *  post - new feedback is added
 *      $iObjectId - feedback id
 *      $iSenderId - feedback's owner
 *
 *  edit - feedback was modified
 *      $iObjectId - feedback id
 *      $iSenderId - feedback's owner
 *
 *  approve - feedback was approved
 *      $iObjectId - feedback id
 *      $iSenderId - admin's id
 *
 *  reject - feedback was rejected
 *      $iObjectId - feedback id
 *      $iSenderId - admin's id
 *
 *  delete - feedback was deleted
 *      $iObjectId - feedback id
 *      $iSenderId - admin's id
 *
 */
class ChFdbModule extends ChWsbTextModule
{
    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);

        //--- Define Membership Actions ---//
        defineMembershipActions(array('feedback delete'), 'ACTION_ID_');
    }

    /**
     * Service methods
     */
    function serviceFeedbackRss($iLength = 0)
    {
        return $this->actionRss($iLength);
    }

    function serviceMyBlock($iStart = 0, $iPerPage = 0)
    {
        if(!$this->isLogged())
            return MsgBox(_t('_feedback_msg_no_results'));

        if(empty($iPerPage))
            $iPerPage = $this->_oConfig->getPerPage();

        return $this->_oTemplate->displayBlock(array(
            'sample_type' => 'owner',
            'sample_params' => array('owner_id' => $this->_oTextData->getAuthorId()),
            'viewer_type' => $this->_oTextData->getViewerType(),
            'start' => $iStart,
            'count' => $iPerPage
        ));
    }

    /**
     * Action methods
     */
    function actionGetFeedback($sSampleType = 'all', $iStart = 0, $iPerPage = 0)
    {
        return $this->actionGetEntries($sSampleType, $iStart, $iPerPage);
    }

    function actionIndex()
    {
        $sMenu = "";
        if(isMember()) {
            $sLink = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'post/';
            $sCaption = _t('_feedback_lcaption_post');

            $sMenu = ChWsbPageView::getBlockCaptionMenu(time(), array(
                'fdb_post' => array('href' => $sLink, 'title' => $sCaption)
            ));
        }
        $sContent = $this->serviceArchiveBlock();

        $aParams = array(
            'index' => 2,
            'css' => array('view.css', 'cmts.css'),
            'title' => array(
                'page' => _t('_feedback_pcaption_all'),
                'block' => _t('_feedback_bcaption_view_all')
            ),
            'content' => array(
                'page_menu_code' => $sMenu,
                'page_main_code' => $sContent
            )
        );
        $this->_oTemplate->getPageCode($aParams);
    }
    function actionPost($sName = '')
    {
  		if (!$this -> _isAllowedPost())
			$this->_oTemplate->displayAccessDenied ();

		$sContentView = DesignBoxContent(_t('_feedback_bcaption_view_my'), $this->serviceMyBlock(), 1);

        if(!empty($sName))
            $sContentForm = $this->serviceEditBlock(process_db_input($sName, CH_TAGS_STRIP));
        else if(isset($_POST['id']))
            $sContentForm = $this->serviceEditBlock((int)$_POST['id']);
        else
            $sContentForm = $this->servicePostBlock();
        $sContentForm = DesignBoxContent(_t('_feedback_bcaption_post'), $sContentForm, 1);

        $aParams = array(
            'index' => 3,
            'css' => array('view.css', 'post.css'),
            'title' => array(
                'page' => _t('_feedback_pcaption_post'),
                'block' => _t('_feedback_bcaption_view_all')
            ),
            'content' => array(
                'page_code_view' => $sContentView,
                'page_code_form' => $sContentForm
            )
        );
        $this->_oTemplate->getPageCode($aParams);
    }

    function actionAdmin($sName = '')
    {
        $GLOBALS['iAdminPage'] = 1;
        require_once(CH_DIRECTORY_PATH_INC . 'admin_design.inc.php');

        check_logged();
        if(!@isAdmin()) {
            send_headers_page_changed();
            login_form("", 1);
            exit;
        }

        //--- Process actions ---//
        $mixedResultSettings = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) {
            $mixedResultSettings = $this->setSettings($_POST);
        }

        if(isset($_POST['feedback-approve']))
            $this->_actPublish($_POST['feedback-ids'], true);
        else if(isset($_POST['feedback-reject']))
            $this->_actPublish($_POST['feedback-ids'], false);
        else if(isset($_POST['feedback-delete']))
            $this->_actDelete($_POST['feedback-ids']);
        //--- Process actions ---//

        $sFilterValue = '';
        if(isset($_GET['feedback-filter']))
            $sFilterValue = process_db_input($_GET['feedback-filter'], CH_TAGS_STRIP);

        $sContent = DesignBoxAdmin(_t('_feedback_bcaption_settings'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $this->getSettingsForm($mixedResultSettings))));
        $sContent .= DesignBoxAdmin(_t('_feedback_bcaption_view_admin'), $this->serviceAdminBlock(0, 0, $sFilterValue));

        $aParams = array(
            'title' => array(
                'page' => _t('_feedback_pcaption_admin')
            ),
            'content' => array(
                'page_main_code' => $sContent
            )
        );
        $this->_oTemplate->getPageCodeAdmin($aParams);
    }

    /**
     * Private methods.
     */
    function _createObjectCmts($iId)
    {
        return new ChFdbCmts($this->_oConfig->getCommentsSystemName(), $iId);
    }
    function _createObjectVoting($iId)
    {
        return new ChFdbVoting($this->_oConfig->getVotesSystemName(), $iId);
    }
    function _isDeleteAllowed($iAuthorId = 0, $bPerform = false)
    {
        if(!isLogged())
            return false;

        if(isAdmin())
            return true;

        $iUserId = getLoggedId();
        if($iAuthorId != 0 && $iAuthorId == $iUserId)
            return true;

        $aCheckResult = checkAction($iUserId, ACTION_ID_FEEDBACK_DELETE, $bPerform);
        return $aCheckResult[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

	function _isAllowedPost()
    {
       return isLogged();
	}

    function _isCommentsAllowed(&$aEntry)
    {
        return $this->_oPrivacy->check('comment', $aEntry['id'], $this->_oTextData->getAuthorId());
    }
    function _isVotesAllowed(&$aEntry)
    {
        return $this->_oPrivacy->check('vote', $aEntry['id'], $this->_oTextData->getAuthorId());
    }
}
