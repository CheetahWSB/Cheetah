<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

/**
 * This class is needed to work with database.
 */
class ChDbConnect
{
    function getResult($sQuery)
    {
        return ChWsbDb::getInstance()->res($sQuery);
    }

    function getArray($sQuery)
    {
        return ChWsbDb::getInstance()->getRow($sQuery);
    }

    function getValue($sQuery)
    {
        return ChWsbDb::getInstance()->getOne($sQuery);
    }

    function getLastInsertId()
    {
        return ChWsbDb::getInstance()->lastId();
    }

    function escape($s)
    {
        return ChWsbDb::getInstance()->escape($s, false);
    }
}

global $oDb;
$oDb = new ChDbConnect();

/*
 * Interface functions are needed to simplify the useing of ChDbConnect class.
 */
function getResult($sQuery)
{
    global $oDb;

    return $oDb->getResult($sQuery);
}

function getArray($sQuery)
{
    global $oDb;

    return $oDb->getArray($sQuery);
}

function getValue($sQuery)
{
    global $oDb;

    return $oDb->getValue($sQuery);
}

function getLastInsertId()
{
    global $oDb;

    return $oDb->getLastInsertId();
}

function getEscapedValue($sValue)
{
    global $oDb;

    return $oDb->escape($sValue);
}
