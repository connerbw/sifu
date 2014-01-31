<?php

/**
 * @author     Dac Chartrand <dac.chartrand@gmail.com>
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt
 */

class Group extends Module {

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
    * Override
    */
    function form() {

        if (!$this->acl('w')) {
            // Permission error, not allowed to edit
            SifuFunct::redirect(SifuFunct::makeUrl('/globals/permission_error'));
        }

        // --------------------------------------------------------------------
        // Process
        // --------------------------------------------------------------------

        $errors = array();

        if (!empty($_POST) && $this->getFormError() === false) {

            $access = new SifuAccess();

            if (!empty($this->_CLEAN['id'])) {

                $access->saveGroup($this->_CLEAN['id'], $this->_CLEAN);
            }
            else {

                $access->saveGroup(null, $this->_CLEAN);
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
        if (empty($this->r->text['form_url'])) $this->r->text['form_url'] = SifuFunct::makeUrl('/admin/group/new');

        // Display
        $this->tpl->display('edit_group.tpl');
    }


    /**
    * Override
    *
    * @param int $id
    */
    function edit($id) {

        $access = new SifuAccess();
        $form = $access->getGroup($id);

        if (!$form) SifuFunct::redirect(SifuFunct::makeUrl('/admin/group/new'));
        else $this->r->text['form_url'] = SifuFunct::makeUrl("/admin/group/edit/$id");

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
    function delete($id) {

        if (!$this->acl('x')) {
            // Permission error, not allowed to delete
            SifuFunct::redirect(SifuFunct::makeUrl('/globals/permission_error'));
        }

        $access = new SifuAccess();
        $form = $access->deleteGroup($id);
        $this->success();
    }


    /**
    * Override
    */
    function listing($q = null) {

        unset($q); // Unused

        $access = new SifuAccess();
        $pager = new SifuPager();

        $res = $access->dumpGroups($pager->getLimit(), $pager->getStart(), 'name', 'ASC', true);
        $pager->setPages($access->countGroups(null, true));

        array_walk_recursive($res, create_function('&$val', '$val = htmlspecialchars($val, ENT_QUOTES, "UTF-8", false);')); // Sanitize

        $this->r->arr['list'] = $res;
        $this->r->text['pager'] = $pager->pagesHtml(SifuFunct::makeUrl('/admin/group/list'));

        $this->tpl->display('list_group.tpl');
    }

}
