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

/* Arabic (Meno25) */
'ar' => array(
	'bookinfo-header' => 'معلومات كتاب',
	'bookinfo-result-title' => 'العنوان:',
	'bookinfo-result-author' => 'المؤلف:',
	'bookinfo-result-publisher' => 'الناشر:',
	'bookinfo-result-year' => 'السنة:',
	'bookinfo-error-invalidisbn' => 'ردمك غير صحيح تم إدخاله.',
	'bookinfo-error-nosuchitem' => 'المدخل غير موجود أو لم يمكن العثور عليه.',
	'bookinfo-error-nodriver' => 'غير قادر على بدأ درايفر معلومات كتاب مناسب.',
	'bookinfo-error-noresponse' => 'لا رد أو الطلب انتهت فترته.',
	'bookinfo-purchase' => 'اشتر هذا الكتاب من $1',
	'bookinfo-provider' => 'مزود البيانات: $1',
),

'bcl' => array(
	'bookinfo-result-title' => 'Titulo:',
	'bookinfo-result-year' => 'Taon:',
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

'ext' => array(
	'bookinfo-header' => 'Enhormación el libru',
	'bookinfo-result-title' => 'Entítulu:',
	'bookinfo-result-author' => 'Autol:',
	'bookinfo-result-publisher' => 'Eitorial:',
	'bookinfo-result-year' => 'Añu:',
	'bookinfo-error-nosuchitem' => 'El artículu nu desisti u nu puei sel alcuentrau.',
	'bookinfo-purchase' => 'Mercal esti libru e $1',
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

'frp' => array(
	'bookinfo-header' => 'Enformacions sur les ôvres',
	'bookinfo-result-title' => 'Titro :',
	'bookinfo-result-author' => 'Ôtor :',
	'bookinfo-result-publisher' => 'Èditor :',
	'bookinfo-result-year' => 'An :',
	'bookinfo-error-invalidisbn' => 'ISBN envalido.',
	'bookinfo-error-nosuchitem' => 'Cél èlèment ègziste pas ou at pas possu étre trovâ.',
	'bookinfo-error-nodriver' => 'Empossiblo d’inicialisar un motor d’enformacion sur les ôvres.',
	'bookinfo-error-noresponse' => 'Niona rèponsa ou ben nion dèpassement du dèlê.',
	'bookinfo-purchase' => 'Achetar cél lévro dessus $1',
	'bookinfo-provider' => 'Fornissor de les balyês : $1',
),

'gl' => array(
	'bookinfo-header' => 'Información do libro',
	'bookinfo-result-title' => 'Título:',
	'bookinfo-result-author' => 'Autor:',
	'bookinfo-result-publisher' => 'Publicación:',
	'bookinfo-result-year' => 'Ano:',
	'bookinfo-error-invalidisbn' => 'O ISBN introducido non é válido.',
	'bookinfo-error-nosuchitem' => 'O artigo non existe ou non foi atopado.',
	'bookinfo-error-nodriver' => 'Non é posíbel comezar unha apropiada búsqueda de información sobre libros.',
	'bookinfo-error-noresponse' => 'Non se recibiu resposta ou a solicitude caducou.',
	'bookinfo-purchase' => 'Compre este libro de $1',
	'bookinfo-provider' => 'Fornecedor de datos: $1',
),

'hsb' => array(
	'bookinfo-header' => 'Informacije wo knihach',
	'bookinfo-result-title' => 'Titul:',
	'bookinfo-result-author' => 'Awtor:',
	'bookinfo-result-publisher' => 'Nakładnistwo:',
	'bookinfo-result-year' => 'Lěto:',
	'bookinfo-error-invalidisbn' => 'ISBN njepłaćiwe.',
	'bookinfo-error-nosuchitem' => 'Artikl njeeksistuje abo njeda so namakać.',
	'bookinfo-error-nodriver' => 'Njeběše móžno wotpowědny ćěrjak za informacije wo knihach inicializować.',
	'bookinfo-error-noresponse' => 'Žana wotmołwa abo překročenje časa.',
	'bookinfo-purchase' => 'Tuta kniha hodźi so wot $1 kupić.',
	'bookinfo-provider' => 'Dodawar datow: $1',
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

'la' => array(
	'bookinfo-result-title' => 'Titulus:',
	'bookinfo-result-author' => 'Auctor:',
	'bookinfo-result-year' => 'Annus:',
),

/* nld / Dutch (Dirk Beetstra) */
'nl' => array(
	'bookinfo-header' => 'Boekinformatie',
	'bookinfo-result-title' => 'Titel:',
	'bookinfo-result-author' => 'Auteur:',
	'bookinfo-result-publisher' => 'Uitgever:',
	'bookinfo-result-year' => 'Jaar:',
	'bookinfo-error-invalidisbn' => 'Onjuist ISBN ingegeven.',
	'bookinfo-error-nosuchitem' => 'Bestaat niet of kon niet gevonden worden.',
	'bookinfo-error-nodriver' => 'Kon de juiste Boekinformatie Driver niet initialiseren.',
	'bookinfo-error-noresponse' => 'Geen antwoord of een time-out.',
	'bookinfo-purchase' => 'Koop dit boek bij $1',
	'bookinfo-provider' => 'Gegevens geleverd door: $1',
),


/* Norwegian (Jon Harald Søby) */
'no' => array(
	'bookinfo-header' => 'Bokinformasjon',
	'bookinfo-result-title' => 'Tittel:',
	'bookinfo-result-author' => 'Forfatter:',
	'bookinfo-result-publisher' => 'Utgiver:',
	'bookinfo-result-year' => 'År:',
	'bookinfo-error-invalidisbn' => 'Ugyldig ISBN oppgitt.',
	'bookinfo-error-nosuchitem' => 'Boken eksisterer ikke, eller kunne ikke finnes.',
	'bookinfo-error-nodriver' => 'Kunne ikke sette i gang en passende bokinformasjonsdriver.',
	'bookinfo-error-noresponse' => 'Ingen respons eller tidsavbrudd.',
	'bookinfo-purchase' => 'Kjøp denne boken fra $1',
	'bookinfo-provider' => 'Dataleverandør: $1',
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
	'bookinfo-purchase' => 'Comprar aqueste libre sus $1',
	'bookinfo-provider' => 'Fornidor de donadas : $1',
),

'pl' => array(
	'bookinfo-header' => 'Informacja o książce',
	'bookinfo-result-title' => 'Tytuł:',
	'bookinfo-result-author' => 'Autor:',
	'bookinfo-result-publisher' => 'Wydawca:',
	'bookinfo-result-year' => 'Rok:',
	'bookinfo-error-invalidisbn' => 'Wprowadzono niepoprawny ISBN',
	'bookinfo-error-nosuchitem' => 'Pozycja nie istnieje lub nie może być znaleziona',
	'bookinfo-error-nodriver' => 'Nie udało się zainicjalizować odpowiedniego Book Information Driver',
	'bookinfo-error-noresponse' => 'Brak odpowiedzi lub przekroczony czas odpowiedzi',
	'bookinfo-purchase' => 'Zakup tę książkę w $1',
	'bookinfo-provider' => 'Dostawca danych: $1',
),

/* Piedmontese (Bèrto 'd Sèra) */
'pms' => array(
	'bookinfo-header' => 'Anformassion ant sëj lìber',
	'bookinfo-result-title' => 'Tìtol:',
	'bookinfo-result-author' => 'Aotor:',
	'bookinfo-result-publisher' => 'Editor:',
	'bookinfo-result-year' => 'Ann:',
	'bookinfo-error-invalidisbn' => 'ISBN pa giusta',
	'bookinfo-error-nosuchitem' => 'Vos che ò ch\'a-i é nen ò ch\'a l\'é pa trovasse.',
	'bookinfo-error-nodriver' => 'As riess nen a fé parte ël pilòta dj\'Anformassion ant sëj Lìber',
	'bookinfo-error-noresponse' => 'Pa d\'arspòsta, ò miraco a la riva mach tròp tard',
	'bookinfo-purchase' => 'Caté ël lìber da: $1',
	'bookinfo-provider' => 'Sorgiss dij dat: $1',
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

'sah' => array(
	'bookinfo-header' => 'Кинигэ туһунан',
	'bookinfo-result-title' => 'Аата:',
	'bookinfo-result-author' => 'Суруйааччы:',
	'bookinfo-result-publisher' => 'Кинигэ кыһата:',
	'bookinfo-result-year' => 'Сыла:',
	'bookinfo-error-invalidisbn' => 'Сыыһа ISBN турбут.',
	'bookinfo-error-nosuchitem' => 'Бу туһунан суох эбэтэр кыайан булуллубата.',
	'bookinfo-error-nodriver' => 'Кинигэ туһунан драйвер сатаан инициализацияламмат.',
	'bookinfo-error-noresponse' => 'Хоруй суох эбэтэр болдьоҕо ааста.',
	'bookinfo-purchase' => 'Бу кинигэни мантан атыылаһарга: $1',
	'bookinfo-provider' => 'Информацияны ким биэрбитэ: $1',
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

'sv' => array(
	'bookinfo-header' => 'Bokinformation',
	'bookinfo-result-title' => 'Titel:',
	'bookinfo-result-author' => 'Författare:',
	'bookinfo-result-publisher' => 'Utgivare:',
	'bookinfo-result-year' => 'År:',
	'bookinfo-error-invalidisbn' => 'Ogiltig ISBN angiven.',
	'bookinfo-error-nosuchitem' => 'Posten existerar inte eller kunde inte hittas.',
	'bookinfo-error-nodriver' => 'Kunde ej starta upp en lämplig drivrutin för bokinformation.',
	'bookinfo-error-noresponse' => 'Fick inget svar eller det tog för lång tid att få svar.',
	'bookinfo-purchase' => 'Köp denna bok från $1',
	'bookinfo-provider' => 'Data hämtat från: $1',
),

/* Cantonese (Shinjiman) */
'yue' => array(
	'bookinfo-header' => '書籍資料',
	'bookinfo-result-title' => '標題:',
	'bookinfo-result-author' => '作者:',
	'bookinfo-result-publisher' => '出版者:',
	'bookinfo-result-year' => '年份:',
	'bookinfo-error-invalidisbn' => '唔正確嘅 ISBN 輸入。',
	'bookinfo-error-nosuchitem' => '項目唔正確或者搵唔到。',
	'bookinfo-error-nodriver' => '唔能夠初始化一個合適嘅書籍資料驅動器。',
	'bookinfo-error-noresponse' => '無回應或要求過時。',
	'bookinfo-purchase' => '響$1買呢本書',
	'bookinfo-provider' => '資料提供者: $1',
),

/* Chinese (Simplified) (Shinjiman) */
'zh-hans' => array(
	'bookinfo-header' => '书籍资料',
	'bookinfo-result-title' => '标题:',
	'bookinfo-result-author' => '作者:',
	'bookinfo-result-publisher' => '出版者:',
	'bookinfo-result-year' => '年份:',
	'bookinfo-error-invalidisbn' => '不正确的 ISBN 输入。',
	'bookinfo-error-nosuchitem' => '项目不正确或找不到。',
	'bookinfo-error-nodriver' => '无法初始化一个合适的书籍资料驱动器。',
	'bookinfo-error-noresponse' => '无反应或要求过时。',
	'bookinfo-purchase' => '在$1买这本书',
	'bookinfo-provider' => '资料提供者: $1',
),

/* Chinese (Traditional) (Shinjiman) */
'zh-hant' => array(
	'bookinfo-header' => '書籍資料',
	'bookinfo-result-title' => '標題:',
	'bookinfo-result-author' => '作者:',
	'bookinfo-result-publisher' => '出版者:',
	'bookinfo-result-year' => '年份:',
	'bookinfo-error-invalidisbn' => '不正確的 ISBN 輸入。',
	'bookinfo-error-nosuchitem' => '項目不正確或找不到。',
	'bookinfo-error-nodriver' => '無法初始化一個合適的書籍資料驅動器。',
	'bookinfo-error-noresponse' => '無回應或要求過時。',
	'bookinfo-purchase' => '在$1買這本書',
	'bookinfo-provider' => '資料提供者: $1',
),

	);

	/* Chinese defaults, fallback to zh-hans or zh-hant */
	$messages['zh'] = $messages['zh-hans'];
	$messages['zh-cn'] = $messages['zh-hans'];
	$messages['zh-hk'] = $messages['zh-hant'];
	$messages['zh-sg'] = $messages['zh-hans'];
	$messages['zh-tw'] = $messages['zh-hant'];
	/* Cantonese default, fallback to yue */
	$messages['zh-yue'] = $messages['yue'];

	return $messages;
}
