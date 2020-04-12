<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbModule.php');

class ChMsgModule extends ChWsbModule
{
    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);

        //--- Define Membership Actions ---//
        $aActions = $this->_oDb->getMembershipActions();
        foreach($aActions as $aAction) {
            $sName = 'ACTION_ID_' . strtoupper(str_replace(' ', '_', $aAction['name']));
            if(!defined($sName))
                define($sName, $aAction['id']);
        }
    }

    function actionGetInvitation()
    {
        $aForm = array(
            'form_attrs' => array(
                'name' => 'invitation_form'
            ),
            'params' => array(
                'remove_form' => true
            ),
            'inputs' => array(
                array(
                    'type' => 'input_set',
                    'colspan' => true,
                    0 => array(
                        'type' => 'button',
                        'name' => 'accept',
                        'value' => _t("_messenger_invitation_accept"),
                        'attrs' => array(
                            'class' => 'ch-btn-small',
                            'onclick' => 'ChMsgPerformAction("__sender_id__", "accept");'
                        )
                    ),
                    1 => array(
                        'type' => 'button',
                        'name' => 'decline',
                        'value' => _t("_messenger_invitation_decline"),
                        'attrs' => array(
                            'class' => 'ch-btn-small',
                            'onclick' => 'ChMsgPerformAction("__sender_id__", "decline");'
                        )
                    ),
                    2 => array(
                        'type' => 'button',
                        'name' => 'block',
                        'value' => _t("_messenger_invitation_block"),
                        'attrs' => array(
                            'class' => 'ch-btn-small',
                            'onclick' => 'ChMsgPerformAction("__sender_id__", "block");'
                        )
                    ),
                    3 => array(
                        'type' => 'button',
                        'name' => 'report',
                        'value' => _t("_messenger_invitation_report"),
                        'attrs' => array(
                            'class' => 'ch-btn-small',
                            'onclick' => 'ChMsgPerformAction("__sender_id__", "spam");'
                        )
                    )
                )
            )
        );

        $oForm = new ChTemplFormView($aForm);

        $aVariables = array(
            'invitation_buttons' => $oForm->getCode()
        );
        $sResult = $this->_oTemplate->parseHtmlByName("invitation.html", $aVariables);
        return $sResult;
    }

    function getMessenger($iSndId, $sSndPassword, $iRspId)
    {
        if(!empty($iSndId) && !empty($sSndPassword) && !empty($iRspId)) {
            $aResult = checkAction($iSndId, ACTION_ID_USE_MESSENGER, true);
            if($aResult[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED)
                $sResult = getApplicationContent('im', 'user', array('sender' => $iSndId, 'password' => $sSndPassword, 'recipient' => $iRspId), false);
            else
                $sResult = MsgBox($aResult[CHECK_ACTION_MESSAGE]);
        } else
            $sResult = MsgBox(_t('_messenger_err_not_logged_in'));

        return $sResult;
    }

    function actionGetThumbnail($iId)
    {
        return get_member_thumbnail($iId, "left");
    }

    function serviceGetInvitation()
    {
        global $sRayXmlUrl;

        $iId = isset($_COOKIE['memberID']) ? (int)$_COOKIE['memberID'] : 0;
        $sPassword = isset($_COOKIE['memberPassword']) ? $_COOKIE['memberPassword'] : '';

        $sResult = '';
        if(!empty($iId) && !empty($sPassword)) {
            $aResult = checkAction($iId, ACTION_ID_USE_MESSENGER);
            if($aResult[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED) {
                $sHomeUrl = $this->_oConfig->getHomeUrl();
                ob_start();
?>
<script language="javascript" type="text/javascript">
<!--
    var ChMsgTopMargin = 25;
    var ChMsgUpdateInterval = <?=rayGetSettingValue("im", "updateInterval") * 1000?>;
    if(isNaN(ChMsgUpdateInterval)) ChMsgUpdateInterval = 30000;
    var sChMsgMemberId = "<?=$iId?>";
    var sChMsgMemberPassword = "<?=$sPassword?>";
    var sChMsgSiteUrl = "<?=CH_WSB_URL_ROOT?>";
    var sChMsgGetUrl = "<?=CH_WSB_URL_ROOT . $this->_oConfig->getBaseUri()?>";
    var sChMsgUpdateUrl = "<?=$sRayXmlUrl?>?module=im&action=updateInvite&recipient=<?=$iId?>";
    ChMsgUpdate();
-->
</script>
<?php
                $this->_oTemplate->addCss("invitation.css");
                $this->_oTemplate->addJs("invite.js");

                $sResult .= ob_get_clean();
            }
        }
        return $sResult;
    }
    function serviceGetActionLink($iMemberId, $iProfileId)
    {
        $aResult = checkAction($iMemberId, ACTION_ID_USE_MESSENGER);
        if($iMemberId > 0 && get_user_online_status($iProfileId) && $aResult[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED && $iMemberId != $iProfileId && !isBlocked($iProfileId, $iMemberId))
            $sResult = _t('_messenger_actions_item');
        else
            $sResult = '';

        return $sResult;
    }
}
