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

if (empty($_SESSION['users_id'])) {
    failure('User not logged in');
}

if (!_GET('name')) {
    failure('Invalid name');
}

if (_GET('id') && filter_var(_GET('id'), FILTER_VALIDATE_INT) === false) {
    failure('Invalid id');
}


// ---------------------------------------------------------------------------
// Procedure
// ---------------------------------------------------------------------------

$name = $_GET['name'];
$id = _GET('id');
$result = true;

try {
    $res = SifuFunct::exists('access_groups', 'name', $name);
}
catch (PDOException $e) {
    failure('Database error');
}

if ($res && $res != $id) {
    // No duplicates allowed
    $gtext = SifuFunct::getGtext('admin');
    $result = !empty($gtext['ajax_error_1']) ? $gtext['ajax_error_1'] : 'ajax_error_1';
}
elseif (strtolower($name) == 'root' || strtolower($name) == 'banned') {
    // Reserved keywords
    $gtext = SifuFunct::getGtext('admin');
    $result = !empty($gtext['ajax_error_2']) ? $gtext['ajax_error_2'] : 'ajax_error_2';
}

// ---------------------------------------------------------------------------
// Json
// ---------------------------------------------------------------------------

header('Content-Type: application/json');
echo json_encode($result);

?>