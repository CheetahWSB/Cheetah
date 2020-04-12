<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbConfig.php');

class ChSitesConfig extends ChWsbConfig
{
    var $_oDb;
    var $_bAutoapprove;
    var $_bComments;
    var $_sCommentsSystemName;
    var $_bVotes;
    var $_sVotesSystemName;
    var $_iPerPage;

    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);
    }

    function init(&$oDb)
    {
        $this->_oDb = &$oDb;

        $this->_bAutoapprove = $this->_oDb->getParam('ch_sites_autoapproval') == 'on';
        $this->_bComments = $this->_oDb->getParam('ch_sites_comments') == 'on';
        $this->_sCommentsSystemName = "ch_sites";
        $this->_bVotes = $this->_oDb->getParam('ch_sites_votes') == 'on';
        $this->_sVotesSystemName = "ch_sites";
        $this->_iPerPage = (int)$this->_oDb->getParam('ch_sites_per_page');
    }

    function isAutoapprove()
    {
        return $this->_bAutoapprove;
    }

    function isCommentsAllowed()
    {
        return $this->_bComments;
    }

    function getCommentsSystemName()
    {
        return $this->_sCommentsSystemName;
    }

    function isVotesAllowed()
    {
        return $this->_bVotes;
    }

    function getVotesSystemName()
    {
        return $this->_sVotesSystemName;
    }

    function getPerPage()
    {
        return $this->_iPerPage;
    }
}
