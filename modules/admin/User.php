<?php

/**
 * @author     Dac Chartrand <dac.chartrand@gmail.com>
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt
 */

namespace Sifu\Modules\Admin;

use Sifu\Funct as Funct;
use Sifu\DbInit as DbInit;

class User extends \Sifu\Modules\Module {

    // Module name
    protected static $module = 'admin';

    /**
     * @var \Sifu\User
     */
    protected $obj;


    /**
     * @param \Pimple $c
     */
    function __construct(\Pimple $c) {

        // Fill in the blanks
        $this->obj = $c['obj'];
        $this->module_url = '/admin/user';
        $this->template_name = 'user';

        // Renderer
        $this->r = $c['renderer'];

        // Let the parent do the rest
        parent::__construct($c);
    }


    /**
    * Override
    *
    * @global bool $CONFIG['REGISTRATIONS']
    */
    function form() {

        if (@$this->r->bool['edit_mode'] !== true && !$GLOBALS['CONFIG']['REGISTRATIONS'] && !$this->acl('w')) {
            // Permission error, registrations are disabled
            Funct::redirect(Funct::makeUrl('/globals/permission_error'));
        }

        if (@$this->r->bool['edit_mode'] !== true && isset($_SESSION['users_id']) && !$this->acl('w')) {
            // Permission error, not allowed to create new users
            Funct::redirect(Funct::makeUrl('/globals/permission_error'));
        }

        if (@$this->r->bool['edit_mode'] === true && @$_SESSION['users_id'] != $this->tpl->getTemplateVars('id') && !$this->acl('w')) {
            // Permission error, not allowed to edit others
            Funct::redirect(Funct::makeUrl('/globals/permission_error'));
        }

        // --------------------------------------------------------------------
        // Process
        // --------------------------------------------------------------------

        $errors = array();

        if (!empty($_POST) && $this->getFormError() === false) {

            // Retrieve and unset 'access_groups_id' from _CLEAN array
            if (isset($this->_CLEAN['access_groups_id'])) $group_id = $this->_CLEAN['access_groups_id'];
            unset($this->_CLEAN['access_groups_id']);

            // Retrieve and unset 'how_they_heard_about_us' from _CLEAN array
            $how_they_heard_about_us = isset($this->_CLEAN['how_they_heard_about_us']) ? $this->_CLEAN['how_they_heard_about_us'] : null;
            unset($this->_CLEAN['how_they_heard_about_us']);

            // Save user
            if (!empty($this->_CLEAN['id'])) {

                    $this->obj->save($this->_CLEAN['id'], $this->_CLEAN);
            }
            else {

                $this->_CLEAN['id'] = $this->obj->save(null, $this->_CLEAN);
            }

            // Save group
            if (isset($group_id) && $this->acl('w')) $this->obj->saveGroup($this->_CLEAN['id'], $group_id);

            // Marketing
            if (@$this->r->bool['edit_mode'] !== true && empty($_SESSION['users_id'])) {

                $tmp['users_id'] = $this->_CLEAN['id'];
                $tmp['how_they_heard_about_us'] = $how_they_heard_about_us;

                $cookie = \Sifu\Marketing::getMarketingCookie();
                if ($cookie) {
                    $tmp = array_merge($tmp, $cookie);
                }

                Funct::shampoo($tmp, array(
                    'id', 'users_id', 'referrer', 'referral_id', 'landing_page',
                    'search_keywords', 'how_they_heard_about_us'
                    ));

                $marketing = new \Sifu\Marketing();
                $marketing->save(null, $tmp);
            }

            // If the user edits themselves, reset session
            if (isset($_SESSION['users_id']) && $_SESSION['users_id'] == $this->_CLEAN['id']) {
                $this->obj->setSession($this->_CLEAN['id']);
            }

            $this->success();
        }


        // --------------------------------------------------------------------
        // Template
        // --------------------------------------------------------------------

        // Errors detected in controller
        if ($this->getFormError()) $this->r->bool['form_error'] = true;

        // Errors detected while checking form
        if (count($errors)) {
            $this->r->bool['form_error'] = true;
            $this->r->arr['errors'] = $errors;
        }

        // Other variables
        $this->tpl->assign($_POST);
        if (empty($this->r->text['form_url'])) $this->r->text['form_url'] = Funct::makeUrl($this->module_url . '/new');

        // Display
        $this->tpl->display("edit_{$this->template_name}.tpl");
    }


    /**
    * Override
    *
    * @param int $id
    */
    function edit($id) {

        $form = $this->obj->get($id, true); // Full profile

        if (!$form) Funct::redirect(Funct::makeUrl($this->module_url . '/new'));
        else $this->r->text['form_url'] = Funct::makeUrl($this->module_url . "/edit/$id");

        array_walk_recursive($form, create_function('&$val', '$val = htmlspecialchars($val, ENT_QUOTES, "UTF-8", false);')); // Sanitize

        $this->r->bool['edit_mode'] = true;
        $this->tpl->assign($form);
        $this->form();
    }


    /**
    * Override
    *
    * @param int $id
    */
    function duplicate($id) {

        $form = $this->obj->get($id, true); // Full profile

        unset($form['id']);

        array_walk_recursive($form, create_function('&$val', '$val = htmlspecialchars($val, ENT_QUOTES, "UTF-8", false);')); // Sanitize

        $this->r->bool['dupe_mode'] = true;
        $this->tpl->assign($form);
        $this->form();
    }


    /**
    * Override with custom SQL query
    */
    function listing($q = null) {

        unset($q); // Unused

        if (!$this->acl('r')) {
            // Permission error, not allowed to export
            Funct::redirect(Funct::makeUrl('/globals/permission_error'));
        }

        $user = $this->obj;
        $db = DbInit::get();

        $q = 'SELECT ';
        if ($db->driver == 'mysql') $q .= 'SQL_CALC_FOUND_ROWS ';
        $q .= "
        {$user->db_table}.*, {$user->db_table_groups}.name AS access_groups_name
        FROM {$user->db_table}
        LEFT JOIN {$user->db_table_groups} ON {$user->db_table_groups}.id = {$user->db_table}.{$user->db_table_groups}_id
        ORDER BY nickname ASC
        ";

        // Let the parent do the rest
        parent::listing($q);
    }


    /**
    * Override, get full profile
    */
    function imprint($id) {

        $form = $this->obj->get($id, true); // Full profile
        $this->tpl->assign($form);

        // Let the parent do the rest
        parent::imprint($id);
    }


}
