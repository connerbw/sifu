<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/
abstract class Module {

    // ------------------------------------------------------------------------
    // Variables
    // ------------------------------------------------------------------------

    /**
    * sanitized $_GET/$_POST
    * @var array
    */
    public $_CLEAN = array();

    /**
    * JS Console code
    * @var string
    */
    public $js_console;

    /**
    * SifuRenderer()
    * @var object
    */
    public $r;

    /**
    * @var SifuTemplate
    */
    public $tpl;

    /**
    * @var SifuObject
    */
    protected $obj;

    /**
    * SifuUser()
    * @var object
    */
    protected $user;

    /**
    * Module Url
    * @var string
    */
    protected $module_url;

    /**
    * Template name
    * @var string
    */
    protected $template_name;

    /**
    * SQL sort order
    * @var string
    */
    protected $order_by = 'id';

    /**
    * SQL sort way
    * @var string ASC, DESC
    */
    protected $sort_order = 'ASC';

    /**
    * @var bool
    */
    protected $form_error = false;

    /**
     * Module name, maximum 20 characters, ISO 8859-1
     * @var string
     */
    static protected $module;

    // ------------------------------------------------------------------------
    // Methods
    // ------------------------------------------------------------------------

    /**
     * @return mixed
     */
    static function getModuleName() {
        return static::$module;
    }


    /**
     * @param Pimple $c
     * @throws Exception
     */
    function __construct(Pimple $c) {

        // Pre-condition sanity check
        if (empty(static::$module)) throw new Exception('static::$module not set');
        if (!($this->r instanceof SifuRenderer)) throw new Exception('$this->r is not an instance of SifuRenderer()');
        if (isset($this->obj) && !($this->obj instanceof SifuObject)) throw new Exception('$this->obj is not an instance of SifuObject()');

        // Template
        $this->tpl = $c['template']; // Template
        $this->tpl->assignByRef('r', $this->r); // Renderer referenced in template
        $this->r->js_console =& $this->js_console; // JS Console reference
        $this->tpl->configLoad('my.conf', static::$module); // Config variables
        // Common objects
        $this->user = $c['user'];
        // Fix stupidities
        if (isset($this->module_url)) {
            $this->module_url = trim($this->module_url);
            $this->module_url = rtrim($this->module_url, '/');
        }
    }


    /**
    * Wrapper, Access control list
    *
    * @param string $val 'root', 'r', 'w', 'x'
    * @return bool
    */
    function acl($val) {

        return SifuFunct::acl($val, static::$module);
    }


    /**
    * Set form error
    *
    * @param bool $val
    */
    function setFormError($val) {

        $this->form_error = $val;
    }


    /**
    * Get form error
    *
    * @return bool $val
    */
    function getFormError() {

        return $this->form_error;
    }


    /**
    * Default controller flow
    *
    * $params[0] new, edit, duplicate, delete, list, imprint, export
    * $params[1] integer
    *
    * @param array $params
    * @param  string $redirect (optional)
    * @return string $redirect
    */
    function flow($params, $redirect = null) {

        $do = @$params[0];
        if (empty($redirect)) $redirect = SifuFunct::getPreviousURL();

        if ($do == 'new') {

            // New
            $this->form();
            return;
        }
        elseif ($do == 'duplicate' || $do == 'edit' || $do == 'imprint' || $do == 'delete') {

            // Duplicate, edit, imprint, delete
            if (empty($params[1]) || filter_var($params[1], FILTER_VALIDATE_INT) === false) {

                SifuFunct::redirect(SifuFunct::makeUrl($redirect));
            }

            $this->$do($params[1]);
            return;
        }
        elseif ($do == 'list') {

            // Listing
            $this->listing();
            return;

        }
        elseif ($do == 'export') {

            // Export
            $this->export();
            return;
        }
        else {

            // Error, Redirect
            SifuFunct::redirect(SifuFunct::getPreviousURL());
        }
    }


    /**
    */
    function form() {

        if (!($this->obj instanceof SifuObject))
            throw new Exception('$this->obj is not an instance of SifuObject()');

        if (empty($this->module_url))
            throw new Exception('$this->module_url is not set');

        if (empty($this->template_name))
            throw new Exception('$this->template_name is not set');

        if (!$this->acl('w')) {
            // Permission error, not allowed to edit
            SifuFunct::redirect(SifuFunct::makeUrl('/globals/permission_error'));
        }

        // --------------------------------------------------------------------
        // Process
        // --------------------------------------------------------------------

        if (!empty($_POST) && $this->getFormError() === false) {

            if (!empty($this->_CLEAN['id'])) {

                $this->obj->save($this->_CLEAN['id'], $this->_CLEAN);
            }
            else {

                $this->obj->save(null, $this->_CLEAN);
            }

            $this->success();
        }


        // --------------------------------------------------------------------
        // Template
        // --------------------------------------------------------------------

        // Errors detected in controller
        if ($this->getFormError()) $this->r->bool['form_error'] = true;

        // Errors detected in child?
        if (isset($this->r->arr['errors']) && is_array($this->r->arr['errors']) && count($this->r->arr['errors'])) {
            $this->r->bool['form_error'] = true;
        }

        // Other variables
        $this->tpl->assign($_POST);
        if (empty($this->r->text['form_url'])) $this->r->text['form_url'] = SifuFunct::makeUrl($this->module_url . '/new');

        // Display
        $this->tpl->display("edit_{$this->template_name}.tpl");
    }


    /**
    * @param int $id
    * @throws Exception
    */
    function duplicate($id) {

        if (!($this->obj instanceof SifuObject))
            throw new Exception('$this->obj is not an instance of SifuObject()');

        $form = $this->obj->get($id);

        unset($form['id']);

        array_walk_recursive($form, create_function('&$val', '$val = htmlspecialchars($val, ENT_QUOTES, "UTF-8", false);')); // Sanitize

        $this->r->bool['dupe_mode'] = true;
        $this->tpl->assign($form);
        $this->form();
    }


    /**
    * @param int $id
    * @throws Exception
    */
    function edit($id) {

        if (!($this->obj instanceof SifuObject))
            throw new Exception('$this->obj is not an instance of SifuObject()');

        $form = $this->obj->get($id);

        if (!$form) SifuFunct::redirect(SifuFunct::makeUrl($this->module_url . '/new'));
        else $this->r->text['form_url'] = SifuFunct::makeUrl($this->module_url . "/edit/$id");

        array_walk_recursive($form, create_function('&$val', '$val = htmlspecialchars($val, ENT_QUOTES, "UTF-8", false);')); // Sanitize

        $this->r->bool['edit_mode'] = true;
        $this->tpl->assign($form);
        $this->form();
    }


    /**
    * @param int $id
    * @throws Exception
    */
    function imprint($id) {

        if (!($this->obj instanceof SifuObject))
            throw new Exception('$this->obj is not an instance of SifuObject()');

        if (empty($this->template_name))
            throw new Exception('$this->template_name is not set');

        if (!$this->acl('r')) {
            // Permission error, not allowed to print
            SifuFunct::redirect(SifuFunct::makeUrl('/globals/permission_error'));
        }

        // --------------------------------------------------------------------
        // Procedure
        // --------------------------------------------------------------------

        $form = $this->obj->get($id);
        if (!$form) SifuFunct::redirect(SifuFunct::makeUrl($this->module_url));

        array_walk_recursive($form, create_function('&$val', '$val = htmlspecialchars($val, ENT_QUOTES, "UTF-8", false);')); // Sanitize

        $this->tpl->assign($form);
        $this->tpl->display("print_{$this->template_name}.tpl");
    }


    /**
    * @param int $id
    * @throws Exception
    */
    function delete($id) {

        if (!($this->obj instanceof SifuObject))
            throw new Exception('$this->obj is not an instance of SifuObject()');

        if (!$this->acl('x')) {
            // Permission error, not allowed to delete
            SifuFunct::redirect(SifuFunct::makeUrl('/globals/permission_error'));
        }

        $form = $this->obj->delete($id);
        $this->success();
    }


    /**
    * @param string $q [optional] custom SQL query
    * @throws Exception
    */
    function listing($q = null) {

        if (!($this->obj instanceof SifuObject))
            throw new Exception('$this->obj is not an instance of SifuObject()');

        if (empty($this->module_url))
            throw new Exception('$this->module_url is not set');

        if (empty($this->template_name))
            throw new Exception('$this->template_name is not set');


        // --------------------------------------------------------------------
        // Procedure
        // --------------------------------------------------------------------

        $pager = new SifuPager();

        if ($q) {

            $q .= ' LIMIT ' . $pager->getLimit() . ' OFFSET ' . $pager->getStart() . ' ';
            $db = SifuDbInit::get();
            $st = $db->pdo->query($q);
            $res = $st->fetchAll(PDO::FETCH_ASSOC);
        }
        else {

            $res = $this->obj->dump($pager->getLimit(), $pager->getStart(), $this->order_by, $this->sort_order, true);
        }

        $pager->setPages($this->obj->count(null, true));

        array_walk_recursive($res, create_function('&$val', '$val = htmlspecialchars($val, ENT_QUOTES, "UTF-8", false);')); // Sanitize

        $this->r->arr['list'] = $res;
        $this->r->text['pager'] = $pager->pagesHtml(SifuFunct::makeUrl($this->module_url . '/list'));

        $this->tpl->display("list_{$this->template_name}.tpl");
    }


    /**
    * Export CSV File
    *
    * @param string $q [optional] custom SQL query
    * @throws Exception
    */
    function export($q = null) {

        if (!($this->obj instanceof SifuObject))
            throw new Exception('$this->obj is not an instance of SifuObject()');

        if (empty($this->template_name))
            throw new Exception('$this->template_name is not set');

        if (!$this->acl('r')) {
            // Permission error, not allowed to export
            SifuFunct::redirect(SifuFunct::makeUrl('/globals/permission_error'));
        }

        // --------------------------------------------------------------------
        // Procedure
        // --------------------------------------------------------------------

        if ($q) {

            $db = SifuDbInit::get();
            $st = $db->pdo->query($q);
            $res = $st->fetchAll(PDO::FETCH_ASSOC);
        }
        else {

            $res = $this->obj->dump(null, 0, $this->order_by, $this->sort_order);
        }

        $this->_exportCSV($res);
        exit;
    }


    /**
    * Redirect to generic success page
    */
    function success() {

        SifuFunct::redirect(SifuFunct::makeUrl('/globals/success'));
    }


    /**
    * Default page
    */
    function fallback() {

        $this->tpl->display('default.tpl');
    }


    /**
    * @param array $res PDO::FETCH_ASSOC
    * @throws Exception
    */
    protected function _exportCSV($res) {

        $output = fopen('php://output', 'w');
        if (!$output) throw new Exception("Can't open php://output");

        $filename = $this->template_name;

        header('Content-Description: File Transfer');
        header('Content-Type: application/csv; charset=utf-8');
        header('Content-Disposition: attachement; filename="' . $filename . '_' . date('Y-m-d') . '.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        @ob_clean();
        flush();

        if (count($res)) {

            // CSV header
            $tmp = array();
            foreach ($res[0] as $key => $val) {
                $key = str_replace(array('\"'), null, $key); // Delete rare but problematic character combinations
                $tmp[] = $key;
            }
            fputcsv($output, $tmp);

            // CSV fields
            foreach ($res as $fields) {
                $fields = str_replace(array('\"'), null, $fields); // Ditto
                fputcsv($output, $fields);
            }
        }

        if (!fclose($output)) throw new Exception("Can't close php://output");
    }

}
