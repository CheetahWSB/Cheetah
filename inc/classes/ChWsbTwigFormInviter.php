<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbProfileFields');

/**
 * Base invite form class for modules like events/groups/store
 */
class ChWsbTwigFormInviter extends ChTemplFormView
{
    function __construct ($oMain, $sMsgNoUsers)
    {
        $aVisitorsPreapare = $oMain->_oDb->getPotentialVisitors ($oMain->_iProfileId);
        $aVisitors = array ();
        foreach ($aVisitorsPreapare as $k => $r) {
            $aVisitors[] = array (
                'Icon' => $GLOBALS['oFunctions']->getMemberIcon($r['ID'], 'left'),
                'Link' => getProfileLink($r['ID']),
                'NickName' => getNickName($r['ID']),
                'ID' => $r['ID'],
            );
        }
        $aVars = array (
            'ch_repeat:rows' => $aVisitors,
            'msg_no_users' => $aVisitors ? '' : $sMsgNoUsers,
        );
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_inviter',
                'action'   => '',
                'method'   => 'post',
            ),

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
            ),

            'inputs' => array(
                'inviter_users' => array(
                    'type' => 'custom',
                    'content' => $oMain->_oTemplate->parseHtmlByName('inviter', $aVars),
                    'name' => 'inviter_users',
                    'caption' => _t('_sys_invitation_step_select_users'),
                    'info' => _t('_sys_invitation_step_select_users_info'),
                    'required' => false,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ),

                'inviter_emails' => array(
                    'type' => 'textarea',
                    'name' => 'inviter_emails',
                    'caption' => _t('_sys_invitation_step_additional_emails'),
                    'info' => _t('_sys_invitation_step_additional_emails_info'),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),

                'inviter_text' => array(
                    'type' => 'textarea',
                    'name' => 'inviter_text',
                    'caption' => _t('_sys_invitation_step_invitation_text'),
                    'info' => _t('_sys_invitation_step_invitation_text_info'),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),

                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                ),
            ),
        );

        parent::__construct ($aCustomForm);
    }
}
