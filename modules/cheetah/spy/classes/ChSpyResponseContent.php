<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbAlerts.php');

    class ChSpyResponseContent extends ChWsbAlertsResponse
    {
        var $_oModule;
        var $aInternalHandlers;

        /**
         * Constructor
         * @param  ChWallModule $oModule - an instance of current module
         */
        function __construct($oModule)
        {
            parent::__construct();

            $this->_oModule = $oModule;
            $aInternalHandlers = $this->_oModule->_oDb->getInternalHandlers();

            // procces all recived handlers;
            if($aInternalHandlers && is_array($aInternalHandlers))
                foreach($aInternalHandlers as $iKey => $aItems)
                    $this -> aInternalHandlers[$aItems['alert_unit'] . '_' . $aItems['alert_action']] = $aItems;
        }

        /**
         * Overwtire the method of parent class.
         *
         * @param ChWsbAlerts $oAlert an instance of alert.
         */
        function response($oAlert)
        {
            $sKey = $oAlert->sUnit . '_' . $oAlert->sAction;

            $iCommentId = 0;
            switch($oAlert->sAction) {
                case 'delete':
                case 'delete_poll':
                case 'delete_post':
                    $this->_oModule->_oDb->deleteActivityByObject($oAlert->sUnit, $oAlert->iObject);
                    return;

                case 'commentPost':
                    if(!isset($oAlert->aExtras['comment_id']) || (int)$oAlert->aExtras['comment_id'] == 0)
                        return;

                    $iCommentId = (int)$oAlert->aExtras['comment_id'];
                    break;

                case 'commentRemoved':
                    if(!isset($oAlert->aExtras['comment_id']) || (int)$oAlert->aExtras['comment_id'] == 0)
                        return;

                    $this->_oModule->_oDb->deleteActivityByObject($oAlert->sUnit, $oAlert->iObject, (int)$oAlert->aExtras['comment_id']);
                    return;
            }

            // call defined method;
            if(!is_array($this -> aInternalHandlers) || !array_key_exists($sKey, $this -> aInternalHandlers))
                return;

            if(!ChWsbRequest::serviceExists($this -> aInternalHandlers[$sKey]['module_uri'], $this -> aInternalHandlers[$sKey]['module_method']))
                return;

            // define functions parameters;
            $aParams = array(
                'action' => $oAlert->sAction,
                'object_id' => $oAlert->iObject,
                'sender_id' => $oAlert->iSender,
                'extra_params' => $oAlert->aExtras,
            );

            $aResult = ChWsbService::call($this->aInternalHandlers[$sKey]['module_uri'], $this->aInternalHandlers[$sKey]['module_method'], $aParams);
            if(empty($aResult))
                return;

            // create new event;
            // define recipent id;
            $iRecipientId = isset($aResult['recipient_id']) ? $aResult['recipient_id'] : $oAlert -> iObject;
            if(isset($aResult['spy_type']) && $aResult['spy_type'] == 'content_activity' && $iRecipientId == $oAlert->iSender)
                $iRecipientId = 0;

            $iEventId = 0;
            if($oAlert->iSender || (!$oAlert->iSender && $this->_oModule->_oConfig->bTrackGuestsActivites))
                $iEventId = $this->_oModule->_oDb->createActivity(
                    $oAlert->sUnit,
                    $oAlert->sAction,
                    $oAlert->iObject,
                    $iCommentId,
                    $oAlert->iSender,
                    $iRecipientId,
                    $aResult
                );

            if(!$iEventId)
                return;

            // try to define all profile's friends;
            $aFriends = getMyFriendsEx($oAlert->iSender);
            if(empty($aFriends) || !is_array($aFriends))
                return;

            // attach event to friends;
            foreach($aFriends as $iFriendId => $aItems)
                $this->_oModule->_oDb->attachFriendEvent($iEventId, $oAlert->iSender, $iFriendId);
        }
    }
