<?php

/* ------------------------------------------------------------------------- */
/* Advanced configuration variables */
/* Don't modify these unless you know what you are doing */
/* ------------------------------------------------------------------------- */

// A list of modules that implement a permission scheme compatible
// with SifuAccess(). Maximum 20 characters

$CONFIG['ACCESS'] = array(
    'admin',
    );

// Supported countries
// Uses 2 letter ISO naming convention
// Full list in 'templates/globals/languages/en.php' and counterparts

// $CONFIG['COUNTRIES'] = array('ca', 'us');

// Supported languages
// Uses 2 letter ISO naming convention
// Full list in SifuRenderer()->getLanguagesOptions() method

// $CONFIG['LANGUAGES'] = array('en', 'fr');

// A list of regular expressions to skip
// used in tandem with the SifuFunct::getPreviousURL() function

$CONFIG['PREV_SKIP'] = array(
    '#^globals/(success|permission_error)$#i',
    '#^user/authenticate/(login|logout)$#i',
    '#/(duplicate|edit|imprint)/\d+$#i',
    '#/new$#i',
    );

// Smarty Error Reporting
// @see: http://php.net/manual/en/errorfunc.constants.php

$CONFIG['SMARTY_ERROR_REPORTING'] = 0;

?>