<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

class SifuMysql extends SifuDb {

    /**
    * @param PDO $pdo
    */
    public function __construct($pdo) {

        // Force UTF-8
        $pdo->query("SET NAMES 'utf8' ");

        // Clear SQL Modes to avoid problems with boolean values in transactions
        $pdo->query("SET SESSION sql_mode='' ");

        // Let PDO handle MySql's (lack of) caching?
        // if (defined('PDO::ATTR_EMULATE_PREPARES')) $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        parent::__construct($pdo);
    }


    /**
    * Show Tables SQL query
    *
    * @return array
    */
    function showTables() {

        $sql = 'SHOW TABLES ';

        $st = $this->pdo->query($sql);
        foreach ($st->fetchAll(PDO::FETCH_NUM) as $row) {
            $tables[] = $row[0];
        }
        return $tables;
    }


    /**
    * Show Columns
    *
    * @param string $table
    * @return array
    */
    function showColumns($table) {

        $sql = "SHOW COLUMNS FROM $table ";

        $st = $this->pdo->query($sql);
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $columns[] = $row['Field'];
        }
        return $columns;
    }


    /**
    * Returns the ID of the last inserted row or sequence value
    *
    * @param string $table
    * @return int
    */
    function lastInsertId($table) {

        // $table is not used for MySql
        return $this->pdo->lastInsertId();
    }


    /**
    * Find all the tables with specific column names in them.
    *
    * @param string|array $col
    * @return array
    */
    function tablesWithColumns($col) {

        if (!is_array($col)) {
            $tmp = $col;
            unset($col);
            $col[] = $tmp;
        }

        // Get current database name
        $sql = 'SELECT DATABASE() ';
        $st = $this->pdo->query($sql);
        $row = $st->fetch(PDO::FETCH_NUM);
        $dbname = $row[0];

        // Find all the tables with specific column names in them
        $tables = array();

        $sql = 'SELECT DISTINCT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME IN (';
        foreach ($col as $val) {
            $sql .= "'$val', ";
        }
        $sql = rtrim($sql, ', ');
        $sql .= ") AND TABLE_SCHEMA = '$dbname' ";

        $st = $this->pdo->query($sql);
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $tables[] = $row['TABLE_NAME'];
        }
        return $tables;
    }

}

?>