<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

class SifuAccess extends SifuObject {

    // variables: table names
    public $db_table = 'access';

    public $db_table_groups = 'access_groups';

    public $db_table_users = 'users';

    /**
    * Constructor
    */
    function __construct() { }


    /**
    * Get the chown, chgrp, and chmod info for a specific module
    *
    * @param int $id
    * @param bool $names [optional] return names
    * @return array
    */
    function get($id, $names = false) {

        $q = "SELECT * FROM {$this->db_table} WHERE id = ? ";

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $id));
        $st->execute();
        $row = $st->fetch(PDO::FETCH_ASSOC);

        if (!$names) return $row;
        else {

            $q = "SELECT nickname FROM {$this->db_table_users} WHERE id = ? ";
            $st = $db->pdo->prepare($q);
            $db->autoBind($st, array(1 => $row['users_id']));
            $st->execute();
            $row['users_nickname'] = $st->fetchColumn();

            $q = "SELECT name FROM {$this->db_table_groups} WHERE id = ? ";
            $st = $db->pdo->prepare($q);
            $db->autoBind($st, array(1 => $row['access_groups_id']));
            $st->execute();
            $row['access_groups_name'] = $st->fetchColumn();

            return $row;
        }
    }


    /**
    * @param int|null $id
    * @param array $item keys match SQL table columns of users and users_info
    * @return int id
    */
    function save($id, array $item) {

        // Double check for stupidity
        if (empty($item['module'])) throw new Exception('Invalid module');

        // Let the parent do the rest
        parent::save($id, $item);
    }


    /**
    * Delete module
    *
    * @param string|int $id
    */
    function delete($id) {

        $q = "DELETE FROM {$this->db_table} WHERE id = ? ";

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $id));
        $st->execute();
    }


    /**
    * Check if a user is root. Looks for reserved keyword 'root' in
    * users_groups table.
    *
    * @global int $_SESSION['users_id']
    * @param int $id [optional]
    * @return bool
    */
    function isRoot($id = null) {

        if (!$id) {
            if (!empty($_SESSION['users_id'])) $id = $_SESSION['users_id'];
            else return false;
        }

        $q = "
        SELECT {$this->db_table_groups}.name FROM {$this->db_table_groups}
        INNER JOIN {$this->db_table_users} ON {$this->db_table_groups}.id = {$this->db_table_users}.access_groups_id
        WHERE {$this->db_table_users}.id = ? AND {$this->db_table_groups}.name = 'root'
        ";

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $id));
        $st->execute();
        $root = $st->fetchColumn();

        if ($root) return true;
        else return false;
    }


    /**
    * Check if a user is banned. Looks for reserved keyword 'banned' in
    * users_groups table.
    *
    * @global int $_SESSION['users_id']
    * @param int $id [optional]
    * @return bool
    */
    function isBanned($id = null) {

        if (!$id) {
            if (!empty($_SESSION['users_id'])) $id = $_SESSION['users_id'];
            else return false;
        }

        $q = "
        SELECT {$this->db_table_groups}.name FROM {$this->db_table_groups}
        INNER JOIN {$this->db_table_users} ON {$this->db_table_groups}.id = {$this->db_table_users}.access_groups_id
        WHERE {$this->db_table_users}.id = ? AND {$this->db_table_groups}.name = 'banned'
        ";

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $id));
        $st->execute();
        $banned = $st->fetchColumn();

        if ($banned) return true;
        else return false;
    }


    /**
    * 0 --- no permission
    * 1 --x execute
    * 2 -w- write
    * 3 -wx write and execute
    * 4 r-- read
    * 5 r-x read and execute
    * 6 rw- read and write
    * 7 rwx read, write and execute
    *
    * @param int $chmod
    * @param string $group 'owner', 'group', 'other'
    * @param string $triplet 'r', 'w', 'x'
    * @return bool
    */
    function hasOctalAccess($chmod, $group, $triplet) {

        if ($group == 'owner') $pos = 0;
        elseif ($group == 'group') $pos = 1;
        elseif ($group == 'other') $pos = 2;
        else return false;

        if ($chmod == null || $chmod == -1) {
            // The chmod was not explicitely set?
            return false;
        }

        $bit = $chmod{$pos};

        if ($triplet == 'r' && ($bit == 4 || $bit == 5 || $bit == 6 || $bit == 7)) return true;
        elseif ($triplet == 'w' && ($bit == 2 || $bit == 3 || $bit == 6 || $bit == 7)) return true;
        elseif ($triplet == 'x' && ($bit == 1 || $bit == 3 || $bit == 5 || $bit == 7)) return true;
        else return false;
    }


    /**
    * @param string|int $module
    * @param int $users_id
    * @param string $triplet 'r', 'w', 'x'
    * @return bool
    */
    function hasAccess($module, $users_id, $triplet) {

        if ($this->isRoot($users_id)) return true; // Root has access to everything

        if (filter_var($module, FILTER_VALIDATE_INT)) {
            $q = "SELECT users_id, access_groups_id, chmod FROM {$this->db_table} WHERE id = ? ";
        }
        else {
            $q = "SELECT users_id, access_groups_id, chmod FROM {$this->db_table} WHERE module = ? ";
        }

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $module));
        $st->execute();
        $res = $st->fetchAll(PDO::FETCH_ASSOC);

        if (!$res) {
            // The module was not explicitely set?
            return false;
        }

        foreach ($res as $access) {

            // Owner
            if ($users_id == $access['users_id']) {
                if ($this->hasOctalAccess($access['chmod'], 'owner', $triplet)) {
                    return true;
                }
                else {
                    return false;
                }
            }

            // Group
            $q = "SELECT access_groups_id FROM {$this->db_table_users} WHERE id = ? ";
            $st = $db->pdo->prepare($q);
            $db->autoBind($st, array(1 => $users_id));
            $st->execute();
            $access_groups_id = $st->fetchColumn();

            if ($access_groups_id == $access['access_groups_id']) {

                if ($this->hasOctalAccess($access['chmod'], 'group', $triplet)) {
                    return true;
                }
                else {
                    return false;
                }
            }

            // Other
            if ($this->hasOctalAccess($access['chmod'], 'other', $triplet)) {
                return true;
            }
        }

        return false;
    }


    /**
    * Check if a user has read access to a module
    *
    * @global int $_SESSION['users_id']
    * @param string|int $module
    * @param int $users_id [optional]
    * @return bool
    */
    function hasReadAccess($module, $users_id = null) {

        // This user
        if (!$users_id) {
            if (!empty($_SESSION['users_id'])) $users_id = $_SESSION['users_id'];
            else return false;
        }

        return $this->hasAccess($module, $users_id, 'r');
    }


    /**
    * Check if a user has write access to a module
    *
    * @global int $_SESSION['users_id']
    * @param string|int $module
    * @param int $users_id [optional]
    * @return bool
    */
    function hasWriteAccess($module, $users_id = null) {

        // This user
        if (!$users_id) {
            if (!empty($_SESSION['users_id'])) $users_id = $_SESSION['users_id'];
            else return false;
        }

        return $this->hasAccess($module, $users_id, 'w');
    }


    /**
    * Check if a user has execute access to a module
    *
    * @global int $_SESSION['users_id']
    * @param string|int $module
    * @param int $users_id [optional]
    * @return bool
    */
    function hasExecuteAccess($module, $users_id = null) {

        // This user
        if (!$users_id) {
            if (!empty($_SESSION['users_id'])) $users_id = $_SESSION['users_id'];
            else return false;
        }

        return $this->hasAccess($module, $users_id, 'x');
    }


    /**
    * Select all groups
    *
    * @param int|null $limit [optional]
    * @param int $start [optional]
    * @param string $order [optional]
    * @param string $way [optional] 'ASC', 'DESC'
    * @param bool $fast_paging [optional]
    * @return array
    */
    function dumpGroups($limit = null, $start = 0, $order = 'id', $way = 'ASC', $fast_paging = false) {

        $db = SifuDbInit::get();

        $q = 'SELECT ';
        if ($fast_paging && $db->driver == 'mysql') $q .= 'SQL_CALC_FOUND_ROWS ';
        $q .= "* FROM {$this->db_table_groups} ";

        // Order
        $way = (strtolower($way) == 'asc') ? 'ASC' : 'DESC';
        $q .= "ORDER BY $order $way ";

        // Limit
        if ($start && $limit) $q .= "LIMIT $limit OFFSET $start ";
        elseif ($limit) $q .= "LIMIT $limit ";

        $st = $db->pdo->query($q);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
    * Similar to dumpGroups(), but with a where clause e.g. throttled results.
    *
    * @param string $where
    * @param int|null $limit [optional]
    * @param int $start [optional]
    * @param string $order [optional]
    * @param string $way [optional] 'ASC', 'DESC'
    * @param bool $fast_paging [optional]
    * @return array
    */
    function pumpGroups($where, $limit = null, $start = 0, $order = 'id', $way = 'ASC', $fast_paging = false) {

        $db = SifuDbInit::get();

        $q = 'SELECT ';
        if ($fast_paging && $db->driver == 'mysql') $q .= 'SQL_CALC_FOUND_ROWS ';
        $q .= "* FROM {$this->db_table_groups} WHERE $where ";

        // Order
        $way = (strtolower($way) == 'asc') ? 'ASC' : 'DESC';
        $q .= "ORDER BY $order $way ";

        // Limit
        if ($start && $limit) $q .= "LIMIT $limit OFFSET $start ";
        elseif ($limit) $q .= "LIMIT $limit ";

        $st = $db->pdo->query($q);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
    * Get group
    *
    * @param int|string $group
    * @return array|false
    */
    function getGroup($group) {

        if (filter_var($group, FILTER_VALIDATE_INT)) {
            $q = "SELECT * FROM {$this->db_table_groups} WHERE id = ? ";
        }
        else {
            $q = "SELECT * FROM {$this->db_table_groups} WHERE name = ? ";
        }

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $group));
        $st->execute();
        $item = $st->fetch(PDO::FETCH_ASSOC);

        if (!$item) return false;
        else return $item;
    }


    /**
    * Save group
    *
    * @param int|null $id
    * @param array $item keys match SQL table columns
    * @return int id
    */
    function saveGroup($id, array $item) {

        // --------------------------------------------------------------------
        // Double check for stupidity
        // --------------------------------------------------------------------

        unset($item['id']); // Don't allow spoofing of the id in the array

        if (empty($item['name'])) throw new Exception('Invalid name');

        if (strtolower($item['name']) == 'root' || strtolower($item['name']) == 'banned') {
            throw new Exception("'{$item['name']}' is a reserved group");
        }

        // Check for duplicate
        $res = SifuFunct::exists($this->db_table_groups, 'name', $item['name']);
        if ($res && $res != $id) throw new Exception('Duplicate group: ' . $item['name']);

        // --------------------------------------------------------------------
        // Go!
        // --------------------------------------------------------------------

        // Begin transaction
        $db = SifuDbInit::get();
        $tid = $db->requestTransaction();

        if ($id) {

            // UPDATE
            $item['id'] = $id;
            $q = $db->prepareUpdateQuery($this->db_table_groups, $item);
            $st = $db->pdo->prepare($q);
            $db->autoBind($st, $item);
            $st->execute();
        }
        else {

            // INSERT
            $q = $db->prepareInsertQuery($this->db_table_groups, $item);
            $st = $db->pdo->prepare($q);
            $db->autoBind($st, $item);
            $st->execute();

            $id = $db->lastInsertId($this->db_table_groups);
        }

        // Commit
        $db->commitTransaction($tid);

        return $id;
    }


    /**
    * Delete group
    *
    * @param int $id
    */
    function deleteGroup($id) {

        $db = SifuDbInit::get();

        $q = "SELECT name FROM {$this->db_table_groups} WHERE id = ? ";
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $id));
        $st->execute();
        $name = $st->fetchColumn();

        if ($name == 'root' || $name == 'banned') {
           throw new Exception("'$name' is a reserved group");
        }

        // Begin transaction
        $tid = $db->requestTransaction();

        $q = "DELETE FROM {$this->db_table_groups} WHERE id = ? ";
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $id));
        $st->execute();

        $q = "UPDATE {$this->db_table} SET access_groups_id = -1 WHERE access_groups_id = ? ";
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $id));
        $st->execute();

        $q = "UPDATE {$this->db_table_users} SET access_groups_id = -1 WHERE access_groups_id = ? ";
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $id));
        $st->execute();

        // Commit
        $db->commitTransaction($tid);
    }


    /**
    * Count group
    *
    * @param string $where [optional]
    * @param bool $fast_paging [optional]
    * @return int
    */
    function countGroups($where = null, $fast_paging = false) {

        $db = SifuDbInit::get();

        if ($fast_paging && $db->driver == 'mysql') {

            $q = 'SELECT FOUND_ROWS() ';
        }
        else {

            $q = "SELECT COUNT(id) FROM {$this->db_table_groups} ";
            if ($where) $q .= "WHERE $where ";
        }

        $st = $db->pdo->query($q);
        return $st->fetchColumn();
    }


}

?>