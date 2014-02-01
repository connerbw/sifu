<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

namespace Sifu;

abstract class Object {

    /**
    * A SifuObject must declare associated database tables as public
    * variables, and must follow the 'db_table_*' naming convention. The
    * variable names are used in auto-magic procedures.
    *
    * @var string $db_table
    /*/
    public $db_table;


    /**
    * Constructor
    */
    function __construct() {

        // Pre-condition sanity check
        if (empty($this->db_table))
            throw new \Exception('$this->db_table not set');
    }


    /**
    * Select all
    *
    * @param int|null $limit [optional]
    * @param int $start [optional]
    * @param string $order [optional]
    * @param string $way [optional] 'ASC', 'DESC'
    * @param bool $fast_paging [optional]
    * @return array
    */
    function dump($limit = null, $start = 0, $order = 'id', $way = 'ASC', $fast_paging = false) {

        $db = DbInit::get();

        $q = 'SELECT ';
        if ($fast_paging && $db->driver == 'mysql') $q .= 'SQL_CALC_FOUND_ROWS ';
        $q .= "* FROM {$this->db_table} ";

        // Order
        $way = (strtolower($way) == 'asc') ? 'ASC' : 'DESC';
        $q .= "ORDER BY $order $way ";

        // Limit
        if ($start && $limit) $q .= "LIMIT $limit OFFSET $start ";
        elseif ($limit) $q .= "LIMIT $limit ";

        $st = $db->pdo->query($q);
        return $st->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
    * Similar to dump(), but with a where clause e.g. throttled results.
    *
    * @param string $where
    * @param int|null $limit [optional]
    * @param int $start [optional]
    * @param string $order [optional]
    * @param string $way [optional] 'ASC', 'DESC'
    * @param bool $fast_paging [optional]
    * @return array
    */
    function pump($where, $limit = null, $start = 0, $order = 'id', $way = 'ASC', $fast_paging = false) {

        $db = DbInit::get();

        $q = 'SELECT ';
        if ($fast_paging && $db->driver == 'mysql') $q .= 'SQL_CALC_FOUND_ROWS ';
        $q .= "* FROM {$this->db_table} WHERE $where ";

        // Order
        $way = (strtolower($way) == 'asc') ? 'ASC' : 'DESC';
        $q .= "ORDER BY $order $way ";

        // Limit
        if ($start && $limit) $q .= "LIMIT $limit OFFSET $start ";
        elseif ($limit) $q .= "LIMIT $limit ";

        $st = $db->pdo->query($q);
        return $st->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
    * Get by id
    *
    * @param int $id
    * @return array|false
    */
    function get($id) {

        $db = DbInit::get();
        $st = $db->pdo->prepare("SELECT * FROM {$this->db_table} WHERE id = ? ");
        $db->autoBind($st, array(1 => $id));
        $st->execute();
        $item = $st->fetch(\PDO::FETCH_ASSOC);

        if (!$item) return false;
        else return $item;
    }


    /**
     * Save
     *
     * @param int|null $id
     * @param array $item keys match SQL table columns
     * @return int id
     * @throws \Exception
     */
    function save($id, array $item) {

        // --------------------------------------------------------------------
        // Double check for stupidity
        // --------------------------------------------------------------------

        unset($item['id']); // Don't allow spoofing of the id in the array

        if ($id != null && $id < 1) throw new \Exception('Invalid id');

        // --------------------------------------------------------------------
        // Go!
        // --------------------------------------------------------------------

        // Begin transaction
        $db = DbInit::get();
        $tid = $db->requestTransaction();

        if ($id) {

            // UPDATE
            $item['id'] = $id;
            $q = $db->prepareUpdateQuery($this->db_table, $item);
            $st = $db->pdo->prepare($q);
            $db->autoBind($st, $item);
            $st->execute();
        }
        else {

            // INSERT
            $q = $db->prepareInsertQuery($this->db_table, $item);
            $st = $db->pdo->prepare($q);
            $db->autoBind($st, $item);
            $st->execute();

            $id = $db->lastInsertId($this->db_table);
        }

        // Commit
        $db->commitTransaction($tid);

        return $id;
    }


    /**
    * Delete
    *
    * In this magic cowboy inspired method, we scan all $this->db_table_*
    * variables and compare them to $this->db_table. If $this->db_table is
    * prefix, then deletion will occur. Else, dissociate.
    *
    * IMPORTANT! Every column in the entire DB named `{$this->db_table}_id`
    * will be affected by this function! This is especially important when
    * considering database INDEXES.
    *
    * The act of dissociation sets a value to -1. If this value
    * occurs twice in a column set as UNIQUE the DB will, of course, barf.
    * And so on.
    *
    * Model your SifuObjects() accordingly.
    *
    * @param int $id
    */
    function delete($id) {

        // Begin transaction
        $db = DbInit::get();
        $tid = $db->requestTransaction();

        // Delete from main table
        $q = "DELETE FROM {$this->db_table} WHERE id = ? ";
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $id));
        $st->execute();

        // Check prefixes
        $delete = array();
        foreach(get_object_vars($this) as $key => $val) {
            if (strpos($key, 'db_table_') === 0 && strpos($val, $this->db_table) === 0) {
                $delete[] = $val;
            }
        }

        // Decide whether to delete or dissociate
        $tables = $db->tablesWithColumns($this->db_table . '_id');
        foreach ($tables as $table) {

            if (in_array($table, $delete)) {
                // Delete
                $q = "DELETE FROM $table WHERE {$this->db_table}_id = ? ";
            }
            else {
                // Dissociate
                $q = "UPDATE $table SET {$this->db_table}_id = -1 WHERE {$this->db_table}_id = ? ";
            }
            $st = $db->pdo->prepare($q);
            $db->autoBind($st, array(1 => $id));
            $st->execute();
        }

        // Commit
        $db->commitTransaction($tid);
    }


    /**
    * Count
    *
    * @param string $where [optional]
    * @param bool $fast_paging [optional]
    * @return int
    */
    function count($where = null, $fast_paging = false) {

        $db = DbInit::get();

        if ($fast_paging && $db->driver == 'mysql') {

            $q = 'SELECT FOUND_ROWS() ';
        }
        else {

            $q = "SELECT COUNT(id) FROM {$this->db_table} ";
            if ($where) $q .= "WHERE $where ";
        }

        $st = $db->pdo->query($q);
        return $st->fetchColumn();
    }

}