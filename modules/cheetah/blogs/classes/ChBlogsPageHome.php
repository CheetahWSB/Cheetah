<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');
class ChBlogsPageHome extends ChWsbPageView
{
    var $oBlogs;

    function __construct(&$oBlogs)
    {
        parent::__construct('ch_blogs_home');
        $this->oBlogs = &$oBlogs;
    }

    function getBlockCode_Top()
    {
        return $this->oBlogs->GenBlogLists('top', false);
    }

    function getBlockCode_Latest($iBlockId)
    {
        $s = $this->oBlogs->serviceBlogsIndexPage(false, $this->oBlogs->_oConfig->getPerPage('home'));
        return $s ? $s : array(MsgBox(_t('_Empty')));
    }

    function getBlockCode_Calendar($iBlockID, $sContent)
    {
        return $this->oBlogs->GenBlogCalendar(true, $iBlockID, $this->oBlogs->genBlogLink('home'));
    }
}
