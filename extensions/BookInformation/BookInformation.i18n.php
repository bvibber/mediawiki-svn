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

'br' => array(
'bookinfo-header' => 'Titouroù war al levr',
'bookinfo-result-title' => 'Titl :',
'bookinfo-result-author' => 'Aozer :',
'bookinfo-result-publisher' => 'Embanner :',
'bookinfo-result-year' => 'Bloaz :',
'bookinfo-error-invalidisbn' => 'ISBN lakaet direzh.',
'bookinfo-error-nosuchitem' => 'Ar pezh zo bet goulennet n\'eus ket anezhañ pe n\'eo ket bet kavet.',
'bookinfo-error-nodriver' => 'N\'hall ket deraouiñ ur sturier titouriñ a-feson war al levrioù.',
'bookinfo-error-noresponse' => 'Respont ebet pe amzer glask re hir.',
'bookinfo-purchase' => 'Prenañ al levr-mañ adal $1',
'bookinfo-provider' => 'Pourvezer roadennoù : $1',
),

'ca' => array(
'bookinfo-header'=> 'Informació del llibre',
'bookinfo-result-title'=> 'Títol:',
'bookinfo-result-author'=> 'Autor:',
'bookinfo-result-publisher'=> 'Editor:',
'bookinfo-result-year'=> 'Any:',
'bookinfo-error-invalidisbn'=> 'L\'ISBN introduït no és vàlid.',
'bookinfo-error-nosuchitem'=> 'L\'element no existeix o no s\'ha pogut trobar.',
'bookinfo-error-nodriver'=> 'No s\'ha pogut inicialitzar un connector d\'informació de llibres apropiat.',
'bookinfo-error-noresponse'=> 'No hi ha cap resposta o el temps de sol·licitud s\'ha esgotat.',
'bookinfo-purchase'=> 'Compra aquest llibre de $1',
'bookinfo-provider'=> 'Proveïdor de dades: $1',
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
'bookinfo-error-nosuchitem' => 'Item yang dimasukkan tidak ada atau tidak ditemukan.',
'bookinfo-error-nodriver' => 'Tidak dapat menginisiasi Book Information Driver.',
'bookinfo-error-noresponse' => 'Tak ada respons atau respons terlalu lama.',
'bookinfo-purchase' => 'Beli buku ini dari $1',
'bookinfo-provider' => 'Penyedia data: $1',
),

/* Italian (BrokenArrow) */
'it' => array(
'bookinfo-header' => 'Informazioni sui libri',
'bookinfo-result-title' => 'Titolo:',
'bookinfo-result-author' => 'Autore:',
'bookinfo-result-publisher' => 'Editore:',
'bookinfo-result-year' => 'Anno:',
'bookinfo-error-invalidisbn' => 'Codice ISBN errato.',
'bookinfo-error-nosuchitem' => 'Elemento inesistente o non trovato.',
'bookinfo-error-nodriver' => 'Impossibile inizializzare un driver corretto per le Informazioni sui libri.',
'bookinfo-error-noresponse' => 'Mancata risposta o risposta assente.',
'bookinfo-purchase' => 'Acquista il libro presso: $1',
'bookinfo-provider' => 'Dati estratti da: $1',
),

'ja' => array(
'bookinfo-header' => '書籍情報',
'bookinfo-result-title' => 'タイトル:',
'bookinfo-result-author' => '著者:',
'bookinfo-result-publisher' => '出版:',
'bookinfo-result-year' => '出版年:',
'bookinfo-error-invalidisbn' => '不正な ISBN です。',
'bookinfo-error-nosuchitem' => '指定したものが見つかりません。',
'bookinfo-error-nodriver' => '適切な Book Information ドライバが認識できません。',
'bookinfo-error-noresponse' => 'リクエストを送信しましたが、応答がないかタイムアウトしました。',
'bookinfo-purchase' => 'この本を $1 から購入する',
'bookinfo-provider' => 'データ提供元: $1',
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

'oc' => array(
'bookinfo-header' => 'Informacions suls obratges',
'bookinfo-result-title' => 'Títol:',
'bookinfo-result-author' => 'Autor:',
'bookinfo-result-publisher' => 'Editor:',
'bookinfo-result-year' => 'Annada:',
'bookinfo-error-invalidisbn' => 'ISBN invalid.',
'bookinfo-error-nosuchitem' => 'Aqueste element existís pas o es pas pogut èsser trobat.',
'bookinfo-error-nodriver' => 'Impossible d’inicializar un motor d’informacion suls obratges.',
'bookinfo-error-noresponse' => 'Cap de responsa o depassament de la sosta.',
'bookinfo-purchase' => 'Comprar aquest libre sus $1',
'bookinfo-provider' => 'Fornidor de donadas : $1',
),

/* Russian (Alexander Sigaachov) */
'ru' => array(
'bookinfo-header' => 'Информация о книге',
'bookinfo-result-title' => 'Название:',
'bookinfo-result-author' => 'Автор:',
'bookinfo-result-publisher' => 'Издательство:',
'bookinfo-result-year' => 'Год:',
'bookinfo-error-invalidisbn' => 'Ошибочная ISBN-запись.',
'bookinfo-error-nosuchitem' => 'Данные отсутствуют или не могут быть найдены.',
'bookinfo-error-nodriver' => 'Ошибка инициализации соответствующего драйвера информации о книгах.',
'bookinfo-error-noresponse' => 'Нет ответа или превышение времени ожидания ответа.',
'bookinfo-purchase' => 'Купить эту книгу на $1',
'bookinfo-provider' => 'Поставщик информации: $1',
),
'sk' => array(
'bookinfo-header' => 'Informácie o knihách',
'bookinfo-result-title' => 'Názov:',
'bookinfo-result-author' => 'Autor:',
'bookinfo-result-publisher' => 'Vydavateľ:',
'bookinfo-result-year' => 'Rok:',
'bookinfo-error-invalidisbn' => 'Zadané neplatné ISBN.',
'bookinfo-error-nosuchitem' => 'Položka neexistuje alebo nebola nenájdená.',
'bookinfo-error-nodriver' => 'Nebolo možné inicializovať vhodný ovládač pre informácie o knihách.',
'bookinfo-error-noresponse' => 'Bez odpovede alebo čas vyhradený na odpoveď vypršal.',
'bookinfo-purchase' => 'Kúpiť túto knihu z $1',
'bookinfo-provider' => 'Poskytovateľ údajov: $1',
),

	);
	return $messages;
}

?>
