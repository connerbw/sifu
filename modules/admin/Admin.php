<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

namespace Sifu\Modules\Admin;

use Sifu\Funct as Funct;

class Admin extends \Sifu\Modules\Module {

    // Module name
    protected static $module = 'admin';


    /**
     * @param \Pimple $c
     */
    function __construct(\Pimple $c) {

        $this->obj = null; // Safety, don't use parent methods
        $this->r = $c['renderer']; // Renderer
        parent::__construct($c); // Let the parent do the rest

        if (!$this->acl('r')) {
            // Permission error
            Funct::redirect(Funct::makeUrl('/globals/permission_error'));
        }
    }

}
