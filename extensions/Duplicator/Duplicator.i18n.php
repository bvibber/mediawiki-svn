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

'duplicator-success' => "<big>'''[[$1]] was copied to [[$2]].'''</big>\n\n",
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

	'duplicator-success' => "<big>'''[[$1]] wurde nach [[$2]] kopiert.'''</big>\n\n",
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

'duplicator-success' => "<big>'''[[$1]] monistettiin sivulle [[$2]].'''</big>\n\n",
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
	
	);
}

?>