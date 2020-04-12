<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbConfig.php');

class ChPollConfig extends ChWsbConfig
{
    var $sUploadPath ;

    var $iAlowMembersPolls;

    // contain number of allowed member's pools;
    var $iAlowPollNumber;

    // allow or disallow the auto activation for polls;
    var $iAutoActivate;

    // contain number of visible profile polls on profile page ;
    var $iProfilePagePollsCount;

    // contain number of visible profile polls on index page ;
    var $iIndexPagePollsCount;

    // contain Db table's name ;
    var $sTableName;

    // contain Db table's name ;
    var $sTablePrefix;

    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);

        // get allowed members polls;
        $this -> iAlowMembersPolls =  getParam( 'enable_poll' );

        // get allowed number of polls;
        $this -> iAlowPollNumber =  getParam( 'profile_poll_num' );

        // chew poll's auto activation;
        $this -> iAutoActivate = getParam( 'profile_poll_act' ) == 'on' ? 1 : 0;

        $this -> iProfilePagePollsCount = getParam( 'profile_page_polls' );
        $this -> iIndexPagePollsCount   = getParam( 'index_page_polls' );

        // define the table name ;
        $this -> sTableName = $this -> getDbPrefix() . 'data';

        // define the prefix ;
        $this -> sTablePrefix = $this -> getDbPrefix();
    }
}
