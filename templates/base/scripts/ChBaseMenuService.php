<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    ch_import('ChWsbMenuService');

    /**
     * @see ChWsbMenuService;
     */
    class ChBaseMenuService extends ChWsbMenuService
    {
        /**
         * Class constructor;
         */
        function __construct()
        {
            parent::__construct();
        }

        function getItems()
        {
            $sContent = parent::getItems();

            return $GLOBALS['oSysTemplate']->parseHtmlByName('extra_sm_thumbnail.html', array(
                'ch_if:show_thumbail' => array(
                    'condition' => $this->aMenuInfo['memberID'] != 0,
                    'content' => array(
                        'thumbnail' => get_member_icon($this->aMenuInfo['memberID'], 'left')
                    )
                ),
                'content' => $sContent
            ));
        }
    }
