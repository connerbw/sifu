<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

class Globals extends Module {

    // Module name
    protected static $module = 'globals';


    /**
     * @param Pimple $c
     */
    function __construct(Pimple $c) {

        $this->obj = null; // Safety, don't use parent methods
        $this->r = $c['renderer']; // Renderer
        parent::__construct($c); // Let the parent do the rest
    }


    /**
    * Display generic form success
    */
    function success() {

        $this->tpl->caching = 1;
        $cache_id = $this->tpl->getCacheId();
        $this->tpl->display('success.tpl', $cache_id);
    }


    /**
    * Display generic permission error
    */
    function permissionError() {

        $this->tpl->caching = 1;
        $cache_id = $this->tpl->getCacheId();
        $this->tpl->display('permission_error.tpl', $cache_id);
    }


    /**
    * Display Banned Screen
    */
    function banned() {

        $this->tpl->caching = 1;
        $this->tpl->display('banned.tpl');
    }


    /**
    * Display Error 404
    */
    function e404() {

        $this->tpl->caching = 1;
        $this->tpl->display('404.tpl');

    }

    /**
     * Display Error 404
     */
    function error($error_message) {

        $this->r->text['error_message'] = $error_message;
        $this->tpl->display('error.tpl');
    }



}


?>