<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

namespace Sifu\Modules\Home;

function sifu($action, $params = null)
{
    $c = new \Pimple();
    $c['user'] = function() { return new \Sifu\User(); };
    $c['template'] = function() { return new \Sifu\Template(Home::getModuleName()); };
    $c['renderer'] = function() { return new \Sifu\Renderer(Home::getModuleName()); };

    switch($action) {

    default:

        $home = new Home($c);
        $home->display();
    }
}

