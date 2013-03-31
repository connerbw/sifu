<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

class Admin extends Module {

    // Module name
    protected $module = 'admin';


    /**
    * Constructor
    */
    function __construct() {

        $this->obj = null; // Safety, don't use parent methods
        $this->r = new AdminRenderer($this->module); // Renderer
        parent::__construct(); // Let the parent do the rest

        if (!$this->acl('r')) {
            // Permission error
            SifuFunct::redirect(SifuFunct::makeUrl('/globals/permission_error'));
        }
    }

}

?>