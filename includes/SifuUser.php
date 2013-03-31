<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

class SifuUser extends SifuObject {

    // variables: table names
    public $db_table = 'users';
    public $db_table_info = 'users_info';
    public $db_table_marketing = 'users_marketing';
    public $db_table_access = 'access';
    public $db_table_groups = 'access_groups';


    /**
    * Get user
    *
    * Overrides SifuObject because we retrieve item from more than one table.
    *
    * @param int $id
    * @param bool $full_profile the entire profile?
    * @return array|false
    */
    function get($id, $full_profile = false) {

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare("SELECT * FROM {$this->db_table} WHERE id = ? ");
        $db->autoBind($st, array(1 => $id));
        $st->execute();
        $user = $st->fetch(PDO::FETCH_ASSOC);

        if (!$user) return false; // User doesn't exist?

        if ($full_profile) {
            $st = $db->pdo->prepare("SELECT * FROM {$this->db_table_info} WHERE users_id = ? ");
            $db->autoBind($st, array(1 => $id));
            $st->execute();
            $tmp = $st->fetch(PDO::FETCH_ASSOC);
            if (is_array($tmp)) {
                unset($tmp['id'], $tmp['users_id']); // Unset ids
                $user = array_merge($user, $tmp); // Merge
            }
        }

        return $user;
    }


    /**
    * Save
    *
    * Overrides SifuObject because we split and store $item into more than one
    * table.
    *
    * @param int|null $id
    * @param array $item keys match SQL table columns
    * @return int id
    */
    function save($id, array $item) {

        // --------------------------------------------------------------------
        // Double check for stupidity
        // --------------------------------------------------------------------

        unset($item['id'], $item['users_id']); // Don't allow spoofing of the id in the array
        unset($item['access_groups_id']); // Don't allow users_groups_id changes with this function
        unset($item['image']); // Don't allow image changes with this function

        if ($id != null && $id < 1) throw new Exception('Invalid user id');

        if (!empty($item['nickname'])) {
            $tmp = $this->getByNickname($item['nickname']);
            if ($tmp && $tmp['id'] != $id) throw new Exception('Duplicate nickname');
        }

        if (!empty($item['email'])) {
            $tmp = $this->getByEmail($item['email']);
            if ($tmp && $tmp['id'] != $id) throw new Exception('Duplicate email');
        }

        // Encrypt the password
        if (!empty($item['password'])) {
            if (empty($item['nickname'])) throw new Exception('No nickname provided');
            $item['password'] = $this->encryptPw($item['nickname'], $item['password']);
        }

        // Don't let a user call themseleves "nobody"
        if (mb_strtolower($item['nickname']) == 'nobody') throw new Exception("'nobody' is a reserved user");

        // --------------------------------------------------------------------
        // Move some parts of $item into $user
        // --------------------------------------------------------------------

        $user = array();

        // Nickname
        if (!empty($item['nickname'])) $user['nickname'] = $item['nickname'];
        unset($item['nickname']);

        // Email
        if (!empty($item['email'])) $user['email'] = $item['email'];
        unset($item['email']);

        // Encrypted password
        if (!empty($item['password'])) $user['password'] = $item['password'];
        unset($item['password']);


        // --------------------------------------------------------------------
        // Go!
        // --------------------------------------------------------------------

        // Begin transaction
        $db = SifuDbInit::get();
        $tid = $db->requestTransaction();

        if ($id) {

            // UPDATE
            $user['id'] = $id;
            $q = $db->prepareUpdateQuery($this->db_table, $user);
            $st = $db->pdo->prepare($q);
            $db->autoBind($st, $user);
            $st->execute();

            $item['users_id'] = $id;
            $q = $db->prepareUpdateQuery($this->db_table_info, $item, 'users_id');
            $st = $db->pdo->prepare($q);
            $db->autoBind($st, $item);
            $st->execute();
        }
        else {

            // INSERT
            $q = $db->prepareInsertQuery($this->db_table, $user);
            $st = $db->pdo->prepare($q);
            $db->autoBind($st, $user);
            $st->execute();

            $id = $db->lastInsertId($this->db_table);

            $item['users_id'] = $id;
            $q = $db->prepareInsertQuery($this->db_table_info, $item);
            $st = $db->pdo->prepare($q);
            $db->autoBind($st, $item);
            $st->execute();
        }

        // Commit
        $db->commitTransaction($tid);

        return $id;
    }


    /**
    * Delete
    *
    * Overrides SifuObject because we don't delete root users
    *
    * @param int $id
    */
    function delete($id) {

        // Double check for stupidity
        $access = new SifuAccess();
        if ($access->isRoot($id)) throw new Exception('Cannot delete a root user');

        // Let the parent do the rest
        parent::delete($id);
    }


    /**
    * Get a user by nickname
    *
    * @param string $nickname nickname
    * @param bool $full_profile the entire profile?
    * @return array|false
    */
    function getByNickname($nickname, $full_profile = false) {

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare("SELECT id FROM {$this->db_table} WHERE nickname = ? ");
        $db->autoBind($st, array(1 => $nickname));
        $st->execute();
        $id = $st->fetchColumn();

        if ($id) return $this->get($id, $full_profile);
        else return false;
    }


    /**
    * Get a user by email
    *
    * @param string $email email
    * @param bool $full_profile the entire profile?
    * @return array|false
    */
    function getByEmail($email, $full_profile = false) {

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare("SELECT id FROM {$this->db_table} WHERE email = ? ");
        $db->autoBind($st, array(1 => $email));
        $st->execute();
        $id = $st->fetchColumn();

        if ($id) return $this->get($id, $full_profile);
        else return false;
    }


    /**
    * Get user group
    *
    * @global int $_SESSION['users_id']
    * @param int $users_id [optional]
    * @return array id, name
    */
    function getGroup($id = null) {

        // This user
        if (!$id) {
            if (!empty($_SESSION['users_id'])) $id = $_SESSION['users_id'];
            else return false;
        }

        $q = "
        SELECT {$this->db_table_groups}.id, {$this->db_table_groups}.name FROM {$this->db_table_groups}
        LEFT JOIN {$this->db_table} ON {$this->db_table_groups}.id = {$this->db_table}.access_groups_id
        WHERE {$this->db_table}.id = ?
        ";

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $id));
        $st->execute();
        return $st->fetch(PDO::FETCH_ASSOC);
    }


    /**
    * Save user group
    *
    * @param int $id
    * @param int $group_id
    */
    function saveGroup($id, $group_id) {

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare("UPDATE {$this->db_table} SET access_groups_id = ? WHERE id = ? ");
        $db->autoBind($st, array(1 => $group_id, 2 => $id));
        $st->execute();
    }


    /**
    * Get user image
    *
    * @global int $_SESSION['users_id']
    * @param int $id [optional]
    * @return string image name
    */
    function getImage($id = null) {

        // This user
        if (!$id) {
            if (!empty($_SESSION['users_id'])) $id = $_SESSION['users_id'];
            else return false;
        }

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare("SELECT image FROM {$this->db_table_info} WHERE users_id = ? ");
        $db->autoBind($st, array(1 => $id));
        $st->execute();
        return $st->fetchColumn();
    }


    /**
    * Save user image
    *
    * @param int $id
    * @param string $image filename
    */
    function saveImage($id, $image) {

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare("UPDATE {$this->db_table_info} SET image = ? WHERE users_id = ? ");
        $db->autoBind($st, array(1 => $image, 2 => $id));
        $st->execute();
    }


    // -----------------------------------------------------------------------
    // User access
    // -----------------------------------------------------------------------

    /**
    * Ban a user. Uses reserved keyword 'banned' in users_groups table.
    *
    * @param int $id
    */
    function ban($id) {

        $db = SifuDbInit::get();

        $q = "SELECT id FROM {$this->db_table_groups} WHERE name = 'banned' ";
        $st = $db->pdo->query($q);
        $users_groups_id = $st->fetchColumn();

        if (!$users_groups_id) throw new Exception('Banned is undefined');

        $st = $db->pdo->prepare("UPDATE {$this->db_table} SET access_groups_id = ? WHERE id = ? ");
        $db->autoBind($st, array(1 => $users_groups_id, 2 => $id));
        $st->execute();
    }


    /**
    * Root a user.  Uses reserved keyword 'root' in users_group table..
    *
    * @param int $id
    */
    function root($id) {

        $db = SifuDbInit::get();

        $q = "SELECT id FROM {$this->db_table_groups} WHERE name = 'root' ";
        $st = $db->pdo->query($q);
        $users_groups_id = $st->fetchColumn();

        if (!$users_groups_id) throw new Exception('Root is undefined');

        $st = $db->pdo->prepare("UPDATE {$this->db_table} SET access_groups_id = ? WHERE id = ? ");
        $db->autoBind($st, array(1 => $users_groups_id, 2 => $id));
        $st->execute();
    }


    // -----------------------------------------------------------------------
    // Security
    // -----------------------------------------------------------------------


    /**
    * Perform one-way encryption of a password
    *
    * @param string $nickname
    * @param string $password
    * @return string
    */
    function encryptPw($nickname, $password) {

        return md5(strrev("{$nickname}:{$password}"));
    }

    /**
    * Check if a user is logged in
    *
    * @param string $redirect a URL to rediect to if the security check fails
    * @return bool
    */
    function isValidSession() {

        if (!empty($_SESSION['users_id']) && !empty($_SESSION['nickname']) && !empty($_SESSION['token'])) {
            if ($this->isValidToken($_SESSION['users_id'], $_SESSION['token'])) {
                return true;
            }
        }
        return false;
    }


    /**
    * Check if a token is valid
    *
    * @global string $CONFIG['SALT']
    * @param int $id user id
    * @param string $token token
    * @return bool
    */
    private function isValidToken($id, $token) {

        $q = "
        SELECT {$this->db_table}.password, {$this->db_table_groups}.name FROM {$this->db_table}
        LEFT JOIN {$this->db_table_groups} ON {$this->db_table_groups}.id = {$this->db_table}.access_groups_id
        WHERE {$this->db_table}.id = ?
        ";

        $db = SifuDbInit::get();
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $id));
        $st->execute();
        $row = $st->fetch();

        // Forcibly redirect a banned user
        if (strtolower($row['name']) == 'banned') {
            $this->killSession();
            SifuFunct::redirect(SifuFunct::makeUrl('/banned'));
        }

        if (empty($row['password'])) {
            return false;
        }
        elseif ($token != md5(date('W') . $row['password'] . @$GLOBALS['CONFIG']['SALT'])) {
            return false;
        }

        return true;
    }


    /**
    * Set a user session
    *
    * @global string $CONFIG['SALT']
    * @global string $CONFIG['LANGUAGE']
    * @param string $id
    */
    function setSession($id) {

        $user = $this->get($id, true);

        if (!$user) {
            $this->killSession();
            return false;
        }

        @session_regenerate_id();

        $_SESSION['users_id'] = $user['id'];
        $_SESSION['nickname'] = $user['nickname'];
        $_SESSION['token'] = md5(date('W') . $user['password'] . $GLOBALS['CONFIG']['SALT']);
        $_SESSION['language'] = isset($user['language']) ? $user['language'] :  $GLOBALS['CONFIG']['LANGUAGE'];
    }


    /**
    * Kill $_SESSION
    */
    function killSession() {

        // Keep breadcrumbs
        $tmp = array();
        if (isset($_SESSION['breadcrumbs'])) $tmp = $_SESSION['breadcrumbs'];

        $_SESSION = array();
        session_destroy();

        @session_start();
        $_SESSION['breadcrumbs'] = $tmp;
    }


}