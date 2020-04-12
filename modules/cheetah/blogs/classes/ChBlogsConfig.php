<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbConfig.php');

class ChBlogsConfig extends ChWsbConfig
{
    var $_iAnimationSpeed;

    var $sUserExFile;
    var $sAdminExFile;
    var $sUserExPermalink;

    var $iPerPageElements;
    var $iPerPageElementsHome;
    var $iPerPageElementsProfile;
    var $iPerPageElementsIndex;

    var $iTopTagsCnt;

    // SQL tables
    var $sSQLCategoriesTable;
    var $sSQLPostsTable;
    var $sSQLBlogsTable;

    var $_sCommentSystemName;
    var $_sRateSystemName;
    var $_sViewSystemName;

    /*
    * Constructor.
    */
    function __construct($aModule)
    {
        parent::__construct($aModule);

        $this->_iAnimationSpeed = 'normal';

        $this->sUserExFile = 'blogs.php';
        $this->sAdminExFile = 'post_mod_blog.php';
        $this->sUserExPermalink = 'blogs/';

        $this->iTopTagsCnt = 20;

        $this->iPerPageElements = (int)getParam('blog_step');
        $this->iPerPageElementsHome = (int)getParam('max_blogs_on_home');
        $this->iPerPageElementsProfile = (int)getParam('max_blogs_on_profile');
        $this->iPerPageElementsIndex = (int)getParam('max_blogs_on_index');

        $this->sSQLCategoriesTable = 'sys_categories';
        $this->sSQLPostsTable = 'ch_blogs_posts';
        $this->sSQLBlogsTable = 'ch_blogs_main';

        $this->_sCommentSystemName = $this -> _sRateSystemName = $this -> _sViewSystemName = 'ch_blogs';
    }

    function getRateSystemName()
    {
        return $this->_sRateSystemName;
    }

    function getCommentSystemName()
    {
        return $this->_sCommentSystemName;
    }

    function getViewSystemName()
    {
        return $this->_sViewSystemName;
    }

    function getPerPage($sType = '')
    {
        $iResult = 10;

        switch($sType) {
            case 'index':
                $iResult = $this->iPerPageElementsIndex;
                break;
            case 'home':
                $iResult = $this->iPerPageElementsHome;
                break;
            case 'profile':
                $iResult = $this->iPerPageElementsProfile;
                break;
            default:
                $iResult = $this->iPerPageElements;
        }

        return $iResult;
    }
}
