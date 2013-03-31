<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

require_once(dirname(__FILE__)  . '/../includes/SifuFunct.php');

// Cache dirs to delete
$cache = array();
$cache[] = realpath(dirname(__FILE__) . '/../temporary/cache/');
$cache[] = realpath(dirname(__FILE__) . '/../temporary/templates_c/');

// Go!
foreach($cache as $dir) {
    SifuFunct::obliterateDir($dir);
}

header('Content-Type: text/plain');

echo 'Caches purged!'

?>