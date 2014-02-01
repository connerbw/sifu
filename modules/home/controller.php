<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

function sifu($action, $params = null)
{
    $c = new Pimple();
    $c['user'] = function() { return new \Sifu\User(); };
    $c['template'] = function() { return new \Sifu\Template(\Sifu\Modules\Home\Home::getModuleName()); };
    $c['renderer'] = function() { return new \Sifu\Renderer(\Sifu\Modules\Home\Home::getModuleName()); };

    switch($action) {

    default:

        $home = new \Sifu\Modules\Home\Home($c);
        $home->display();
    }
}

