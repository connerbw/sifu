<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

namespace Sifu;

class Marketing extends \Sifu\Object {

    // variables: table names
    public $db_table = 'users_marketing';


    /**
    * Constructor
    */
    function __construct() { }


    /**
     * Save
     *
     * @param int|null $id
     * @param array $item keys match SQL table columns
     * @return int id
     * @throws \Exception
     */
    function save($id, array $item) {

        // Double check for stupidity
        if (!$id && empty($item['users_id'])) throw new \Exception('Invalid users_id');
        if (!$id && empty($item['signup_date'])) $item['signup_date'] = date('Y-m-d');

        // Let the parent do the rest
        parent::save($id, $item);
    }


    /**
    * Serializes, base64 encodes, then stores an array in $_COOKIE['referrer']
    * for 90 days. Contains landing page stats.
    *
    * @global string $_COOKIE['referrer'] serialized array
    * @global string $_GET['referral_id']
    * @global string $_SERVER['HTTP_REFERER']
    * @global string $_SERVER['REQUEST_URI']
    */
    static function setMarketingCookie() {

        if (!empty($_SESSION['users_id']) || !empty($_COOKIE['referrer'])) return; // Abort

        $referral_id = null;
        $search_keywords = null;

        // Referal id
        foreach($_GET as $key => $val) {
            if ('referral_id' == strtolower($key)) {
                $referral_id = $val;
                break;
            }
        }

        // Search engine keywords
        if (!empty($_SERVER['HTTP_REFERER'])) {

            $parts = parse_url($_SERVER['HTTP_REFERER']);
            if ($parts !== false && isset($parts['query']) && isset($parts['host'])) {

                $search_engines = array(
                    'bing' => 'q',
                    'google' => 'q',
                    'yahoo' => 'p',
                    );

                $query = array();
                $matches = array();
                parse_str($parts['query'], $query);
                preg_match('/(' . implode('|', array_keys($search_engines)) . ')\./', $parts['host'], $matches);
                $search_keywords = isset($matches[1]) && isset($query[$search_engines[$matches[1]]]) ? $query[$search_engines[$matches[1]]] : '';
            }
        }

        $marketing = base64_encode(serialize(array(
            'referrer' =>  isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null,
            'referral_id' => $referral_id,
            'landing_page' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null,
            'search_keywords' => $search_keywords,
            )));

        setcookie('referrer', $marketing, time() +3600 *24 *90);
    }


    /**
    * Base64 decode, unserialize, and return referrer array.  Contains landing
    * page stats.
    *
    * @global string $_COOKIE['referrer'] serialized array
    * @return array
    */
    static function getMarketingCookie() {

        if (!empty($_COOKIE['referrer'])) return unserialize(base64_decode($_COOKIE['referrer']));
        else return false;
    }

}
