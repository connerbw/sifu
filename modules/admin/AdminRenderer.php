<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

namespace Sifu\Modules\Admin;

use Sifu\Access as Access;

class AdminRenderer extends \Sifu\Renderer {

    /**
    * @param string $module
    */
    function __construct($module) {

        parent::__construct($module); // Call parent
    }


    function isMe($id) {

        if ($_SESSION['users_id'] == $id) return true;
        else return false;
    }


    /**
    * Modules that implement a permission scheme compatible with SifuAccess().
    *
    * @global array $CONFIG['ACCESS']
    * @return array
    */
    function getAccessOptions() {

        // Cache
        static $arr = null;
        if (is_array($arr)) return $arr;

        // Procedure
        $arr[null] = '---';
        foreach($GLOBALS['CONFIG']['ACCESS'] as $val) {
            $arr[$val] = $val;
        }

        return $arr;
    }


    /**
    * Users
    *
    * @return array
    */
    function getUsersOptions() {

        // Cache
        static $arr = null;
        if (is_array($arr)) return $arr;

        // Procedure
        $arr[-1] = '---';
        $user = new \Sifu\User();
        $res = $user->dump(null, 0, 'nickname');
        foreach ($res as $val) {
            $arr[$val['id']] = $val['nickname'];
        }

        return $arr;
    }


    /**
    * User groups
    *
    * @return array
    */
    function getGroupsOptions() {

        // Cache
        static $arr = null;
        if (is_array($arr)) return $arr;

        // Procedure
        $arr[null] = '---';
        $access = new Access();
        $res = $access->dumpGroups(null, 0, 'name');
        foreach ($res as $val) {
            $arr[$val['id']] = $val['name'];
        }

        return $arr;
    }


    /**
    * Get timezones
    *
    * @return array
    */
    function getTimezonesOptions() {

        // Cache
        static $arr = null;
        if (is_array($arr)) return $arr;

        $tzid = \DateTimeZone::listIdentifiers();
        $continent = null;
        $optgroup = null;

        $arr[null] = '---';
        foreach($tzid as $val) {

            if (preg_match('#^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)/#', $val)) {

                $ex = explode('/', $val); //obtain continent, city
                if ($continent != $ex[0]) $optgroup = $ex[0];
                $continent = $ex[0];
                $city = $ex[1];
                $arr[$optgroup][$val] = str_replace('_', ' ', $city);
            }
        }

        return $arr;
    }

}
