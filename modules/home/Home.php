<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

class Home extends Module {

    // Module name
    protected $module = 'home';


    /**
    * Constructor
    *
    */
    function __construct() {

        $this->obj = null; // Safety, don't use parent methods
        $this->r = new SifuRenderer($this->module); // Renderer
        parent::__construct(); // Let the parent do the rest
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

?>