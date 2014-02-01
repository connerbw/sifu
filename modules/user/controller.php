<?php

/**
 * @author     Dac Chartrand <dac.chartrand@gmail.com>
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt
 */
function sifu($action, $params = null)
{

    $c = new \Pimple();
    $c['user'] = function() { return new \Sifu\User(); };
    $c['template'] = function() { return new \Sifu\Template(\Sifu\Modules\User\UserAuthenticate::getModuleName()); };
    $c['renderer'] = function() { return new \Sifu\Renderer(\Sifu\Modules\User\UserAuthenticate::getModuleName()); };

    if ('authenticate' == $action) {

        // ---------------------------------------------------------------------
        // Authenticate
        // ---------------------------------------------------------------------

        $obj = new \Sifu\Modules\User\UserAuthenticate($c);

        if (!empty($_POST)) {

            $error = false;
            $keys = array(
                'nickname',
                'password',
                );
            \Sifu\Funct::shampoo($_POST, $keys);

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

                \Sifu\Funct::redirect(\Sifu\Funct::makeUrl('/user'));
        }
    }
    else {

        // ---------------------------------------------------------------------
        // Default
        // ---------------------------------------------------------------------

        \Sifu\Funct::redirect(\Sifu\Funct::makeUrl('/home'));
    }
}
