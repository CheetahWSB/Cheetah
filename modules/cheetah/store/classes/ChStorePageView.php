<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbTwigPageView');

class ChStorePageView extends ChWsbTwigPageView
{
    function __construct(&$oMain, &$aDataEntry)
    {
        parent::__construct('ch_store_view', $oMain, $aDataEntry);
    }

    function getBlockCode_Info()
    {
        $sContent = $this->_blockInfo ($this->aDataEntry, $this->_oTemplate->blockFields($this->aDataEntry));
        return array($sContent, array(), array(), false);
    }

    function getBlockCode_Desc()
    {
        $sContent = $this->_oTemplate->blockDesc ($this->aDataEntry);
        return array($sContent, array(), array(), false);
    }

    function getBlockCode_Photo()
    {
        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
    }

    function getBlockCode_Video()
    {
        return $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
    }

    function getBlockCode_Files()
    {
        return $this->_oTemplate->blockFiles ($this->aDataEntry);
    }

    function getBlockCode_Rate()
    {
        ch_store_import('Voting');
        $o = new ChStoreVoting ('ch_store', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled())
            return '';

        $sContent = $o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry));
        return array($sContent, array(), array(), false);
    }

    function getBlockCode_Comments()
    {
        ch_store_import('Cmts');
        $o = new ChStoreCmts ('ch_store', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
    }

    function getBlockCode_Actions()
    {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
        	$sCode = '';

            $oSubscription = ChWsbSubscription::getInstance();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'ch_store', '', (int)$this->aDataEntry['id']);
            $sCode .= $oSubscription->getData();

            $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => (int)$this->aDataEntry['uri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'TitleSubscribe' => $aSubscribeButton['title'],
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_ch_store_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_ch_store_action_title_delete') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_ch_store_action_title_share') : '',
                'TitleBroadcast' => $this->_oMain->isAllowedBroadcast($this->aDataEntry) ? _t('_ch_store_action_title_broadcast') : '',
                'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_ch_store_action_remove_from_featured') : _t('_ch_store_action_add_to_featured')) : '',
                'TitleActivate' => method_exists($this->_oMain, 'isAllowedActivate') && $this->_oMain->isAllowedActivate($this->aDataEntry) ? _t('_ch_store_admin_activate') : '',
            );

            $aInfo['repostCpt'] = $aInfo['repostScript'] = '';
        	if(ChWsbRequest::serviceExists('wall', 'get_repost_js_click')) {
				$sCode .= ChWsbService::call('wall', 'get_repost_js_script');

				$aInfo['repostCpt'] = _t('_Repost');
				$aInfo['repostScript'] = ChWsbService::call('wall', 'get_repost_js_click', array($this->_oMain->_iProfileId, 'ch_store', 'add', (int)$this->aDataEntry['id']));
			}

			$sCodeActions = $oFunctions->genObjectsActions($aInfo, 'ch_store');
            if(empty($sCodeActions))
                return '';

            return $sCode . $sCodeActions;
        }

        return '';
    }

}
