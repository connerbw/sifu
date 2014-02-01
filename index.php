<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

require_once(__DIR__ . '/config.php'); // Configuration

try {

    require_once($GLOBALS['CONFIG']['PATH'] . '/initialize.php'); // Initialization

    // ------------------------------------------------------------------------
    // Marketing
    // ------------------------------------------------------------------------

    \Sifu\Marketing::setMarketingCookie();

    // ------------------------------------------------------------------------
    // Prepare
    // ------------------------------------------------------------------------

    // Defaults
    $controller = 'home';
    $action = 'default';
    $params = array();

    // Get controller & params
    if (!empty($_GET['c'])) {
        $params = explode('/', $_GET['c']);
        $controller = array_shift($params);
        $action = array_shift($params);
    }

    // Sanity check controller
    $controller = mb_strtolower($controller);

    // Banned
    if ($controller == 'banned') {

        $controller = 'globals';
        if (is_file($GLOBALS['CONFIG']['PATH'] . '/biz/modules/globals/controller.php')) {
            include_once($GLOBALS['CONFIG']['PATH'] . '/biz/modules/globals/controller.php');
        }
        else {
            include_once($GLOBALS['CONFIG']['PATH'] . '/modules/globals/controller.php');
        }
        sifu('banned');
        exit;
    }
    // 404 Not Found
    elseif ($controller == '404' || !preg_match('/^(\w|\-)+$/', $controller) || !is_file($GLOBALS['CONFIG']['PATH'] . "/biz/modules/{$controller}/controller.php") && !is_file($GLOBALS['CONFIG']['PATH'] . "/modules/{$controller}/controller.php")) {

        $controller = 'globals';
        if (!headers_sent()) header('HTTP/1.0 404 Not Found');
        if (is_file($GLOBALS['CONFIG']['PATH'] . '/biz/modules/globals/controller.php')) {
            include_once($GLOBALS['CONFIG']['PATH'] . '/biz/modules/globals/controller.php');
        }
        else {
            include_once($GLOBALS['CONFIG']['PATH'] . '/modules/globals/controller.php');
        }
        sifu('e404');
        exit;
    }

    // Sanity check action
    $action = mb_strtolower($action);
    if (!preg_match('/^(\w|\-)+$/', $action)) $action = 'default';

    // Sanity check params
    foreach ($params as $key => $val) {
        if (!preg_match('/^(\w|\-)+$/', $val)) $params[$key] = null;
    }

    // ------------------------------------------------------------------------
    // Go!
    // ------------------------------------------------------------------------

    if (is_file($GLOBALS['CONFIG']['PATH'] . "/biz/modules/{$controller}/controller.php")) {
        include_once($GLOBALS['CONFIG']['PATH'] . "/biz/modules/{$controller}/controller.php");
    }
    else {
        include_once($GLOBALS['CONFIG']['PATH'] . "/modules/{$controller}/controller.php");
    }
    sifu($action, $params);
}
catch (Exception $e) {

    // Abort and rollback transactions
    foreach ($GLOBALS['CONFIG']['DSN'] as $key => $val) {
        \Sifu\DbInit::abort($key);
    }

    $message = 'Error: ';
    $message .= html_entity_decode($e->getMessage()) . "\n";
    if ($GLOBALS['CONFIG']['DEBUG']) {
        $message .= "File: " . $e->getFile() . "\n";
        $message .= "Line: " . $e->getLine() . "\n\n";
        $message .= "Backtrace: \n" . print_r($e->getTraceAsString(), true) . "\n\n";
    }

    try {
        if (function_exists('sifu')) {
            throw new Exception();
        }

        $controller = 'globals';
        if (is_file($GLOBALS['CONFIG']['PATH'] . '/biz/modules/globals/controller.php')) {
            include_once($GLOBALS['CONFIG']['PATH'] . '/biz/modules/globals/controller.php');
        }
        else {
            include_once($GLOBALS['CONFIG']['PATH'] . '/modules/globals/controller.php');
        }
        sifu('error', array(nl2br($message)));
        die();
    }
    catch (Exception $garbage) {
        // Template failed?! Try again, print to screen
        if (!headers_sent()) header('Content-Type: text/plain');
        die($message);
    }

}

// ----------------------------------------------------------------------------
// Breadcrumbs
// ----------------------------------------------------------------------------

\Sifu\Funct::breadcrumbs();
