<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

class Home extends Module {

    // Module name
    protected static $module = 'home';


    /**
     * @param Pimple $c
     */
    function __construct(Pimple $c) {

        $this->obj = null; // Safety, don't use parent methods
        $this->r = $c['renderer']; // Renderer
        parent::__construct($c); // Let the parent do the rest
    }


    /**
    * Display home
    */
    function display() {

        $this->tpl->caching = 1;
        $cache_id = $this->tpl->getCacheId();
        $this->tpl->display('home.tpl', $cache_id);
    }
}
