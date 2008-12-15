<?php
/**
 * Internationalisation file for Asksql extension
 *
 * @addtogroup Extensions
 * @author Bertrand Grondin <bertrand.grondin@tiscali.fr>
 */

$messages = array();

/** English
 * @author Rob Church
 */
$messages['en'] = array(
	'asksql' => 'SQL query',
	'asksql-desc' => 'Do SQL queries through a [[Special:Asksql|special page]]',
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
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'asksql'          => 'استعلام SQL',
	'asksql-desc'     => 'عمل كويري إس كيو إل من خلال [[Special:Asksql|صفحة خاصة]]',
	'asksqltext'      => "استخدم الاستمارة بالأسفل لعمل استعلام مباشر لقاعدة البيانات.
استخدم علامات مفردة ('مثل هذه') لتحديد حدود الخيوط.
هذا عادة يضيف عبئا كبيرا للخادم، لذا من فضلك استخدم هذه الخاصية بقلة.",
	'sqlislogged'     => 'من فضلك لاحظ أن كل الاستعلامات مسجلة.',
	'sqlquery'        => 'أدخل الاستعلام',
	'querybtn'        => 'تنفيذ الاستعلام',
	'selectonly'      => 'فقط استعلامات القراءة فقط مسموحة.',
	'querysuccessful' => 'الاستعلام ناجح',
);

/** Asturian (Asturianu)
 * @author Esbardu
 */
$messages['ast'] = array(
	'asksql'          => 'Consulta SQL',
	'asksql-desc'     => 'Fai consultes SQL nuna [[Special:Asksql|páxina especial]]',
	'asksqltext'      => "Usa'l formulariu d'embaxo pa facer una consulta direuta
a la base de datos.
Usa comines simples ('como estes') pa dellimitar les cadenes lliterales.
Esto pue añader davezu grandes cargues nel servidor, asina que por
favor usa esta función con xacíu.",
	'sqlislogged'     => 'Por favor fíxate en que toles consultes queden rexistraes.',
	'sqlquery'        => 'Introducir consulta',
	'querybtn'        => 'Unviar consulta',
	'selectonly'      => 'Namái se permiten consultes de solo llectura.',
	'querysuccessful' => 'Consulta efeutuada correutamente',
);

/** Kotava (Kotava)
 * @author Nkosi ya Cabinda
 */
$messages['avk'] = array(
	'sqlquery'        => 'Va kucilara bazel !',
	'querybtn'        => 'Va kucilara staksel !',
	'selectonly'      => 'Anton belisa kucilara zo rictar.',
	'querysuccessful' => 'Kucilanhara',
);

/** Bikol Central (Bikol Central)
 * @author Filipinayzd
 */
$messages['bcl'] = array(
	'asksql'          => 'Hapot na SQL',
	'sqlislogged'     => 'Giromdomon na an gabos na hapot nakalaog.',
	'sqlquery'        => 'Ilaog an hapot',
	'querybtn'        => 'Isumitir an hapot',
	'selectonly'      => 'Solamenteng an mga hapot na read-only sana an tinotogotan.',
	'querysuccessful' => 'Matriumpo an paghapot',
);

/** Bulgarian (Български)
 * @author Spiritia
 * @author DCLXVI
 */
$messages['bg'] = array(
	'asksql'          => 'SQL заявка',
	'asksql-desc'     => 'Отправяне на SQL-заявки към базата данни през [[Special:Asksql|специална страница]]',
	'asksqltext'      => "Формулярът по-долу служи за отправяне на директни заявки към базата данни.
За ограничаване на низовите литерали се използват единични кавички ('като тези').
Заявките към базата данни могат значително да натоварят сървъра, затова е желателно тази функционалност да се използва пестеливо.",
	'sqlislogged'     => 'Имайте предвид, че за всички заявки се пази информация.',
	'sqlquery'        => 'Въвеждане на заявка',
	'querybtn'        => 'Изпращане',
	'selectonly'      => 'Позволени са единствено заявки за четене.',
	'querysuccessful' => 'Заявката беше изпълнена успешно',
);

/** Bengali (বাংলা)
 * @author Zaheen
 */
$messages['bn'] = array(
	'asksql'          => 'এসকিউএল কোয়েরি',
	'asksql-desc'     => 'একটি [[Special:Asksql|বিশেষ পাতার]] সাহায্যে এসকিউএল কোয়েরি সম্পাদন করা যাবে',
	'asksqltext'      => "ডাটাবেজে সরাসরি কোয়েরি করার জন্য নিচের ফর্মটি ব্যবহার করুন।
স্ট্রিং লিটেরাল সীমায়িত করার জন্য একক উদ্ধৃতিচিহ্ন ('এ ভাবে') ব্যবহার করুন।
এতে সার্ভারের উপর যথেষ্ট চাপ পড়তে পারে, তাই অনুগ্রহ করে কৃচ্ছ্রতার সাথে এই ফাংশনটি ব্যবহার করুন।",
	'sqlislogged'     => 'অনুগ্রহ করে লক্ষ্য করুন যে সমস্ত কোয়েরি লগ করা হবে।',
	'sqlquery'        => 'কোয়েরি প্রবেশ করান',
	'querybtn'        => 'কোয়েরি জমা দিন',
	'selectonly'      => 'শুধু-পঠনযোগ্য কোয়েরিগুলিই কেবল অনুমোদিত।',
	'querysuccessful' => 'কোয়েরি সফল',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'asksql'          => 'Reked SQL',
	'asksql-desc'     => 'Sevel a ra rekedoù SQL dre ur [[Special:Asksql|bajenn zibar]]',
	'asksqltext'      => "Ober gant ar furmskrid a-is evit sevel ur reked war-eeun ouzh ar bank titouroù.
Ober gant unskraboù('evel-hen') evit termeniñ an neudennad.
An dra-se a c'hall kargañ ar servijer spontus, setu n'emañ ket da vezañ implijet re alies.",
	'sqlislogged'     => 'Notennit mat eo marilhet an holl rekedoù.',
	'sqlquery'        => 'Sevel ur reked',
	'querybtn'        => 'Kas ar reked',
	'selectonly'      => "N'eo aotreet nemet ar rekedoù lenn-hepken",
	'querysuccessful' => "Reked disoc'het",
);

/** Czech (Česky)
 * @author Matěj Grabovský
 * @author Li-sung
 */
$messages['cs'] = array(
	'asksql'          => 'SQL dotaz',
	'asksql-desc'     => 'Provádí SQL dotazy pomocí [[{{ns:Special}}:Asksql|speciální stránky]]',
	'asksqltext'      => "Použijte tento formulář pro zadání přímého požadavku do databáze.
Použijte jednoduché uvozovky (' a ') pro oddělení řetězcových literálů.
Toto může znamenat závažnou dodatečnou zátěž serverů, proto prosím
používejte s rozmyslem.",
	'sqlislogged'     => 'Prosím mějte na paměti, že všechny dotazy jsou zaznamenávané.',
	'sqlquery'        => 'Zadat dotaz',
	'querybtn'        => 'Odeslat dotaz',
	'selectonly'      => 'Jsou povoleny dotazy pouze pro čtení.',
	'querysuccessful' => 'Dotaz byl úspěšně dokončen',
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'asksql'          => 'SQL-Abfragen',
	'asksql-desc'     => 'SQL-Abfragen über eine [[Special:Asksql|Spezialseite]] durchführen.',
	'asksqltext'      => "Benutze das Formular, um direkte Abfragen an die Datenbank durchzuführen.
Benutze einfache Anführungszeichen ('wie diese'), um Zeichenketten zu trennen.
Die Abfragen können die Server sehr stark belasten, deshalb nutze die Funktion mit Bedacht.",
	'sqlislogged'     => 'Bitte beachte, dass alle Abfragen dokumentiert werden.',
	'sqlquery'        => 'Abfrage eingeben',
	'querybtn'        => 'Abfrage durchführen',
	'selectonly'      => 'Es sind nur reine Lesezugriffe erlaubt.',
	'querysuccessful' => 'Abfrage erfolgreich',
);

/** German - formal adress (Deutsch - förmlich)
 * @author Raimond Spekking
 */
$messages['de-formal'] = array(
	'asksqltext'      => "Benutzen Sie das Formular, um direkte Abfragen an die Datenbank durchzuführen.
Benutzen Sie einfache Anführungszeichen ('wie diese'), um Zeichenketten zu trennen.
Die Abfragen können die Server sehr stark belasten, deshalb nutzen Sie die Funktion mit Bedacht.",
	'sqlislogged'     => 'Bitte beachten Sie, dass alle Abfragen dokumentiert werden.',
);

/** Greek (Ελληνικά)
 * @author Dead3y3
 */
$messages['el'] = array(
	'asksql'          => 'ερώτημα SQL',
	'asksqltext'      => "Χρησιμοποιήστε τη φόρμα παρακάτω για να κάνετε ένα ευθύ ερώτημα στη βάση δεδομένων.
Χρησιμοποιήστε απλά εισαγωγικά ('όπως αυτά') για να οριοθετήσετε string literals.
Αυτό μπορεί συχνά να προσθέσει σημαντικό φορτίο στον εξυπηρετητή, οπότε παρακαλώ χρησιμοποιήστε φειδωλά αυτή τη λειτουργία.",
	'sqlislogged'     => 'Παρακαλώ σημειώστε ότι όλα τα ερωτήματα καταγράφονται.',
	'sqlquery'        => 'Εισαγωγή ερωτήματος',
	'querybtn'        => 'Αποστολή ερωτήματος',
	'selectonly'      => 'Μόνο ερωτήματα τύπου «μόνο για ανάγνωση» επιτρέπονται.',
	'querysuccessful' => 'Ερώτημα επιτυχές',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'asksql'          => 'SQL-serĉomendo',
	'sqlislogged'     => 'Bonvolu noti ke ĉiuj serĉomendoj estas listigitaj en la loglibro.',
	'sqlquery'        => 'Entajpu SQL-serĉomendon',
	'querybtn'        => 'Sendu SQL-serĉomendon',
	'selectonly'      => 'Nur nurlegecaj serĉomendoj estas permesitaj.',
	'querysuccessful' => 'Serĉomendo sukcesas',
);

/** Finnish (Suomi)
 * @author Crt
 */
$messages['fi'] = array(
	'asksql'          => 'SQL-kysely',
	'asksql-desc'     => 'Mahdollistaa SQL-kyselyiden tekemisen [[Special:Asksql|toimintosivun]] kautta',
	'asksqltext'      => "Käytä alla olevaa lomaketta tehdäksesi suoria kyselyitä tietokannasta. Merkkijonovakioita merkitään yksinkertaisilla lainausmerkeillä ('näin'). Kyselyt voivat usein kuormittaa palvelinta huomattavasti, joten käytä tätä toimintoa säästeliäästi.",
	'sqlislogged'     => 'Huomioithan, että kaikki kyselyt kirjataan.',
	'sqlquery'        => 'Kirjoita kysely',
	'querybtn'        => 'Lähetä kysely',
	'selectonly'      => 'Ainoastaan vain luku -kyselyt ovat sallittuja.',
	'querysuccessful' => 'Kysely onnistui',
);

/** French (Français)
 * @author Urhixidur
 */
$messages['fr'] = array(
	'asksql'          => 'Requête SQL',
	'asksql-desc'     => 'Effectue des requêtes SQL à travers une [[Special:Asksql|page spéciale]]',
	'asksqltext'      => "Utilisez ce formulaire pour faire une requête directe dans la base de données.
Utilisez les apostrophes ('comme ceci') pour les chaînes de caractères. Ceci peut souvent surcharger le serveur. Aussi, utilisez cette fonction avec parcimonie.",
	'sqlislogged'     => 'Notez bien que toutes les requêtes sont journalisées.',
	'sqlquery'        => 'Entrez la requête',
	'querybtn'        => 'Soumettre la requête',
	'selectonly'      => 'Seules les requêtes en lecture seulement sont permises.',
	'querysuccessful' => 'La requête a été exécutée avec succès.',
);

/** Cajun French (Français cadien)
 * @author JeanVoisin
 * @author RoyAlcatraz
 */
$messages['frc'] = array(
	'asksql'          => 'Demande SQL',
	'asksqltext'      => "Usez la forme en bas pour faire une demande directe de la base d'information. Usez des marques de citation simples ('comme ça ici') pour délimiter les chaînes en ligne. Cette fonction peut mettre un gros voyage dessus le serveur, s'il vous plaît, usez cette fonction accordant.",
	'sqlislogged'     => 'Soyez connaissant que toutes le demandes sont notées.',
	'sqlquery'        => 'Mettez la demande',
	'querybtn'        => 'Envoyez la demande',
	'selectonly'      => "Juste les demandes marquées 'seulement lisable' sont acceptées.",
	'querysuccessful' => 'La demande est faite.',
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'asksql'          => 'Requéta SQL',
	'asksqltext'      => "Utilisâd ceti formulèro por fâre una requéta drêta
dens la bâsa de balyês.
Utilisâd les apostrofes ('d’ense') por les chênes de caractèros.
Cen pôt sovent surchargiér lo sèrvior. Donc, utilisâd
cela fonccion avouéc parcimonie.",
	'sqlislogged'     => 'Notâd bien que totes les requétes sont jornalisâs.',
	'sqlquery'        => 'Entrâd la requéta',
	'querybtn'        => 'Sometre la requéta',
	'selectonly'      => 'Solètes les requétes en lèctures solètes sont pèrmêses.',
	'querysuccessful' => 'La requéta at étâ ègzécutâ avouéc reusséta.',
);

/** Galician (Galego)
 * @author Alma
 * @author Xosé
 */
$messages['gl'] = array(
	'asksql'          => 'Consulta SQL',
	'asksql-desc'     => 'Facer consultas SQL a través dunha [[Special:Asksql|páxina especial]]',
	'asksqltext'      => 'Use o formulario de embaixo para facer unha consulta directa na base de datos.
Use só as comiñas ("desta maneira") para delimitar cadeas literais.
Isto con frecuencia pode engadir unha carga considerábel ao servidor, así que por favor use esta función moderadamente.',
	'sqlislogged'     => 'Por favor dese conta de que todas as consultas son rexistradas.',
	'sqlquery'        => 'Introducir consulta',
	'querybtn'        => 'Enviar consulta',
	'selectonly'      => 'Só se permiten consultas de só lectura',
	'querysuccessful' => 'Consulta con éxito',
);

/** Croatian (Hrvatski)
 * @author SpeedyGonsales
 */
$messages['hr'] = array(
	'asksql'          => 'SQL upit',
	'asksqltext'      => "Rabite donju formu za direktne upite na bazu podataka.
Početak i kraj stringa ograničava se jednostrukim navodnicima ('poput ovih').
Ova funkcija može opteretiti poslužitelj, stoga ju nemojte rabiti prečesto.",
	'sqlislogged'     => 'Svi upiti se evidentiraju.',
	'sqlquery'        => 'Upišite upit',
	'querybtn'        => 'Izvrši upit',
	'selectonly'      => 'Samo upiti koji čitaju iz baze su dozvoljeni.',
	'querysuccessful' => 'Upit uspješno izvršen',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'asksql'          => 'SQL wotprašenje',
	'asksql-desc'     => 'Naprašowanja SQL přez [[Special:Asksql|specialnu stronu]] činić',
	'asksqltext'      => "Wužij tutón formular, zo by datowu banku direktnje wotprašował.
Wužij jednore pazorki ('kaž tutej'); zo by znamješkowe literale wotdźělił.
To móže husto serwer sylnišo wobćežić, prošu wužij tuž tutu funkciju zrědka.",
	'sqlislogged'     => 'Wobkedźbuj, zo so wšě wotprašenja protokoluja.',
	'sqlquery'        => 'Wotprašenje zapodać',
	'querybtn'        => 'Wotprašenje wotesłać',
	'selectonly'      => 'Su jenož wotprašenja dowolene, kotrež su jenož čitajomne.',
	'querysuccessful' => 'Wotprašenje wuspěšne',
);

/** Hungarian (Magyar)
 * @author Bdanee
 */
$messages['hu'] = array(
	'asksql'          => 'SQL lekérdezés',
	'asksqltext'      => "Az alábbi űrlap segítségével közvetlen lekérdezéseket végezhetsz az
adatbázisból.
Az aposztrófok ('mint ez') sztring literálokat határolnak.
A folyamat gyakran leterhelheti a szervert, ezért ritkán használd ezt
az eszközt.",
	'sqlislogged'     => 'Az összes lekérdezés naplózva van.',
	'sqlquery'        => 'Lekérdezés beírása',
	'querybtn'        => 'Lekérdezés elküldése',
	'selectonly'      => 'Csak az olvasást végző lekérdezések engedélyezettek.',
	'querysuccessful' => 'Lekérdezés sikeresen megtörtént',
);

/* Indonesian
 * @author Ivan Lanin
 */
$messages['id'] = array(
	'asksql'          => 'Kueri SQL',
	'asksqltext'      => "Gunakan isian berikut untuk melakukan kueri langsung ke basis data. Gunakan kutip tunggal ('seperti ini') untuk membatasi literal string. Hal ini cukup membebani server, jadi gunakanlah fungsi ini secukupnya.",
	'sqlislogged'     => 'Ingatlah bahwa semua kueri akan dicatat.',
	'sqlquery'        => 'Masukkan kueri',
	'querybtn'        => 'Kirim',
	'selectonly'      => 'Hanya kueri baca-saja yang diijinkan.',
	'querysuccessful' => 'Kueri berhasil',
);

/** Icelandic (Íslenska)
 * @author S.Örvarr.S
 */
$messages['is'] = array(
	'asksql'          => 'SQL-fyrirspurn',
	'asksqltext'      => "Notaðu eyðublaðið fyrir neðan til að gera beina fyrirspurn til gagnagrunnsins. Notaðu einfaldar gæsalappir ('eins og þessar') til að afmarka strenglesgildi. Þetta getur bætt töluverðugu álagi á vefþjónin, svo notaðu þessa aðgerð sparlega.",
	'sqlislogged'     => 'Athugið að allar fyrirspurnir eru skráðar.',
	'sqlquery'        => 'Skrifaðu fyrirspurn',
	'querybtn'        => 'Sendu fyrirspurnina',
	'selectonly'      => 'Einungis lestrarfyrirspurnir leyfðar.',
	'querysuccessful' => 'Fyrirspurn heppnaðist',
);

/* Italian
 * @author BrokenArrow
 */
$messages['it'] = array(
	'asksql'          => 'Query SQL',
	'asksqltext'      => "Il modulo riportato di seguito consente di eseguire interrogazioni dirette sul database.
Usare apici singoli ('come questi') per indicare le stringhe costanti.
Questa funzione può essere molto onerosa nei confronti dei server, si
prega quindi di usarla con molta parsimonia.",
	'sqlislogged'     => 'Attenzione! Tutte le query vengono registrate.',
	'sqlquery'        => 'Inserire la query',
	'querybtn'        => 'Invia query',
	'selectonly'      => 'Sono consentite unicamente query di lettura.',
	'querysuccessful' => 'Query eseguita correttamente',
);

/** Japanese (日本語)
 * @author Kkkdc
 * @author JtFuruhata
 */
$messages['ja'] = array(
	'asksql'          => 'SQLクエリの実行',
	'asksql-desc'     => '[[Special:Asksql|{{int:specialpage}}]]からSQLクエリを実行する',
	'asksqltext'      => "以下のフォームを使用して、データベースへ直接SQLクエリを送信できます。
文字列リテラルの区切りにはシングルクォート（'～'）を用いてください。
この機能の使用はサーバに相当の負荷をかけることがあります。使用は控えめにしてください。",
	'sqlislogged'     => '実行した全てのクエリはログに記録されます。',
	'sqlquery'        => 'SQL文:',
	'querybtn'        => '送信',
	'selectonly'      => '読み込み用のクエリのみが許可されています。',
	'querysuccessful' => 'クエリは成功しました',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'asksql'          => 'SQL Offro',
	'asksqltext'      => "Benotzt de Formular hei ënnedrënner fir eng Ufro un d'Datebank ze rrichten.
Benotzt dëst Zeechen' ('esou wéi hei') fir Werter an der Ufro ofzegrenzen.
Esou Ufroen kënnen zu enger grousser Belaaschtung vun de Servere féieren, dofir froe mir iech dës Funktioun mat Moderatioun ze benotzen.",
	'sqlislogged'     => 'Zu ärer Informatioun: All Ufroen ginn an e Logbuch agedro',
	'sqlquery'        => 'Ufro aginn',
	'querybtn'        => 'Ufro schécken',
	'querysuccessful' => "D'Offro gouf mat Erfolleg ausgefouert",
);

/** Lithuanian (Lietuvių)
 * @author Hugo.arg
 * @author Matasg
 */
$messages['lt'] = array(
	'asksql'          => 'SQL prieiga',
	'asksql-desc'     => 'Daryti SQL užklausas [[Special:Asksql|specialiajame puslapyje]]',
	'sqlislogged'     => 'Atminkite, kad visos užklausos yra įregistruotos.',
	'sqlquery'        => 'Įvesti užklausą',
	'querybtn'        => 'Patvirtinti užklausą',
	'selectonly'      => 'Tiktai neredaguojamos užklausos yra leidžiamos.',
	'querysuccessful' => 'Užklausa sėkminga.',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'asksql'          => 'SQL-zoekopdracht',
	'asksql-desc'     => 'Voer SQL-zoekopdrachten uit via een [[Special:Asksql|speciale pagina]]',
	'asksqltext'      => "Gebruik het onderstaande formulier om direct een query op de database te maken.
Gebruik apostrofs ('zo dus') als delimiter voor strings.
Dit kan zorgen voor zware belasting van de server, gebruik deze functie dus spaarzaam.",
	'sqlislogged'     => 'Alle zoekopdrachten worden in een logboek opgeslagen.',
	'sqlquery'        => 'Voer een zoekopdracht in',
	'querybtn'        => 'Voer zoekopdracht uit',
	'selectonly'      => 'U kunt slechts alleen-lezen zoekopdrachten uitvoeren.',
	'querysuccessful' => 'Zoekopdracht uitgevoerd',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'asksql'          => 'SQL-spørring',
	'asksql-desc'     => 'Gjør SQL-spørringer gjennom en [[Special:Asksql|spesialside]]',
	'asksqltext'      => "Bruk skjemaet under for å foreta en direkte spørring av databasen. Bruk enkle anførselstegn ('som dette') for å merke strenger. Dette kan putte press på tjenerytelsen, så bruk funksjonen med varsomhet.",
	'sqlislogged'     => 'Merk at alle spørringer logges.',
	'sqlquery'        => 'Skriv inn spørring',
	'querybtn'        => 'Kjør spørring',
	'selectonly'      => 'Kun lesespørringer godtas',
	'querysuccessful' => 'Spørring vellykket',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'asksql'          => 'Requèsta SQL',
	'asksql-desc'     => 'Efectua de requèstas SQL a travèrs una [[Special:Asksql|pagina especiala]]',
	'asksqltext'      => "Utilizatz aqueste formulari per far una requèsta dirècta dins la banca de donadas. Utilizatz los apostròfes ('atal') per las cadenas de caractèrs. Aquò pòt sovent subrecargar lo serveire. Alara, utilizatz aquesta foncion amb parsimoniá.",
	'sqlislogged'     => 'Notatz plan que totas las requèstas son jornalizadas.',
	'sqlquery'        => 'Entratz la requèsta',
	'querybtn'        => 'Sometre la requèsta',
	'selectonly'      => 'Solas las requèstas en lecturas solas son permesas.',
	'querysuccessful' => 'La requèsta es estada executada amb succès.',
);

/** Polish (Polski)
 * @author Derbeth
 */
$messages['pl'] = array(
	'asksql'          => 'zapytanie SQL',
	'asksqltext'      => "Użyj formularza poniżej by wykonać bezpośrednie zapytanie do bazy danych. Napisy otocz pojedynczymi apostrofami ('w ten sposób'). Często takie zapytania mocno obciążają serwer, więc używaj tej funkcji rozważnie.",
	'sqlislogged'     => 'Przypomnienie: wszystkie zapytania są rejestrowane.',
	'sqlquery'        => 'Wpisz zapytanie',
	'querybtn'        => 'Wyślij zapytanie',
	'selectonly'      => 'Dozwolone są tylko zapytania czytające dane.',
	'querysuccessful' => 'Zapytanie zakończone powodzeniem',
);

/** Piemontèis (Piemontèis)
 * @author Bèrto 'd Sèra
 */
$messages['pms'] = array(
	'asksql'          => 'Operassion SQL',
	'asksqltext'      => "Ch'a dòvra ël quàder ëd domanda ambelessì sota për fé dj'operassion bele drita ant sla base dat.
Ch'a dòvra le virgolëtte sìngole ('parèj') për marchèj'espression leteraj.
Sòn soèns a men-a a carié ëd travaj la màchina serventa, donca për piasì ch'a lo dòvra con criteri.",
	'sqlislogged'     => "Ch'a ten-a da ment che tute j'operassion a resto marcà ant un registr a pòsta.",
	'sqlquery'        => "Ch'a scriva soa operassion",
	'querybtn'        => "Mandé an là l'operassion",
	'selectonly'      => 'As peul mach fesse operassion ëd letura.',
	'querysuccessful' => 'Operassion andaita a bon fin',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'asksql'          => 'Consulta SQL',
	'asksql-desc'     => 'Realize consultas SQL através da [[{{ns:special}}:Asksql|página especial]]',
	'asksqltext'      => "Utilize o formulário abaixo para realizar consultas directas à base de dados.
Use aspas simples ('como estas') para delimitar cadeias de caracteres literais.
Esta função frequentemente adiciona uma carga considerável ao servidor, por isso utilize-a com reserva.",
	'sqlislogged'     => 'Por favor, note que todas as consultas são registadas.',
	'sqlquery'        => 'Introduza consulta',
	'querybtn'        => 'Submeter consulta',
	'selectonly'      => 'Apenas consultas só de leitura são permitidas.',
	'querysuccessful' => 'Consulta com sucesso',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 */
$messages['ro'] = array(
	'asksql'          => 'Interogare SQL',
	'asksqltext'      => "Folosiţi formularul de mai jos pentru a face o interogare în mod direct asupra bazei de date.
Folosiţi apostrofuri ('în acest fel') pentru a delimita şiruri de caractere.
Această opţiune încarcă de obicei serverul, deci vă rugăm să o folosiţi cât mai puţin.",
	'sqlislogged'     => 'Atenţie, toate interogările sunt memorate.',
	'sqlquery'        => 'Introduceţi interogare',
	'querybtn'        => 'Trimite interogare',
	'selectonly'      => 'Sunt permise doar interogări care efectuează numai citiri.',
	'querysuccessful' => 'Interogare terminată cu succes',
);

/** Russian (Русский)
 * @author .:Ajvol:.
 */
$messages['ru'] = array(
	'asksql'          => 'SQL-запрос',
	'asksql-desc'     => 'Выполнение SQL-запросов с помощью [[Special:Asksql|служебной страницы]]',
	'asksqltext'      => "Данную форму можно использовать для прямых запросов к базе данных.
Используйте одинарные кавычки для обозначения символьных последоветельностей ('вот так').
Запросы могут стать причиной значительной нагрузки на сервер, используйте данную функцию осторожно.",
	'sqlislogged'     => 'Все запросы записываются в журнал.',
	'sqlquery'        => 'Ввод запроса',
	'querybtn'        => 'Отправить запрос',
	'selectonly'      => 'Разрешены только запросы на чтение.',
	'querysuccessful' => 'Запрос выполнен',
);

/** Yakut (Саха тыла)
 * @author Bert Jickty
 */
$messages['sah'] = array(
	'asksql'          => 'SQL ыйытык',
	'asksqltext'      => "Бу халыыбы билии олоҕор быһа ыйытыкка тутун. Сиимбол тиһиликтэрин биирдии кавычкаларынан саҕалаа уонна түмүктээ ('бу курдук'). Бу сиэрбэри олус толкуйдатыан сөп, онон сэрэнэн тутун.",
	'sqlislogged'     => 'Бары ыйытыктар тиһиллэн иһэллэр',
	'sqlquery'        => 'Ыйытык киллэрии',
	'querybtn'        => 'Ыйытыгы ыытыы',
	'selectonly'      => 'Ааҕарга эрэ аналлаах ыйытыктар көҥүллэнэллэр',
	'querysuccessful' => 'Ыйытык оҥоһулунна',
);

/** Sicilian (Sicilianu)
 * @author Tonyfroio
 * @author Siebrand
 */
$messages['scn'] = array(
	'asksql'          => 'Query SQL',
	'asksqltext'      => "Lu mòdulu riportatu ccà sutta cunzenti di esequiri query diretti supra lu databbasi.
Usari apici singuli ('comu chisti') pi nnicari li stringhi costanti.
Chista funzioni pò èssiri moltu pisanti pô server, pirciò
si prega di usàrila cu giudizziu.",
	'sqlislogged'     => 'Accura: tutti li query vennu arriggistrati.',
	'sqlquery'        => 'Nzeriri la query',
	'querybtn'        => 'Suttamitta query',
	'selectonly'      => 'Sugnu cunzintiti sulu query di littura.',
	'querysuccessful' => 'Query esequita currittamenti',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'asksql'          => 'SQL požiadavka',
	'asksql-desc'     => 'Vykonávanie SQL požiadaviek prostredníctvom [[Special:Asksql|špeciálnej stránky]]',
	'asksqltext'      => "Použite tento formulár na zadanie priamej požiadavky do databázy.
Použite jednoduché úvodzovky ('takéto') na oddelenie reťazcových literálov.
Toto môže často znamenať závažnú dodatočnú záťaž serverov, preto prosím
používajte túto funkciu s rozmyslom.",
	'sqlislogged'     => 'Prosím majte na pamäti, že všetky požiadavky sú zaznamenávané.',
	'sqlquery'        => 'Zadať požiadavku',
	'querybtn'        => 'Poslať požiadavku',
	'selectonly'      => 'Sú povolené požiadavky iba na čítanie.',
	'querysuccessful' => 'Požiadavka úspešne vykonaná',
);

/** ћирилица (ћирилица)
 * @author Sasa Stefanovic
 */
$messages['sr-ec'] = array(
	'sqlquery'        => 'Унеси упит',
	'querybtn'        => 'Постави упит',
	'querysuccessful' => 'Упит успешан',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'asksql'          => 'SQL Froage',
	'asksql-desc'     => 'SQL-Oufroagen uur ne [[Special:Asksql|Spezialsiede]] truchfiere.',
	'asksqltext'      => "Bruuk dät Formular hier unner uum fluks ne Oufroage fon ju Doatenboank tou moakjen.
Bruuk eenpelde Anfierengsteekene ('as disse') uum Riegen-Eenden outougränsjen.
Dit kon oafte dän Server oarich beläästigje, bruuk dät deeruum spoarsoam.",
	'sqlislogged'     => 'Beoachtje dät aal Oufroagen apteekend sunt.',
	'sqlquery'        => 'Fang ju Oufroage oun',
	'querybtn'        => 'Reek ju Oufroage ien',
	'selectonly'      => 'Bloot schrieuwschutsede Oufroagen sunt ferlööwed.',
	'querysuccessful' => 'Oufroage glukked',
);

/** Sundanese (Basa Sunda)
 * @author Kandar
 */
$messages['su'] = array(
	'asksql'          => 'Pamundut SQL',
	'asksqltext'      => "Paké pormulir di handap ieu pikeun mundut langsung ti pangkalan data. Paké curek tunggal ('kawas kieu') pikeun ngawatesan string nu dimaksud. Hal ieu bisa ngabeungbeuratan ka server, ku kituna mangga anggo saperluna.",
	'sqlislogged'     => 'Perhatoskeun yén sadaya pamundut aya logna.',
	'sqlquery'        => 'Asupkeun pamundut',
	'querybtn'        => 'Kirimkeun pamundut',
	'selectonly'      => 'Ngan pamundut ukur-maca nu diwenangkeun.',
	'querysuccessful' => 'Pamundut tos laksana',
);

/** Swedish (Svenska)
 * @author Sannab
 * @author Siebrand
 * @author Lejonel
 */
$messages['sv'] = array(
	'asksql'          => 'SQL-fråga',
	'asksql-desc'     => 'Ger möjlighet att ställa SQL-frågor via en [[Special:Asksql|specialsida]]',
	'asksqltext'      => "Använd nedanstående formulär för att ställa en direkt fråga till databasen.
Använd enkla citationstecken ('så här') för att avgränsa textsträngar.
Detta kan leda till väsentlig belastning av servern, så använd denna funktion med måtta.",
	'sqlislogged'     => 'Observera att alla frågor loggförs.',
	'sqlquery'        => 'Mata in fråga',
	'querybtn'        => 'Skicka in fråga',
	'selectonly'      => 'Endast läs-frågor tillåts.',
	'querysuccessful' => 'Frågan lyckades',
);

/** Vietnamese (Tiếng Việt)
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'asksql'          => 'Truy vấn SQL',
	'asksqltext'      => "Sử dụng mẫu ở dưới để viết truy vấn trực tiếp đến cơ sở dữ liệu.
Sử dụng dấu nháy đơn ('giống như vầy') để phân cách chuỗi ký tự.
Việc làm này thường kéo tải của máy chủ một cách đáng kể, do đó xin hãy dùng chức năng này một cách nhẹ nhàng.",
	'sqlislogged'     => 'Xin chú ý rằng tất cả các truy vấn sẽ được ghi vào nhật trình.',
	'sqlquery'        => 'Nhập câu truy vấn',
	'querybtn'        => 'Gửi câu truy vấn',
	'selectonly'      => 'Chỉ chấp nhận câu truy vấn "chỉ đọc"',
	'querysuccessful' => 'Truy vấn thành công',
);

/* Cantonese
 * @author Shinjiman
 */
$messages['yue'] = array(
	'asksql'          => 'SQL查詢',
	'asksqltext'      => "使用下面嘅表可以直接查詢數據庫。
用單引號（'好似咁'）來界定字串符。
噉做有可能會增加伺服器嘅負擔，所以請慎用呢個功能。",
	'sqlislogged'     => '請注意全部的查詢都會被記錄落來。',
	'sqlquery'        => '輸入查詢',
	'querybtn'        => '遞交查詢',
	'selectonly'      => '只允許唯讀模式嘅查詢。',
	'querysuccessful' => '查詢完成',
);

/* Chinese (Simplified)
 * @author Formulax
 * @author Shizhao
 */
$messages['zh-hans'] = array(
	'asksql'          => 'SQL查询',
	'asksqltext'      => "使用下面的表单可以直接查询数据库。
使用单引号（'像这样'）来界定字串符。
这样做有可能增加服务器的负担，所以请慎用本功能。",
	'sqlislogged'     => '请注意全部的查询会被记录。',
	'sqlquery'        => '输入查询',
	'querybtn'        => '提交查询',
	'selectonly'      => '只允许只读方式的查询。',
	'querysuccessful' => '查询完成',
);

/* Chinese (Traditional)
 * @author Shinjiman
 * @author Vipuser
 */
$messages['zh-hant'] = array(
	'asksql'          => 'SQL查詢',
	'asksqltext'      => "使用下面的表單可以直接查詢數據庫。
使用單引號（'像這樣'）來界定字串符。
這樣做有可能增加伺服器的負擔，所以請慎用本功能。",
	'sqlislogged'     => '請注意全部的查詢會被記錄。',
	'sqlquery'        => '輸入查詢',
	'querybtn'        => '遞交查詢',
	'selectonly'      => '只允許唯讀模式的查詢。',
	'querysuccessful' => '查詢完成',
);

