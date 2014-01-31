<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

class Marketing extends Module {

    // Module name
    protected static $module = 'admin';


    /**
     * @param Pimple $c
     */
    function __construct(Pimple $c) {

        $this->obj = null; // Safety, don't use parent methods
        $this->r = $c['renderer']; // Renderer
        parent::__construct($c); // Let the parent do the rest

        if (!$this->acl('r')) {
            // Permission error
            SifuFunct::redirect(SifuFunct::makeUrl('/globals/permission_error'));
        }
    }


    /**
    */
    function dump() {

        $user = new SifuUser();
        $marketing = new SifuMarketing();
        $db = SifuDbInit::get();

        $q = "SELECT
        {$marketing->db_table}.*, {$user->db_table}.nickname, {$user->db_table}.email, {$user->db_table_info}.*
        FROM {$marketing->db_table}
        LEFT JOIN {$user->db_table_info} ON {$user->db_table_info}.users_id = {$marketing->db_table}.users_id
        LEFT JOIN {$user->db_table} ON {$user->db_table}.id = {$marketing->db_table}.users_id
        ";

        $st = $db->pdo->query($q);
        $res = $st->fetchAll(PDO::FETCH_ASSOC);

        // Sanitze
        foreach ($res as $key => &$val) {
            unset($val['id']);
            unset($val['image']);
            if ($val['dob'] == '0000-00-00') $val['dob'] = null;
            $val['url'] = !empty($val['url']) ? SifuFunct::canonicalizeUrl($val['url']) : null;
            $val['referrer'] = !empty($val['referrer']) ? SifuFunct::canonicalizeUrl($val['referrer']) : null;
        }
        unset($val); // dereference

        array_walk_recursive($res, create_function('&$val', '$val = htmlspecialchars($val, ENT_QUOTES, "UTF-8", false);')); // Sanitize

        $this->r->arr['list'] = $res;

        $this->tpl->display('dump_marketing_table.tpl');
    }


    /**
    */
    function export($q = null) {

        unset($q); // Unused

        $user = new SifuUser();
        $marketing = new SifuMarketing();

        $q = "SELECT
        {$marketing->db_table}.*, {$user->db_table}.nickname, {$user->db_table}.email, {$user->db_table_info}.*
        FROM {$marketing->db_table}
        LEFT JOIN {$user->db_table_info} ON {$user->db_table_info}.users_id = {$marketing->db_table}.users_id
        LEFT JOIN {$user->db_table} ON {$user->db_table}.id = {$marketing->db_table}.users_id
        ";

        // Let the parent do the rest
        $this->obj = $marketing;
        $this->template_name = 'marketing';
        parent::export($q);
    }

}
