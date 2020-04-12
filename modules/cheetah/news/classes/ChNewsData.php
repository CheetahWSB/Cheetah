<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbTextData');

class ChNewsData extends ChWsbTextData
{
    function __construct(&$oModule)
    {
        parent::__construct($oModule);

        $this->_aForm['params']['db']['table'] = $this->_oModule->_oDb->getPrefix() . 'entries';
        $this->_aForm['form_attrs']['action'] = CH_WSB_URL_ROOT . $this->_oModule->_oConfig->getBaseUri() . 'admin/';
        $this->_aForm['inputs']['author_id']['value'] = 0;
        $this->_aForm['inputs']['snippet']['checker']['params'][1] = $this->_oModule->_oConfig->getSnippetLength();
        $this->_aForm['inputs']['allow_comment_to'] = array(
            'type' => 'hidden',
            'name' => 'comment',
            'value' => 0,
            'db' => array (
                'pass' => 'Int',
            ),
        );
        $this->_aForm['inputs']['allow_vote_to'] = array(
            'type' => 'hidden',
            'name' => 'vote',
            'value' => 0,
            'db' => array (
                'pass' => 'Int',
            ),
        );
    }
}
