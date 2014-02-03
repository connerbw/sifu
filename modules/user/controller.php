<?php

/**
 * @author     Dac Chartrand <dac.chartrand@gmail.com>
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt
 */

namespace Sifu\Modules\User;

use Sifu\Funct as Funct;

function sifu($action, $params = null)
{

    $c = new \Pimple();
    $c['user'] = function() { return new \Sifu\User(); };
    $c['template'] = function() { return new \Sifu\Template(UserAuthenticate::getModuleName()); };
    $c['renderer'] = function() { return new \Sifu\Renderer(UserAuthenticate::getModuleName()); };

    if ('authenticate' == $action) {

        // ---------------------------------------------------------------------
        // Authenticate
        // ---------------------------------------------------------------------

        $obj = new UserAuthenticate($c);

        if (!empty($_POST)) {

            $error = false;
            $keys = array(
                'nickname',
                'password',
                );
            Funct::shampoo($_POST, $keys);

            // Required
            if (!_POST('nickname')) $error = 'nickname';
            if (!_POST('password')) $error = 'password';

            if ($error) {
                // Write error to console
                $obj->js_console .= \Sifu\Log::jsConsole('Error: ', $error);
                $obj->js_console .= \Sifu\Log::jsConsole('$_POST: ', $_POST);
                // Set form error
                $obj->setFormError(true);
            }
            else $obj->_CLEAN = $_POST;
        }

        switch (isset($params[0]) ? $params[0] : 'default') {

            case 'login':

                // ----------------------------------------------------------------
                // Login
                // -----------------------------------------------------------------

                $obj->login();
                break;


            case 'logout':

                // -----------------------------------------------------------------
                // Logout
                // -----------------------------------------------------------------

                $obj->logout();
                break;


            default:

                Funct::redirect(Funct::makeUrl('/user'));
        }
    }
    else {

        // ---------------------------------------------------------------------
        // Default
        // ---------------------------------------------------------------------

        Funct::redirect(Funct::makeUrl('/home'));
    }
}
