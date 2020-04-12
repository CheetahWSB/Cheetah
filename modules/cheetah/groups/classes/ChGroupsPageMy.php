<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChGroupsPageMy extends ChWsbPageView
{
    var $_oMain;
    var $_oTemplate;
    var $_oDb;
    var $_oConfig;
    var $_aProfile;

    function __construct(&$oMain, &$aProfile)
    {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->_aProfile = $aProfile;
        parent::__construct('ch_groups_my');
    }

    function getBlockCode_Owner()
    {
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch (ch_get('ch_groups_filter')) {
        case 'add_group':
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_groups':
            $sContent = $this->getBlockCode_My ();
            break;
        case 'pending_groups':
            $sContent = $this->getBlockCode_Pending ();
            break;
        default:
            $sContent = $this->getBlockCode_Main ();
        }

        $sBaseUrl = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aMenu = array(
            _t('_ch_groups_block_submenu_main') => array('href' => $sBaseUrl, 'active' => !ch_get('ch_groups_filter')),
            _t('_ch_groups_block_submenu_add_group') => array('href' => $sBaseUrl . '&ch_groups_filter=add_group', 'active' => 'add_group' == ch_get('ch_groups_filter')),
            _t('_ch_groups_block_submenu_manage_groups') => array('href' => $sBaseUrl . '&ch_groups_filter=manage_groups', 'active' => 'manage_groups' == ch_get('ch_groups_filter')),
            _t('_ch_groups_block_submenu_pending_groups') => array('href' => $sBaseUrl . '&ch_groups_filter=pending_groups', 'active' => 'pending_groups' == ch_get('ch_groups_filter')),
        );
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse()
    {
        ch_groups_import ('SearchResult');
        $o = new ChGroupsSearchResult('user', process_db_input ($this->_aProfile['NickName'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION));
        $o->aCurrent['rss'] = 0;

        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_ch_groups_page_title_my_groups');

        if ($o->isError) {
            return DesignBoxContent(_t('_ch_groups_block_users_groups'), MsgBox(_t('_Empty')), 1);
        }

        if ($s = $o->processing()) {
            $this->_oTemplate->addCss (array('unit.css', 'twig.css', 'main.css'));
            return $s;
        } else {
            return DesignBoxContent(_t('_ch_groups_block_users_groups'), MsgBox(_t('_Empty')), 1);
        }
    }

    function getBlockCode_Main()
    {
        $iActive = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        if ($iPending)
            $aVars['msg'] = sprintf(_t('_ch_groups_msg_you_have_pending_approval_groups'), $sBaseUrl . '&ch_groups_filter=pending_groups', $iPending);
        elseif (!$iActive)
            $aVars['msg'] = sprintf(_t('_ch_groups_msg_you_have_no_groups'), $sBaseUrl . '&ch_groups_filter=add_group');
        else
            $aVars['msg'] = sprintf(_t('_ch_groups_msg_you_have_some_groups'), $sBaseUrl . '&ch_groups_filter=manage_groups', $iActive, $sBaseUrl . '&ch_groups_filter=add_group');
        return $this->_oTemplate->parseHtmlByName('my_groups_main', $aVars);
    }

    function getBlockCode_Add()
    {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
        $this->_oMain->_addForm(CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my');
        $aVars = array ('form' => ob_get_clean(), 'id' => '');
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_groups_create_group', $aVars);
    }

    function getBlockCode_Pending()
    {
        $sForm = $this->_oMain->_manageEntries ('my_pending', '', false, 'ch_groups_pending_user_form', array(
            'action_delete' => '_ch_groups_admin_delete',
        ), 'ch_groups_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'ch_groups_my_pending');
        return $this->_oTemplate->parseHtmlByName('my_groups_manage', $aVars);
    }

    function getBlockCode_My()
    {
        $sForm = $this->_oMain->_manageEntries ('user', process_db_input ($this->_aProfile['NickName'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION), false, 'ch_groups_user_form', array(
            'action_delete' => '_ch_groups_admin_delete',
        ), 'ch_groups_my_active', true, 7);
        $aVars = array ('form' => $sForm, 'id' => 'ch_groups_my_active');
        return $this->_oTemplate->parseHtmlByName('my_groups_manage', $aVars);
    }
}
