<?php

/**
 * @author     Dac Chartrand <dac.chartrand@gmail.com>
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt
 */

namespace Sifu\Modules\Admin;

use Sifu\Funct as Funct;
use Sifu\DbInit as DbInit;

class Permissions extends \Sifu\Modules\Module {

    // Module name
    protected static $module = 'admin';

    /**
     * @var \Sifu\Access
     */
    protected $obj;


    /**
     * @param \Pimple $c
     */
    function __construct(\Pimple $c) {

        // Fill in the blanks
        $this->obj = $c['obj'];
        $this->module_url = '/admin/permissions';
        $this->template_name = 'permissions';

        // Renderer
        $this->r = $c['renderer'];

        // Let the parent do the rest
        parent::__construct($c);

        if (!$this->acl('r')) {
            // Permission error
            Funct::redirect(Funct::makeUrl('/globals/permission_error'));
        }
    }


    /**
    * Override with custom SQL query
    */
    function listing($q = null) {

        unset($q); // Unused

        $access = $this->obj;
        $db = DbInit::get();

        $q = 'SELECT ';
        if ($db->driver == 'mysql') $q .= 'SQL_CALC_FOUND_ROWS ';
        $q .= "
        {$access->db_table}.*, {$access->db_table_groups}.name AS access_groups_name, {$access->db_table_users}.nickname AS users_nickname
        FROM {$access->db_table}
        LEFT JOIN {$access->db_table_groups} ON {$access->db_table_groups}.id = {$access->db_table}.{$access->db_table_groups}_id
        LEFT JOIN {$access->db_table_users} ON {$access->db_table_users}.id = {$access->db_table}.{$access->db_table_users}_id
        ORDER BY module ASC
        ";

        // Let the parent do the rest
        parent::listing($q);
    }

}
