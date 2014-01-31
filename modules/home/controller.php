<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

function sifu($action, $params = null)
{
    $c = new Pimple();
    $c['user'] = function() { return new SifuUser(); };
    $c['template'] = function() { return new SifuTemplate(Home::getModuleName()); };
    $c['renderer'] = function() { return new SifuRenderer(Home::getModuleName()); };

    switch($action) {

    default:

        $home = new Home($c);
        $home->display();
    }
}

