<?php

/**
*
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

namespace Sifu\Db;

class Pgsql extends Db {

    /**
    * @param PDO $pdo
    */
    public function __construct($pdo) {

        parent::__construct($pdo);
    }


    /**
    * Show Tables
    *
    * @return array
    */
    function showTables() {

    	$sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE' ";
    	
        $st = $this->pdo->query($sql);
        foreach ($st->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $tables[] = $row['table_name'];
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

        $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '$table' ";
        
        $st = $this->pdo->query($sql);
        foreach ($st->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $columns[] = $row['column_name'];
        }
        return $columns;
    }


    /**
    * Returns the ID of the last inserted row or sequence value
    * @param string $table
    * @return int
    */
    function lastInsertId($table) {

        return $this->pdo->lastInsertId("{$table}_id_seq");
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

        // Find all the tables with specific column names in them
        $tables = array();

        $sql = 'SELECT DISTINCT table_name FROM information_schema.columns WHERE column_name IN (';
        foreach ($col as $val) {
            $sql .= "'$val', ";
        }
        $sql = rtrim($sql, ', ');
        $sql .= ") AND table_schema = 'public' ";

        $st = $this->pdo->query($sql);
        foreach ($st->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $tables[] = $row['table_name'];
        }
        return $tables;
    }

}
