<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

require(__DIR__ . '/global-functions.php');

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
\Sifu\DbInit::init($GLOBALS['CONFIG']['DSN']);

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
    $u = new \Sifu\User();
    if (!$u->isValidSession()) {
        $u->killSession();
        \Sifu\Funct::redirect(\Sifu\Funct::makeUrl('/home'));
    }
    unset($u);
}
