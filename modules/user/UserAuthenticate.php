<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

namespace Sifu\Modules\User;

use Sifu\Funct as Funct;

class UserAuthenticate extends \Sifu\Modules\Module {

    // Module name
    protected static $module = 'user';

    // Maximum password failures
    protected $max_password_failures = 5;


    /**
     * @param \Pimple $c
     */
    function __construct(\Pimple $c) {

        $this->obj = null; // Safety, don't use parent methods
        $this->r = $c['renderer']; // Renderer
        parent::__construct($c); // Let the parent do the rest
    }


    /**
    * Login
    */
    function login() {

        if ($this->user->isValidSession())
            Funct::redirect(Funct::getPreviousURL()); // Already logged in

        // --------------------------------------------------------------------
        // Process
        // --------------------------------------------------------------------

        $errors = array();

        if (!empty($_POST) && $this->getFormError() === false) {

            $auth_user = $this->user->getByNickname($this->_CLEAN['nickname']);

            if (!$auth_user || $auth_user['password'] != $this->user->encryptPw($auth_user['nickname'], $this->_CLEAN['password'])) {

                // User/password failure
                $errors[] = $this->r->gtext('form_error_1');
                $_SESSION['failure'] = isset($_SESSION['failure']) ? ++$_SESSION['failure'] : 1;
            }
            else {

                // Login sucessful
                unset($_SESSION['failure']);
                $this->user->setSession($auth_user['id']);
                Funct::redirect(Funct::getPreviousURL());
            }
        }

        if (isset($_SESSION['failure']) && $_SESSION['failure'] > $this->max_password_failures) {
            $this->r->title .= " | {$this->r->gtext('pw_failure')}";
            $this->tpl->display('pw_failure.tpl');
            exit;
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
        $this->r->text['form_url'] = Funct::makeUrl("/user/authenticate/login");

        // Display
        $this->tpl->display('login.tpl');
    }


    /**
    * Logout
    */
    function logout() {

        if ($this->user->isValidSession()) {
            $this->user->killSession();
        }

        Funct::redirect(Funct::makeUrl('/home'));
    }


}
