<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    require_once(CH_DIRECTORY_PATH_CLASSES . "ChWsbInstaller.php");

    class ChSimpleMessengerInstaller extends ChWsbInstaller
    {
        function __construct( $aConfig )
        {
            parent::__construct($aConfig);
        }

        function actionCheckMemberMenu()
        {
            return getParam('ext_nav_menu_enabled') == 'on' ? CH_WSB_INSTALLER_SUCCESS : CH_WSB_INSTALLER_FAILED;
        }

        function actionCheckMemberMenuFailed()
        {
            return _t('_simple_messenger_error_member_menu');
        }
    }
