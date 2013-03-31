<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

/**
* A renderer object acts as a bridge and is passed to a template object.
* Example:
*
*   <code>
*   $r = new SifuRenderer('module');
*   $smarty->assignByRef('r', $r);
*   </code>
*
* PHP webpages are procedural in the sense that the page will be rendered once,
* from top to bottom, then PHP will exit until called again.
*
* In Sifu, template rendering is supposed to happen after everything else. This
* means the state of the application doesn't change while rendering.
*
* Knowing this, a SifuRenderer object can do interesting things, such as cache
* the return values of functions and other bridge like behaviour between
* Templates and the rest of the app.
*
* For more info:
* http://www.phpinsider.com/smarty-forum/viewtopic.php?t=12683
*/
class SifuRenderer {

    // Strings
    public $module; // Module name
    public $html_header; // Full path to html_header.tpl
    public $html_footer; // Full path to html_footer.tpl
    public $letterhead; // Full path to letterhead.tpl

    // More strings
    public $url; // Site URL Prefix, e.g. /my/sifu
    public $partition; // Sifu partition name
    public $title; // Variable to put between <title> tags
    public $sitename; // Sitename
    public $stylesheets; // Variable to put stylesheets/text
    public $header; // Variable to put header/text
    public $js_console; // Variable to put JS Console code, if any

    // Arrays
    public $text  = array(); // Variable to store dynamic text in
    public $arr = array(); // Variable to keep arrays
    public $bool = array(); // Variable to keep bool values

    // Gtext
    public $_gtext = array(); // Variable to store gtext in


    /**
    * Constructor
    *
    * @global string $CONFIG['LANGUAGE']
    * @global string $CONFIG['PATH']
    * @global string $CONFIG['URL']
    * @global string $CONFIG['PARTITION']
    * @global string $CONFIG['TITLE']
    * @param string $module
    * @param string $tpl_engine (optional)
    */
    function __construct($module, $tpl_engine = 'smarty') {

        // Defaults
        $this->url = $GLOBALS['CONFIG']['URL'];
        $this->title = $GLOBALS['CONFIG']['TITLE'];
        $this->sitename = $GLOBALS['CONFIG']['TITLE'];
        $this->bool['analytics'] = false;

        $this->_gtext = SifuFunct::getGtext($module); // Gtext
        $this->module = $module; // Module

        // Partition
        if (!empty($_SESSION['partition'])) $this->partition = $_SESSION['partition'];
        else $this->partition  = $GLOBALS['CONFIG']['PARTITION'];

        // Path to HTML header
        $this->html_header = $GLOBALS['CONFIG']['PATH'] . '/biz/templates/_partitions_/' . $this->partition  . '/globals/' . $tpl_engine . '/html_header.tpl';
        if (!file_exists($this->html_header)) $this->html_header = $GLOBALS['CONFIG']['PATH'] . '/biz/templates/globals/' . $tpl_engine . '/html_header.tpl';
        if (!file_exists($this->html_header)) $this->html_header = $GLOBALS['CONFIG']['PATH'] . '/templates/globals/' . $tpl_engine . '/html_header.tpl';

        // Path to HTML footer
        $this->html_footer = $GLOBALS['CONFIG']['PATH'] . '/biz/templates/_partitions_/' . $this->partition  . '/globals/' . $tpl_engine . '/html_footer.tpl';
        if (!file_exists($this->html_footer)) $this->html_footer = $GLOBALS['CONFIG']['PATH'] . '/biz/templates/globals/' . $tpl_engine . '/html_footer.tpl';
        if (!file_exists($this->html_footer)) $this->html_footer = $GLOBALS['CONFIG']['PATH'] . '/templates/globals/' . $tpl_engine . '/html_footer.tpl';

        // Path to letterhead
        $this->letterhead = $GLOBALS['CONFIG']['PATH'] . '/biz/templates/_partitions_/' . $this->partition  . '/globals/' . $tpl_engine . '/letterhead.tpl';
        if (!file_exists($this->letterhead)) $this->letterhead = $GLOBALS['CONFIG']['PATH'] . '/biz/templates/globals/' . $tpl_engine . '/letterhead.tpl';
        if (!file_exists($this->letterhead)) $this->letterhead = $GLOBALS['CONFIG']['PATH'] . '/templates/globals/' . $tpl_engine . '/letterhead.tpl';

        // Base stylesheet (mandatory)
        if (file_exists($GLOBALS['CONFIG']['PATH'] . "/biz/media/_partitions_/{$this->partition}/css/base.css")) {
            $this->stylesheets = "<link rel='stylesheet' type='text/css' href='{$this->url}/biz/media/_partitions_/{$this->partition}/css/base.css' />\n";
        }
        elseif (file_exists($GLOBALS['CONFIG']['PATH'] . "/biz/media/css/base.css")) {
            $this->stylesheets = "<link rel='stylesheet' type='text/css' href='{$this->url}/biz/media/css/base.css' />\n";
        }
        else {
            $this->stylesheets = "<link rel='stylesheet' type='text/css' href='{$this->url}/media/css/base.css' />\n";
        }

        // Module stylesheet (optional)
        if (file_exists($GLOBALS['CONFIG']['PATH'] . "/biz/media/_partitions_/{$this->partition}/css/{$this->module}.css")) {
            $this->stylesheets .= "<link rel='stylesheet' type='text/css' href='{$this->url}/biz/media/_partitions_/{$this->partition}/css/{$this->module}.css' />\n";
        }
        if (file_exists($GLOBALS['CONFIG']['PATH'] . "/biz/media/css/{$this->module}.css")) {
            $this->stylesheets .= "<link rel='stylesheet' type='text/css' href='{$this->url}/biz/media/css/{$this->module}.css' />\n";
        }
        elseif (file_exists($GLOBALS['CONFIG']['PATH'] . "/media/css/{$this->module}.css")) {
            $this->stylesheets .= "<link rel='stylesheet' type='text/css' href='{$this->url}/media/css/{$this->module}.css' />\n";
        }
    }


    // -------------------------------------------------------------------------
    // General utility
    // -------------------------------------------------------------------------


    /**
    * Get gtext string
    *
    * @param string $key
    * @return string
    */
    function gtext($key) {

        return isset($this->_gtext[$key]) ? $this->_gtext[$key] : "\$gtext.$key";
    }


    /**
    * Assign, used to access this object's variables from inside a template
    *
    * @param string $variable the public variable to work with
    * @param string $value content
    * @param string|bool $k either key, or append
    */
    function assign($variable, $value, $k = false) {

        // Array
        if (is_array($this->$variable)) {
            if (!$k) return;
            else {

                $this->$variable[$k] = $value;
                return;
            }
        }

        // Text
        if ($k) $this->$variable .= $value; // Append
        else $this->$variable = $value;
    }


    /**
    * Return a unique id
    *
    * @return string random md5
    */
    function uniqueId() {

        return md5(uniqid(time()));
    }


    /**
    * Detect $_POST
    *
    * @return bool
    */
    function detectPOST() {

        if (isset($_POST) && count($_POST)) return true;
        else return false;
    }


    /**
    * Check if a user is logged in
    */
    function isLoggedIn() {

        return isset($_SESSION['users_id']) ? true : false;
    }


    /**
    * Wrapper, Access control list
    *
    * Important! For obvious reasons this should be used with {nocache}
    *
    * @param string $val 'root', 'r', 'w', 'x'
    * @return bool
    */
    function acl($val) {

        return SifuFunct::acl($val, $this->module);
    }


    /**
    * Wrapper, Clean up a unique file name
    *
    * @param string $filename
    * @return string
    */
    function cleanFileName($filename) {

        return SifuFunct::cleanFileName($filename);
    }


    /**
    * Wrapper, make URL
    *
    * @param string $path controler value in /this/style
    * @param array $query http_build_query compatible array
    * @param bool $full return full url?
    * @return string url
    */
    function makeUrl($path, $query = null, $full = false) {

        $url = SifuFunct::makeUrl($path, $query, $full);
        return htmlspecialchars($url); // Rendering HTML, fix it
    }


    /**
    * Wrapper, myHttpServer
    *
    * @return string url
    */
    function myHttpServer() {

        return SifuFunct::myHttpServer();
    }


    // -------------------------------------------------------------------------
    // Navlist
    // -------------------------------------------------------------------------

    /**
    * Construct a navigation div
    *
    * @global bool $CONFIG['CLEAN_URL']
    * @global string $CONFIG['URL']
    * @return string the html code
    */
    static function navlist() {

        $gtext = SifuFunct::getGtext();
        if (isset($gtext['navcontainer'])) $list = $gtext['navcontainer'];
        else return '';

        // Deal with special "__get_module_menu__::" keyword
        $tmp = array();
        foreach($list as $key => $val) {
            if (strpos($key, '__get_module_menu__::') === 0) {
                $get = explode('::', $key);
                $menu = SifuFunct::getModuleMenu($get[1]);
                if (is_array($menu)) {
                    foreach($menu as $key2 => $val2) {
                        $tmp[$key2] = $val2;
                    }
                }
            }
            else {
                $tmp[$key] = $val;
            }
        }
        $list = $tmp;

        // Make an educated guess as to which controller we are currently using?
        $compare = 'home';
        if (!empty($_GET['c'])) {
            $params = explode('/', $_GET['c']);
            $compare = array_shift($params);
        }

        if (!$GLOBALS['CONFIG']['CLEAN_URL']) $compare = "?c=$compare";
        else $compare = ltrim($GLOBALS['CONFIG']['URL'] . "/$compare", '/');

        $selected = null;
        if ($compare) {
            foreach ($list as $key => $val) {
                if (is_array($val) && mb_strpos($val[0], $compare)) { // First item in sub-menu
                    $selected = $key;
                    break;
                }
                elseif (is_string($val) && mb_strpos($val, $compare)) { // No sub-menu
                    $selected = $key;
                    break;
                }
            }
        }

        // Makeshift renderer object
        $r['arr']['list'] = $list;
        $r['text']['selected'] = $selected;
        $r = (object) $r;

        // Template
        $tpl = new SifuTemplate('globals');
        $tpl->assignByRef('r', $r);

        return $tpl->fetch('navlist.tpl');
    }


    // -------------------------------------------------------------------------
    // HTML/Js helpers
    // -------------------------------------------------------------------------


    /**
    * Return a best guess URL for a media type file
    *
    * @global string $CONFIG['PATH']
    * @param string $filename
    * @return string
    */
    function asset($filename) {

        // Fix stupidties
        $filename = trim($filename);
        $filename = ltrim($filename, '/');
        $filename = rtrim($filename, '/');

        if (file_exists($GLOBALS['CONFIG']['PATH'] . "/biz/media/_partitions_/{$this->partition}/$filename")) {
            return "{$this->url}/biz/media/_partitions_/{$this->partition}/$filename";
        }
        elseif (file_exists($GLOBALS['CONFIG']['PATH'] . "/biz/media/$filename")) {
            return "{$this->url}/biz/media/$filename";
        }
        else {
            return "{$this->url}/media/$filename";
        }
    }


    /**
    * Scripts snippet
    *
    * @global string $CONFIG['PATH']
    * @return string
    */
    function scripts() {

        // Globals.js (mandatory)
        if (file_exists($GLOBALS['CONFIG']['PATH'] . "/biz/media/_partitions_/{$this->partition}/js/globals.js")) {
            $scripts = "<script type='text/javascript' src='{$this->url}/media/_partitions_/{$this->partition}/js/globals.js'></script>\n";
        }
        elseif (file_exists($GLOBALS['CONFIG']['PATH'] . "/biz/media/js/globals.js")) {
            $scripts = "<script type='text/javascript' src='{$this->url}/media/js/globals.js'></script>\n";
        }
        else {
            $scripts = "<script type='text/javascript' src='{$this->url}/media/js/globals.js'></script>\n";
        }

        // Module script (optional)
        if (file_exists($GLOBALS['CONFIG']['PATH'] . "/biz/media/_partitions_/{$this->partition}/js/{$this->module}.js")) {
            $scripts .= "<script type='text/javascript' src='{$this->url}/media/_partitions_/{$this->partition}/js/{$this->module}.js'></script>\n";
        }
        if (file_exists($GLOBALS['CONFIG']['PATH'] . "/biz/media/js/{$this->module}.js")) {
            $scripts .= "<script type='text/javascript' src='{$this->url}/media/js/{$this->module}.js'></script>\n";
        }
        elseif (file_exists($GLOBALS['CONFIG']['PATH'] . "/biz/media/js/{$this->module}.js")) {
            $scripts .= "<script type='text/javascript' src='{$this->url}/media/js/{$this->module}.js'></script>\n";
        }

        return $scripts;
    }


    /**
    * jQuery Initialization
    *
    * @global string $CONFIG['URL']
    * @param bool $ui include jQuery-Ui component.
    * @return string html header code
    */
    function jQueryInit($ui = false) {

        // jQuery
        $tmp1 = $GLOBALS['CONFIG']['URL'] . '/includes/symbionts/jquery-ui/js/jquery-1.7.2.min.js';
        // jQuery UI
        $tmp2 = $GLOBALS['CONFIG']['URL'] . '/includes/symbionts/jquery-ui/js/jquery-ui-1.8.21.custom.min.js';
        $tmp3 = $GLOBALS['CONFIG']['URL'] . '/includes/symbionts/jquery-ui/css/smoothness/jquery-ui-1.8.21.custom.css';

        $js = '<script type="text/javascript" src="' . $tmp1 . '"></script>' . "\n";
        if ($ui) {
            $js .= '<script type="text/javascript" src="' . $tmp2 . '"></script>' . "\n";
            $js .= '<link rel="stylesheet" type="text/css" href="' . $tmp3 . '" />' . "\n";

        }
        return $js;
    }


    /**
    * jQuery Datepicker localization. The desired localization file should be
    * included after the main datepicker code.
    *
    * @global string $CONFIG['LANGUAGE']
    * @global string $CONFIG['URL']
    * @return string html header code
    */
    function jQueryDatepickerLocale() {

        if (!empty($_SESSION['language'])) $lang = $_SESSION['language'];
        else $lang = $GLOBALS['CONFIG']['LANGUAGE'];

        if (file_exists($GLOBALS['CONFIG']['PATH'] . "/includes/symbionts/jquery-ui/development-bundle/ui/i18n/jquery.ui.datepicker-{$lang}.js")) {

            $tmp = $GLOBALS['CONFIG']['URL'] . "/includes/symbionts/jquery-ui/development-bundle/ui/i18n/jquery.ui.datepicker-{$lang}.js";
            $js = '<script type="text/javascript" src="' . $tmp . '"></script>' . "\n";
            return $js;
        }
    }


    /**
    * jQuery Validation Initialization
    *
    * @global string $CONFIG['LANGUAGE']
    * @global string $CONFIG['URL']
    * @global string $CONFIG['PATH']
    * @return string html header code
    */
    function jQueryValidatorInit() {

        if (!empty($_SESSION['language'])) $lang = $_SESSION['language'];
        else $lang = $GLOBALS['CONFIG']['LANGUAGE'];

        $tmp1 = $GLOBALS['CONFIG']['URL'] . '/includes/symbionts/jqueryAddons/jquery-validation/jquery.validate.min.js';
        $tmp2 = "/includes/symbionts/jqueryAddons/jquery-validation/localization/messages_{$lang}.js";

        $js = '<script type="text/javascript" src="' . $tmp1 . '"></script>' . "\n";
        if (file_exists($GLOBALS['CONFIG']['PATH'] . $tmp2)) {
            $js .= '<script type="text/javascript" src="' . $GLOBALS['CONFIG']['URL'] . $tmp2 . '"></script>' . "\n";
        }
        return $js;
    }


    /**
    * jQuery, generic hover effects
    *
    * @return string jquery ui code
    */
    function jQueryHoverCode() {

        return '
        // jQuery UI icons
        $(".ui-state-default").hover(
          function(){ $(this).addClass("ui-state-hover"); },
          function(){ $(this).removeClass("ui-state-hover"); }
        );
        $(".ui-state-default").click(function(){ $(this).toggleClass("ui-state-active"); });
        // Table tr
        $("tr").hover(function() {
          $(this).addClass("pretty-hover");
        },
        function() {
          $(this).removeClass("pretty-hover");
        });
        ';
    }


    /**
    * JS Console
    *
    * @return string
    */
    function jsConsole() {

        if (!empty($this->js_console)) {

            $tmp = "<script type='text/javascript'>\n// <![CDATA[\n";
            $tmp .= SifuLog::jsConsoleInit() . $this->js_console;
            $tmp .= "\n// ]]>\n</script>\n";

            return $tmp;
        }
    }


    // -------------------------------------------------------------------------
    // Smarty compatible option arrays
    // -------------------------------------------------------------------------

    /**
    * Get countries, Uses 2 letter ISO naming convention
    *
    * @global array $CONFIG['COUNTRIES']
    * @return array
    */
    function getCountriesOptions() {

        // Cache
        static $arr = null;
        if (is_array($arr)) return $arr;

        // Procedure
        $tmp = $this->gtext('countries_arr');
        asort($tmp);
        $arr = array_merge(array(null => '---'), $tmp);
        return $arr;
    }


    /**
    * Get active countries, Uses 2 letter ISO naming convention
    *
    * @global array $CONFIG['COUNTRIES']
    * @return array
    */
    function getActiveCountriesOptions() {

        // Cache
        static $arr = null;
        if (is_array($arr)) return $arr;

        // Procedure
        $arr[null] = '---';
        $tmp = $this->getCountriesOptions();
        if (!count($GLOBALS['CONFIG']['COUNTRIES'])) return $tmp;
        else {

            foreach ($GLOBALS['CONFIG']['COUNTRIES'] as $val) {
                $arr[$val] = $tmp[$val];
            }
        }

        return $arr;
    }



    /**
    * Get languages, uses 2 letter ISO naming convention
    *
    * @return array
    */
    function getLanguagesOptions() {

        // Cache
        static $arr = null;
        if (is_array($arr)) return $arr;

        // Procedure
        $tmp = array(
            'ar' => 'العربية',
            'bg' => 'Български',
            'cs' => 'Čeština',
            'da' => 'Dansk',
            'de' => 'Deutsch',
            'el' => 'Ελληνικά',
            'en' => 'English',
            'es' => 'Español',
            'et' => 'Eesti',
            'fi' => 'Suomi',
            'fr' => 'Français',
            'ga' => 'Gaeilge',
            'he' => 'עִבְרִית',
            'hu' => 'Magyar',
            'it' => 'Italiano',
            'ja' => '日本語',
            'ko' => '한국어',
            'lt' => 'Lietuvių',
            'lv' => 'Latviešu',
            'mt' => 'Malti',
            'nl' => 'Nederlands',
            'pl' => 'Polski',
            'pt' => 'Português',
            'ro' => 'Română',
            'sk' => 'Slovenčina',
            'sl' => 'Slovenščina',
            'sv' => 'Svenska',
            'zh' => '中文',
            );
        asort($tmp);
        $arr = array_merge(array(null => '---'), $tmp);
        return $arr;
    }


    /**
    * Get active languages, uses 2 letter ISO naming convention
    *
    * @global array $CONFIG['LANGUAGES']
    * @return array
    */
    function getActiveLanguagesOptions() {

        // Cache
        static $arr = null;
        if (is_array($arr)) return $arr;

        // Procedure
        $arr[null] = '---';
        $tmp = $this->getLanguagesOptions();
        if (!count($GLOBALS['CONFIG']['LANGUAGES'])) return $tmp;
        else {

            foreach ($GLOBALS['CONFIG']['LANGUAGES'] as $val) {
                $arr[$val] = $tmp[$val];
            }
        }

        return $arr;
    }

}


// -------------------------------------------------------------------------
// Smarty extras
// -------------------------------------------------------------------------

/**
* Render navlist
*
* @global string $_SESSION['nickname']
* @param array $params smarty {insert} parameters
* @return string html
*/
function insert_navlist($params) {

    unset($params); // Not used
    return SifuRenderer::navlist();
}


/**
* Render userInfo
*
* @global string $_SESSION['nickname']
* @global bool $CONFIG['REGISTRATIONS']
* @param array $params smarty {insert} parameters
* @return string html
*/
function insert_userInfo($params) {

    unset($params); // Not used

    $tpl = new SifuTemplate('globals');
    $r = new SifuRenderer('globals'); // Renderer
    $tpl->assignByRef('r', $r); // Renderer referenced in template


    if (!empty($_SESSION['nickname'])) {

        if (SifuFunct::acl('r', 'admin')) $r->bool['acl'] = true;
        $r->text['nickname'] = $_SESSION['nickname'];
        $r->text['users_id'] = $_SESSION['users_id'];

        return $tpl->fetch('userinfo.tpl');
    }
    else {

        $r->bool['registrations'] = $GLOBALS['CONFIG']['REGISTRATIONS'];
        return $tpl->fetch('userlogin.tpl');
    }
}


/**
* getPreviousURL wrapper
*
* @param array $params smarty {insert} parameters
* @return string url
*/
function insert_previousURL($params) {

    unset($params); // Not used

    return SifuFunct::getPreviousURL();
}


?>