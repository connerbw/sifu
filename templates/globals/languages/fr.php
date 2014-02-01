<?php

/* Navigation menu */

$gtext['navcontainer'] = array(
    'Accueil' => \Sifu\Funct::makeUrl('/home'),
    // '__get_module_menu__::home' => null,
    'Code source' => 'https://github.com/connerbw/sifu/',
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
*         \Sifu\Funct::makeUrl('/example1'),
*         array(
*             // Another example
*             'Trotch' => 'http://www.trotch.com/',
*             'Google' => 'http://www.google.com/',
*             'Yet Another Home' => \Sifu\Funct::makeUrl('/home'),
*             ),
*         ),
*
*     // Can also accept string as second argument
*     'Sourcecode' => 'https://github.com/connerbw/sifu/',
*
*     );
*/

/* 404 and Banned pages */

$gtext['404_continue'] = 'Cliquez ici pour continuer';
$gtext['404_h1'] = 'Oups, page introuvable (Erreur 404)';
// $gtext['404_p1'] = 'For some reason (mis-typed URL, faulty referral from another site, out-of-date search engine listing or we simply deleted a file) the page you were after is not here.';
$gtext['banned_continue'] = 'Cliquez ici pour continuer';
$gtext['banned_h1'] = 'Banni';
// $gtext['banned_p1'] = 'You have been a bad person, a very very bad person.';

/* Generic globals */

$gtext['admin'] = 'Administration';
$gtext['alert_delete_1'] = 'Ceci est une action très dangereuse! Tous les contenus connexes seront dissociées.';
$gtext['alert_delete_2'] = 'Etes-vous sûr de vouloir supprimer:';
$gtext['back'] = 'Retour à la page précédente';
$gtext['cancel'] = 'Annuler';
$gtext['clone'] = 'Clone';
$gtext['continue'] = 'Continuer';
$gtext['delete'] = 'Supprimer';
$gtext['edit'] = 'Modifier';
$gtext['export_csv'] = 'Export CSV';
$gtext['form_error'] = 'Erreur de formulaire';
$gtext['home'] = 'Accueil';
$gtext['login'] = 'Connexion';
$gtext['logout'] = 'Fin de session';
$gtext['noscript'] = 'Ce site est optimisé pour JavaScript.';
$gtext['permission_error'] = 'Erreur de permission';
$gtext['permission_error_2'] = 'Vous n\'avez pas la permission de faire cette action.';
$gtext['print'] = 'Imprimer';
$gtext['register'] = 'S\'enregistrer';
$gtext['submit'] = 'Soumettre';
$gtext['success'] = 'Succès!';
$gtext['success_2'] = 'Tout semble bien.';
$gtext['unknown_error'] = 'Erreur inconnue. Vérifiez la console JavaScript pour plus de détails.';
$gtext['warning'] = 'Attention';
$gtext['welcome'] = 'Bienvenue';

/* Countries, uses 2 letter ISO naming convention */

$gtext['countries_arr'] = array(
    'af' => 'Afghanistan',
    'za' => 'Afrique du sud',
    'ax' => 'Åland, îles',
    'al' => 'Albanie',
    'dz' => 'Algérie',
    'de' => 'Allemagne',
    'ad' => 'Andorre',
    'ao' => 'Angola',
    'ai' => 'Anguilla',
    'aq' => 'Antarctique',
    'ag' => 'Antigua-et-barbuda',
    'sa' => 'Arabie saoudite',
    'ar' => 'Argentine',
    'am' => 'Arménie',
    'aw' => 'Aruba',
    'au' => 'Australie',
    'at' => 'Autriche',
    'az' => 'Azerbaïdjan',
    'bs' => 'Bahamas',
    'bh' => 'Bahreïn',
    'bd' => 'Bangladesh',
    'bb' => 'Barbade',
    'by' => 'Bélarus',
    'be' => 'Belgique',
    'bz' => 'Belize',
    'bj' => 'Bénin',
    'bm' => 'Bermudes',
    'bt' => 'Bhoutan',
    'bo' => 'Bolivie, l\'état plurinational de',
    'bq' => 'Bonaire, saint-eustache et saba',
    'ba' => 'Bosnie-herzégovine',
    'bw' => 'Botswana',
    'bv' => 'Bouvet, île',
    'br' => 'Brésil',
    'bn' => 'Brunéi darussalam',
    'bg' => 'Bulgarie',
    'bf' => 'Burkina faso',
    'bi' => 'Burundi',
    'ky' => 'Caïmanes, îles',
    'kh' => 'Cambodge',
    'cm' => 'Cameroun',
    'ca' => 'Canada',
    'cv' => 'Cap-vert',
    'cf' => 'Centrafricaine, république',
    'cl' => 'Chili',
    'cn' => 'Chine',
    'cx' => 'Christmas, île',
    'cy' => 'Chypre',
    'cc' => 'Cocos (keeling), îles',
    'co' => 'Colombie',
    'km' => 'Comores',
    'cg' => 'Congo',
    'cd' => 'Congo, la république démocratique du',
    'ck' => 'Cook, îles',
    'kr' => 'Corée, république de',
    'kp' => 'Corée, république populaire démocratique de',
    'cr' => 'Costa rica',
    'ci' => 'Côte d\'ivoire',
    'hr' => 'Croatie',
    'cu' => 'Cuba',
    'cw' => 'Curaçao',
    'dk' => 'Danemark',
    'dj' => 'Djibouti',
    'do' => 'Dominicaine, république',
    'dm' => 'Dominique',
    'eg' => 'Égypte',
    'sv' => 'El salvador',
    'ae' => 'Émirats arabes unis',
    'ec' => 'Équateur',
    'er' => 'Érythrée',
    'es' => 'Espagne',
    'ee' => 'Estonie',
    'us' => 'États-unis',
    'et' => 'Éthiopie',
    'fk' => 'Falkland, îles (malvinas)',
    'fo' => 'Féroé, îles',
    'fj' => 'Fidji',
    'fi' => 'Finlande',
    'fr' => 'France',
    'ga' => 'Gabon',
    'gm' => 'Gambie',
    'ge' => 'Géorgie',
    'gs' => 'Géorgie du sud et les îles sandwich du sud',
    'gh' => 'Ghana',
    'gi' => 'Gibraltar',
    'gr' => 'Grèce',
    'gd' => 'Grenade',
    'gl' => 'Groenland',
    'gp' => 'Guadeloupe',
    'gu' => 'Guam',
    'gt' => 'Guatemala',
    'gg' => 'Guernesey',
    'gn' => 'Guinée',
    'gw' => 'Guinée-bissau',
    'gq' => 'Guinée équatoriale',
    'gy' => 'Guyana',
    'gf' => 'Guyane française',
    'ht' => 'Haïti',
    'hm' => 'Heard, île et mcdonald, îles',
    'hn' => 'Honduras',
    'hk' => 'Hong-kong',
    'hu' => 'Hongrie',
    'im' => 'Île de man',
    'um' => 'Îles mineures éloignées des états-unis',
    'vg' => 'Îles vierges britanniques',
    'vi' => 'Îles vierges des états-unis',
    'in' => 'Inde',
    'id' => 'Indonésie',
    'ir' => 'Iran, république islamique d\'',
    'iq' => 'Iraq',
    'ie' => 'Irlande',
    'is' => 'Islande',
    'il' => 'Israël',
    'it' => 'Italie',
    'jm' => 'Jamaïque',
    'jp' => 'Japon',
    'je' => 'Jersey',
    'jo' => 'Jordanie',
    'kz' => 'Kazakhstan',
    'ke' => 'Kenya',
    'kg' => 'Kirghizistan',
    'ki' => 'Kiribati',
    'kw' => 'Koweït',
    'la' => 'Lao, république démocratique populaire',
    'ls' => 'Lesotho',
    'lv' => 'Lettonie',
    'lb' => 'Liban',
    'lr' => 'Libéria',
    'ly' => 'Libyenne, jamahiriya arabe',
    'li' => 'Liechtenstein',
    'lt' => 'Lituanie',
    'lu' => 'Luxembourg',
    'mo' => 'Macao',
    'mk' => 'Macédoine, l\'ex-république yougoslave de',
    'mg' => 'Madagascar',
    'my' => 'Malaisie',
    'mw' => 'Malawi',
    'mv' => 'Maldives',
    'ml' => 'Mali',
    'mt' => 'Malte',
    'mp' => 'Mariannes du nord, îles',
    'ma' => 'Maroc',
    'mh' => 'Marshall, îles',
    'mq' => 'Martinique',
    'mu' => 'Maurice',
    'mr' => 'Mauritanie',
    'yt' => 'Mayotte',
    'mx' => 'Mexique',
    'fm' => 'Micronésie, états fédérés de',
    'md' => 'Moldova, république de',
    'mc' => 'Monaco',
    'mn' => 'Mongolie',
    'me' => 'Monténégro',
    'ms' => 'Montserrat',
    'mz' => 'Mozambique',
    'mm' => 'Myanmar',
    'na' => 'Namibie',
    'nr' => 'Nauru',
    'np' => 'Népal',
    'ni' => 'Nicaragua',
    'ne' => 'Niger',
    'ng' => 'Nigéria',
    'nu' => 'Niué',
    'nf' => 'Norfolk, île',
    'no' => 'Norvège',
    'nc' => 'Nouvelle-calédonie',
    'nz' => 'Nouvelle-zélande',
    'io' => 'Océan indien, territoire britannique de l\'',
    'om' => 'Oman',
    'ug' => 'Ouganda',
    'uz' => 'Ouzbékistan',
    'pk' => 'Pakistan',
    'pw' => 'Palaos',
    'ps' => 'Palestinien occupé, territoire',
    'pa' => 'Panama',
    'pg' => 'Papouasie-nouvelle-guinée',
    'py' => 'Paraguay',
    'nl' => 'Pays-bas',
    'pe' => 'Pérou',
    'ph' => 'Philippines',
    'pn' => 'Pitcairn',
    'pl' => 'Pologne',
    'pf' => 'Polynésie française',
    'pr' => 'Porto rico',
    'pt' => 'Portugal',
    'qa' => 'Qatar',
    're' => 'Réunion',
    'ro' => 'Roumanie',
    'gb' => 'Royaume-uni',
    'ru' => 'Russie, fédération de',
    'rw' => 'Rwanda',
    'eh' => 'Sahara occidental',
    'bl' => 'Saint-barthélemy',
    'sh' => 'Sainte-hélène, ascension et tristan da cunha',
    'lc' => 'Sainte-lucie',
    'kn' => 'Saint-kitts-et-nevis',
    'sm' => 'Saint-marin',
    'mf' => 'Saint-martin(partie française)',
    'sx' => 'Saint-martin (partie néerlandaise)',
    'pm' => 'Saint-pierre-et-miquelon',
    'va' => 'Saint-siège (état de la cité du vatican)',
    'vc' => 'Saint-vincent-et-les grenadines',
    'sb' => 'Salomon, îles',
    'ws' => 'Samoa',
    'as' => 'Samoa américaines',
    'st' => 'Sao tomé-et-principe',
    'sn' => 'Sénégal',
    'rs' => 'Serbie',
    'sc' => 'Seychelles',
    'sl' => 'Sierra leone',
    'sg' => 'Singapour',
    'sk' => 'Slovaquie',
    'si' => 'Slovénie',
    'so' => 'Somalie',
    'sd' => 'Soudan',
    'lk' => 'Sri lanka',
    'se' => 'Suède',
    'ch' => 'Suisse',
    'sr' => 'Suriname',
    'sj' => 'Svalbard et île jan mayen',
    'sz' => 'Swaziland',
    'sy' => 'Syrienne, république arabe',
    'tj' => 'Tadjikistan',
    'tw' => 'Taïwan, province de chine',
    'tz' => 'Tanzanie, république-unie de',
    'td' => 'Tchad',
    'cz' => 'Tchèque, république',
    'tf' => 'Terres australes françaises',
    'th' => 'Thaïlande',
    'tl' => 'Timor-leste',
    'tg' => 'Togo',
    'tk' => 'Tokelau',
    'to' => 'Tonga',
    'tt' => 'Trinité-et-tobago',
    'tn' => 'Tunisie',
    'tm' => 'Turkménistan',
    'tc' => 'Turks et caïques, îles',
    'tr' => 'Turquie',
    'tv' => 'Tuvalu',
    'ua' => 'Ukraine',
    'uy' => 'Uruguay',
    'vu' => 'Vanuatu',
    've' => 'Venezuela, république bolivarienne du',
    'vn' => 'Viet nam',
    'wf' => 'Wallis et futuna',
    'ye' => 'Yémen',
    'zm' => 'Zambie',
    'zw' => 'Zimbabwe',
    );
