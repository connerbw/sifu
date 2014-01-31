<?php

/**
 * @author     Dac Chartrand <dac.chartrand@gmail.com>
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt
 */
function sifu($action, $params = null)
{

    $c = new Pimple();
    $c['user'] = function() { return new SifuUser(); };
    $c['template'] = function() { return new SifuTemplate(UserAuthenticate::getModuleName()); };
    $c['renderer'] = function() { return new SifuRenderer(UserAuthenticate::getModuleName()); };

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
            SifuFunct::shampoo($_POST, $keys);

            // Required
            if (!_POST('nickname')) $error = 'nickname';
            if (!_POST('password')) $error = 'password';

            if ($error) {
                // Write error to console
                $obj->js_console .= SifuLog::jsConsole('Error: ', $error);
                $obj->js_console .= SifuLog::jsConsole('$_POST: ', $_POST);
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

                SifuFunct::redirect(SifuFunct::makeUrl('/user'));
        }
    }
    else {

        // ---------------------------------------------------------------------
        // Default
        // ---------------------------------------------------------------------

        SifuFunct::redirect(SifuFunct::makeUrl('/home'));
    }
}
