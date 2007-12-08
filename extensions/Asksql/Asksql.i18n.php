<?php

/**
 * Internationalisation file for Asksql extension
 *
 * @addtogroup Extensions
 * @author Bertrand Grondin <bertrand.grondin@tiscali.fr>
 */

function efAsksqlMessages() {
	$messages = array(

/* English (Rob Church) */
'en' => array(
	'asksql' => 'SQL query',
	'asksqltext' => "Use the form below to make a direct query of the
database.
Use single quotes ('like this') to delimit string literals.
This can often add considerable load to the server, so please use
this function sparingly.",
	'sqlislogged' => 'Please note that all queries are logged.',
	'sqlquery' => 'Enter query',
	'querybtn' => 'Submit query',
	'selectonly' => 'Only read-only queries are allowed.',
	'querysuccessful' => 'Query successful',
),

'ar' => array(
	'asksql'           => 'استعلام SQL',
	'asksqltext'       => 'استخدم الاستمارة بالأسفل لعمل استعلام مباشر لقاعدة البيانات.
استخدم علامات مفردة (\'مثل هذه\') لتحديد حدود الخيوط.
هذا عادة يضيف عبئا كبيرا للخادم، لذا من فضلك استخدم هذه الخاصية بقلة.',
	'sqlislogged'      => 'من فضلك لاحظ أن كل الاستعلامات مسجلة.',
	'sqlquery'         => 'أدخل الاستعلام',
	'querybtn'         => 'تنفيذ الاستعلام',
	'selectonly'       => 'فقط استعلامات القراءة فقط مسموحة.',
	'querysuccessful'  => 'الاستعلام ناجح',
),

'bcl' => array(
	'asksql'           => 'Hapot na SQL',
	'sqlislogged'      => 'Giromdomon na an gabos na hapot nakalaog.',
	'sqlquery'         => 'Ilaog an hapot',
	'querybtn'         => 'Isumitir an hapot',
	'selectonly'       => 'Solamenteng an mga hapot na read-only sana an tinotogotan.',
	'querysuccessful'  => 'Matriumpo an paghapot',
),

'bg' => array(
	'asksql'           => 'SQL заявка',
	'asksqltext'       => 'Използвайте формата по-долу, за да отправите директна заявка към базата данни.
Използвайте единични кавички (\'като тези\'), за да ограничите низовите литерали.
С действието си може значително да натоварите сървъра, затова е желателно да използвате тази функционалност пестеливо.',
	'sqlislogged'      => 'Имайте предвид, че за всички заявки се пази информация.',
	'sqlquery'         => 'Въвеждане на заявка',
	'querybtn'         => 'Изпращане на заявка',
	'selectonly'       => 'Позволени са единствено заявки само за четене.',
	'querysuccessful'  => 'Заявката е успешна',
),

'br' => array(
	'asksql'           => 'Reked SQL',
	'asksqltext'       => 'Ober gant ar furmskrid a-is evit sevel ur reked war-eeun ouzh ar bank titouroù.
Ober gant unskraboù(\'evel-hen\') evit termeniñ an neudennad.
An dra-se a c\'hall kargañ ar servijer spontus, setu n\'emañ ket da vezañ implijet re alies.',
	'sqlislogged'      => 'Notennit mat eo marilhet an holl rekedoù.',
	'sqlquery'         => 'Sevel ur reked',
	'querybtn'         => 'Kas ar reked',
	'selectonly'       => 'N\'eo aotreet nemet ar rekedoù lenn-hepken',
	'querysuccessful'  => 'Reked disoc\'het',
),

/* Greece */
'el' => array(
	'asksql'           => 'ερώτημα SQL',
	'asksqltext'       => 'Χρησιμοποιήστε τη φόρμα παρακάτω για να κάνετε ένα ευθύ ερώτημα στη βάση δεδομένων.
Χρησιμοποιήστε απλά εισαγωγικά (\'όπως αυτά\') για να οριοθετήσετε string literals.
Αυτό μπορεί συχνά να προσθέσει σημαντικό φορτίο στον εξυπηρετητή, οπότε παρακαλώ χρησιμοποιήστε φειδωλά αυτή τη λειτουργία.',
	'sqlislogged'      => 'Παρακαλώ σημειώστε ότι όλα τα ερωτήματα καταγράφονται.',
	'sqlquery'         => 'Εισαγωγή ερωτήματος',
	'querybtn'         => 'Αποστολή ερωτήματος',
	'selectonly'       => 'Μόνο ερωτήματα τύπου «μόνο για ανάγνωση» επιτρέπονται.',
	'querysuccessful'  => 'Ερώτημα επιτυχές',
),

'fi' => array(
	'asksql'           => 'SQL-kysely',
	'asksqltext'       => 'Käytä alla olevaa lomaketta tehdäksesi suoria kyselyitä tietokannasta. Merkkijonovakioita merkitään yksinkertaisilla lainausmerkeillä (\'näin\'). Kyselyt voivat usein kuormittaa palvelinta huomattavasti, joten käytä tätä toimintoa säästeliäästi.',
	'sqlislogged'      => 'Huomioithan, että kaikki kyselyt kirjataan.',
	'sqlquery'         => 'Kirjoita kysely',
	'querybtn'         => 'Lähetä kysely',
	'selectonly'       => 'Ainoastaan vain luku -kyselyt ovat sallittuja.',
	'querysuccessful'  => 'Kysely onnistui',
),

/*French (Bertrand Grondin) */
'fr' => array(
	'asksql' => 'Requête SQL',
	'asksqltext' => "Utilisez ce formulaire pour faire une requête directe dans la base de donnée.
Utilisez les apostrophes ('comme ceci') pour les chaînes de caractères. Ceci peut souvent surcharger le serveur. Aussi, utilisez cette fonction avec parcimonie.",
	'sqlislogged' => 'Notez bien que toutes les requêtes sont journalisées.',
	'sqlquery' => 'Entrez la requête',
	'querybtn' => 'Soumettre la requête',
	'selectonly' => 'Seules les requêtes en lectures seules sont permises.',
	'querysuccessful' => 'La requête a été exécutée avec succès.',
),

'frc' => array(
	'asksql'           => 'Demande SQL',
	'asksqltext'       => 'Usez la forme en bas pour faire une demande directe de la base d\'information. Usez des marques de citation simples (\'comme ça ici\') pour délimiter les chaînes en ligne. Cette fonction peut mettre un gros voyage dessus le serveur, s\'il vous plaît, usez cette fonction accordant.',
	'sqlislogged'      => 'Soyez connaissant que toutes le demandes sont notées.',
	'sqlquery'         => 'Mettez la demande',
	'querybtn'         => 'Envoyez la demande',
	'selectonly'       => 'Juste les demandes marquées \'seulement lisable\' sont acceptées.',
	'querysuccessful'  => 'La demande est faite.',
),

'frp' => array(
	'asksql'           => 'Requéta SQL',
	'asksqltext'       => 'Utilisâd ceti formulèro por fâre una requéta drêta
dens la bâsa de balyês.
Utilisâd les apostrofes (\'d’ense\') por les chênes de caractèros.
Cen pôt sovent surchargiér lo sèrvior. Donc, utilisâd
cela fonccion avouéc parcimonie.',
	'sqlislogged'      => 'Notâd bien que totes les requétes sont jornalisâs.',
	'sqlquery'         => 'Entrâd la requéta',
	'querybtn'         => 'Sometre la requéta',
	'selectonly'       => 'Solètes les requétes en lèctures solètes sont pèrmêses.',
	'querysuccessful'  => 'La requéta at étâ ègzécutâ avouéc reusséta.',
),

'gl' => array(
	'asksql'           => 'Consulta SQL',
	'asksqltext'       => 'Use o formulario de embaixo para facer unha consulta directa na base de datos.
Use só as comiñas ("desta maneira") para delimitar cadeas literais.
Isto con frecuencia pode engadir unha carga considerábel ao servidor, así que por favor use esta función moderadamente.',
	'sqlislogged'      => 'Por favor dese conta de que todas as consultas son rexistradas.',
	'sqlquery'         => 'Introducir consulta',
	'querybtn'         => 'Enviar consulta',
	'selectonly'       => 'Só se permiten consultas de só lectura',
	'querysuccessful'  => 'Consulta con éxito',
),

'hr' => array(
	'asksql'           => 'SQL upit',
	'asksqltext'       => 'Rabite donju formu za direktne upite na bazu podataka.
Početak i kraj stringa ograničava se jednostrukim navodnicima (\'poput ovih\').
Ova funkcija može opteretiti poslužitelj, stoga ju nemojte rabiti prečesto.',
	'sqlislogged'      => 'Svi upiti se evidentiraju.',
	'sqlquery'         => 'Upišite upit',
	'querybtn'         => 'Izvrši upit',
	'selectonly'       => 'Samo upiti koji čitaju iz baze su dozvoljeni.',
	'querysuccessful'  => 'Upit uspješno izvršen',
),

'hsb' => array(
	'asksql'           => 'SQL wotprašenje',
	'asksqltext'       => 'Wužij tutón formular, zo by datowu banku direktnje wotprašował.
Wužij jednore pazorki (\'kaž tutej\'), zo by znamješkowe literale wotdźělił.
To móže husto serwer sylnišo wobćežić, prošu wužij tuž tutu funkciju zrědka.',
	'sqlislogged'      => 'Wobkedźbuj, zo so wšě wotprašenja protokoluja.',
	'sqlquery'         => 'Wotprašenje zapodać',
	'querybtn'         => 'Wotprašenje wotesłać',
	'selectonly'       => 'Su jenož wotprašenja dowolene, kotrež su jenož čitajomne.',
	'querysuccessful'  => 'Wotprašenje wuspěšne',
),

/* Indonesian (Ivan Lanin) */
'id' => array(
	'asksql' => 'Kueri SQL',
	'asksqltext' => "Gunakan isian berikut untuk melakukan kueri langsung ke basis data. Gunakan kutip tunggal ('seperti ini') untuk membatasi literal string. Hal ini cukup membebani server, jadi gunakanlah fungsi ini secukupnya.",
	'sqlislogged' => 'Ingatlah bahwa semua kueri akan dicatat.',
	'sqlquery' => 'Masukkan kueri',
	'querybtn' => 'Kirim',
	'selectonly' => 'Hanya kueri baca-saja yang diijinkan.',
	'querysuccessful' => 'Kueri berhasil',
),

'is' => array(
	'asksql'           => 'SQL-fyrirspurn',
	'asksqltext'       => 'Notaðu eyðublaðið fyrir neðan til að gera beina fyrirspurn til gagnagrunnsins. Notaðu einfaldar gæsalappir (\'eins og þessar\') til að afmarka strenglesgildi. Þetta getur bætt töluverðugu álagi á vefþjónin, svo notaðu þessa aðgerð sparlega.',
	'sqlislogged'      => 'Athugið að allar fyrirspurnir eru skráðar.',
	'sqlquery'         => 'Skrifaðu fyrirspurn',
	'querybtn'         => 'Sendu fyrirspurnina',
	'selectonly'       => 'Einungis lestrarfyrirspurnir leyfðar.',
	'querysuccessful'  => 'Fyrirspurn heppnaðist',
),

/* Italian (BrokenArrow) */
'it' => array(
	'asksql' => 'Query SQL',
	'asksqltext' => "Il modulo riportato di seguito consente di eseguire interrogazioni dirette sul database.
Usare apici singoli ('come questi') per indicare le stringhe costanti.
Questa funzione può essere molto onerosa nei confronti dei server, si
prega quindi di usarla con molta parsimonia.",
	'sqlislogged' => 'Attenzione! Tutte le query vengono registrate.',
	'sqlquery' => 'Inserire la query',
	'querybtn' => 'Invia query',
	'selectonly' => 'Sono consentite unicamente query di lettura.',
	'querysuccessful' => 'Query eseguita correttamente',
),

/* nld / Dutch (Siebrand Mazeland) */
'nl' => array(
	'asksql'           => 'SQL-zoekopdracht',
	'asksqltext'       => 'Gebruik het onderstaande formulier om direct een query op de database te maken.
Gebruik apostrofs (\'zo dus\') als delimiter voor strings.
Dit kan zorgen voor zware belasting van de server, gebruik deze functie dus spaarzaam.',
	'sqlislogged'      => 'Alle zoekopdrachten worden in een logboek opgeslagen.',
	'sqlquery'         => 'Voer een zoekopdracht in',
	'querybtn'         => 'Voer zoekopdracht uit',
	'selectonly'       => 'U kunt slechts alleen-lezen zoekopdrachten uitvoeren.',
	'querysuccessful'  => 'Zoekopdracht uitgevoerd',
),

/* Norwegian (Jon Harald Søby) */
'no' => array(
	'asksql'           => 'SQL-spørring',
	'asksqltext'       => 'Bruk skjemaet under for å foreta en direkte spørring av databasen. Bruk enkle anførselstegn (\'som dette\') for å merke strenger. Dette kan putte press på tjenerytelsen, så bruk funksjonen med varsomhet.',
	'sqlislogged'      => 'Merk at alle spørringer logges.',
	'sqlquery'         => 'Skriv inn spørring',
	'querybtn'         => 'Kjør spørring',
	'selectonly'       => 'Kun lesespørringer godtas',
	'querysuccessful'  => 'Spørring vellykket',
),

/* Occitan (Cedric31) */
'oc' => array(
	'asksql'           => 'Requèsta SQL',
	'asksqltext'       => 'Utilizatz aqueste formulari per far una requèsta dirècta dins la banca de donadas. Utilizatz los apostròfes (\'atal\') per las cadenas de caractèrs. Aquò pòt sovent subrecargar lo serveire. Alara, utilizatz aquesta foncion amb parsimoniá.',
	'sqlislogged'      => 'Notatz plan que totas las requèstas son jornalizadas.',
	'sqlquery'         => 'Entratz la requèsta',
	'querybtn'         => 'Sometre la requèsta',
	'selectonly'       => 'Solas las requèstas en lecturas solas son permesas.',
	'querysuccessful'  => 'La requèsta es estada executada amb succès.',
),

'pl' => array(
	'asksql'           => 'zapytanie SQL',
	'asksqltext'       => 'Użyj formularza poniżej by wykonać bezpośrednie zapytanie do bazy danych. Napisy otocz pojedynczymi apostrofami (\'w ten sposób\'). Często takie zapytania mocno obciążają serwer, więc używaj tej funkcji rozważnie.',
	'sqlislogged'      => 'Przypomnienie: wszystkie zapytania są rejestrowane.',
	'sqlquery'         => 'Wpisz zapytanie',
	'querybtn'         => 'Wyślij zapytanie',
	'selectonly'       => 'Dozwolone są tylko zapytania czytające dane.',
	'querysuccessful'  => 'Zapytanie zakończone powodzeniem',
),

/* Piedmontese (Bèrto 'd Sèra) */
'pms' => array(
	'asksql'           => 'Operassion SQL',
	'asksqltext'       => 'Ch\'a dòvra ël quàder ëd domanda ambelessì sota për fé dj\'operassion bele drita ant sla base dat.
Ch\'a dòvra le virgolëtte sìngole (\'parèj\') për marchèj\'espression leteraj.
Sòn soèns a men-a a carié ëd travaj la màchina serventa, donca për piasì ch\'a lo dòvra con criteri.',
	'sqlislogged'      => 'Ch\'a ten-a da ment che tute j\'operassion a resto marcà ant un registr a pòsta.',
	'sqlquery'         => 'Ch\'a scriva soa operassion',
	'querybtn'         => 'Mandé an là l\'operassion',
	'selectonly'       => 'As peul mach fesse operassion ëd letura.',
	'querysuccessful'  => 'Operassion andaita a bon fin',
),

'pt' => array(
	'asksql'           => 'Consulta SQL',
	'asksqltext'       => 'Utilize o formulário abaixo para realizar consultas directas à base de dados.
Use aspas simples (\'como estas\') para delimitar cadeias de caracteres literais.
Esta função frequentemente adiciona uma carga considerável ao servidor, por isso utilize-a com reserva.',
	'sqlislogged'      => 'Por favor, note que todas as consultas são registadas.',
	'sqlquery'         => 'Introduza consulta',
	'querybtn'         => 'Submeter consulta',
	'selectonly'       => 'Apenas consultas só de leitura são permitidas.',
	'querysuccessful'  => 'Consulta com sucesso',
),

/* Romanian (KlaudiuMihăilă) */
'ro' => array(
	'asksql'           => 'Interogare SQL',
	'asksqltext'       => 'Folosiţi formularul de mai jos pentru a face o interogare în mod direct asupra bazei de date.
Folosiţi apostrofuri (\'în acest fel\') pentru a delimita şiruri de caractere.
Această opţiune încarcă de obicei serverul, deci vă rugăm să o folosiţi cât mai puţin.',
	'sqlislogged'      => 'Atenţie, toate interogările sunt memorate.',
	'sqlquery'         => 'Introduceţi interogare',
	'querybtn'         => 'Trimite interogare',
	'selectonly'       => 'Sunt permise doar interogări care efectuează numai citiri.',
	'querysuccessful'  => 'Interogare terminată cu succes',
),

/* Russian (Alexander Sigachov) */
'ru' => array(
	'asksql' => 'SQL-запрос',
	'asksqltext' => "Данную форму можно использовать для прямых запросов к базе данных.
Используйте одинарные кавычки для обозначения символьных последоветельностей ('вот так').
Запросы могут стать причиной значительной нагрузки на сервер, используйте данную функцию осторожно.",
	'sqlislogged' => 'Все запросы записываются в журнал.',
	'sqlquery' => 'Ввод запроса',
	'querybtn' => 'Отправить запрос',
	'selectonly' => 'Разрешены только запросы на чтение.',
	'querysuccessful' => 'Запрос выполнен',
),

'sah' => array(
	'asksql'           => 'SQL ыйытык',
	'asksqltext'       => 'Бу халыыбы билии олоҕор быһа ыйытыкка тутун. Сиимбол тиһиликтэрин биирдии кавычкаларынан саҕалаа уонна түмүктээ (\'бу курдук\'). Бу сиэрбэри олус толкуйдатыан сөп, онон сэрэнэн тутун.',
	'sqlislogged'      => 'Бары ыйытыктар тиһиллэн иһэллэр',
	'sqlquery'         => 'Ыйытык киллэрии',
	'querybtn'         => 'Ыйытыгы ыытыы',
	'selectonly'       => 'Ааҕарга эрэ аналлаах ыйытыктар көҥүллэнэллэр',
	'querysuccessful'  => 'Ыйытык оҥоһулунна',
),

'scn' => array(
	'asksql'           => 'Query SQL',
	'asksqltext'       => 'Lu mòdulu riportatu ccà sutta cunzenti di esequiri query diretti supra lu databbasi.
Usari apici singuli (\'comu chisti\') pi nnicari li stringhi costanti.
Chista funzioni pò èssiri moltu pisanti pô server, pirciò 
si prega di usàrila cu giudizziu.',
	'sqlislogged'      => 'Accura: tutti li query vennu arriggistrati.',
	'sqlquery'         => 'Nzeriri la query',
	'querybtn'         => 'Suttamitta query',
	'selectonly'       => 'Sugnu cunzintiti sulu query di littura.',
	'querysuccessful'  => 'Query esequita currittamenti',
),

/* Slovak (helix84) */
'sk' => array(
	'asksql' => 'SQL požiadavka',
	'asksqltext' => "Použite tento formulár na zadanie priamej požiadavky do databázy.
Použite jednoduché úvodzovky ('takéto') na oddelenie reťazcových literálov.
Toto môže často znamenať závažnú dodatočnú záťaž serverov, preto prosím
používajte túto funkciu s rozmyslom.",
	'sqlislogged' => 'Prosím majte na pamäti, že všetky požiadavky sú zaznamenávané.',
	'sqlquery' => 'Zadať požiadavku',
	'querybtn' => 'Poslať požiadavku',
	'selectonly' => 'Sú povolené požiadavky iba na čítanie.',
	'querysuccessful' => 'Požiadavka úspešne vykonaná',
),

/* Sundanese (Kandar via BetaWiki) */
'su' => array(
	'asksql'           => 'Pamundut SQL',
	'asksqltext'       => 'Paké pormulir di handap ieu pikeun mundut langsung ti pangkalan data. Paké curek tunggal (\'kawas kieu\') pikeun ngawatesan string nu dimaksud. Hal ieu bisa ngabeungbeuratan ka server, ku kituna mangga anggo saperluna.',
	'sqlislogged'      => 'Perhatoskeun yén sadaya pamundut aya logna.',
	'sqlquery'         => 'Asupkeun pamundut',
	'querybtn'         => 'Kirimkeun pamundut',
	'selectonly'       => 'Ngan pamundut ukur-maca nu diwenangkeun.',
	'querysuccessful'  => 'Pamundut tos laksana',
),

'sv' => array(
	'asksql'           => 'SQL-fråga',
	'asksqltext'       => 'Använd nedanstående formulär för att ställa en direkt fråga till databasen. 
Använd enkla citationstecken (\'så här\') för att avgränsa textsträngar.
Detta kan leda till väsentlig belastning av servern, så använd denna funktion med måtta.',
	'sqlislogged'      => 'Observera att alla frågor loggförs.',
	'sqlquery'         => 'Mata in fråga',
	'querybtn'         => 'Skicka in fråga',
	'selectonly'       => 'Endast läs-frågor tillåts.',
	'querysuccessful'  => 'Frågan lyckades',
),

/* Cantonese (Shinjiman) */
'yue' => array(
	'asksql' => 'SQL查詢',
	'asksqltext' => "使用下面嘅表可以直接查詢數據庫。
用單引號（'好似咁'）來界定字串符。
噉做有可能會增加伺服器嘅負擔，所以請慎用呢個功能。",
	'sqlislogged' => '請注意全部的查詢都會被記錄落來。',
	'sqlquery' => '輸入查詢',
	'querybtn' => '遞交查詢',
	'selectonly' => '只允許唯讀模式嘅查詢。',
	'querysuccessful' => '查詢完成',
),

/* Chinese (Simplified) (Formulax, Shizhao) */
'zh-hans' => array(
	'asksql' => 'SQL查询',
	'asksqltext' => "使用下面的表单可以直接查询数据库。
使用单引号（'像这样'）来界定字串符。
这样做有可能增加服务器的负担，所以请慎用本功能。",
	'sqlislogged' => '请注意全部的查询会被记录。',
	'sqlquery' => '输入查询',
	'querybtn' => '提交查询',
	'selectonly' => '只允许只读方式的查询。',
	'querysuccessful' => '查询完成',
),

/* Chinese (Traditional) (Shinjiman, Vipuser) */
'zh-hant' => array(
	'asksql' => 'SQL查詢',
	'asksqltext' => "使用下面的表單可以直接查詢數據庫。
使用單引號（'像這樣'）來界定字串符。
這樣做有可能增加伺服器的負擔，所以請慎用本功能。",
	'sqlislogged' => '請注意全部的查詢會被記錄。',
	'sqlquery' => '輸入查詢',
	'querybtn' => '遞交查詢',
	'selectonly' => '只允許唯讀模式的查詢。',
	'querysuccessful' => '查詢完成',
),

	);

	/* Chinese defaults, fallback to zh-hans or zh-hant */
	$messages['zh'] = $messages['zh-hans'];
	$messages['zh-cn'] = $messages['zh-hans'];
	$messages['zh-hk'] = $messages['zh-hant'];
	$messages['zh-tw'] = $messages['zh-hans'];
	$messages['zh-sg'] = $messages['zh-hant'];
	/* Cantonese default, fallback to yue */
	$messages['zh-yue'] = $messages['yue'];

	return $messages;
}
