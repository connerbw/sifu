<?php

/**
 * @author     Dac Chartrand <dac.chartrand@gmail.com>
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt
 */

function sifu($action, $params = null)
{

    switch ($action) {

        case'success':

            $g = new Globals();
            $g->success();
            break;


        case'permission_error':

            $g = new Globals();
            $g->permissionError();
            break;


        case'banned':

            $g = new Globals();
            $g->banned();
            break;


        case'e404':

            $g = new Globals();
            $g->e404();
            break;


        case'error':

            $g = new Globals();
            $g->error($params[0]);
            break;

    }
}

?>