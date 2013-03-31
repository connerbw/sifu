<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*
* Example menu. To activate see: ./templates/globals/languages/en.php
*/

function home_menu() {

    if (!isset($_SESSION['users_id'])) {
        // Not logged in, don't show menu
        return null;
    }

    if (SifuFunct::acl('r', 'home')) {
        // Read permissions OK, return menu
        $gtext = SifuFunct::getGtext();
        return array(
            $gtext['home'] => array(
                SifuFunct::makeUrl('/home'),
                array(
                    'Hello' => 'http://www.trotch.com/',
                    'World' => SifuFunct::makeUrl('#'),
                    ),
                ),
            );
    }
    else {
        // No permissions, don't show menu
    	return null;
    }

}

?>