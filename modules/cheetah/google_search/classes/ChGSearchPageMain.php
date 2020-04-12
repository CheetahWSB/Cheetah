<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');

class ChGSearchPageMain extends ChWsbPageView
{
    var $_oTemplate;
    var $_oConfig;

    function __construct(&$oModule)
    {
        $this->_oTemplate = $oModule->_oTemplate;
        $this->_oConfig = $oModule->_oConfig;
        parent::__construct('ch_gsearch');
    }

    function getBlockCode_SearchForm()
    {
        $aVars = array ();
        return array($this->_oTemplate->parseHtmlByName('search_form', $aVars));
    }

    function getBlockCode_SearchResults()
    {
        $aVars = array (
            'msg' => !getParam('ch_gsearch_id') ? MsgBox(_t('_ch_gsearch_no_search_engine_id')) : '',
            'cx' => getParam('ch_gsearch_id'),
        );
        return array($this->_oTemplate->parseHtmlByName('search', $aVars));
    }
}
