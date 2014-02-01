<?php

/**
* @see: http://docs.jquery.com/Plugins/Validation/Methods/remote
*/

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/../../initialize.php');

// ---------------------------------------------------------------------------
// Error checking
// ---------------------------------------------------------------------------

function failure($msg = null) {
    if (!headers_sent()) header("HTTP/1.0 500 Internal Server Error");
    if ($msg) echo "Something went wrong: \n\n $msg";
    die();
}

if (!_GET('email')) {
    failure('Invalid email');
}

if (_GET('id') && filter_var(_GET('id'), FILTER_VALIDATE_INT) === false) {
    failure('Invalid id');
}


// ---------------------------------------------------------------------------
// Procedure
// ---------------------------------------------------------------------------

$email = $_GET['email'];
$id = _GET('id');
$result = true;

try {
    $res = \Sifu\Funct::exists('users', 'email', $email);
}
catch (PDOException $e) {
    failure('Database error');
}

if ($res && $res != $id) {
    // No duplicates allowed
    $gtext = \Sifu\Funct::getGtext('admin');
    $result = !empty($gtext['ajax_error_4']) ? $gtext['ajax_error_4'] : 'ajax_error_4';
}

// ---------------------------------------------------------------------------
// Json
// ---------------------------------------------------------------------------

header('Content-Type: application/json');
echo json_encode($result);
