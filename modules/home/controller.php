<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

function sifu($action, $params = null) {

    switch($action) {

    default:

        $home = new Home();
        $home->display();
    }
}

?>