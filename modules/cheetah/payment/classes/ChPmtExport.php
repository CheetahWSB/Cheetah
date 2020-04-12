<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbExport');

class ChPmtExport extends ChWsbExport
{
    protected function __construct($aSystem)
    {
        parent::__construct($aSystem);
        $this->_aTables = array(
            'ch_pmt_cart' => '`client_id` = {profile_id}',
            'ch_pmt_transactions' => '`client_id` = {profile_id} OR `seller_id` = {profile_id}',
            'ch_pmt_transactions_pending' => '`client_id` = {profile_id} OR `seller_id` = {profile_id}',
            'ch_pmt_user_values' => '`user_id` = {profile_id}'
        );
    }
}
