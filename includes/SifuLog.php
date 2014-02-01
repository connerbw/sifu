<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

namespace Sifu;

class Log {

    /**
    *
    */
    function __construct() {

    }


    /**
    * Initialize console. Return code that catches errors for IE and other
    * browsers w/o console.
    *
    * @return string
    */
    static function jsConsoleInit() {

        /// Catch errors for IE and other browsers w/o console
        $tmp = 'if (!window.console) console = {};';
        $tmp .= 'console.log = console.log || function(){};';
        $tmp .= 'console.warn = console.warn || function(){};';
        $tmp .= 'console.error = console.error || function(){};';
        $tmp .= 'console.info = console.info || function(){};';
        $tmp .= 'console.debug = console.debug || function(){};';
        $tmp .= "\n";

        return $tmp;
    }


    /**
    * Return JavaScript console code
    *
    * @param string $name
    * @param object $var [optional]
    * @param string $type [optional] 'log', 'info', 'warn', 'error'
    * @return string
    */
    static function jsConsole($name, $var = null, $type = 'log') {

        $type = strtolower($type);

        $tmp = '';
        switch($type)  {

        case 'info':
            $tmp .= 'console.info("'.$name.'");'. "\n";
            break;

        case 'warn':
            $tmp .= 'console.warn("'.$name.'");'. "\n";
            break;

        case 'error':
            $tmp .= 'console.error("'.$name.'");'. "\n";
            break;

        default:
            $tmp .= 'console.log("'.$name.'");' . "\n";
            break;
        }

        if (!empty($var)) {
            if (is_object($var) || is_array($var)) {

                $object = json_encode($var);
                $tmp .= 'var object'.preg_replace('~[^A-Z|0-9]~i',"_",$name).' = \''.str_replace("'","\'",$object).'\';'. "\n";
                $tmp .= 'var val'.preg_replace('~[^A-Z|0-9]~i',"_",$name).' = eval("(" + object'.preg_replace('~[^A-Z|0-9]~i',"_",$name).' + ")" );'. "\n";

                switch($type) {

                case 'info':
                    $tmp .= 'console.info(val'.preg_replace('~[^A-Z|0-9]~i',"_",$name).');'. "\n";
                    break;

                case 'warn':
                    $tmp .= 'console.warn(val'.preg_replace('~[^A-Z|0-9]~i',"_",$name).');'. "\n";
                    break;

                case 'error':
                    $tmp .= 'console.error(val'.preg_replace('~[^A-Z|0-9]~i',"_",$name).');'. "\n";
                    break;

                default:
                    $tmp .= 'console.debug(val'.preg_replace('~[^A-Z|0-9]~i',"_",$name).');'. "\n";
                    break;
                }
            }
            else {
                switch($type) {

                case 'info':
                    $tmp .= 'console.info("'.str_replace('"','\\"',$var).'");'. "\n";
                    break;

                case 'warn':
                    $tmp .= 'console.warn("'.str_replace('"','\\"',$var).'");'. "\n";
                    break;

                case 'error':
                    $tmp .= 'console.error("'.str_replace('"','\\"',$var).'");'. "\n";
                    break;

                default:
                    $tmp .= 'console.debug("'.str_replace('"','\\"',$var).'");'. "\n";
                    break;
                }
            }
        }

        return $tmp;
    }
}

?>