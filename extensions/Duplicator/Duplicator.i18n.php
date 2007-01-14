<?php

/**
 * Internationalisation file for the Duplicator extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */

function efDuplicatorMessages() {
	return array(

/* English (Rob Church) */
'en' => array(
'duplicator' => 'Duplicate an article',
'duplicator-toolbox' => 'Duplicate this article',
'duplicator-header' => 'This page allows the complete duplication of an article, creating independent
copies of all histories. This is useful for article forking, etc.',

'duplicator-options' => 'Options',
'duplicator-source' => 'Source:',
'duplicator-dest' => 'Destination:',
'duplicator-dotalk' => 'Duplicate discussion page (if applicable)',
'duplicator-submit' => 'Duplicate',

'duplicator-summary' => 'Copied from [[$1]]',

'duplicator-success' => "<big>'''[[$1]] was copied to [[$2]].'''</big>",
'duplicator-success-revisions' => '$1 revisions were copied.',
'duplicator-success-talkcopied' => 'The discussion page was also copied.',
'duplicator-success-talknotcopied' => 'The talk page could not be copied.',
'duplicator-failed' => 'The page could not be duplicated. An unknown error occurred.',

'duplicator-source-invalid' => 'Please provide a valid source title.',
'duplicator-source-notexist' => '[[$1]] does not exist. Please provide the title of a page that exists.',
'duplicator-dest-invalid' => 'Please provide a valid destination title.',
'duplicator-dest-exists' => '[[$1]] already exists. Please provide a destination title which doesn\'t exist.',
'duplicator-toomanyrevisions' => '[[$1]] has too many ($2) revisions and cannot be copied. The current limit is $3.',
),

/* German (Leon Weber) */
'de' => array(
	'duplicator' => 'Einen Artikel duplizieren',
	'duplicator-header' => 'Mit dieser Spezialseite können Artikel komplett dupliziert werden. Dabei wird die gesamte ' .
				'Versionsgeschichte übernommen. Dies kann beispielsweise nützlich sein, um eine Seite in ' .
				'Unterartikel aufzuteilen.',
	'duplicator-options' => 'Optionen',
	'duplicator-source' => 'Quelle:',
	'duplicator-dest' => 'Ziel:',
	'duplicator-dotalk' => 'Diskussionsseite mitkopieren (wenn möglich)',
	'duplicator-submit' => 'Artikel duplizieren',

	'duplicator-summary' => '[[$1]] wurde dupliziert',

	'duplicator-success' => "<big>'''[[$1]] wurde nach [[$2]] kopiert.'''</big>",
	'duplicator-success-revisions' => '$1 Versionen wurden dupliziert.',
	'duplicator-success-talkcopied' => 'Die Diskussionsseite wurde auch dupliziert.',
	'duplicator-success-talknotcopied' => 'Die Diskussionsseite konnte nicht dupliziert werden.',
	'duplicator-failed' => 'Der Artikel konnte nicht dupliziert werden, da ein unbekannter Fehler auftrat.',

	'duplicator-source-invalid' => 'Bitte geben Sie einen gültigen Quell-Artikel an.',
	'duplicator-source-notexist' => 'Der Artikel [[$1]] existiert nicht. Bitte geben Sie einen existierenden Artikel an.',
	'duplicator-dest-invalid' => 'Bitte geben Sie einen gültigen Ziel-Artikel an.',
	'duplicator-dest-exists' => 'Der Artikel [[$1]] existiert bereits. Bitte geben Sie einen nicht existierenden Artikel an.',
	'duplicator-toomanyrevisions' => 'Der Artikel [[$1]] hat zu viele ($2) Versionen, um dupliziert zu werden, da nur Artikel mit ' .
					'maximal $3 Versionen dupliziert werden können.',
),

/* Finnish (Niklas Laxström) */
'fi' => array(
'duplicator' => 'Monista sivu',
'duplicator-toolbox' => 'Monista tämä sivu',
'duplicator-header' => 'Tällä sivulla voit luoda artikkelista täydellisen kopion historioineen.',

'duplicator-options' => 'Asetukset',
'duplicator-source' => 'Lähdesivu:',
'duplicator-dest' => 'Kohdesivu:',
'duplicator-dotalk' => 'Monista myös keskustelusivu, jos mahdollista',
'duplicator-submit' => 'Monista',

'duplicator-summary' => 'Täydellinen kopio sivusta [[$1]]',

'duplicator-success' => "<big>'''[[$1]] monistettiin sivulle [[$2]].'''</big>",
'duplicator-success-revisions' => '$1 muutosta kopioitiin.',
'duplicator-success-talkcopied' => 'Myös keskustelusivu monistettiin.',
'duplicator-success-talknotcopied' => 'Keskustelusivua ei monistettu.',
'duplicator-failed' => 'Sivun monistaminen ei onnistunut.',

'duplicator-source-invalid' => 'Lähdesivun nimi ei kelpaa.',
'duplicator-source-notexist' => 'Sivua [[$1]] ei ole olemassa.',
'duplicator-dest-invalid' => 'Kohdesivun nimi ei kelpaa.',
'duplicator-dest-exists' => '[[$1]] on jo olemassa. Anna nimi, joka ei ole vielä käytössä.',
'duplicator-toomanyrevisions' => 'Sivu [[$1]] koostuu liian monesta muutoksesta ($2), minkä takia sitä ei voi monistaa. Nykyinen raja on $3.',
),

/* French */
'fr' => array(
'duplicator' => 'Dupliquer un article',
'duplicator-toolbox' => 'Dupliquer cet article',
'duplicator-header' => 'Cette page permet la duplication complète d’un article, en créant deux versions 
indépendantes de l’historique complet. Il sert par exemple à séparer un article en deux.',

'duplicator-options' => 'Options',
'duplicator-source' => 'Source :',
'duplicator-dest' => 'Destination :',
'duplicator-dotalk' => 'Dupliquer la page de discussion (si elle existe)',
'duplicator-submit' => 'Dupliquer',

'duplicator-summary' => 'Copié depuis [[$1]]',

'duplicator-success' => "<big>'''[[$1]] a été copié vers [[$2]].'''</big>\n\n",
'duplicator-success-revisions' => '$1 révisions ont été copiées.',
'duplicator-success-talkcopied' => 'La page de discussion a également été copiée.',
'duplicator-success-talknotcopied' => 'La page de discussion n’a pas pu être copiée.',
'duplicator-failed' => 'La page n’a pas pu être dupliquée. Une erreur inconnue s’est produite.',

'duplicator-source-invalid' => 'Veuillez donner un nom valide pour l’article.',
'duplicator-source-notexist' => '[[$1]] n’existe pas. Veuillez donner le nom d’un article existant.',
'duplicator-dest-invalid' => 'Veuillez donner un nom valide pour la destination.',
'duplicator-dest-exists' => '[[$1]] existe déjà. Veuillez donner le nom d’un article qui n’existe pas encore.',
'duplicator-toomanyrevisions' => '[[$1]] a trop ($2) de révisions et ne peut pas être copié. La limite actuelle est de $3.',
),

/* Indonesia (Ivan Lanin) */
'id' => array(
'duplicator' => 'Duplikasikan suatu artikel',
'duplicator-toolbox' => 'Duplikasikan artikel ini',
'duplicator-header' => 'Halaman ini menyediakan fasilitas untuk membuat duplikat lengkap suatu artikel, membuat salinan independen dari semua versi terdahulu. Hal ini berguna untuk mencabangkan artikel, dll.',

'duplicator-options' => 'Opsi',
'duplicator-source' => 'Sumber:',
'duplicator-dest' => 'Tujuan:',
'duplicator-dotalk' => 'Duplikasikan halaman pembicaraan (jika tersedia)',
'duplicator-submit' => 'Duplikasi',

'duplicator-summary' => 'Disalin dari [[$1]]',

'duplicator-success' => "<big>'''[[$1]] telah disalin ke [[$2]].'''</big>",
'duplicator-success-revisions' => '$1 revisi telah disalin.',
'duplicator-success-talkcopied' => 'Halaman pembicaraan juga telah disalin.',
'duplicator-success-talknotcopied' => 'Halaman pembicaraan tidak dapat disalin.',
'duplicator-failed' => 'Halaman tidak dapat diduplikasi. Telah terjadi suatu kesalahan yang tak dikenal.',

'duplicator-source-invalid' => 'Harap masukkan judul sumber yang sah.',
'duplicator-source-notexist' => '[[$1]] tidak ditemukan. Harap masukkan judul halaman yang sudah ada.',
'duplicator-dest-invalid' => 'Harap masukkan judul tujuan yang sah.',
'duplicator-dest-exists' => '[[$1]] telah ada. Harap berikan judul tujuan yang halamannya belum ada.',
'duplicator-toomanyrevisions' => '[[$1]] memiliki terlalu banyak ($2) revisi dan tidak dapat disalin. Limit saat ini adalah $3.',
),

	);
}

?>