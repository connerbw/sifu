<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

namespace Sifu;

class Funct {

    // Static class, no cloning or instantiating allowed
    final private function __construct() { }
    final private function __clone() { }


    // ------------------------------------------------------------------------
    // Form helpers
    // ------------------------------------------------------------------------

    /**
    * First steps in sanitizing user input.
    *
    * @param array $dirty reference to unverified $_GET or $_POST
    * @param array $keys
    */
    static function shampoo(&$dirty, $keys) {

        $keys = array_flip($keys);
        $dirty = array_intersect_key($dirty, $keys);
        array_walk_recursive($dirty, create_function('&$val', '$val = trim($val);'));
    }


    /**
    * Find if a record exists in the db
    *
    * @param string $table db table
    * @param string $col db column
    * @param string $val the value we are looking for
    * @return int|false
    */
    static function exists($table, $col, $val) {

        $q = "SELECT id FROM $table WHERE $col = ? ";

        $db = DbInit::get();
        $st = $db->pdo->prepare($q);
        $db->autoBind($st, array(1 => $val));
        $st->execute();
        $res = $st->fetchColumn();

        if ($res) return $res;
        else return false;
    }


    /**
    * Unique file name
    *
    * @param string $filename
    * @return string
    */
    static function uniqueFileName($filename) {

        $format = explode('.', $filename);
        $format = strtolower(end($format)); // Extension
        $format = filter_var($format, FILTER_SANITIZE_STRING);

        $pattern = "/\.$format\$/";
        $uniqid = time() . substr(md5(microtime()), 0, rand(5, 12));
        $replacement = "_{$uniqid}" . "$1" . ".$format";
        $new_name = preg_replace($pattern, $replacement, $filename);

        return $new_name;
    }


    /**
    * Clean up a unique file name
    *
    * @param string $filename
    * @return string
    */
    static function cleanFileName($filename) {

        $format = explode('.', $filename);
        $format = strtolower(end($format)); // Extension
        $format = filter_var($format, FILTER_SANITIZE_STRING);

        $pattern = "/_[A-Za-z0-9]+\.$format\$/";
        $replacement = ".$format";
        $old_name = preg_replace($pattern, $replacement, $filename);

        return $old_name;
    }


    // ------------------------------------------------------------------------
    // Get content
    // ------------------------------------------------------------------------

    /**
    * Get module menu
    *
    * @global string $CONFIG['PATH']
    * @param string $module the name of a module
    * @return array|bool SifuRenderer::navlist() compatible data structue
    */
    static function getModuleMenu($module) {

        $path_to_menu = $GLOBALS['CONFIG']['PATH'] . "/modules/{$module}/menu.php";
        $override = $GLOBALS['CONFIG']['PATH'] . "/biz/modules/{$module}/menu.php";

        $found = false;
        if (is_file($override)) {
            $path_to_menu = $override;
            $found = true;
        }
        if ($found || is_file($path_to_menu)) {
            include_once($path_to_menu);
            $funct = "{$module}_menu";
            if (function_exists($funct)) {
                return $funct();
            }
        }

        return false;
    }


    /**
    * Use output buffering to include a file into a string
    *
    * @param string $filename the file path and name to your PHP file.
    * @return string|bool the file contents if successful, false if file not found
    */
    static function getIncludeContents($filename) {
        if (is_file($filename)) {
            ob_start();
            include($filename);
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
        return false;
    }

    // ------------------------------------------------------------------------
    // Miscelaneous
    // ------------------------------------------------------------------------

    /**
    * Sanitize HTML
    *
    * @param string $html the html to sanitize
    * @param int $trusted -1, 0, or 1
    * @return string sanitized html
    */
    static function sanitizeHtml($html, $trusted = -1) {

        if ($trusted > 0) {
            // Allow all (*) except -script and -iframe
            $config = array(
                'elements' => '*-script-iframe',
                );
        }
        elseif ($trusted < 0) {
            // Paranoid mode, i.e. only allow a small subset of elements to pass
            // Transform strike and u to span for better XHTML 1-strict compliance
            $config = array(
                'safe' => 1,
                'elements' => 'a,em,strike,strong,u,p,br,img,li,ol,ul',
                'make_tag_strict' => 1,
                );
        }
        else {
            // Safe
            $config = array(
                'safe' => 1,
                'deny_attribute' => 'style,class',
                'comment' => 1,
                );
        }

        require_once(dirname(__FILE__) . '/symbionts/htmLawed/htmLawed.php');
        return htmLawed($html, $config);

    }


    /**
     * Return data directory
     *
     * @global string $CONFIG ['PATH']
     * @param string $module
     * @return string
     * * @throws \Exception
     */
    static function dataDir($module) {

        $data_dir = $GLOBALS['CONFIG']['PATH'] . "/data/$module";
        if(!is_dir($data_dir) && !mkdir($data_dir, 0777, true)) {
            throw new \Exception('Missing data dir ' . $data_dir);
        }

        return $data_dir;

    }


    /**
    * Unzip
    *
    * @param string $file
    * @param string $dir
    * @return bool
    */
    static function unzip($file, $dir) {

        if (class_exists('ZipArchive')) {

            $zip = new \ZipArchive();
            if ($zip->open($file) === true) {
                $_ret = $zip->extractTo($dir);
                $zip->close();
                return $_ret;
            }
            else return false;

        }
        else {

            // Escape
            $file = escapeshellarg($file);
            $dir = escapeshellarg($dir);

            $cmd = "unzip {$file} -d {$dir}"; // Info-zip assumed to be in path

            $res = -1; // any nonzero value
            $unused = array();
            $unused2 = exec($cmd, $unused, $res);
            if ($res != 0) trigger_error("Warning: unzip return value is $res ", E_USER_WARNING);

            return ($res == 0 || $res == 1); // http://www.info-zip.org/FAQ.html#error-codes

        }

    }



    /**
    * Remove directory
    *
    * @param string $dirname
    * @return bool
    */
    static function obliterateDir($dirname) {

        if (!is_dir($dirname)) return false;

        if (isset($_ENV['OS']) && strripos($_ENV['OS'], "windows", 0) !== FALSE) {

            // Windows patch for buggy perimssions on some machines
            $command = 'cmd /C "rmdir /S /Q "'.str_replace('//', '\\', $dirname).'\\""';
            $wsh = new \COM("WScript.Shell");
            $wsh->Run($command, 7, false);
            $wsh = null;
            return true;

        }
        else {

            $dscan = array(realpath($dirname));
            $darr = array();
            while (!empty($dscan)) {
                $dcur = array_pop($dscan);
                $darr[] = $dcur;
                if ($d = opendir($dcur)) {
                    while ($f=readdir($d)) {
                        if ($f == '.' || $f == '..') continue;
                        $f = $dcur . '/' . $f;
                        if (is_dir($f)) $dscan[] = $f;
                        else unlink($f);
                    }
                    closedir($d);
                }
            }

            for ($i=count($darr)-1; $i >= 0 ; $i--) {
                if (!rmdir($darr[$i]))
                    trigger_error("Warning: There was a problem deleting a temporary file in $dirname ", E_USER_WARNING);
            }

            return (!is_dir($dirname));

        }

    }


	/**
	* Get MIME type of file
	*
	* @param string $file fullpath to file
	* @return string
	*/
	protected function getMimeType($file) {

		if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mime = finfo_file($finfo, $file);
			finfo_close($finfo);
		}
		elseif (function_exists('mime_content_type')) {
			$mime = @mime_content_type($file); // Supress deprecated message
		}
		else {
			// Guess popular mime types from extension
			$ext = explode('.', $file);
			$ext = strtolower(end($ext));
			$ext = filter_var($ext, FILTER_SANITIZE_STRING);

			if ('css' == $ext) $mime = 'text/css';
			elseif ('doc' == $ext) $mime = 'application/msword';
			elseif ('gif' == $ext) $mime = 'image/gif';
			elseif ('jpg' == $ext || 'jpeg' == $ext) $mime = 'image/jpg';
			elseif ('js' == $ext) $mime = 'text/javascript';
			elseif ('pdf' == $ext) $mime = 'application/pdf';
			elseif ('png' == $ext) $mime = 'image/png';
			elseif ('ppt' == $ext) $mime = 'application/vnd.ms-powerpoint';
			elseif ('txt' == $ext) $mime = 'text/plain';
			elseif ('xls' == $ext) $mime = 'application/vnd.ms-excel';
			elseif ('xml' == $ext) $mime = 'text/xml';
			elseif ('zip' == $ext) $mime = 'application/zip';
			else $mime = 'application/octet-stream';
		}

		return $mime;
	}


    /**
    * Marked as deprecated
    *
    * @param string $deprecated
    * @param callback $replacement
    * @param array $args the parameters to be passed to $replacement, as an indexed array
    * @return mixed the return value of the callback, or false
    */
    static function markedAsDeprecated($deprecated, $replacement = null, $args = array()) {

    	trigger_error("Deprecated function/method: $deprecated ", E_USER_NOTICE);

    	if ($replacement) {
    		return call_user_func_array($replacement, $args);
    	}
    	else return false;
    }


    // ------------------------------------------------------------------------
    // Dates and times
    // ------------------------------------------------------------------------


    /**
     * Get the last day of a month
     *
     * @param string $month
     * @param string $year
     * @return string|bool YYYY-MM-DD
     */
    static function lastDay($month = '', $year = '') {

        if (empty($month)) $month = date('m');
        if (empty($year)) $year = date('Y');
        $result = strtotime("{$year}-{$month}-01");
        $result = strtotime('-1 second', strtotime('+1 month', $result));
        return date('Y-m-d', $result);

    }


    // ------------------------------------------------------------------------
    // URLs
    // ------------------------------------------------------------------------


    /**
    * Redirection
    *
    * @param string $href a uniform resource locator (URL)
    */
    static function redirect($href) {

        $href = filter_var($href, FILTER_SANITIZE_URL);

        if (!isset($_SESSION['birdfeed'])) $_SESSION['birdfeed'] = 0;
        if ($_SESSION['birdfeed'] > 3) $href = self::makeUrl('/home'); // Avoid infinite redirects
        ++$_SESSION['birdfeed'];

        if (!headers_sent()) {
            header("Location: $href");
        }
        else {
            // Javascript hack
            echo "
            <script type='text/javascript'>
            // <![CDATA[
            window.location = '{$href}';
            // ]]>
            </script>
            ";
        }

        exit; // Quit script
    }


    /**
    * Get the server url
    *
    * @return string http server url
    */
    static function myHttpServer() {

        // Autodetect ourself
        $s = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off' ? 's' : '';
        $host = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'];
        if (($s && $port == "443") || (!$s && $port == "80") || preg_match("/:$port\$/", $host)) {
            $p = '';
        }
        else {
            $p = ':' . $port;
        }

        return "http$s://$host$p";

    }


    /**
    * Make URL based on $CONFIG['CLEAN'] setting
    *
    * @global string $CONFIG['URL']
    * @global string $CONFIG['CLEAN_URL']
    * @param string $path controller value in /this/style
    * @param array $query http_build_query compatible array
    * @param bool $full return full url?
    * @return string url
    */
    static function makeUrl($path, $query = null, $full = false) {

        // Fix stupidties
        $path = trim($path);
        $path = ltrim($path, '/');
        $path = rtrim($path, '/');

        $tmp = '';
        if ($full)  $tmp .= self::myHttpServer();
        $tmp .= $GLOBALS['CONFIG']['URL'];
        $tmp .= ($GLOBALS['CONFIG']['CLEAN_URL'] ? '/' : '/index.php?c=');
        $tmp .= $path;
        $tmp = rtrim($tmp, '/'); // In case path is null

        if (is_array($query) && count($query)) {
            $q = mb_strpos($tmp, '?') ? '&' : '?';
            $tmp .= $q . http_build_query($query);
        }

        return $tmp;
    }


    /**
    * Get this user's previous URL
    *
    * @global string $CONFIG['PREV_SKIP']
    * @param string|array $skip regular expressions
    * @return bool
    */
    static function getPreviousURL($skip = null) {

        if ($skip == null) $skip = $GLOBALS['CONFIG']['PREV_SKIP'];
        elseif (!is_array($skip)) $skip = array($skip);

        if (isset($_SESSION['breadcrumbs'])) {
            foreach($_SESSION['breadcrumbs'] as $val) {
                $ok = true;
                foreach ($skip as $val2) {
                    if (preg_match($val2, $val)) $ok = false;
                }
                if ($ok) return self::makeUrl($val);
            }
        }
        return self::makeUrl('/home'); // Some default;
    }


    /**
     * Canonicalize URL
     *
     * @param  string $url
     * @return string
     */
    static function canonicalizeUrl($url) {

        // remove trailing slash
        $url = rtrim(trim($url), '/');

        // Add http:// if it's missing
        if (!preg_match('#^https?://#i', $url)) {
            // Remove ftp://, gopher://, fake://, etc
            if (mb_strpos($url, '://')) list($garbage, $url) = mb_split('://', $url);
            // Prepend http
            $url = 'http://' . $url;
            if (preg_match('#^http:///#', $url)) {
                return null; // This is wrong...
            }
        }

        // protocol and domain to lowercase (but NOT the rest of the URL),
        $scheme = @parse_url($url, PHP_URL_SCHEME);
        $url = preg_replace("/$scheme/", mb_strtolower($scheme), $url, 1);
        $host = @parse_url($url, PHP_URL_HOST);
        $url = preg_replace("/$host/", mb_strtolower($host), $url, 1);

        // Sanitize for good measure
        $url = filter_var($url, FILTER_SANITIZE_URL);

        return $url;

    }


    /**
    * Create a trail of breadcrumbs with URLs
    */
    static function breadcrumbs() {

        $crumb = filter_var(trim((isset($_GET['c']) ? $_GET['c'] : 'home'), '/'), FILTER_SANITIZE_URL);
        if (count($_GET) > 1) {
            $crumb .= $GLOBALS['CONFIG']['CLEAN_URL'] ? '?' : '&';
            $tmp = $_GET;
            ksort($tmp);
            foreach($tmp as $key => $val) {
                if ($key == 'c') continue;
                elseif (strtolower($key) == 'page' && $val == 1) continue;
                else $crumb .= filter_var("$key=$val&", FILTER_SANITIZE_URL);
            }
            $crumb = rtrim($crumb, '&?');
        }
        if (!isset($_SESSION['breadcrumbs'])) $_SESSION['breadcrumbs'] = array();
        array_unshift($_SESSION['breadcrumbs'], $crumb);
        $_SESSION['breadcrumbs'] = array_unique($_SESSION['breadcrumbs']);
        $_SESSION['breadcrumbs'] = array_slice($_SESSION['breadcrumbs'], 0, 10); // maximum 10
        $_SESSION['birdfeed'] = 0; // Reset, used to prevent infinite redirects
    }


    // ------------------------------------------------------------------------
    // ACL
    // ------------------------------------------------------------------------

    /**
    * Access control list with cheap "SifuRenderer" style cache
    *
    * @param string $val 'r', 'w', 'x'
    * @param string $module
    * @return bool
    */
    static function acl($val, $module) {

        if (empty($_SESSION['users_id'])) return false;

        $valid = array('r', 'w', 'x');
        if (!in_array($val, $valid)) return false;

        $key = $_SESSION['users_id'] . "-{$module}-{$val}";

        // Cache
        static $arr = array();
        if (isset($arr[$key])) return $arr[$key];

        $access = new Access();

        // More cache
        static $user_is_root = null;
        if (is_bool($user_is_root)) {
            if ($user_is_root) return true;
        }
        else {
            $user_is_root = $access->isRoot();
            if ($user_is_root) return true;
        }

        if ('r' == $val) {
            // Read
            $arr[$key] = $access->hasReadAccess($module);
            return $arr[$key];
        }
        elseif ('w' == $val) {
            // Write
            $arr[$key] = $access->hasWriteAccess($module);
            return $arr[$key];
        }
        elseif ('x' == $val) {
            // Execute
            $arr[$key] = $access->hasExecuteAccess($module);
            return $arr[$key];
        }
        else {
            return false;
        }
    }


    // ------------------------------------------------------------------------
    // Languages
    // ------------------------------------------------------------------------

    /**
    * Get the user's language
    *
    * @global string $CONFIG['PARTITION']
    * @global string $CONFIG['LANGUAGE']
    * @global string $CONFIG['PATH']
    * @param string $module
    * @return array $gtext
    */
    static function getGtext($module = 'globals') {

        // Cache
        static $gtext_cache = array();
        if (isset($gtext_cache[$module])) return $gtext_cache[$module];

        $gtext = array();

        // Partition
        if (!empty($_SESSION['partition'])) $partition = $_SESSION['partition'];
        else $partition  = $GLOBALS['CONFIG']['PARTITION'];

        // Language
        if (!empty($_SESSION['language'])) $lang = $_SESSION['language'];
        else $lang = $GLOBALS['CONFIG']['LANGUAGE'];

        $search = array('globals');
        if ($module != 'globals') $search[] = $module;

        foreach ($search as $location) {

            $o1 = $GLOBALS['CONFIG']['PATH'] . "/templates/{$location}/languages/en.php";
            $o2 = $GLOBALS['CONFIG']['PATH'] . "/templates/{$location}/languages/{$lang}.php";
            $o3 = $GLOBALS['CONFIG']['PATH'] . "/biz/templates/{$location}/languages/en.php";
            $o4 = $GLOBALS['CONFIG']['PATH'] . "/biz/templates/{$location}/languages/{$lang}.php";
            $o5 = $GLOBALS['CONFIG']['PATH'] . "/biz/templates/_partitions_/{$partition}/{$location}/languages/en.php";
            $o6 = $GLOBALS['CONFIG']['PATH'] . "/biz/templates/_partitions_/{$partition}/{$location}/languages/{$lang}.php";
            $o7 = $GLOBALS['CONFIG']['PATH'] . "/templates/{$location}/languages/_override.php";
            $o8 = $GLOBALS['CONFIG']['PATH'] . "/biz/templates/{$location}/languages/_override.php";
            $o9 = $GLOBALS['CONFIG']['PATH'] . "/biz/templates/_partitions_/{$partition}/{$location}/languages/_override.php";

            if (is_readable($o1)) include($o1);
            if (is_readable($o2)) include($o2);
            if (is_readable($o3)) include($o3);
            if (is_readable($o4)) include($o4);
            if (is_readable($o5)) include($o5);
            if (is_readable($o6)) include($o6);
            if (is_readable($o7)) include($o7);
            if (is_readable($o8)) include($o8);
            if (is_readable($o9)) include($o9);

            $gtext_cache[$location] = $gtext;
        }

        if (!is_array($gtext_cache[$module]) || !count($gtext_cache[$module])) return false; // something is wrong
        else {
            return $gtext_cache[$module];
        }
    }


}
