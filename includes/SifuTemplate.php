<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

namespace Sifu;

require_once(dirname(__FILE__) . '/symbionts/Smarty/libs/Smarty.class.php');

class Template extends \Smarty {

    public $module;
    public $partition;
    public $template_dir_fallback_1;
    public $template_dir_fallback_2;

    /**
    * Constructor
    *
    * @global string $CONFIG['DEBUG']
    * @global string $CONFIG['PATH']
    * @global string $CONFIG['PARTITION']
    * @param string $module
    */
    function __construct($module) {

        // Call parent
        parent::__construct();

        // Trim
        $this->registerFilter('output', '_sifu_template_trim');

        // Set seperate error reporting for templates
        $this->error_reporting = $GLOBALS['CONFIG']['SMARTY_ERROR_REPORTING'];

        // --------------------------------------------------------------------
        // Plugins directory
        // --------------------------------------------------------------------

        $this->setPluginsDir(array(
            $GLOBALS['CONFIG']['PATH'] . '/includes/symbionts/Smarty/libs/plugins',
            // Add more plugin dirs here...
            ));

        // --------------------------------------------------------------------
        // Setup
        // --------------------------------------------------------------------

        if (!empty($_SESSION['partition'])) $partition = $_SESSION['partition'];
        else $partition = $GLOBALS['CONFIG']['PARTITION'];

        $this->setModule($module, $partition);
    }


    /**
     * Set the template for a module
     *
     * @global string $CONFIG ['PATH']
     * @global string $CONFIG ['CACHE_LIFETIME']
     * @param string $module
     * @param string $partition
     * @throws \Exception
     */
    function setModule($module, $partition = 'sifu') {

        // --------------------------------------------------------------------
        // Compile directory
        // --------------------------------------------------------------------

        $compile_dir = $GLOBALS['CONFIG']['PATH'] . "/temporary/smarty/templates_c/$partition/$module/";
        if(!is_dir($compile_dir) && !mkdir($compile_dir, 0777, true)) {
            throw new \Exception('Missing compile dir ' . $compile_dir);
        }
        $this->setCompileDir($compile_dir);


        // --------------------------------------------------------------------
        // Cache directory and variables
        // --------------------------------------------------------------------

        $cache_dir = $GLOBALS['CONFIG']['PATH'] . "/temporary/smarty/cache/$partition/$module/";
        if(!is_dir($cache_dir) && !mkdir($cache_dir, 0777, true)) {
            throw new \Exception('Missing cache dir ' . $cache_dir);
        }
        $this->setCacheDir($cache_dir);
        $this->cache_lifetime = $GLOBALS['CONFIG']['CACHE_LIFETIME'];
        $this->caching = 0; // Caching off by default, enable in module if needed

        // --------------------------------------------------------------------
        // Config dir
        // --------------------------------------------------------------------

        $config_dir = $GLOBALS['CONFIG']['PATH'] . "/biz/templates/_partitions_/$partition/globals/smarty/";
        $config_dir_fallback_1 = $GLOBALS['CONFIG']['PATH'] . '/biz/templates/globals/smarty/';
        $config_dir_fallback_2 = $GLOBALS['CONFIG']['PATH'] . '/templates/globals/smarty/';

        if(!is_file($config_dir . 'my.conf')) {
            $config_dir = $config_dir_fallback_1;
            if(!is_file($config_dir . 'my.conf')) {
                $config_dir = $config_dir_fallback_2;
            }
        }

        $this->setConfigDir($config_dir);

        // --------------------------------------------------------------------
        // Template directory
        // --------------------------------------------------------------------

        $this->module = $module;
        $this->partition = $partition;

        $this->setTemplateDir(array(
            $GLOBALS['CONFIG']['PATH'] . "/biz/templates/_partitions_/$partition/$module/smarty/",
            $GLOBALS['CONFIG']['PATH'] . "/biz/templates/$module/smarty/",
            $GLOBALS['CONFIG']['PATH'] . "/templates/$module/smarty",
            ));

    }


    /**
    * Get Smarty cache_id by using nickname
    *
    * Smarty incorporates the concept of cache groups by using the pipe |
    * character. Nickname is used because Sifu is multilingual and users
    * may have a different language, so templates will differ from user to
    * user.
    *
    * @see http://www.smarty.net/docs/en/caching.groups.tpl
    *
    * @global string $_SESSION['nickname']
    * @param string [optional] Smarty compatible cache id
    * @return string
    */
    function getCacheId($cache_id = null) {

        $nn = isset($_SESSION['nickname']) ? $_SESSION['nickname'] : 'nobody';

        if ($cache_id) return "$nn|$cache_id";
        else return $nn;
    }


    /**
    * htmLawed Tidy
    *
    * Note: htmLawed is meant for input that goes into the body of HTML
    * documents. HTML's head-level elements are not supported, nor are the
    * frameset elements "frameset", "frame" and "noframes".
    *
    * @see http://www.bioinformatics.org/phplabware/internal_utilities/htmLawed/htmLawed_README.htm#s3.3.5
    * @param string $html
    * @param int $tidy [optional] -1 = compact, 1 = beautify
    * @return string
    */
    function tidy($html, $tidy = -1) {

        require_once(dirname(__FILE__) . '/symbionts/htmLawed/htmLawed.php');
        $config = array(
            'tidy' => $tidy,
            );
        return htmLawed($html, $config);
    }

}

