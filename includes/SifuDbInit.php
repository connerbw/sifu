<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

class SifuDbInit {

    private static $dsn = array();

    /**
     * @var SifuDb[]
     */
    private static $db = array();

    // Static class, no cloning or instantiating allowed
    final private function __construct() { }
    final private function __clone() { }


    // ------------------------------------------------------------------------
    // Factory, sort of
    // ------------------------------------------------------------------------

    /**
    * @param array $dsn
    */
    static public function init(array $dsn) {

        self::$dsn = $dsn;
    }


    /**
    * @param string $key [optional]
    */
    static public function get($key = null) {

        if (!$key) {
            // Assume we want the first key from the DSN
            $key = array_keys(self::$dsn);
            $key = array_shift($key);
        }

        if (!isset(self::$dsn[$key])) throw new Exception("Unknown DSN key: $key");

        if (!isset(self::$db[$key])) {

            try {

                // Figure out what kind of PDO DSN this is supposed to be
                if (is_array(self::$dsn[$key])) {
                    // Call appropriate PDO constructor and provide array arguments
                    $c = new ReflectionClass('PDO');
                    $pdo = $c->newInstanceArgs(self::$dsn[$key]);
                }
                else {
                    // Call PDO with a string
                    $pdo = new PDO(self::$dsn[$key]);
                }

                $driver = ucfirst(strtolower($pdo->getAttribute(PDO::ATTR_DRIVER_NAME)));
                $driver = 'Sifu' . $driver;
                $file = dirname(__FILE__) . '/SifuDb/' . $driver . '.php';
                if (!is_file($file)) {
                    throw new Exception($driver . '() is an unsupported database driver');
                }
                else {
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    require_once($file);
                    self::$db[$key] = new $driver($pdo);
                    if (!(self::$db[$key] instanceof ISifuDb)) throw new Exception($driver . '() does not implement ISifuDb interface');
                }
            }
            catch (Exception $e) {
                $message = 'There was a problem connecting to the database: ';
                $message .= $e->getMessage();
                throw new Exception($message);
            }
        }

        return self::$db[$key];
    }


    /**
    * Abort and rollback transactions
    *
    * @param string $key [optional]
    */
    static public function abort($key = null) {

        if (!$key) {
            // Assume we want the first key from the DSN
            $key = array_keys(self::$dsn);
            $key = array_shift($key);
        }

        // Abort
        if (isset(self::$db[$key])) {
            self::$db[$key]->abortTransaction();
        }
    }

}

?>