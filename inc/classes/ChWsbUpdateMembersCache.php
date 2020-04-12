<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

 class ChWsbUpdateMembersCache extends ChWsbAlertsResponse
 {
    // system event
    function response($o)
    {

        $sProfileStatus = null;
        $iProfileId = $o->iObject;

        if ( $iProfileId )
            $sProfileStatus = db_value
            (
                "
                    SELECT
                        `Status`
                    FROM
                        `Profiles`
                    WHERE
                        `ID` = {$iProfileId}
                "
            );

        if ( $sProfileStatus == 'Active' ) {

            if ('profile' == $o->sUnit)
            switch ($o->sAction) {

                case 'join':
                case 'edit':
                case 'delete':
                    // clean cache
                    $GLOBALS['MySQL']->cleanCache('sys_browse_people');
                break;

            }

        }
    }

 }
