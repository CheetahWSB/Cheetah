<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChEventsPageMy extends ChWsbPageView
{
    var $_oMain;
    var $_oTemplate;
    var $_oConfig;
    var $_oDb;
    var $_aProfile;

    function __construct(&$oMain, &$aProfile)
    {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oConfig = $oMain->_oConfig;
        $this->_oDb = $oMain->_oDb;
        $this->_aProfile = &$aProfile;
        parent::__construct('ch_events_my');
    }

    function getBlockCode_Owner()
    {
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch (ch_get('ch_events_filter')) {
        case 'add_event':
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_events':
            $sContent = $this->getBlockCode_Manage ();
            break;
        case 'pending_events':
            $sContent = $this->getBlockCode_Pending ();
            break;
        default:
            $sContent = $this->getBlockCode_Main ();
        }

        $sBaseUrl = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aMenu = array(
            _t('_ch_events_block_submenu_main') => array('href' => $sBaseUrl, 'active' => !ch_get('ch_events_filter')),
            _t('_ch_events_block_submenu_add') => array('href' => $sBaseUrl . '&ch_events_filter=add_event', 'active' => 'add_event' == ch_get('ch_events_filter')),
            _t('_ch_events_block_submenu_manage') => array('href' => $sBaseUrl . '&ch_events_filter=manage_events', 'active' => 'manage_events' == ch_get('ch_events_filter')),
            _t('_ch_events_block_submenu_pending') => array('href' => $sBaseUrl . '&ch_events_filter=pending_events', 'active' => 'pending_events' == ch_get('ch_events_filter')),
        );
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse()
    {
        ch_events_import ('SearchResult');
        $o = new ChEventsSearchResult('user', process_db_input ($this->_aProfile['NickName'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION));
        $o->aCurrent['rss'] = 0;

        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_ch_events_block_my_events');

        if ($o->isError) {
            return MsgBox(_t('_Empty'));
        }

        if ($s = $o->processing()) {
            $this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
            return $s;
        } else {
            return DesignBoxContent(_t('_ch_events_block_user_events'), MsgBox(_t('_Empty')), 1);
        }
    }

    function getBlockCode_Main()
    {
        $iActive = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        if ($iPending)
            $aVars['msg'] = sprintf(_t('_ch_events_msg_you_have_pending_approval_events'), $sBaseUrl . '&ch_events_filter=pending_events', $iPending);
        elseif (!$iActive)
            $aVars['msg'] = sprintf(_t('_ch_events_msg_you_have_no_events'), $sBaseUrl . '&ch_events_filter=add_event');
        else
            $aVars['msg'] = sprintf(_t('_ch_events_msg_you_have_some_events'), $sBaseUrl . '&ch_events_filter=manage_events', $iActive, $sBaseUrl . '&ch_events_filter=add_event');
        return $this->_oTemplate->parseHtmlByName('my_events_main', $aVars);
    }

    function getBlockCode_Add()
    {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
        $this->_oMain->_addForm(CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my');
        $aVars = array ('form' => ob_get_clean());
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_events_create_event', $aVars);
    }

    function getBlockCode_My()
    {
        return $this->getBlockCode_Manage();
    }

    function getBlockCode_Manage()
    {
        $sForm = $this->_oMain->_manageEntries ('user', process_db_input ($this->_aProfile['NickName'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION), false, 'ch_events_my_active', array(
                'action_delete' => '_ch_events_admin_delete',
        ), 'ch_events_my_active', 7);
        $aVars = array ('form' => $sForm, 'id' => 'ch_events_my_active');
        return $this->_oTemplate->parseHtmlByName('my_events_manage', $aVars);
    }

    function getBlockCode_Pending()
    {
        $sForm = $this->_oMain->_manageEntries ('my_pending', '', false, 'ch_events_my_pending', array(
                'action_delete' => '_ch_events_admin_delete',
        ), 'ch_events_my_pending', 7);
        $aVars = array ('form' => $sForm, 'id' => 'ch_events_my_pending');
        return $this->_oTemplate->parseHtmlByName('my_events_manage', $aVars);
    }
}
