<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbModule');
ch_import('ChTemplSearchResult');

class ChSctrSearchResult extends ChTemplSearchResult
{
    var $aCurrent = array(
        'name' => 'ch_sctr',
        'title' => '_ch_sctr',
        'table' => 'ch_sctr_units',
        'ownFields' => array('id', 'name', 'caption', 'css_name', 'type'),
        'searchFields' => array(),
        'restriction' => array(
            'type' => array('value' => '', 'field' => 'type', 'operator' => '='),
        ),
        'ident' => 'id'
    );
    var $aPermalinks;

    var $_oModule;
    var $_sType;

    function __construct($sType, $oModule = null)
    {
        parent::__construct();

        if(!empty($oModule))
            $this->_oModule = $oModule;
        else
            $this->_oModule = &ChWsbModule::getInstance('ChSctrModule');

        $this->aCurrent['restriction']['type']['value'] = $sType;
        $this->_sType = $sType;
    }

    function displaySearchUnit($aData)
    {
        return $this->_oModule->_oTemplate->parseHtmlByName('admin_unit.html', array(
            'caption' => $aData['caption'],
            'value' => $aData['id'],
            'edit_url' => CH_WSB_URL_ROOT . $this->_oModule->_oConfig->getBaseUri() . 'administration/' . $this->_sType . '/' . $aData['id'],
            'edit_str' => _t('_ch_sctr_edit')
        ));
    }

    function displayResultBlock()
    {
        $sResult = parent::displayResultBlock();

        return $sResult;
    }

    function getAlterOrder ()
    {
        return array(
            'order' => " ORDER BY `id`"
        );
    }
}
