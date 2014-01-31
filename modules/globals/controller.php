<?php

/**
 * @author     Dac Chartrand <dac.chartrand@gmail.com>
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt
 */

function sifu($action, $params = null)
{
    $c = new Pimple();
    $c['user'] = function() { return new SifuUser(); };
    $c['template'] = function() { return new SifuTemplate(Globals::getModuleName()); };
    $c['renderer'] = function() { return new SifuRenderer(Globals::getModuleName()); };

    switch ($action) {

        case'success':

            $g = new Globals($c);
            $g->success();
            break;


        case'permission_error':

            $g = new Globals($c);
            $g->permissionError();
            break;


        case'banned':

            $g = new Globals($c);
            $g->banned();
            break;


        case'e404':

            $g = new Globals($c);
            $g->e404();
            break;


        case'error':

            $g = new Globals($c);
            $g->error($params[0]);
            break;

    }
}
