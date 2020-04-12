<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once(CH_DIRECTORY_PATH_CLASSES . 'ChWsbAlerts.php');

class ChWallResponse extends ChWsbAlertsResponse
{
    var $_oModule;

    /**
     * Constructor
     * @param ChWallModule $oModule - an instance of current module
     */
    function __construct($oModule)
    {
        parent::__construct();

        $this->_oModule = $oModule;
    }
    /**
     * Overwtire the method of parent class.
     *
     * @param ChWsbAlerts $oAlert an instance of alert.
     */
    function response($oAlert)
    {
        $bFromWall = !empty($oAlert->aExtras) && (int)$oAlert->aExtras['from_wall'] == 1;
        if(is_array($oAlert->aExtras) && isset($oAlert->aExtras['privacy_view']) && $oAlert->aExtras['privacy_view'] == CH_WSB_PG_HIDDEN)
			return;

        if($bFromWall) {
            $this->_oModule->_iOwnerId = (int)$oAlert->aExtras['owner_id'];
            $sMedia = $this->_oModule->_oConfig->getCommonType($oAlert->sUnit);
            $aMedia = $this->_oModule->_oTemplate->_getCommonMedia($sMedia, $oAlert->iObject);

            $iOwnerId = $this->_oModule->_iOwnerId;
            $iObjectId = $this->_oModule->_getAuthorId();
            $sType = $this->_oModule->_oConfig->getCommonPostPrefix() . $sMedia;
            $sAction = '';
            $sContent = serialize(array('type' => $sMedia, 'id' => $oAlert->iObject));
            $sTitle = $aMedia['title'];
            $sDescription = $aMedia['description'];
        }
        else if($this->_oModule->_oConfig->isSystemComment($oAlert->sUnit, $oAlert->sAction)) {
            $sType = $oAlert->aExtras['object_system'];
            $sAction = $oAlert->sUnit . '_' . $oAlert->sAction;
	        if(!$this->_oModule->_oConfig->isHandler($sType . '_' . $sAction))
	            return;

			$iOwnerId = $oAlert->iSender;
            $iObjectId = $oAlert->iObject;
            $sContent = serialize(array('object_id' => $oAlert->aExtras['object_id']));
            $sTitle = $sDescription = '';
        }
        else {
            $iOwnerId = $oAlert->iSender;
            $iObjectId = $oAlert->iObject;
            $sType = $oAlert->sUnit;
            $sAction = $oAlert->sAction;
            $sContent = is_array($oAlert->aExtras) && !empty($oAlert->aExtras) ? serialize($oAlert->aExtras) : '';
            $sTitle = $sDescription = '';
        }

        if($oAlert->sUnit == 'profile' && $oAlert->sAction == 'delete') {
            $this->_oModule->_oDb->deleteEvent(array('owner_id' => $oAlert->iObject));
            $this->_oModule->_oDb->deleteEventCommon(array('object_id' => $oAlert->iObject));

            //delete all subscriptions
			$oSubscription = ChWsbSubscription::getInstance();
			$oSubscription->unsubscribe(array('type' => 'object_id', 'unit' => 'ch_wall', 'object_id' => $oAlert->iObject));
            return;
        }

        if($oAlert->sUnit == 'profile' && $oAlert->sAction == 'edit' && $iOwnerId != $iObjectId) {
            return;
        }

        $iId = $this->_oModule->_oDb->insertEvent(array(
            'owner_id' => $iOwnerId,
            'object_id' => $iObjectId,
            'type' => $sType,
            'action' => $sAction,
            'content' => process_db_input($sContent, CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION),
            'title' => process_db_input($sTitle, CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION),
            'description' => process_db_input($sDescription, CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION),
        ));

        if($bFromWall)
            echo "<script>parent." . $this->_oModule->_oConfig->getJsObject('post') . "._getPost(null, " . $iId . ")</script>";
        else
            $this->_oModule->_oDb->updateSimilarObject($iId, $oAlert);
    }
}
