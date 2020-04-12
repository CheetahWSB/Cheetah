<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

/**
 * Alert/Handler engine.
 *
 * Is needed to fire some alert(event) in one place and caught it with a handler somewhere else.
 *
 * Related classes:
 *  ChWsbAlertsResponse - abstract class for all response classes.
 *  ChWsbAlertsResponseUser - response class to process standard profile related alerts.
 *
 * Example of usage:
 * 1. Fire an alert
 *
 * ch_import('ChWsbAlerts');
 * $oZ = new ChWsbAlerts('unit_name', 'action', 'object_id', 'sender_id', 'extra_params');
 * $oZ->alert();
 *
 * 2. Add handler and caught alert(s) @see ChWsbAlertsResponseUser
 *  a. Create Response class extending ChWsbAlertsResponse class. It should process all necessary alerts which are passed to it.
 *  b. Register your handler in the database by adding it in `sys_alerts_handlers` table.
 *  c. Associate necessary alerts with the handler by adding them in the `sys_alerts` table.
 *
 *
 * Memberships/ACL:
 * Doesn't depend on user's membership.
 *
 *
 * Alerts:
 * no alerts available
 *
 */
class ChWsbAlerts
{
    var $sUnit;
    var $sAction;
    var $iObject;
    var $iSender;
    var $aExtras;

    var $_aAlerts;
    var $_aHandlers;

    /**
     * Constructor
     * @param string $sType     - system type
     * @param string $sAction   - system action
     * @param int    $iObjectId - object id
     * @param int    $iSenderId - sender (action's author) id
     */
    function __construct($sUnit, $sAction, $iObjectId, $iSender = 0, $aExtras = array())
    {
        require_once(CH_DIRECTORY_PATH_INC . 'db.inc.php');
        $oDb = ChWsbDb::getInstance();
        $oCache = $oDb->getDbCacheObject();
        $aData = $oCache->getData($oDb->genDbCacheKey('sys_alerts'));
        if (null === $aData)
            $aData = ChWsbAlerts::cache();

        $this->_aAlerts = $aData['alerts'];
        $this->_aHandlers = $aData['handlers'];

        $this->sUnit = $sUnit;
        $this->sAction = $sAction;
        $this->iObject = (int)$iObjectId;
        $this->iSender = !empty($iSender) ? (int)$iSender :
            (empty($_COOKIE['memberID']) ? 0 : (int)$_COOKIE['memberID']);
        $this->aExtras = $aExtras;
    }

    /**
     * Notifies the necessary handlers about the alert.
     */
    function alert()
    {
        ch_import('ChWsbSubscription');
        $oSubscription = ChWsbSubscription::getInstance();
        $oSubscription->send($this->sUnit, $this->sAction, $this->iObject, $this->aExtras);

        if(isset($this->_aAlerts[$this->sUnit]) && isset($this->_aAlerts[$this->sUnit][$this->sAction]))
            foreach($this->_aAlerts[$this->sUnit][$this->sAction] as $iHandlerId) {
                $aHandler = $this->_aHandlers[$iHandlerId];

                if(!empty($aHandler['file']) && !empty($aHandler['class']) && file_exists(CH_DIRECTORY_PATH_ROOT . $aHandler['file'])) {
                    if(!class_exists($aHandler['class']))
                        require_once(CH_DIRECTORY_PATH_ROOT . $aHandler['file']);

                    $oHandler = new $aHandler['class']();
                    $oHandler->response($this);
                } else if(!empty($aHandler['eval'])) {
                    eval($aHandler['eval']);
                }
            }
    }

    /**
     * Cache alerts and handlers.
     *
     * @return an array with all alerts and handlers.
     */
    public static function cache()
    {
        $aResult = array('alerts' => array(), 'handlers' => array());

        $rAlerts = db_res("SELECT `unit`, `action`, `handler_id` FROM `sys_alerts` ORDER BY `id` ASC");
        while($aAlert = $rAlerts->fetch())
            $aResult['alerts'][$aAlert['unit']][$aAlert['action']][] = $aAlert['handler_id'];

        $rHandlers = db_res("SELECT `id`, `class`, `file`, `eval` FROM `sys_alerts_handlers` ORDER BY `id` ASC");
        while($aHandler = $rHandlers->fetch())
            $aResult['handlers'][$aHandler['id']] = array('class' => $aHandler['class'], 'file' => $aHandler['file'], 'eval' => $aHandler['eval']);


        $oCache = $GLOBALS['MySQL']->getDbCacheObject();
        $oCache->setData ($GLOBALS['MySQL']->genDbCacheKey('sys_alerts'), $aResult);

        return $aResult;
    }
}

class ChWsbAlertsResponse
{
    function __construct(){}
    function response($oAlert) {}
}
