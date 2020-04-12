<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChTemplSearchResultText');

class ChWsbPrivacySearch extends ChTemplSearchResultText
{
    var $_sSearchUnitTmpl;
    var $aCurrent = array(
        'name' => 'ps_search',
        'title' => '_ps_search_object',
        'table' => 'Profiles',
        'ownFields' => array('ID', 'DateReg'),
        'searchFields' => array('NickName', 'City', 'DescriptionMe', 'Tags'),
        'restriction' => array(
            'active' => array('value' => 'Active', 'field' => 'Status', 'operator' => '='),
            'owner' => array('value' => '', 'field' => 'ID', 'operator' => '!='),
            'keyword' => array('value' => '', 'field' => '', 'operator' => 'against')
        ),
        'paginate' => array(
            'totalNum' => 0,
            'totalPages' => 0,
            'perPage' => 1000000
        )
    );

    function __construct($iOwnerId, $sValue)
    {
        parent::__construct();

        global $oSysTemplate;

        $this->aCurrent['restriction']['owner']['value'] = $iOwnerId;
        $this->aCurrent['restriction']['keyword']['value'] = process_db_input($sValue, CH_TAGS_STRIP);

        $this->_sSearchUnitTmpl = $oSysTemplate->getHtml('ps_search_unit.html');
    }

    function displaySearchUnit($aData)
    {
        global $oSysTemplate;

        return $oSysTemplate->parseHtmlByContent($this->_sSearchUnitTmpl, array(
            'action' => 'add',
            'member_id' => $aData['id'],
            'member_thumbnail' => get_member_thumbnail($aData['id'], 'none', true)
        ));
    }

    function displayResultBlock()
    {
        $sResult = parent::displayResultBlock();

        if(empty($sResult))
            $sResult = MsgBox(_t('_Empty'));

        return $sResult;
    }
    function _getPseud ()
    {
        return array(
            'id' => 'ID',
            'date' => 'DateReg'
        );
    }
}
