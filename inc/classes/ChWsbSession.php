<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbSessionQuery');

define('CH_WSB_SESSION_LIFETIME', 3600);
define('CH_WSB_SESSION_COOKIE', 'memberSession');

class ChWsbSession
{
    var $oDb;
    var $sId;
    var $iUserId;
    var $aData;

    public function __construct()
    {
        $this->oDb = new ChWsbSessionQuery();
        $this->sId = '';
        $this->iUserId = 0;
        $this->aData = array();
    }

    public static function getInstance()
    {
        if(!isset($GLOBALS['chWsbClasses']['ChWsbSession']))
            $GLOBALS['chWsbClasses']['ChWsbSession'] = new ChWsbSession();

        if(!$GLOBALS['chWsbClasses']['ChWsbSession']->getId())
            $GLOBALS['chWsbClasses']['ChWsbSession']->start();

        return $GLOBALS['chWsbClasses']['ChWsbSession'];
    }

    public function start()
    {
        if (defined('CH_WSB_CRON_EXECUTE'))
            return true;

        if($this->exists($this->sId))
            return true;

        $this->sId = genRndPwd(32, true);

        $aUrl = parse_url($GLOBALS['site']['url']);
        $sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';
        setcookie(CH_WSB_SESSION_COOKIE, $this->sId, 0, $sPath, '', false, true);

        $this->save();
        return true;
    }

    public function destroy()
    {
        $aUrl = parse_url($GLOBALS['site']['url']);
        $sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';
        setcookie(CH_WSB_SESSION_COOKIE, '', time() - 86400, $sPath, '', false, true);
        unset($_COOKIE[CH_WSB_SESSION_COOKIE]);

        $this->oDb->delete($this->sId);

        $this->sId = '';
        $this->iUserId = 0;
        $this->aData = array();
    }

    public function exists($sId = '')
    {
        if(empty($sId) && isset($_COOKIE[CH_WSB_SESSION_COOKIE]))
            $sId = process_db_input($_COOKIE[CH_WSB_SESSION_COOKIE], CH_TAGS_STRIP);

        $mixedSession = array();
        if(($mixedSession = $this->oDb->exists($sId)) !== false) {
            $this->sId = $mixedSession['id'];
            $this->iUserId = (int)$mixedSession['user_id'];
            $this->aData = unserialize($mixedSession['data']);
            return true;
        } else
            return false;
    }

    public function getId()
    {
        return $this->sId;
    }

    public function setValue($sKey, $mixedValue)
    {
        if(empty($this->sId))
            $this->start();

        $this->aData[$sKey] = $mixedValue;
        $this->save();
    }

    public function unsetValue($sKey)
    {
        if(empty($this->sId))
            $this->start();

        unset($this->aData[$sKey]);

        if(!empty($this->aData))
            $this->save();
        else
            $this->destroy();
    }

    public function getValue($sKey)
    {
        if(empty($this->sId))
            $this->start();

        return isset($this->aData[$sKey]) ? $this->aData[$sKey] : false;
    }

    private function save()
    {
        if($this->iUserId == 0)
            $this->iUserId = getLoggedId();

        $this->oDb->save($this->sId, array(
            'user_id' => $this->iUserId,
            'data' => serialize($this->aData)
        ));
    }
}
