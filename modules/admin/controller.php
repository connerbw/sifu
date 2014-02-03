<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

namespace Sifu\Modules\Admin;

use Sifu\Funct as Funct;

function sifu($action, $params = null) {

    $c = new \Pimple();
    $c['user'] = function() { return new \Sifu\User(); };

    if ('user' == $action) {

        // ---------------------------------------------------------------------
        // User
        // ---------------------------------------------------------------------

        $c['template'] = function() { return new \Sifu\Template(User::getModuleName()); };
        $c['renderer'] = function() { return new AdminRenderer(User::getModuleName()); };
        $c['obj'] = function() { return new \Sifu\User(); };
        $obj = new User($c);

        if (!empty($_POST)) {

            $error = false;
            $keys = array(
                'access_groups_id',
                'country',
                'dob',
                'email',
                'family_name',
                'gender',
                'given_name',
                'how_they_heard_about_us',
                'id',
                'language',
                'locality',
                'nickname',
                'password',
                'postcode',
                'region',
                'street_address',
                'tel',
                'timezone',
                'url',
                );
            Funct::shampoo($_POST, $keys);

            // Required
            if (!_POST('nickname')) $error = 'nickname';
            if (filter_var(_POST('email'), FILTER_VALIDATE_EMAIL) === false) $error = 'email';
            if (isset($_SESSION['nickname']) && isset($_SESSION['users_id']) && _POST('id') && _POST('id') == $_SESSION['users_id'] && _POST('nickname') != $_SESSION['nickname'] && !_POST('password')) $error = 'password';
            elseif(!_POST('id') && !_POST('password')) $error = 'password';
            // Optional
            if (_POST('id') && filter_var(_POST('id'), FILTER_VALIDATE_INT) === false) $error = 'id';
            if (_POST('access_groups_id') && filter_var(_POST('access_groups_id'), FILTER_VALIDATE_INT) === false) $error = 'access_groups_id';
            if (_POST('url') && filter_var(_POST('url'), FILTER_VALIDATE_URL) === false) $error = 'url';
            if (_POST('dob') && !preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/", _POST('dob'))) $error = 'dob'; // YYYY-MM-DD
            // No dupes
            $res = Funct::exists('users', 'nickname', _POST('nickname'));
            if ($res && $res != _POST('id')) $error = 'nickname';
            $res = Funct::exists('users', 'nickname', _POST('email'));
            if ($res && $res != _POST('id')) $error = 'email';

            if ($error) {
                // Write error to console
                $obj->js_console .= \Sifu\Log::jsConsole('Error: ', $error);
                $obj->js_console .= \Sifu\Log::jsConsole('$_POST: ', $_POST);
                // Set form error
                $obj->setFormError(true);
            }
            else $obj->_CLEAN = $_POST;
        }

        // No export
        if (isset($params[0]) && $params[0] == 'export') {

            Funct::redirect(Funct::getPreviousURL());
        }
        $obj->flow($params, '/admin/user');

    }
    elseif ('permissions' == $action) {

        // ---------------------------------------------------------------------
        // Permissions
        // ---------------------------------------------------------------------

        $c['template'] = function() { return new \Sifu\Template(Permissions::getModuleName()); };
        $c['renderer'] = function() { return new AdminRenderer(Permissions::getModuleName()); };
        $c['obj'] = function() { return new \Sifu\Access(); };
        $obj = new Permissions($c);

        if (!empty($_POST)) {

            $error = false;
            $keys = array(
                'access_groups_id',
                'chmod',
                'id',
                'module',
                'users_id',
                );
            Funct::shampoo($_POST, $keys);

            // Required
            if (!_POST('module')) $error = 'module';
            if (filter_var(_POST('users_id'), FILTER_VALIDATE_INT) === false) $error = 'users_id';
            if (filter_var(_POST('access_groups_id'), FILTER_VALIDATE_INT) === false) $error = 'access_groups_id';
            if (!_POST('chmod')) $error = 'chmod';
            // Optional
            if (_POST('id') && filter_var(_POST('id'), FILTER_VALIDATE_INT) === false) $error = 'id';

            if ($error) {
                // Write error to console
                $obj->js_console .= \Sifu\Log::jsConsole('Error: ', $error);
                $obj->js_console .= \Sifu\Log::jsConsole('$_POST: ', $_POST);
                // Set form error
                $obj->setFormError(true);
            }
            else $obj->_CLEAN = $_POST;
        }

        // No print, no export
        if (isset($params[0]) && $params[0] == 'imprint' || $params[0] == 'export') {

            Funct::redirect(Funct::getPreviousURL());
        }
        $obj->flow($params, '/admin/permissions');

    }
    elseif ('group' == $action) {

        // ---------------------------------------------------------------------
        // Groups
        // ---------------------------------------------------------------------

        $c['template'] = function() { return new \Sifu\Template(Group::getModuleName()); };
        $c['renderer'] = function() { return new AdminRenderer(Group::getModuleName()); };
        $obj = new Group($c);

        if (!empty($_POST)) {

            $error = false;
            $keys = array(
                'id',
                'name',
                );
            Funct::shampoo($_POST, $keys);

            // Required
            if (!_POST('name')) $error = 'name';
            // Optional
            if (_POST('id') && filter_var(_POST('id'), FILTER_VALIDATE_INT) === false) $error = 'id';
            // No dupes
            $res = Funct::exists('access_groups', 'name', _POST('name'));
            if ($res && $res != _POST('id')) $error = 'name';

            if ($error) {
                // Write error to console
                $obj->js_console .= \Sifu\Log::jsConsole('Error: ', $error);
                $obj->js_console .= \Sifu\Log::jsConsole('$_POST: ', $_POST);
                // Set form error
                $obj->setFormError(true);
            }
            else $obj->_CLEAN = $_POST;
        }

        // No print, no export
        if (isset($params[0]) && $params[0] == 'imprint' || $params[0] == 'export') {

            Funct::redirect(Funct::getPreviousURL());
        }
        $obj->flow($params, '/admin/permissions');

    }
    elseif ('marketing' == $action) {

        // ---------------------------------------------------------------------
        // Marketing
        // ---------------------------------------------------------------------

        $c['template'] = function() { return new \Sifu\Template(Marketing::getModuleName()); };
        $c['renderer'] = function() { return new \Sifu\Renderer(Marketing::getModuleName()); };
        $obj = new Marketing($c);

        switch (isset($params[0]) ? $params[0] : 'default') {


        case 'dump':

            // -------------------------------------------------------------
            // List
            // -------------------------------------------------------------

            $obj->dump();
            break;


        case 'export':

            // -------------------------------------------------------------
            // Export CSV
            // -------------------------------------------------------------

            $obj->export();
            break;


        default:

            Funct::redirect(Funct::makeUrl('/admin'));
        }
    }
    else {

        // ---------------------------------------------------------------------
        //  Default
        // ---------------------------------------------------------------------

        $c['template'] = function() { return new \Sifu\Template(Admin::getModuleName()); };
        $c['renderer'] = function() { return new AdminRenderer(Admin::getModuleName()); };
        $obj = new Admin($c);

        $obj->fallback();
    }
}
