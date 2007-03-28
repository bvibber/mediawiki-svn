<?php

/**
 * Internationalisation file for the Duplicator extension
 *
 * @addtogroup Extensions
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
'duplicator-success-revisions' => '$1 {{PLURAL:$1|revision was|revisions were}} copied.',
'duplicator-success-talkcopied' => 'The discussion page was also copied.',
'duplicator-success-talknotcopied' => 'The talk page could not be copied.',
'duplicator-failed' => 'The page could not be duplicated. An unknown error occurred.',

'duplicator-source-invalid' => 'Please provide a valid source title.',
'duplicator-source-notexist' => '[[$1]] does not exist. Please provide the title of a page that exists.',
'duplicator-dest-invalid' => 'Please provide a valid destination title.',
'duplicator-dest-exists' => '[[$1]] already exists. Please provide a destination title which doesn\'t exist.',
'duplicator-toomanyrevisions' => '[[$1]] has too many ($2) revisions and cannot be copied. The current limit is $3.',
),

'br' => array(
'duplicator'=> 'Eilañ ur pennad',
'duplicator-toolbox'=> 'Eilañ ar pennad-mañ',
'duplicator-header'=> 'Dre ar bajenn-mañ e c\'haller eilañ ur pennad penn-da-benn ha sevel stummoù emren evit pep kemm degaset. Talvoudus eo evit diforc\'hañ pennadoù, da skouer.',

'duplicator-options'=> 'Dibarzhioù',
'duplicator-source'=> 'Mammenn :',
'duplicator-dest'=> 'Lec\'h-kas :',
'duplicator-dotalk'=> 'Eilañ ar bajenn gaozeal (mar galler)',
'duplicator-submit'=> 'Eilañ',

'duplicator-summary'=> 'Eilet eus [[$1]]',

'duplicator-success'=> '<big>\'\'\'Eilet eo bet [[$1]] war [[$2]].\'\'\'</big>',
'duplicator-success-revisions'=> '$1 kemm zo bet eilet.',
'duplicator-success-talkcopied'=> 'Eilet eo bet ar bajenn gaozeal ivez.',
'duplicator-success-talknotcopied'=> 'N\'eus ket bet gallet eilañ ar bajenn gaozeal.',
'duplicator-failed'=> 'N\'eus ket bet gallet eilañ ar bajenn-mañ. C\'hoarvezet ez eus ur fazi digomprenus.',

'duplicator-source-invalid'=> 'Mar plij, lakait anv ur pennad zo anezhañ c\'hoazh.',
'duplicator-source-notexist'=> 'N\'eus ket eus [[$1]]. Lakait titl ur pennad zo anezhañ mar plij',
'duplicator-dest-invalid'=> 'Merkit un titl reizh evel lec\'h-kas, mar plij',
'duplicator-dest-exists'=> 'Bez\' ez eus eus [[$1]] c\'hoazh. Merkit titl ul lec\'h-kas n\'eo ket bet krouet c\'hoazh.',
'duplicator-toomanyrevisions'=> 'Re a ($2) gemmoù zo gant [[$1]]. N\'haller ket o eilañ. $3 eo ar vevenn e talvoud.',
),

'ca' => array(
'duplicator'=> 'Duplica un article',
'duplicator-toolbox'=> 'Duplica aquest article',
'duplicator-header'=> 'Aquesta pàgina permet la duplicació completa d\'un article, creant còpies independents de totes les històries. Això és útil per a l\'edició de nous articles a partir d\'altres, etc.',
'duplicator-options'=> 'Opcions',
'duplicator-source'=> 'Origen:',
'duplicator-dest'=> 'Destinació',
'duplicator-dotalk'=> 'Duplica la pàgina de discussió (quan així es pugui)',
'duplicator-submit'=> 'Duplica',
'duplicator-summary'=> 'Copiat des de [[$1]]',
'duplicator-success'=> '<big>\'\'\'[[$1]] s\'ha copiat a [[$2]].\'\'\'</big>',
'duplicator-success-revisions'=> '{{PLURAL:$1|S\'ha copiat una revisió|S\'han copiat $1 revisions}}.',
'duplicator-success-talkcopied'=> 'La pàgina de discussió també s\'ha copiat.',
'duplicator-success-talknotcopied'=> 'La pàgina de discussió no s\'ha pogut copiar.',
'duplicator-failed'=> 'La pàgina no s\'ha pogut duplicar. S\'ha produït un error desconegut.',
'duplicator-source-invalid'=> 'Si us plau, proporcioneu un títol de pàgina original vàlid.',
'duplicator-source-notexist'=> '[[$1]] no existeix. Proporcioneu un títol d\'una pàgina que existeixi.',
'duplicator-dest-invalid'=> 'Si us plau, proporcioneu un títol de destinació vàlid.',
'duplicator-dest-exists'=> '[[$1]] ja existeix. Proporcioneu un títol de destinació que no existeixi.',
'duplicator-toomanyrevisions'=> 'La pàgina [[$1]] té $2 revisions i no pot ser copiada. EL límit màxim d\'edicions que es poden copiar és de $3.',
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
	'duplicator-success-revisions' => '{{PLURAL:$1|Eine Version wurde|$1 Versionen wurden}} dupliziert.',
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
'duplicator-success-revisions' => '{{PLURAL:$1|yksi muutos|$1 muutosta}} kopioitiin.',
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

/* Italian (BrokenArrow) */
'it' => array(
'duplicator' => 'Duplica una pagina',
'duplicator-toolbox' => 'Duplica questa pagina',
'duplicator-header' => "Questa pagina speciale consente la duplicazione completa di una pagina,
dando origine a due copie distinte della relativa cronologia. Tale operazione può essere
utile per scindere due pagine (''forking''), ecc.",

'duplicator-options' => 'Opzioni',
'duplicator-source' => 'Pagina di partenza:',
'duplicator-dest' => 'Pagina di arrivo:',
'duplicator-dotalk' => 'Duplica anche la pagina di discussione, se esiste',
'duplicator-submit' => 'Duplica',

'duplicator-summary' => 'Pagina copiata da [[$1]]',

'duplicator-success' => "<big>La pagina '''[[$1]] è stata copiata in [[$2]].'''</big>",
'duplicator-success-revisions' => '$1 {{PLURAL:$1|revisione copiata|revisioni copiate}}.',
'duplicator-success-talkcopied' => 'Anche la pagina di discussione è stata copiata.',
'duplicator-success-talknotcopied' => 'Impossibile copiare la pagina di discussione.',
'duplicator-failed' => 'Impossibile duplicare la pagina. Errore sconosciuto.',

'duplicator-source-invalid' => 'Indicare un titolo di partenza valido.',
'duplicator-source-notexist' => 'La pagina [[$1]] non esiste. Indicare il titolo di una pagina esistente.',
'duplicator-dest-invalid' => 'Indicare un titolo di arrivo valido.',
'duplicator-dest-exists' => 'La pagina [[$1]] esiste già. Indicare un titolo di arrivo non ancora esistente.',
'duplicator-toomanyrevisions' => 'Impossibile copiare [[$1]]. La pagina ha troppe revisioni ($2). Il limite attuale è $3.',
),

/* nld / Dutch (Siebrand Mazeland) */
'nl' => array(
'duplicator' => 'Kopieer een pagina',
'duplicator-toolbox' => 'Kopieer deze pagina',
'duplicator-header' => 'Deze pagina maakt het mogelijk een pagina volledig te kopiëren, waardoor er onafhankelijke
kopiën ontstaan met een volledige geschiedenis. DIt is handig voor forks, enzovoort.',

'duplicator-options' => 'Opties',
'duplicator-source' => 'Bron:',
'duplicator-dest' => 'Doel:',
'duplicator-dotalk' => 'Kopieer overlegpagina (als van toepassing)',
'duplicator-submit' => 'Kopiëren',

'duplicator-summary' => 'Gekopieerd van [[$1]]',

'duplicator-success' => "<big>'''[[$1]] is gekopieerd naar [[$2]].'''</big>",
'duplicator-success-revisions' => '$1 versies gekopieerd.',
'duplicator-success-talkcopied' => 'De overlegpagina is ook gekopieerd.',
'duplicator-success-talknotcopied' => 'De overlegpagina kon niet gekopieerd worden.',
'duplicator-failed' => 'De pagina kon niet gekopieerd worden. Er is een onbekende fout opgetreden.',

'duplicator-source-invalid' => 'Geef alstublieft een geldige bronpagina op.',
'duplicator-source-notexist' => '[[$1]] bestaat niet. Geef alstublieft een pagina op die bestaat.',
'duplicator-dest-invalid' => 'Geef alstublieft een geldige doelpagina op.',
'duplicator-dest-exists' => '[[$1]] bestaat al. Geeft alstublieft een doelpagina op die niet bestaat.',
'duplicator-toomanyrevisions' => '[[$1]] heeft te veel versies ($2) en kan niet gekopieerd worden. De huidige limiet is $3.',
),

'oc' => array(
'duplicator' => 'Duplicar un article',
'duplicator-toolbox' => 'Duplicar aqueste article',
'duplicator-header' => 'Aquesta pagina permet la duplicacion complèta d’un article, en creant doas versions independentas de l’istoric complet. Servís per exemple a separar un article en dos.',
'duplicator-options' => 'Opcions',
'duplicator-source' => 'Font:',
'duplicator-dest' => 'Destinacion:',
'duplicator-dotalk' => 'Duplicar la pagina de discussion (se existís)',
'duplicator-submit' => 'Duplicar',
'duplicator-summary' => 'Copiat dempuèi [[$1]]',
'duplicator-success' => '<big>\'\'\'[[$1]] es estat copiat vèrs [[$2]].\'\'\'</big>',
'duplicator-success-revisions' => '$1 {{PLURAL:$1|revision es estada copiada|revisions son estadas copiadas}}.',
'duplicator-success-talkcopied' => 'La pagina de discussion es estada copiada tanben.',
'duplicator-success-talknotcopied' => 'La pagina de discussion es pas pogut èsser copiada.',
'duplicator-failed' => 'La pagina es pas pogut èsser duplicada. Una error desconeguda s’es producha.',
'duplicator-source-invalid' => 'Donatz un nom valid per l’article.',
'duplicator-source-notexist' => '[[$1]] existís pas. Donatz lo nom d’un article existent.',
'duplicator-dest-invalid' => 'Donatz un nom valid per la destinacion.',
'duplicator-dest-exists' => '[[$1]] existís ja. Donatz lo nom d’un article qu’existís pas encara.',
'duplicator-toomanyrevisions' => '[[$1]] a tròp ($2) de revisions e pòt pas èsser copiat. La limita actuala es de $3.',
),

/* Russian */
'ru' => array(
'duplicator' => 'Клонировать статью',
'duplicator-toolbox' => 'Клонировать статью',
'duplicator-header' => 'Эта страница позволяет полностью клонировать статью, создать независимую копию истории её изменений. Данная функция полезна при разделении статьи на две отдельные.',

'duplicator-options' => 'Настройки',
'duplicator-source' => 'Откуда:',
'duplicator-dest' => 'Куда:',
'duplicator-dotalk' => 'Клонировать страницу обсуждения (если возможно)',
'duplicator-submit' => 'Клонировать',

'duplicator-summary' => 'Копия [[$1]]',

'duplicator-success' => "<big>'''[[$1]] клонирована в [[$2]].'''</big>",
'duplicator-success-revisions' => '$1 {{PLURAL:$1|изменение было|изменения было|изменений было}} скопировано.',
'duplicator-success-talkcopied' => 'Страница обсуждения была скопирована.',
'duplicator-success-talknotcopied' => 'Страница обсуждения не была скопирована.',
'duplicator-failed' => 'Страница не может быть клопирована. Неизвестная ошибка.',

'duplicator-source-invalid' => 'Пожалуйста, введите корректное название статьи-источника.',
'duplicator-source-notexist' => 'Страница «[[$1]]» не существует. Пожалуйста, введите название страницы, которая существует.',
'duplicator-dest-invalid' => 'Пожалуйста введите корректное название страницы-назначения.',
'duplicator-dest-exists' => 'Страница «[[$1]]» уже существует. Пожалуйста, введите название несуществующей страницы-назначения.',
'duplicator-toomanyrevisions' => 'Страница «[[$1]]» имеет слишком много ($2) изменений. Текущим ограничением является $3.',
),

/* Slovak (helix84) */
'sk' => array(
'duplicator' => 'Duplikácia článku',
'duplicator-toolbox' => 'Duplikovať tento článok',
'duplicator-header' => 'Táto stránka umožňuje kompletnú duplikáciu článku, čím sa vytvorí nazávislá kópia všetkých histórií. Je to užitočné napríklad pri vetvení a pod.',
'duplicator-options' => 'Možnosti',
'duplicator-source' => 'Zdroj:',
'duplicator-dest' => 'Cieľ:',
'duplicator-dotalk' => 'Duplikovať aj diskusnú stránku (ak existuje)',
'duplicator-submit' => 'Duplikovať',
'duplicator-summary' => 'Skopírované z [[$1]]',
'duplicator-success' => '<big>Vytvorená kópia \'\'\'[[$1]] na [[$2]].\'\'\'</big>',
'duplicator-success-revisions' => 'Skopírovaných $1 revízií.',
'duplicator-success-talkcopied' => 'Diskusné stránky boli tiež skopírované.',
'duplicator-success-talknotcopied' => 'Nebolo možné skopírovať diskusné stránky.',
'duplicator-failed' => 'Túto stránku nebolo možné duplikovať. Vyskytla sa neznáma chyba.',
'duplicator-source-invalid' => 'Poskytnite platný názov zdrojovej stránky.',
'duplicator-source-notexist' => '[[$1]] neexistuje. Poskytnite názov zdrojovej stránky, ktorá už existuje.',
'duplicator-dest-invalid' => 'Prosím, zadajte platný názov cieľovej stránky.',
'duplicator-dest-exists' => '[[$1]] už existuje. Prosím zadajte cieľ, ktorý ešte neexistuje.',
'duplicator-toomanyrevisions' => '[[$1]] má príliš veľa ($2) revízií a preto ho nie je možné skopírovať. Aktuálny limit je $3.',
),

	);
}

?>
