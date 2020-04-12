<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

class ChWsbXMLRPCSearch
{
    function getSearchResultsLocation($sUser, $sPwd, $sLang, $sCountryCode, $sCity, $isOnlineOnly, $isWithPhotosOnly, $iStart, $iPP)
    {
        if (!($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        ChWsbXMLRPCUtil::setLanguage ($sLang);

        $sCountryCode = process_db_input ($sCountryCode, CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION);
        $sCity = process_db_input ($sCity, CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION);

        $sWhere = '';
        if ($sCountryCode)
            $sWhere .= " AND `Country`= '$sCountryCode' ";
        if ($sCity)
            $sWhere .= " AND `City`LIKE '$sCity' ";
        if ($isWithPhotosOnly)
            $sWhere .= " AND `Avatar` ";
        if ($isOnlineOnly) {
            $iOnlineTime = (int)getParam( 'member_online_time' );
            $sWhere .= " AND `DateLastNav` >= DATE_SUB(NOW(), INTERVAL $iOnlineTime MINUTE)";
        }
        $iStart = (int)$iStart;
        if (!$iStart || $iStart < 0)
            $iStart = 0;
        $iPP = (int)$iPP;
        if (!$iPP || $iPP < 1)
            $iPP = 1;

        $r = db_res ("
            SELECT * FROM `Profiles`
            WHERE `Status` = 'Active' AND (`Profiles`.`Couple` = 0 OR `Profiles`.`Couple` > `Profiles`.`ID`) $sWhere
            ORDER BY `DateLastNav` DESC
            LIMIT $iStart, $iPP");

        while ($aRow = $r->fetch())
            $aProfiles[] = new xmlrpcval(ChWsbXMLRPCUtil::fillProfileArray($aRow, 'thumb'), 'struct');

        return new xmlrpcval ($aProfiles, "array");
    }

    function getSearchResultsNearMe($sUser, $sPwd, $sLang, $sLat, $sLng, $isOnlineOnly, $isWithPhotosOnly, $iStart, $iPP)
    {
        if (!($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        ChWsbXMLRPCUtil::setLanguage ($sLang);

        $sLat = (float)$sLat;
        $sLng = (float)$sLng;

        if ((!$sLat || !$sLng) && ChWsbRequest::serviceExists('wmap', 'get_location')) {
            $aLocation = ChWsbService::call('wmap', 'get_location', array('profiles', $iId, $iId));
            if ($aLocation && !empty($aLocation['lat']) && !empty($aLocation['lng'])) {
                $sLat = $aLocation['lat'];
                $sLng = $aLocation['lng'];
            }
        }

        if (!$sLat || !$sLng)
            return new xmlrpcval (array(), "array");

        $sWhere = '';
        $sJoin = '';
        $sLocation = '';
        $sDistance = ", (POW($sLat-`loc`.`lat`, 2)+POW($sLng-`loc`.`lng`, 2)) AS `distance`";
        $sJoin .= " INNER JOIN `ch_wmap_locations` AS `loc` ON  (`loc`.`part` = 'profiles' AND `loc`.`id` = `Profiles`.`ID` AND `loc`.`failed` = 0) ";

        if ($isWithPhotosOnly)
            $sWhere .= " AND `Avatar` ";
        if ($isOnlineOnly) {
            $iOnlineTime = (int)getParam( 'member_online_time' );
            $sWhere .= " AND `DateLastNav` >= DATE_SUB(NOW(), INTERVAL $iOnlineTime MINUTE)";
        }
        $iStart = (int)$iStart;
        if (!$iStart || $iStart < 0)
            $iStart = 0;
        $iPP = (int)$iPP;
        if (!$iPP || $iPP < 1)
            $iPP = 1;

        $r = db_res ("
            SELECT * " . $sDistance  . " FROM `Profiles`
            $sJoin
            WHERE `Status` = 'Active' AND (`Profiles`.`Couple` = 0 OR `Profiles`.`Couple` > `Profiles`.`ID`) $sWhere
            ORDER BY `distance` ASC
            LIMIT $iStart, $iPP");

        while ($aRow = $r->fetch())
            $aProfiles[] = new xmlrpcval(ChWsbXMLRPCUtil::fillProfileArray($aRow, 'thumb'), 'struct');

        return new xmlrpcval ($aProfiles, "array");
    }

    function getSearchResultsKeyword($sUser, $sPwd, $sLang, $sKeyword, $isOnlineOnly, $isWithPhotosOnly, $iStart, $iPP)
    {
        if (!($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        ChWsbXMLRPCUtil::setLanguage ($sLang);

        $sKeyword = process_db_input ($sKeyword, CH_TAGS_NO_ACTION, CH_SLASHES_NO_ACTION);

        $sMatch = '';
        if ($sKeyword && strlen($sKeyword) > 2) {
            $sMatch .= " MATCH (`NickName`,`FullName`,`FirstName`,`LastName`,`City`,`DescriptionMe`,`Tags`) AGAINST ('$sKeyword') ";

            $sWhere .= " AND $sMatch  ";
        }
        if ($isWithPhotosOnly)
            $sWhere .= " AND `Avatar` ";
        if ($isOnlineOnly) {
            $iOnlineTime = (int)getParam( 'member_online_time' );
            $sWhere .= " AND `DateLastNav` >= DATE_SUB(NOW(), INTERVAL $iOnlineTime MINUTE)";
        }
        $iStart = (int)$iStart;
        if (!$iStart || $iStart < 0)
            $iStart = 0;
        $iPP = (int)$iPP;
        if (!$iPP || $iPP < 1)
            $iPP = 1;

        $r = db_res ("
            SELECT * " . ( $sMatch ? ", $sMatch" : '') . " FROM `Profiles`
            WHERE `Status` = 'Active' AND (`Profiles`.`Couple` = 0 OR `Profiles`.`Couple` > `Profiles`.`ID`)  $sWhere
            ORDER BY `DateLastNav` DESC
            LIMIT $iStart, $iPP");

        while ($aRow = $r->fetch())
            $aProfiles[] = new xmlrpcval(ChWsbXMLRPCUtil::fillProfileArray($aRow, 'thumb'), 'struct');

        return new xmlrpcval ($aProfiles, "array");
    }

    function getSeachHomeMenu3($sUser, $sPwd, $sLang)
    {
        if (!($iId = ChWsbXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        ChWsbXMLRPCUtil::setLanguage ($sLang);

        $aRet = ChWsbXMLRPCUtil::getUserInfo($iId);

        $aMarkersReplace = array (
            'member_id' => $iId,
            'member_username' => rawurlencode($sUser),
            'member_password' => $sPwd,
        );
        $aRet['menu'] = new xmlrpcval(ChWsbXMLRPCUtil::getMenu('search', $aMarkersReplace), 'array');

        return new xmlrpcval ($aRet, "struct");
    }
}
