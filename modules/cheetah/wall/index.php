<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('Module', $aModule);
ch_import('ChTemplProfileView');
ch_import('ChTemplProfileGenerator');
ch_import('ChTemplConfig');

class ChWallPage extends ChTemplProfileView
{
    var $_sOwner;
    var $_oWall;

    function __construct($sOwner, &$oWall)
    {
        $this->_sOwner = $sOwner;
        $this->_oWall = &$oWall;

        $this->oProfileGen = new ChTemplProfileGenerator(getId($sOwner, 0));
        $this->aConfSite = $GLOBALS['site'];
        $this->aConfDir  = $GLOBALS['dir'];
        ChWsbPageView::__construct('wall');
    }
    function getBlockCode_Post()
    {
    	$sResult = '';

        if(!empty($this->_sOwner))
            $sResult = $this->_oWall->servicePostBlockProfileTimeline($this->_sOwner, 'username');
		else if(isLogged())
			$sResult = $this->_oWall->servicePostBlockProfileTimeline(getLoggedId());

		return !empty($sResult) ? $sResult : MsgBox(_t('_wall_msg_no_results'));
    }
    function getBlockCode_View()
    {
    	$sResult = '';

        if(!empty($this->_sOwner))
            $sResult = $this->_oWall->serviceViewBlockProfileTimeline($this->_sOwner, -1, -1, '', '', 'username');
        else if(isLogged())
            $sResult = $this->_oWall->serviceViewBlockProfileTimeline(getLoggedId());

        return !empty($sResult) ? $sResult : MsgBox(_t('_wall_msg_no_results'));
    }
    function getCode()
    {
        if(!empty($this->_sOwner)) {
            $aOwner = $this->_oWall->_oDb->getUser($this->_sOwner, 'username');
            if((int)$aOwner['id'] == 0)
                return MsgBox(_t('_wall_msg_page_not_found'));
        }

        return parent::getCode();
    }
}

global $_page;
global $_page_cont;

$iIndex = 1;
$_page['name_index'] = $iIndex;
$_page['css_name'] = 'cmts.css';
$_page['js_name'] = 'ChWsbCmts.js';
$_page['header'] = _t('_wall_page_caption');

$oSubscription = ChWsbSubscription::getInstance();
$oWall = new ChWallModule($aModule);
$sOwnerUsername = isset($aRequest[0]) ? process_db_input($aRequest[0], CH_TAGS_STRIP) : '';
$oWallPage = new ChWallPage($sOwnerUsername, $oWall);
$_page_cont[$iIndex]['page_main_code'] = $oSubscription->getData() . $oWallPage->getCode();

$oWall->_oTemplate->setPageTitle((!empty($sOwnerUsername) ? _t('_wall_page_caption', ucfirst($sOwnerUsername)) : _t('_wall_page_caption_my')) );
PageCode($oWall->_oTemplate);
