<?php

/* Navigation menu */

$gtext['navcontainer'] = array(
    'Home' => SifuFunct::makeUrl('/home'),
    // '__get_module_menu__::home' => null,
    'Sourcecode' => 'https://github.com/connerbw/sifu/',
    );

/*
* Example of a more complex navcontainer structure:
*
* $gtext['navcontainer'] = array(
*
*     // This will load "menu.php" from "modules/home"
*     '__get_module_menu__::home' => null,
*
*     // Typical menu structure
*     'Example 1' => array(
*         SifuFunct::makeUrl('/example1'),
*         array(
*             // Another example
*             'Trotch' => 'http://www.trotch.com/',
*             'Google' => 'http://www.google.com/',
*             'Yet Another Home' => SifuFunct::makeUrl('/home'),
*             ),
*         ),
*
*     // Can also accept string as second argument
*     'Sourcecode' => 'https://github.com/connerbw/sifu/',
*
*     );
*/

/* 404 and Banned pages */

$gtext['404_continue'] = 'Click here to continue';
$gtext['404_h1'] = 'Oops, Page Not Found (Error 404)';
$gtext['404_p1'] = 'For some reason (mis-typed URL, faulty referral from another site, out-of-date search engine listing or we simply deleted a file) the page you were after is not here.';
$gtext['banned_continue'] = 'Click here to continue';
$gtext['banned_h1'] = 'Banned';
$gtext['banned_p1'] = 'You have been a bad person, a very very bad person.';

/* Generic globals */

$gtext['admin'] = 'Administration';
$gtext['alert_delete_1'] = 'This is a very dangerous action! All related content will be dissociated.';
$gtext['alert_delete_2'] = 'Are you sure you want to delete:';
$gtext['back'] = 'Back to previous page';
$gtext['cancel'] = 'Cancel';
$gtext['clone'] = 'Clone';
$gtext['continue'] = 'Continue';
$gtext['delete'] = 'Delete';
$gtext['edit'] = 'Edit';
$gtext['error'] = 'Error';
$gtext['export_csv'] = 'Export CSV';
$gtext['form_error'] = 'Form error';
$gtext['home'] = 'Home';
$gtext['login'] = 'Login';
$gtext['logout'] = 'Logout';
$gtext['noscript'] = 'This site works best with JavaScript enabled.';
$gtext['permission_error'] = 'Permission error';
$gtext['permission_error_2'] = 'You don\'t have permission to do this.';
$gtext['print'] = 'Print';
$gtext['register'] = 'Register';
$gtext['submit'] = 'Submit';
$gtext['success'] = 'Success!';
$gtext['success_2'] = 'Everything seems fine.';
$gtext['unknown_error'] = 'Unknown error. Check the JavaScript console for more details.';
$gtext['warning'] = 'Warning';
$gtext['welcome'] = 'Welcome';

/* Countries, uses 2 letter ISO naming convention */

$gtext['countries_arr'] = array(
    'af' => 'Afghanistan',
    'ax' => 'Åland Islands',
    'al' => 'Albania',
    'dz' => 'Algeria',
    'as' => 'American Samoa',
    'ad' => 'Andorra',
    'ao' => 'Angola',
    'ai' => 'Anguilla',
    'aq' => 'Antarctica',
    'ag' => 'Antigua and Barbuda',
    'ar' => 'Argentina',
    'am' => 'Armenia',
    'aw' => 'Aruba',
    'au' => 'Australia',
    'at' => 'Austria',
    'az' => 'Azerbaijan',
    'bs' => 'Bahamas',
    'bh' => 'Bahrain',
    'bd' => 'Bangladesh',
    'bb' => 'Barbados',
    'by' => 'Belarus',
    'be' => 'Belgium',
    'bz' => 'Belize',
    'bj' => 'Benin',
    'bm' => 'Bermuda',
    'bt' => 'Bhutan',
    'bo' => 'Bolivia',
    'ba' => 'Bosnia and Herzegovina',
    'bw' => 'Botswana',
    'bv' => 'Bouvet Island',
    'br' => 'Brazil',
    'io' => 'British Indian Ocean Territory',
    'bn' => 'Brunei Darussalam',
    'bg' => 'Bulgaria',
    'bf' => 'Burkina Faso',
    'bi' => 'Burundi',
    'kh' => 'Cambodia',
    'cm' => 'Cameroon',
    'ca' => 'Canada',
    'cv' => 'Cape Verde',
    'ky' => 'Cayman Islands',
    'cf' => 'Central African Republic',
    'td' => 'Chad',
    'cl' => 'Chile',
    'cn' => 'China',
    'cx' => 'Christmas Island',
    'cc' => 'Cocos (Keeling) Islands',
    'co' => 'Colombia',
    'km' => 'Comoros',
    'cg' => 'Congo',
    'cd' => 'Congo, the Democratic Republic of the',
    'ck' => 'Cook Islands',
    'cr' => 'Costa Rica',
    'ci' => 'Côte d\'Ivoire',
    'hr' => 'Croatia',
    'cu' => 'Cuba',
    'cy' => 'Cyprus',
    'cz' => 'Czech Republic',
    'dk' => 'Denmark',
    'dj' => 'Djibouti',
    'dm' => 'Dominica',
    'do' => 'Dominican Republic',
    'ec' => 'Ecuador',
    'eg' => 'Egypt',
    'sv' => 'El Salvador',
    'gq' => 'Equatorial Guinea',
    'er' => 'Eritrea',
    'ee' => 'Estonia',
    'et' => 'Ethiopia',
    'fk' => 'Falkland Islands (Malvinas)',
    'fo' => 'Faroe Islands',
    'fj' => 'Fiji',
    'fi' => 'Finland',
    'fr' => 'France',
    'gf' => 'French Guiana',
    'pf' => 'French Polynesia',
    'tf' => 'French Southern Territories',
    'ga' => 'Gabon',
    'gm' => 'Gambia',
    'ge' => 'Georgia',
    'de' => 'Germany',
    'gh' => 'Ghana',
    'gi' => 'Gibraltar',
    'gr' => 'Greece',
    'gl' => 'Greenland',
    'gd' => 'Grenada',
    'gp' => 'Guadeloupe',
    'gu' => 'Guam',
    'gt' => 'Guatemala',
    'gg' => 'Guernsey',
    'gn' => 'Guinea',
    'gw' => 'Guinea-Bissau',
    'gy' => 'Guyana',
    'ht' => 'Haiti',
    'hm' => 'Heard Island and Mcdonald Islands',
    'va' => 'Holy See (Vatican City State)',
    'hn' => 'Honduras',
    'hk' => 'Hong Kong',
    'hu' => 'Hungary',
    'is' => 'Iceland',
    'in' => 'India',
    'id' => 'Indonesia',
    'ir' => 'Iran',
    'iq' => 'Iraq',
    'ie' => 'Ireland',
    'im' => 'Isle of Man',
    'il' => 'Israel',
    'it' => 'Italy',
    'jm' => 'Jamaica',
    'jp' => 'Japan',
    'je' => 'Jersey',
    'jo' => 'Jordan',
    'kz' => 'Kazakhstan',
    'ke' => 'Kenya',
    'ki' => 'Kiribati',
    'kp' => 'Korea, Democratic People\'s Republic of (North)',
    'kr' => 'Korea, Republic of (South)',
    'kw' => 'Kuwait',
    'kg' => 'Kyrgyzstan',
    'la' => 'Lao People\'s Democratic Republic',
    'lv' => 'Latvia',
    'lb' => 'Lebanon',
    'ls' => 'Lesotho',
    'lr' => 'Liberia',
    'ly' => 'Libyan arab Jamahiriya',
    'li' => 'Liechtenstein',
    'lt' => 'Lithuania',
    'lu' => 'Luxembourg',
    'mo' => 'Macao',
    'mk' => 'Macedonia, the Former Yugoslav Republic of',
    'mg' => 'Madagascar',
    'mw' => 'Malawi',
    'my' => 'Malaysia',
    'mv' => 'Maldives',
    'ml' => 'Mali',
    'mt' => 'Malta',
    'mh' => 'Marshall islands',
    'mq' => 'Martinique',
    'mr' => 'Mauritania',
    'mu' => 'Mauritius',
    'yt' => 'Mayotte',
    'mx' => 'Mexico',
    'fm' => 'Micronesia, Federated States of',
    'md' => 'Moldova, Republic of',
    'mc' => 'Monaco',
    'mn' => 'Mongolia',
    'me' => 'Montenegro',
    'ms' => 'Montserrat',
    'ma' => 'Morocco',
    'mz' => 'Mozambique',
    'mm' => 'Myanmar',
    'na' => 'Namibia',
    'nr' => 'Nauru',
    'np' => 'Nepal',
    'nl' => 'Netherlands',
    'an' => 'Netherlands Antilles',
    'nc' => 'New Caledonia',
    'nz' => 'New Zealand',
    'ni' => 'Nicaragua',
    'ne' => 'Niger',
    'ng' => 'Nigeria',
    'nu' => 'Niue',
    'nf' => 'Norfolk Island',
    'mp' => 'Northern Mariana Islands',
    'no' => 'Norway',
    'om' => 'Oman',
    'pk' => 'Pakistan',
    'pw' => 'Palau',
    'ps' => 'Palestinian Territory',
    'pa' => 'Panama',
    'pg' => 'Papua New Guinea',
    'py' => 'Paraguay',
    'pe' => 'Peru',
    'ph' => 'Philippines',
    'pn' => 'Pitcairn',
    'pl' => 'Poland',
    'pt' => 'Portugal',
    'pr' => 'Puerto Rico',
    'qa' => 'Qatar',
    're' => 'Reunion',
    'ro' => 'Romania',
    'ru' => 'Russian Federation',
    'rw' => 'Rwanda',
    'bl' => 'Saint Barthélemy',
    'sh' => 'Saint Helena',
    'kn' => 'Saint Kitts and Nevis',
    'lc' => 'Saint Lucia',
    'mf' => 'Saint Martin',
    'pm' => 'Saint Pierre and Miquelon',
    'vc' => 'Saint Vincent and the Grenadines',
    'ws' => 'Samoa',
    'sm' => 'San Marino',
    'st' => 'Sao Tome and Principe',
    'sa' => 'Saudi Arabia',
    'sn' => 'Senegal',
    'rs' => 'Serbia',
    'sc' => 'Seychelles',
    'sl' => 'Sierra Leone',
    'sg' => 'Singapore',
    'sk' => 'Slovakia',
    'si' => 'Slovenia',
    'sb' => 'Solomon Islands',
    'so' => 'Somalia',
    'za' => 'South Africa',
    'gs' => 'South Georgia and the South Sandwich Islands',
    'es' => 'Spain',
    'lk' => 'Sri Lanka',
    'sd' => 'Sudan',
    'sr' => 'Suriname',
    'sj' => 'Svalbard and Jan Mayen',
    'sz' => 'Swaziland',
    'se' => 'Sweden',
    'ch' => 'Switzerland',
    'sy' => 'Syrian Arab Republic',
    'tw' => 'Taiwan',
    'tj' => 'Tajikistan',
    'tz' => 'Tanzania, United Republic of',
    'th' => 'Thailand',
    'tl' => 'Timor-Leste',
    'tg' => 'Togo',
    'tk' => 'Tokelau',
    'to' => 'Tonga',
    'tt' => 'Trinidad and Tobago',
    'tn' => 'Tunisia',
    'tr' => 'Turkey',
    'tm' => 'Turkmenistan',
    'tc' => 'Turks and Caicos Islands',
    'tv' => 'Tuvalu',
    'ug' => 'Uganda',
    'ua' => 'Ukraine',
    'ae' => 'United Arab Emirates',
    'gb' => 'United Kingdom',
    'us' => 'United States',
    'um' => 'United States Minor Outlying Islands',
    'uy' => 'Uruguay',
    'uz' => 'Uzbekistan',
    'vu' => 'Vanuatu',
    've' => 'Venezuela',
    'vn' => 'Viet Nam',
    'vg' => 'Virgin Islands, British',
    'vi' => 'Virgin Islands, U.S.',
    'wf' => 'Wallis and futuna',
    'eh' => 'Western Sahara',
    'ye' => 'Yemen',
    'zm' => 'Zambia',
    'zw' => 'Zimbabwe',
    );

?>