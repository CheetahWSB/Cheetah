<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbCron');
ch_import('ChWsbAlerts');
ch_import('ChWsbCategories');

class ChWsbTextCron extends ChWsbCron
{
    var $_oModule;

    function __construct()
    {
        parent::__construct();

        $this->_oModule = null;
    }

    function processing()
    {
        $aIds = array();
        if($this->_oModule->_oDb->publish($aIds))
            foreach($aIds as $iId) {
                //--- Entry -> Publish for Alerts Engine ---//
                $oAlert = new ChWsbAlerts($this->_oModule->_oConfig->getAlertsSystemName(), 'publish', $iId);
                $oAlert->alert();
                //--- Entry -> Publish for Alerts Engine ---//

                //--- Reparse Global Tags ---//
                $oTags = new ChWsbTags();
                $oTags->reparseObjTags($this->_oModule->_oConfig->getTagsSystemName(), $iId);
                //--- Reparse Global Tags ---//

                //--- Reparse Global Categories ---//
                $oCategories = new ChWsbCategories();
                $oCategories->reparseObjTags($this->_oModule->_oConfig->getCategoriesSystemName(), $iId);
                //--- Reparse Global Categories ---//
            }
    }
}
