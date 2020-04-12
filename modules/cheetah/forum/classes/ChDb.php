<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

// common database operations

class ChDb extends Mistake
{
    /**
     * execute sql query and return one row result
     *
     * @param     $query
     * @param     $bindings
     * @param int $arr_type
     * @return array
     */
    function getRow($query, $bindings = [], $arr_type = PDO::FETCH_ASSOC)
    {
        return ChWsbDb::getInstance()->getRow($query, $bindings, $arr_type);
    }

    /**
     * execute sql query and return one value result
     *
     * @param $query
     * @param $bindings
     * @return mixed
     */
    function getOne($query, $bindings = [])
    {
        return ChWsbDb::getInstance()->getOne($query, $bindings);
    }

    /**
     * @param $query
     * @param $bindings
     * @return array
     */
    function getColumn($query, $bindings = [])
    {
        return ChWsbDb::getInstance()->getColumn($query, $bindings);
    }

    /**
     * execute sql query and return the first row of result
     * and keep $array type and poiter to all data
     *
     * @param string $query
     * @param  array $bindings
     * @param int    $arr_type
     * @return array
     */
    function getFirstRow($query, $bindings = [], $arr_type = PDO::FETCH_ASSOC)
    {
        return ChWsbDb::getInstance()->getFirstRow($query, $bindings, $arr_type);
    }

    /**
     * return next row of pointed last getFirstRow calling data
     */
    function getNextRow()
    {
        return ChWsbDb::getInstance()->getNextRow();
    }

    /**
     * return number of affected rows in current mysql result
     *
     * @param PDOStatement $res
     * @return int
     */
    function getNumRows($res = null)
    {
        return ChWsbDb::getInstance()->getAffectedRows($res);
    }

    /**
     * get last insert id
     */
    function getLastId()
    {
        return ChWsbDb::getInstance()->lastId();
    }

    /**
     * execute any query return number of rows affected/false
     *
     * @param $query
     * @param $bindings
     * @return int
     */
    function query($query, $bindings = [])
    {
        return ChWsbDb::getInstance()->query($query, $bindings);
    }

    /**
     * execute sql query and return table of records as result
     *
     * @param     $query
     * @param     $bindings
     * @param int $arr_type
     * @return array
     */
    function getAll($query, $bindings = [], $arr_type = PDO::FETCH_ASSOC)
    {
        return ChWsbDb::getInstance()->getAll($query, $bindings, $arr_type);
    }

    /**
     * @param $s
     * @return string
     */
    function escape($s)
    {
        return ChWsbDb::getInstance()->escape($s, false);
    }

    function unescape($s)
    {
        return ChWsbDb::getInstance()->unescape($s, false);
    }
}
