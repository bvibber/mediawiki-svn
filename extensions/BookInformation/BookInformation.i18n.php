<?php

/**
 * Internationalisation file for the Book Information extension
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */

function efBookInformationMessages() {
	$messages = array(

/* English (Rob Church) */
'en' => array(
'bookinfo-header' => 'Book information',
'bookinfo-result-title' => 'Title:',
'bookinfo-result-author' => 'Author:',
'bookinfo-result-publisher' => 'Publisher:',
'bookinfo-result-year' => 'Year:',
'bookinfo-error-invalidisbn' => 'Invalid ISBN entered.',
'bookinfo-error-nosuchitem' => 'Item does not exist or could not be found.',
'bookinfo-error-nodriver' => 'Unable to initialise an appropriate Book Information Driver.',
'bookinfo-error-noresponse' => 'No response or request timed out.',
'bookinfo-purchase' => 'Purchase this book from $1',
'bookinfo-provider' => 'Data provider: $1',
),

/* German (Raymond) */
'de' => array(
'bookinfo-header' => 'Informationen über Bücher',
'bookinfo-result-title' => 'Titel:',
'bookinfo-result-author' => 'Autor:',
'bookinfo-result-publisher' => 'Verlag:',
'bookinfo-result-year' => 'Jahr:',
'bookinfo-error-invalidisbn' => 'ISBN ungültig.',
'bookinfo-error-nosuchitem' => 'Der Artikel ist nicht vorhanden oder wurde nicht gefunden.',
'bookinfo-error-nodriver' => 'Es war nicht möglich, die entsprechende Buchinformations-Schnittstelle zu initialisieren.',
'bookinfo-error-noresponse' => 'Keine Antwort oder Zeitüberschreitung.',
'bookinfo-purchase' => 'Dieses Buch kann von $1 bezogen werden.',
'bookinfo-provider' => 'Daten-Lieferant: $1',
),

/* Finnish (Niklas Laxström) */
'fi' => array(
'bookinfo-header' => 'Kirjan tiedot',
'bookinfo-result-title' => 'Nimi:',
'bookinfo-result-author' => 'Tekijä:',
'bookinfo-result-publisher' => 'Kustantaja:',
'bookinfo-result-year' => 'Vuosi:',
'bookinfo-error-invalidisbn' => 'Kelpaamaton ISBN.',
'bookinfo-error-nosuchitem' => 'Nimikettä ei ole olemassa tai sitä ei löytynyt.',
'bookinfo-error-nodriver' => 'Kirjatietoajurin alustus ei onnistunut.',
'bookinfo-error-noresponse' => 'Ei vastausta tai pyyntö aikakatkaistiin.',
'bookinfo-purchase' => 'Osta tämä kirja: $1',
'bookinfo-provider' => 'Tietolähde: $1',
),

/* French */
'fr' => array(
'bookinfo-header' => 'Informations sur les ouvrages',
'bookinfo-result-title' => 'Titre :',
'bookinfo-result-author' => 'Auteur :',
'bookinfo-result-publisher' => 'Éditeur :',
'bookinfo-result-year' => 'Année :',
'bookinfo-error-invalidisbn' => 'ISBN invalide.',
'bookinfo-error-nosuchitem' => 'Cet élément n’existe pas ou n’a pas pu être trouvé.',
'bookinfo-error-nodriver' => 'Impossible d’initialiser un moteur d’information sur les ouvrages.',
'bookinfo-error-noresponse' => 'Aucune réponse ou dépassement du délai.',
'bookinfo-purchase' => 'Acheter ce livre sur $1',
'bookinfo-provider' => 'Fournisseur des données : $1',
),

/* Indonesian (Ivan Lanin) */
'id' => array(
'bookinfo-header' => 'Informasi buku',
'bookinfo-result-title' => 'Judul:',
'bookinfo-result-author' => 'Pengarang:',
'bookinfo-result-publisher' => 'Penerbit:',
'bookinfo-result-year' => 'Tahun:',
'bookinfo-error-invalidisbn' => 'ISBN yang dimasukkan tidak sah.',
'bookinfo-error-nodriver' => 'Tidak dapat menginisiasi Book Information Driver.',
'bookinfo-error-noresponse' => 'Tak ada respons atau respons terlalu lama.',
'bookinfo-purchase' => 'Beli buku ini dari $1',
'bookinfo-provider' => 'Penyedia data: $1',
),

/* nld / Dutch (Dirk Beetstra) */
'nl' => array(
'bookinfo-header' => 'Boekinformatie',
'bookinfo-result-title' => 'Titel:',
'bookinfo-result-author' => 'Auteur:',
'bookinfo-result-publisher' => 'Uitgever:',
'bookinfo-result-year' => 'Jaar:',
'bookinfo-error-invalidisbn' => 'Onjuist ISBN-nummer ingegeven.',
'bookinfo-error-nodriver' => 'Kon de juiste Boekinformatie Driver niet initialiseren.',
'bookinfo-error-noresponse' => 'Geen antwoord of een time-out.',
'bookinfo-purchase' => 'Koop dit boek bij $1',
'bookinfo-provider' => 'Gegevens geleverd door: $1',
),

	);
	return $messages;
}

?>