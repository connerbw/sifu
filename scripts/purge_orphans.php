<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

require_once(dirname(__FILE__)  . '/../config.php');
require_once(dirname(__FILE__)  . '/../initialize.php');

// ----------------------------------------------------------------------------
// Set debug mode, if true nothing actually gets deleted
// ----------------------------------------------------------------------------

$debug = true;

set_time_limit(900); // Set the timeout to 15 minutes.

// ----------------------------------------------------------------------------
// Purge orphaned link tables
// ----------------------------------------------------------------------------

header('Content-Type: text/plain');

if ($debug) echo "> \$debug = true, nothing will be deleted.\n  Edit the code in the header of this file if you want to change this. \n\n";

$db = SifuDbInit::get();

// Scan for missing links, push them in $not_found array
$link_tables = $db->getLinkTables();

$not_found = array();
foreach ($link_tables as $val) {

    $parts = explode('__', $val);
    if (count($parts) != 3) die('Unexpected result, ejecting early to avoid catastrophe...');

    $st = $db->pdo->query("SELECT * FROM {$val} ");
    $tmp =  $st->fetchAll(PDO::FETCH_ASSOC);

    foreach ($tmp as $val2) {

        $tmp2 = "{$parts[1]}_id";
        $tmp3 = "{$parts[2]}_id";

        // Table 1
        $query = 'SELECT id FROM ' . $parts[1] . " WHERE id = {$val2[$tmp2]} ";
        $st = $db->pdo->query($query);
        if ($st->fetchColumn() <= 0) {
            $not_found[] = array($val, $tmp2, $val2[$tmp2], $tmp3, $val2[$tmp3]);
            continue;
        }

        // Table 2
        $query = 'SELECT id FROM ' . $parts[2] . " WHERE id = {$val2[$tmp3]} ";
        $st = $db->pdo->query($query);
        if ($st->fetchColumn() <= 0) {
            $not_found[] = array($val, $tmp3, $val2[$tmp3], $tmp2, $val2[$tmp2]);
            continue;
        }

    }

}

try
{
    // Delete dead links
    $count = 0;
    $tid = $db->requestTransaction();
    foreach ($not_found as $val) {

        // $val[0] -> link_table_name
        // $val[1] -> column_name_1
        // $val[2] -> column_id_1
        // $val[3] -> column_name_2
        // $val[4] -> column_id_2

        $query = "DELETE FROM {$val[0]} WHERE {$val[1]} = {$val[2]} AND {$val[3]} = {$val[4]} ";
        if (!$debug) $count += $db->pdo->exec($query);
        echo $query . "; \n";

    }
    $db->commitTransaction($tid);
    echo "> $count links deleted \n";

}
catch (Exception $e) {

    SifuDbInit::abort(); // Abort and rollback transactions
    throw($e); // Hot potato!
}

?>