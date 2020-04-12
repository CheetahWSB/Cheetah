<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbPageView.php');

class ChSitesPageProfile extends ChWsbPageView
{
    var $_oSites;
    var $_oDb;
    var $_oTemplate;
    var $_oConfig;
    var $_sSubMenu;
    var $_aProfile;

    function __construct(&$oSites, $aProfile, $sSubMenu)
    {
        parent::__construct('ch_sites_profile');

        $GLOBALS['oTopMenu']->setCurrentProfileNickName($aProfile['NickName']);
        $this->_oSites = &$oSites;
        $this->_oDb = $oSites->_oDb;
        $this->_oTemplate = $oSites->_oTemplate;
        $this->_oConfig = $oSites->_oConfig;
        $this->_aProfile = $aProfile;
        $this->_sSubMenu = $sSubMenu;
    }

    function getBlockCode_Administration()
    {
        $sContent = '';
        $bPadding = true;
        switch ($this->_sSubMenu) {
            case 'add':
                $sContent = $this->getBlockCode_Add();
                break;

            case 'manage':
                $sContent = $this->getBlockCode_Manage();
                $bPadding = false;
                break;

            case 'pending':
                $sContent = $this->getBlockCode_Pending();
                $bPadding = false;
                break;

            default:
                $sContent = $this->getBlockCode_Main();
        }

        $sBaseUrl = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my';

        $aMenu = array(
            _t('_ch_sites_block_submenu_main') => array('href' => $sBaseUrl, 'active' => !$this->_sSubMenu),
            _t('_ch_sites_block_submenu_add_site') => array('href' => $sBaseUrl . '/add', 'active' => $this->_sSubMenu == 'add'),
            _t('_ch_sites_block_submenu_manage_sites') => array('href' => $sBaseUrl . '/manage', 'active' => $this->_sSubMenu == 'manage'),
            _t('_ch_sites_block_submenu_pending_sites') => array('href' => $sBaseUrl . '/pending', 'active' => $this->_sSubMenu == 'pending'),
        );

        return array($sContent, $aMenu, '', $bPadding ? false : '');
    }

    function getBlockCode_Owner()
    {
        ch_sites_import('SearchResult');
        $oSearchResult = new ChSitesSearchResult('user', process_db_input($this->_aProfile['NickName'], CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION));

        if ($s = $oSearchResult->displayResultBlock(true))
            return $s;
        else
            return MsgBox(_t('_Empty'));
    }

    function getBlockCode_Main()
    {
        $iActive = $this->_oDb->getCountByOwnerAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByOwnerAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');

        if ($iActive)
            $sActive = sprintf(_t('_ch_sites_msg_you_have_active_sites'), $sBaseUrl . '/manage', $iActive);
        if ($iPending)
            $sPending = ($iActive ? ', ' : '') . sprintf(_t('_ch_sites_msg_you_have_pending_sites'), $sBaseUrl . '/pending', $iPending);

        if (isset($sActive) || isset($sPending))
            $aVars['msg'] = sprintf(_t('_ch_sites_msg_you_have_sites'),
                isset($sActive) ? $sActive : '', isset($sPending) ? $sPending : '');
        else
            $aVars['msg'] = _t('_ch_sites_msg_no_sites');

        if ($this->_oSites->isAllowedAdd())
            $aVars['msg'] .= (strlen($aVars['msg']) ? ' ' : '') . sprintf(_t('_ch_sites_msg_add_more_sites'), $sBaseUrl . '/add');

        return $this->_oTemplate->parseHtmlByName('my_sites_main.html', $aVars);

    }

    function getBlockCode_Add()
    {
        if ($this->_oSites->isAllowedAdd())
            return $this->_oSites->_addSiteForm();
        else
            return MsgBox(_t('_ch_sites_msg_access_denied'));
    }

    function getBlockCode_Manage()
    {
        // check delete sites
        if ($_POST['action_delete'] && is_array($_POST['entry']))
            foreach ($_POST['entry'] as $iSiteId)
                $this->_oSites->deleteSite($iSiteId);
        // refresh sites thumbnail
        if ($_POST['action_refresh_thumb'] && is_array($_POST['entry']))
            foreach ($_POST['entry'] as $iSiteId)
                $this->_oSites->refreshSiteThumb($iSiteId);

        $aButtons = array(
            'action_delete' => '_ch_sites_admin_delete',
        );

        if (getParam('ch_sites_redo') == 'on' && getParam('ch_sites_account_type') == 'Enabled') {
            $aButtons['action_refresh_thumb'] = '_ch_sites_admin_refresh_thumb';
        }

        $sForm = $this->_oSites->_manageSites('user', $this->_aProfile['NickName'], $aButtons);
        $aVars = array ('form' => $sForm);

        return $this->_oTemplate->parseHtmlByName('my_sites_manage.html', $aVars);
    }

    function getBlockCode_Pending()
    {
        // check delete sites
        if ($_POST['action_delete'] && is_array($_POST['entry']))
            foreach ($_POST['entry'] as $iSiteId)
                $this->_oSites->deleteSite($iSiteId);

        $aButtons = array(
            'action_delete' => '_ch_sites_admin_delete'
        );
        $sForm = $this->_oSites->_manageSites('my_pending', '', $aButtons);
        $aVars = array ('form' => $sForm);

        return $this->_oTemplate->parseHtmlByName('my_sites_manage.html', $aVars);
    }
}
