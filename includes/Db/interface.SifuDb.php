<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

namespace Sifu\Db;

interface iDb {

    /**
    * @return array
    */
    function showTables();

    /**
    * @param string $table
    * @return array
    */
    function showColumns($table);

    /**
    * @param string $table
    * @return int
    */
    function lastInsertId($table);

    /**
    * @param string|array $col
    * @return array
    */
    function tablesWithColumns($col);
}

?>