<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

    /**
     * Simple Menu
     *
     * Related classes:
     *  @see ChBaseMenuSimple - simple menu base representation
     *  @see ChTemplMenuSimple - simple menu template representation
     *
     * Memberships/ACL:
     * no levels
     *
     * Alerts:
     * no alerts
     */
    class ChWsbMenuSimple
    {
        var $sName;
        var $sDbTable;
        var $sCacheKey;

        var $aMenuInfo;
        var $aItems;

        var $oPermalinks;

        function __construct()
        {
            $this->sName = 'bottom';
            $this->sDbTable = 'sys_menu_bottom';
            $this->sCacheKey = 'sys_menu_bottom';

            $this->aMenuInfo = array();
            if(isMember()) {
                $this->aMenuInfo['memberID'] = getLoggedId();
                $this->aMenuInfo['memberNick'] = getNickName($this->aMenuInfo['memberID']);
                $this->aMenuInfo['memberPass'] = getPassword($this->aMenuInfo['memberID']);
                $this->aMenuInfo['memberLink'] = getProfileLink($this->aMenuInfo['memberID']);
                $this->aMenuInfo['visible'] = 'memb';
            } else {
                $this->aMenuInfo['memberID'] = 0;
                $this->aMenuInfo['memberNick'] = '';
                $this->aMenuInfo['memberPass'] = '';
                $this->aMenuInfo['memberLink'] = '';
                $this->aMenuInfo['visible'] = 'non';
            }
            $this->aItems = array();

            $this->oPermalinks = new ChWsbPermalinks();
        }

        function load()
        {
            $oCache = $GLOBALS['MySQL']->getDbCacheObject();
            $this->aItems = $oCache->getData($GLOBALS['MySQL']->genDbCacheKey($this->sCacheKey));

            if($this->aItems === null) {
                if(!$this->compile())
                    return false;

                $this->aItems = $oCache->getData($GLOBALS['MySQL']->genDbCacheKey($this->sCacheKey));
            }

            if(!$this->aItems || !is_array($this->aItems)) {
                echo '<b>Warning!</b> Cannot evaluate ' . $this->sName . ' menu cache.';
                return false;
            }

            return true;
        }

        function compile()
        {
            $sEval =  "return array(\n";
            $aFields = array('Caption', 'Name', 'Icon', 'Link', 'Script', 'Target', 'Order', 'Visible');

            $sQuery = "SELECT `ID`, `" . implode('`, `', $aFields ) . "` FROM `" . $this->sDbTable . "` WHERE `Active`='1' ORDER BY `Order`";
            $rMenu = db_res($sQuery);

            while($aItem = $rMenu->fetch()) {
                $sEval .= "  " . str_pad( $aItem['ID'], 2 ) . " => array(\n";

                foreach( $aFields as $sKey => $sField ) {
                    $sCont = $aItem[$sField];

                    $sCont = str_replace( '\\', '\\\\', $sCont );
                    $sCont = str_replace( '"', '\\"',   $sCont );
                    $sCont = str_replace( '$', '\\$',   $sCont );

                    $sCont = str_replace( "\n", '',     $sCont );
                    $sCont = str_replace( "\r", '',     $sCont );
                    $sCont = str_replace( "\t", '',     $sCont );

                    $sEval .= "    " . str_pad( "'$sField'", 11 ) . " => \"$sCont\",\n";
                }

                $sEval .= "  ),\n";
            }

            $sEval .= ");\n";
            $aResult = eval($sEval);

            $oCache = $GLOBALS['MySQL']->getDbCacheObject();
            return $oCache->setData ($GLOBALS['MySQL']->genDbCacheKey($this->sCacheKey), $aResult);
        }

        function checkToShow( $aItem )
        {
            if(!$this->checkVisible($aItem['Visible']))
                return false;

            return true;
        }

        function checkVisible( $sVisible )
        {
            return strpos($sVisible, $this->aMenuInfo['visible']) !== false;
        }

        function replaceMetas($sData)
        {
            foreach($this->aMenuInfo as $k => $v)
                $sData = str_replace('{'.$k.'}', $v, $sData);

            return $sData;
        }
    }
