<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

class Admin extends Module {

    // Module name
    protected static $module = 'admin';


    /**
     * @param Pimple $c
     */
    function __construct(Pimple $c) {

        $this->obj = null; // Safety, don't use parent methods
        $this->r = $c['renderer']; // Renderer
        parent::__construct($c); // Let the parent do the rest

        if (!$this->acl('r')) {
            // Permission error
            SifuFunct::redirect(SifuFunct::makeUrl('/globals/permission_error'));
        }
    }

}
