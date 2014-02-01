<?php

// ----------------------------------------------------------------------------
// Global functions
// ----------------------------------------------------------------------------

// Autoloader
function sifu_autoload($class_name) {

    if ('Pimple' == $class_name) {
        require($GLOBALS['CONFIG']['PATH'] . '/includes/symbionts/Pimple.php');
        return;
    }
    if (strpos($class_name, 'Sifu\\') !== 0) {
        return; // Ignore classes not in our namespace
    } else {
        $parts = explode('\\', $class_name);
        $_ = array_shift($parts);
    }

    $dirname = $GLOBALS['CONFIG']['PATH'];
    $class_name = array_pop($parts);
    $file_name = $class_name . '.php';
    $path = '';
    $prefix = '';
    $look_for_class = array();

    if ('Modules' == @$parts[0]) {
        $parts = array_map('strtolower', $parts);
    } else {
        array_unshift($parts, 'includes');
        $prefix = 'Sifu';
    }
    foreach ($parts as $part) {
        $path .= '/' . $part;
    }

    $look_for_class[] = $dirname . "/biz{$path}/{$prefix}{$file_name}";
    $look_for_class[] = $dirname . "{$path}/{$prefix}{$file_name}";

    foreach ($look_for_class as $f) {
        if (is_file($f)) {
            require($f);
            if ( class_exists( $class_name ) ) {
                break;
            }
        }
    }
}
spl_autoload_register('sifu_autoload');


/**
 * Get $_GET var
 *
 * @param string $name
 * @return $_GET
 */
function _GET($name) {

    return (isset($_GET[$name])) ? $_GET[$name] : null;
}


/**
 * Get $_POST var
 *
 * @param string $name
 * @return $_POST
 */
function _POST($name) {

    return (isset($_POST[$name])) ? $_POST[$name] : null;
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
    return \Sifu\Renderer::navlist();
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

    $tpl = new \Sifu\Template('globals');
    $r = new \Sifu\Renderer('globals'); // Renderer
    $tpl->assignByRef('r', $r); // Renderer referenced in template


    if (!empty($_SESSION['nickname'])) {

        if (\Sifu\Funct::acl('r', 'admin')) $r->bool['acl'] = true;
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

    return \Sifu\Funct::getPreviousURL();
}

/**
 * Simple trim filter for Smarty templates
 *
 * @param string $tpl_output
 * @param \Smarty_Internal_Template $template
 * @return string
 */
function _sifu_template_trim($tpl_output, \Smarty_Internal_Template $template) {
    return trim($tpl_output);
}