<?php

/* ------------------------------------------------------------------------- */
/* Error reporting and debugging */
/* ------------------------------------------------------------------------- */

error_reporting(E_ALL | E_STRICT); // Development
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING); // Hosting

$CONFIG['DEBUG'] = true;

/* ------------------------------------------------------------------------- */
/* Configuration variables */
/* ------------------------------------------------------------------------- */

// Enable/disable registrations
$CONFIG['REGISTRATIONS'] = true;

// Database parameters, PDO compatible, NB: first key is default
$CONFIG['DSN'] =  array(
    // Mysql
    'sifu' => array('mysql:host=localhost;dbname=Sifu', 'root', 'root'),    
    );

// Site title
$CONFIG['TITLE'] = 'Sifu';

// The auto-detected path to your Sifu installation.
// If you set this yourself, no trailing slash!

$CONFIG['PATH'] = dirname(__FILE__);

// The url suffix to your site. For example, if your site is
// http://www.sifu.org/ then '' is appropriate. If your site is
// http://domain.com/my/Sifu/ then '/my/Sifu' is the correct value.
// No trailing slash!

$CONFIG['URL'] = '/~dac514/Sifu';

// Default language for site, uses a 2 letter l10n ISO-CODE naming convention
// See: http://www.loc.gov/standards/iso639-2/php/code_list.php
// Available choices: 'en', 'fr'

$CONFIG['LANGUAGE'] = 'en';

// Default partition for site, available choices:

$CONFIG['PARTITION'] = 'sifu';

// Use clean Url?
// If apache rewrite rules aren't working for you, change to false

$CONFIG['CLEAN_URL'] = true;

// A salt used to create data verification hashes. Any random word will do.
// If a malicious user discovers your salt, this offers no real protection.
// Guard the salt zealously, change it as needed.

$CONFIG['SALT'] = 'flyingturtle';

// Sifu modules may cache templates, set the duration in seconds below.
$CONFIG['CACHE_LIFETIME'] = 15;

// Timzeone, pick yours from the list available at http://php.net/manual/en/timezones.php
$CONFIG['TIMEZONE'] = 'America/Montreal';


/* ------------------------------------------------------------------------- */
/* Advanced configuration variables */
/* Don't modify these unless you know what you are doing */
/* ------------------------------------------------------------------------- */

require($GLOBALS['CONFIG']['PATH'] . '/config.advanced.php');

// Override below...

$CONFIG['SMARTY_ERROR_REPORTING'] = E_ALL ^ E_NOTICE;

?>