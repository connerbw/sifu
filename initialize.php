<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

// ----------------------------------------------------------------------------
// Global functions
// ----------------------------------------------------------------------------

// Autoloader
function sifu_autoload($class_name) {

    if (strpos($class_name, 'Smarty') === 0) return;

    if ('Pimple' == $class_name) {
        require($GLOBALS['CONFIG']['PATH'] . '/includes/symbionts/Pimple.php');
        return;
    }

    global $controller;
    $dirname = $GLOBALS['CONFIG']['PATH'];

    $file[] = "$dirname/biz/includes/$class_name.php";
    $file[] = "$dirname/includes/$class_name.php";
    if (!empty($controller)) {
        $file[] = "$dirname/biz/modules/$controller/$class_name.php";
        $file[] = "$dirname/modules/$controller/$class_name.php";
    }
    $file[] = "$dirname/biz/modules/$class_name.php";
    $file[] = "$dirname/modules/$class_name.php";

    foreach ($file as $f) {
        if (is_file($f)) {
            require($f);
            break;
        }
    }
}
spl_autoload_register('sifu_autoload');


/**
* Get $_GET var
*
* @param string $name
* @return $_GET
*/
function _GET($name) {

 return (isset($_GET[$name])) ? $_GET[$name] : null;
}


/**
* Get $_POST var
*
* @param string $name
* @return $_POST
*/
function _POST($name) {

 return (isset($_POST[$name])) ? $_POST[$name] : null;
}


// ----------------------------------------------------------------------------
// Procedure
// ----------------------------------------------------------------------------

// Get rid of register_globals
if (ini_get('register_globals')) {
    foreach ($_REQUEST as $k => $v) {
        unset($GLOBALS[$k]);
    }
}

// Enforce config
if (!isset($GLOBALS['CONFIG'])) {
    die("Something is wrong, can't initialize without configuration.");
}

// Set debug stuff
if (isset($GLOBALS['CONFIG']['DEBUG']) && $GLOBALS['CONFIG']['DEBUG']) {
    $GLOBALS['CONFIG']['DEBUG'] = true;

}
else {
    $GLOBALS['CONFIG']['DEBUG'] = false;
}

// Initialize SifuDb
require_once($GLOBALS['CONFIG']['PATH'] . '/includes/SifuDbInit.php');
SifuDbInit::init($GLOBALS['CONFIG']['DSN']);

// Include SifuFunct
require_once($GLOBALS['CONFIG']['PATH'] . '/includes/SifuFunct.php');

// Sessions
ini_set('session.use_only_cookies', true);
session_start();

// Set utf-8
header('Content-Type: text/html;charset=utf-8');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
mb_language('uni');

// Avoid problems with arg_separator.output
ini_set('arg_separator.output', '&');

// Set the default timezone
date_default_timezone_set($GLOBALS['CONFIG']['TIMEZONE']);

// Get rid of magic quotes
if (get_magic_quotes_gpc() && (!ini_get('magic_quotes_sybase'))) {
    $in = array(&$_GET, &$_POST, &$_REQUEST, &$_COOKIE, &$_FILES);
    while (list($k,$v) = each($in)) {
        foreach ($v as $key => $val) {
            if (!is_array($val)) {
                $in[$k][$key] = stripslashes($val);
                continue;
            }
            $in[] =& $in[$k][$key];
        }
    }
    unset($in);
}

// Include SifuUser
require_once($GLOBALS['CONFIG']['PATH'] . '/includes/SifuUser.php');

// Validate user $_SESSION
if (isset($_SESSION['users_id']) || isset($_SESSION['nickname'])) {
    $u = new SifuUser();
    if (!$u->isValidSession()) {
        $u->killSession();
        SifuFunct::redirect(SifuFunct::makeUrl('/home'));
    }
    unset($u);
}
