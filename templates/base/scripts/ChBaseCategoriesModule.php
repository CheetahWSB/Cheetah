<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');
ch_import('ChBaseCategories');

class ChBaseCategoriesModule extends ChWsbPageView
{
    var $_sPage;
    var $_sTitle;
    var $_sUrl;
    var $_aParam;

    function __construct($aParam, $sTitle, $sUrl)
    {
        $this->_sPage = 'categ_module';
        $this->_sTitle = $sTitle ? $sTitle : _t('_categ_users');
        $this->_sUrl = $sUrl;
        $this->_aParam = $aParam;
        parent::__construct($this->_sPage);
    }

    function getBlockCode_Common($iBlockId, $isDisableOrderPanel = false)
    {
        $oCateg = new ChBaseCategories();
        $oCateg->getTagObjectConfig();
        $aParam = array(
            'type' => $this->_aParam['type'],
            'common' => true
        );

        return $oCateg->display($aParam, $iBlockId, '', !(boolean)$isDisableOrderPanel, 1, $this->_sUrl);
    }

    function getBlockCode_All($iBlockId)
    {
        $oCateg = new ChBaseCategories();
        $oCateg->getTagObjectConfig();
        $this->_aParam['common'] = false;

        return array(
            $oCateg->display($this->_aParam, $iBlockId, '', true, getParam('categ_show_columns'), $this->_sUrl),
            array(),
            array(),
            $this->_sTitle
        );
    }
}
