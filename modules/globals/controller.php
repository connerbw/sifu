<?php

/**
 * @author     Dac Chartrand <dac.chartrand@gmail.com>
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt
 */

namespace Sifu\Modules\Globals;

function sifu($action, $params = null)
{
    $c = new \Pimple();
    $c['user'] = function() { return new \Sifu\User(); };
    $c['template'] = function() { return new \Sifu\Template(Globals::getModuleName()); };
    $c['renderer'] = function() { return new \Sifu\Renderer(Globals::getModuleName()); };

    $g = new Globals($c);

    switch ($action) {

        case'success':

            $g->success();
            break;


        case'permission_error':

            $g->permissionError();
            break;


        case'banned':

            $g->banned();
            break;


        case'e404':

            $g->e404();
            break;


        case'error':

            $g->error($params[0]);
            break;

    }
}
