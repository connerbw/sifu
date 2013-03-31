<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

function sifu($action, $params = null) {

    if ('user' == $action) {

        // ---------------------------------------------------------------------
        // User
        // ---------------------------------------------------------------------

        $obj = new User();

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
            SifuFunct::shampoo($_POST, $keys);

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
            $res = SifuFunct::exists('users', 'nickname', _POST('nickname'));
            if ($res && $res != _POST('id')) $error = 'nickname';
            $res = SifuFunct::exists('users', 'nickname', _POST('email'));
            if ($res && $res != _POST('id')) $error = 'email';

            if ($error) {
                // Write error to console
                $obj->js_console .= SifuLog::jsConsole('Error: ', $error);
                $obj->js_console .= SifuLog::jsConsole('$_POST: ', $_POST);
                // Set form error
                $obj->setFormError(true);
            }
            else $obj->_CLEAN = $_POST;
        }

        // No export
        if (isset($params[0]) && $params[0] == 'export') {

            SifuFunct::redirect(SifuFunct::getPreviousURL());
        }
        $obj->flow($params, '/admin/user');

    }
    elseif ('permissions' == $action) {

        // ---------------------------------------------------------------------
        // Permissions
        // ---------------------------------------------------------------------

        $obj = new Permissions();

        if (!empty($_POST)) {

            $error = false;
            $keys = array(
                'access_groups_id',
                'chmod',
                'id',
                'module',
                'users_id',
                );
            SifuFunct::shampoo($_POST, $keys);

            // Required
            if (!_POST('module')) $error = 'module';
            if (filter_var(_POST('users_id'), FILTER_VALIDATE_INT) === false) $error = 'users_id';
            if (filter_var(_POST('access_groups_id'), FILTER_VALIDATE_INT) === false) $error = 'access_groups_id';
            if (!_POST('chmod')) $error = 'chmod';
            // Optional
            if (_POST('id') && filter_var(_POST('id'), FILTER_VALIDATE_INT) === false) $error = 'id';

            if ($error) {
                // Write error to console
                $obj->js_console .= SifuLog::jsConsole('Error: ', $error);
                $obj->js_console .= SifuLog::jsConsole('$_POST: ', $_POST);
                // Set form error
                $obj->setFormError(true);
            }
            else $obj->_CLEAN = $_POST;
        }

        // No print, no export
        if (isset($params[0]) && $params[0] == 'imprint' || $params[0] == 'export') {

            SifuFunct::redirect(SifuFunct::getPreviousURL());
        }
        $obj->flow($params, '/admin/permissions');

    }
    elseif ('group' == $action) {

        // ---------------------------------------------------------------------
        // Groups
        // ---------------------------------------------------------------------

        $obj = new Group();

        if (!empty($_POST)) {

            $error = false;
            $keys = array(
                'id',
                'name',
                );
            SifuFunct::shampoo($_POST, $keys);

            // Required
            if (!_POST('name')) $error = 'name';
            // Optional
            if (_POST('id') && filter_var(_POST('id'), FILTER_VALIDATE_INT) === false) $error = 'id';
            // No dupes
            $res = SifuFunct::exists('access_groups', 'name', _POST('name'));
            if ($res && $res != _POST('id')) $error = 'name';

            if ($error) {
                // Write error to console
                $obj->js_console .= SifuLog::jsConsole('Error: ', $error);
                $obj->js_console .= SifuLog::jsConsole('$_POST: ', $_POST);
                // Set form error
                $obj->setFormError(true);
            }
            else $obj->_CLEAN = $_POST;
        }

        // No print, no export
        if (isset($params[0]) && $params[0] == 'imprint' || $params[0] == 'export') {

            SifuFunct::redirect(SifuFunct::getPreviousURL());
        }
        $obj->flow($params, '/admin/permissions');

    }
    elseif ('marketing' == $action) {

        // ---------------------------------------------------------------------
        // Marketing
        // ---------------------------------------------------------------------

        $obj = new Marketing();

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

            SifuFunct::redirect(SifuFunct::makeUrl('/admin'));
        }
    }
    else {

        // ---------------------------------------------------------------------
        //  Default
        // ---------------------------------------------------------------------

        $obj = new Admin();
        $obj->fallback();
    }
}

?>