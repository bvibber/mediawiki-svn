<?php
/**
 * Internationalisation file for extension ParserDiffTest.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'parserdifftest' => 'Parser diff test',
	'pdtest-desc' => '[[Special:ParserDiffTest|Special page]] for comparing the output of two different parsers',
	'pdtest_no_target' => 'No target specified.',
	'pdtest_page_missing' => 'The specified page was not found in the database.',
	'pdtest_no_changes' => 'No changes detected.',
	'pdtest_time_report' => '<b>$1</b> took $2 seconds, <b>$3</b> took $4 seconds.',
	'pdtest_title' => 'Context title:',
	'pdtest_text' => 'Input text:',
	'pdtest_ok' => 'OK',
	'pdtest_get_text' => 'Get text from page',
	'pdtest_diff' => 'Differences',
	'pdtest_side_by_side' => 'Output comparison',
	'pdt_comparing_page' => 'Comparing parser output from [[$1]]',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'parserdifftest'      => 'اختبار فرق البارسر',
	'pdtest-desc'         => '[[Special:ParserDiffTest|صفحة خاصة]] لمقارنة ناتج بارسرين مختلفين',
	'pdtest_no_target'    => 'لا هدف تم تحديده.',
	'pdtest_page_missing' => 'الصفحة المحددة لم يتم العثور عليها في قاعدة البيانات.',
	'pdtest_no_changes'   => 'لا تغييرات تم تبينها.',
	'pdtest_time_report'  => '<b>$1</b> استغرق $2 ثانية، <b>$3</b> استغرق $4 ثانية.',
	'pdtest_title'        => 'عنوان السياق:',
	'pdtest_text'         => 'النص المدخل:',
	'pdtest_ok'           => 'موافق',
	'pdtest_get_text'     => 'الحصول على النص من الصفحة',
	'pdtest_diff'         => 'الفروقات',
	'pdtest_side_by_side' => 'مقارنة الناتج',
	'pdt_comparing_page'  => 'مقارنة ناتج البارسر من [[$1]]',
);

/** Asturian (Asturianu)
 * @author Esbardu
 */
$messages['ast'] = array(
	'parserdifftest'      => 'Test de difencies pal análisis sintáuticu',
	'pdtest_no_target'    => "Nun s'especificó l'oxetivu.",
	'pdtest_page_missing' => "La páxina especificada nun s'atopó na base de datos.",
	'pdtest_no_changes'   => 'Nun se deteutaron cambeos.',
	'pdtest_time_report'  => '<b>$1</b> llevó $2 segundos, <b>$3</b> llevó $4 segundos.',
	'pdtest_title'        => 'Títulu del contestu:',
	'pdtest_text'         => "Testu d'entrada:",
	'pdtest_ok'           => 'Aceutar',
	'pdtest_get_text'     => 'Obtener testu de la páxina',
	'pdtest_diff'         => 'Diferencies',
	'pdtest_side_by_side' => 'Comparanza de salida',
	'pdt_comparing_page'  => "Comparando l'análisis sintáuticu de salida dende [[$1]]",
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'pdtest_no_target'    => 'Не беше посочена цел.',
	'pdtest_page_missing' => 'Поисканата страница не беше открита в базата данни.',
	'pdtest_no_changes'   => 'Не са направени промени.',
	'pdtest_time_report'  => '<b>$1</b> отне $2 секунди, <b>$3</b> отне $4 секунди.',
	'pdtest_ok'           => 'Добре',
	'pdtest_get_text'     => 'Извличане на текст от страница',
	'pdtest_diff'         => 'Разлики',
);

/** Bengali (বাংলা)
 * @author Bellayet
 * @author Zaheen
 */
$messages['bn'] = array(
	'parserdifftest'      => 'পার্সার পার্থক্য পরীক্ষা',
	'pdtest-desc'         => 'দুইটি ভিন্ন পার্সারের আউটপুটের তুলনা করার জন্য [[Special:ParserDiffTest|বিশেষ পাতা]]',
	'pdtest_no_target'    => 'কোন লক্ষ্য নির্ধারিত হয়নি।',
	'pdtest_page_missing' => 'ডাটাবেজে এ ধরনের পাতা খুজে পাওয়া যায়নি।',
	'pdtest_no_changes'   => 'কোন পরিবর্তন পাওয়া যায়নি।',
	'pdtest_time_report'  => '<b>$1</b> সময় নেয় $2 সেকেন্ড, <b>$3</b> সময় নেয় $4 সেকেন্ড।',
	'pdtest_title'        => 'প্রতিবেশ শিরোনাম:',
	'pdtest_text'         => 'লেখা ইনপুট:',
	'pdtest_ok'           => 'ঠিকা আছে',
	'pdtest_get_text'     => 'পাতা থেকে লেখা পাওয়া যাবে',
	'pdtest_diff'         => 'পার্থক্যসমূহ',
	'pdtest_side_by_side' => 'ফলাফলের তুলনা',
	'pdt_comparing_page'  => '[[$1]] থেকে পার্সার আউটপুট তুলনা করা হচ্ছে',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'pdtest_ok' => 'Mat eo',
);

/** Czech (Česky)
 * @author Li-sung
 * @author Matěj Grabovský
 */
$messages['cs'] = array(
	'parserdifftest'      => 'Test rozdílu parserů',
	'pdtest-desc'         => '[[Special:ParserDiffTest|Speciální stránka]] pro porovnání výstupů dvou rozdílných syntaktických analyzátorů',
	'pdtest_no_target'    => 'Není určen cíl.',
	'pdtest_page_missing' => 'Určená stránka nebyla v databázi nalezena.',
	'pdtest_no_changes'   => 'Neprojevily se žádné změny.',
	'pdtest_time_report'  => '<b>$1</b> trval $2 {{plural:$2|sekundu|sekundy|sekund}}, <b>$3</b> trval $4 {{plural:$4|sekundu|sekundy|sekund}}.',
	'pdtest_title'        => 'Název stránky kvůli kontextu:',
	'pdtest_text'         => 'Vstupní text:',
	'pdtest_ok'           => 'OK',
	'pdtest_get_text'     => 'Použít text ze stránky',
	'pdtest_diff'         => 'Rozdíly',
	'pdtest_side_by_side' => 'Porovnání výstupu',
	'pdt_comparing_page'  => 'Porovnání výstupu parserů pro stránku [[$1]]',
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'parserdifftest'      => 'Parser-Differenz-Test',
	'pdtest-desc'         => '[[Special:ParserDiffTest|Spezialseite]] zum Vergleich der Ausgabe von zwei verschiedenen Parsern',
	'pdtest_no_target'    => 'Kein Ziel angegeben.',
	'pdtest_page_missing' => 'Die angegebene Seite wurde nicht in der Datenbank gefunden.',
	'pdtest_no_changes'   => 'Keine Unterschiede gefunden.',
	'pdtest_time_report'  => '<b>$1</b> benötigte $2 Sekunden, <b>$3</b> benötigte $4 Sekunden.',
	'pdtest_title'        => 'Kontexttitel:',
	'pdtest_text'         => 'Eingabe:',
	'pdtest_ok'           => 'OK',
	'pdtest_get_text'     => 'Hole Text von Seite',
	'pdtest_diff'         => 'Unterschiede',
	'pdtest_side_by_side' => 'Ausgabe im Vergleich',
	'pdt_comparing_page'  => 'Vergleiche Parser-Ausgabe für [[$1]]',
);

/** Persian (فارسی)
 * @author Huji
 */
$messages['fa'] = array(
	'parserdifftest'      => 'آزمون تفاوت تحلیلگر',
	'pdtest-desc'         => '[[Special:ParserDiffTest|صفحهٔ ویژه]] برای مقایسهٔ خروجی دو تجزیه‌کنندهٔ مختلف',
	'pdtest_no_target'    => 'هدفی تعیین نشده‌است.',
	'pdtest_page_missing' => 'صفحهٔ موردنظر در پایگاه داده یافت نشد.',
	'pdtest_no_changes'   => 'هیچ تغییر حذف نشد.',
	'pdtest_time_report'  => 'برای <b>$1</b> مدت $2 ثانیه صرف شد، برای <b>$3</b> مدت $4 ثانیه صرف شد.',
	'pdtest_title'        => 'عنوان متن:',
	'pdtest_text'         => 'متن ورودی:',
	'pdtest_ok'           => 'تایید',
	'pdtest_get_text'     => 'دریافت متن از صفحه',
	'pdtest_diff'         => 'تفاوت‌ها',
	'pdtest_side_by_side' => 'مقایسهٔ خروجی',
	'pdt_comparing_page'  => 'مقایسهٔ خروجی تحلیلگر از [[$1]]',
);

/** French (Français)
 * @author Grondin
 * @author Urhixidur
 */
$messages['fr'] = array(
	'parserdifftest'      => 'Test de diff pour parseur',
	'pdtest-desc'         => 'Page spéciale pour comparer la sortie de deux parseurs différents.',
	'pdtest_no_target'    => "Aucune cible d'indiquée",
	'pdtest_page_missing' => 'La page indiquée n’a pas été trouvée dans la base de données.',
	'pdtest_no_changes'   => 'Aucun changement de détecté.',
	'pdtest_time_report'  => '<b>$1</b> a pris $2 secondes, <b>$3</b> a pris $4 secondes.',
	'pdtest_title'        => 'Titre du contexte :',
	'pdtest_text'         => "Texte d'entrée :",
	'pdtest_ok'           => 'Valider',
	'pdtest_get_text'     => 'Obtenir le texte depuis la page',
	'pdtest_diff'         => 'Différences',
	'pdtest_side_by_side' => 'Comparaison en sortie',
	'pdt_comparing_page'  => 'Comparaison des sorties du parseur pour [[$1]]',
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'parserdifftest'      => 'Èprôva de dif por parsar',
	'pdtest-desc'         => '[[Special:ParserDiffTest|Pâge spèciâla]] por comparar la sortia de doux parsors difèrents.',
	'pdtest_no_target'    => 'Niona ciba endicâ.',
	'pdtest_page_missing' => 'La pâge endicâ at pas étâ trovâ dens la bâsa de balyês.',
	'pdtest_no_changes'   => 'Nion changement dècelâ.',
	'pdtest_time_report'  => '<b>$1</b> at prês $2 secondes, <b>$3</b> at prês $4 secondes.',
	'pdtest_title'        => 'Titro du contèxte :',
	'pdtest_text'         => 'Tèxte d’entrâ :',
	'pdtest_ok'           => 'Validar',
	'pdtest_get_text'     => 'Obtegnir lo tèxte dês la pâge',
	'pdtest_diff'         => 'Difèrences',
	'pdtest_side_by_side' => 'Comparèson en sortia',
	'pdt_comparing_page'  => 'Comparèson du parsèr en sortia dês [[$1]]',
);

/** Galician (Galego)
 * @author Alma
 * @author Xosé
 */
$messages['gl'] = array(
	'pdtest_no_target'    => 'Non se especificou o destino.',
	'pdtest_page_missing' => 'A páxina especificada non foi atopada na base de datos.',
	'pdtest_no_changes'   => 'Non foron detectados cambios.',
	'pdtest_ok'           => 'De acordo',
	'pdtest_get_text'     => 'Obter texto da páxina',
	'pdtest_diff'         => 'Diferenzas',
);

/** Hebrew (עברית)
 * @author Rotem Liss
 */
$messages['he'] = array(
	'parserdifftest'      => 'בדיקת השינויים במפענח',
	'pdtest_no_target'    => 'לא נבחר יעד.',
	'pdtest_page_missing' => 'הדף המבוקש לא נמצא במסד הנתונים.',
	'pdtest_no_changes'   => 'לא נמצאו הבדלים.',
	'pdtest_time_report'  => '<b>$1</b> לקח $2 שניות, <b>$3</b> לקח $4 שניות.',
	'pdtest_title'        => 'כותרת הדף:',
	'pdtest_text'         => 'טקסט לבדיקה:',
	'pdtest_ok'           => 'שליחה',
	'pdtest_get_text'     => 'שימוש בטקסט מהדף',
	'pdtest_diff'         => 'הבדלים',
	'pdtest_side_by_side' => 'השוואת הפלט',
	'pdt_comparing_page'  => 'משווה את פלט המפענחים מהדף [[$1]]',
);

/** Croatian (Hrvatski)
 * @author Dnik
 */
$messages['hr'] = array(
	'parserdifftest'      => 'Test razlika parsera',
	'pdtest-desc'         => '[[Special:ParserDiffTestSpecial|Posebna stranica]] za usporedbu rezultata dva različita parsera',
	'pdtest_no_target'    => 'Odredište nije zadano.',
	'pdtest_page_missing' => 'Zadana stranica nije nađena u bazi podataka.',
	'pdtest_no_changes'   => 'Promjene nisu nađene.',
	'pdtest_time_report'  => '<b>$1</b> je trajalo $2 {{PLURAL:$2|sekundu|sekunde|sekundi}}, <b>$3</b> je trajalo $4 {{PLURAL:$4|sekundu|sekunde|sekundi}}.',
	'pdtest_title'        => 'Naslov konteksta:',
	'pdtest_text'         => 'Ulazni tekst:',
	'pdtest_ok'           => 'U redu',
	'pdtest_get_text'     => 'Izvadi tekst iz stranice',
	'pdtest_diff'         => 'Razlike',
	'pdtest_side_by_side' => 'Usporedba rezultata',
	'pdt_comparing_page'  => 'Usporedba rezultata parsera stranice [[$1]]',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'parserdifftest'      => 'Test rozdźělow parsera',
	'pdtest-desc'         => 'Specialna strona za přirunanje wudaća dweju rozdźělneju parserow.',
	'pdtest_no_target'    => 'Žadyn cil podaty.',
	'pdtest_page_missing' => 'Podata strona njebu w datowej bance namakana.',
	'pdtest_no_changes'   => 'Žane změny wotkryte.',
	'pdtest_time_report'  => '<b>$1</b> traješe $2 {{PLURAL:$2|sekunda|sekundźe|sekundy|sekundow}}, <b>$3</b> traješe $4 {{PLURAL:$4|sekunda|sekundźe|sekundy|sekundow}}.',
	'pdtest_title'        => 'Titul konteksta:',
	'pdtest_text'         => 'Tekst zapodaća:',
	'pdtest_ok'           => 'W porjadku',
	'pdtest_get_text'     => 'Tekst ze strony wzać',
	'pdtest_diff'         => 'Rozdźěle',
	'pdtest_side_by_side' => 'Přirunanje wudaća',
	'pdt_comparing_page'  => 'Wudaće parsera z [[$1]] so přirunuje',
);

/** Hungarian (Magyar)
 * @author Bdanee
 */
$messages['hu'] = array(
	'parserdifftest'      => 'Értelmezők közötti eltérés tesztelése',
	'pdtest-desc'         => '[[Special:ParserDiffTest|Speciális lap]] a két különböző szövegértelmező kimenetének összehasonlítására',
	'pdtest_no_target'    => 'Nincs cél megadva',
	'pdtest_page_missing' => 'A megadott oldal nem található az adatbázisban.',
	'pdtest_no_changes'   => 'Nincsenek eltérések.',
	'pdtest_time_report'  => '<b>$1</b> $2 másodpercig, míg <b>$3</b> $4 másodpercig tartott.',
	'pdtest_title'        => 'Szöveg címe:',
	'pdtest_text'         => 'Bemeneti szöveg:',
	'pdtest_ok'           => 'OK',
	'pdtest_get_text'     => 'Szöveg elmentése',
	'pdtest_diff'         => 'Eltérések',
	'pdtest_side_by_side' => 'Kimenet összehasonlítása',
	'pdt_comparing_page'  => '[[$1]] kimeneteinek összehasonlítása',
);

/** Icelandic (Íslenska)
 * @author S.Örvarr.S
 */
$messages['is'] = array(
	'pdtest_text' => 'Inntakstexti:',
	'pdtest_ok'   => 'Í lagi',
);

/** Italian (Italiano)
 * @author BrokenArrow
 */
$messages['it'] = array(
	'parserdifftest'      => 'Verifica delle modifiche introdotte nel parser',
	'pdtest-desc'         => '[[Special:ParserDiffTest|Pagina speciale]] per mettere a confronto il risultato di due parser diversi',
	'pdtest_no_target'    => 'Nessuna pagina specificata.',
	'pdtest_page_missing' => 'La pagina richiesta non è stata trovata nel database.',
	'pdtest_no_changes'   => 'Non sono state rilevate differenze.',
	'pdtest_time_report'  => "L'elaborazione di <b>$1</b> ha richiesto $2 secondi, quella di <b>$3</b> $4 secondi.",
	'pdtest_title'        => 'Titolo del contesto:',
	'pdtest_text'         => 'Testo di input:',
	'pdtest_ok'           => 'OK',
	'pdtest_get_text'     => 'Preleva il testo dalla pagina',
	'pdtest_diff'         => 'Differenze',
	'pdtest_side_by_side' => 'Confronto tra gli output',
	'pdt_comparing_page'  => 'Confronto tra gli output del parser per [[$1]]',
);

/** Japanese (日本語)
 * @author JtFuruhata
 */
$messages['ja'] = array(
	'parserdifftest'      => 'パーサー比較テスト',
	'pdtest-desc'         => '2つの異なるパーサー間での出力結果を比較する[[{{ns:special}}:ParserDiffTest|{{int:specialpage}}]]。',
	'pdtest_no_target'    => '対象が指定されていません。',
	'pdtest_page_missing' => '指定されたページはデータベースに存在していません。',
	'pdtest_no_changes'   => '違いが検出できませんでした。',
	'pdtest_time_report'  => '<b>$1</b> の処理に $2 秒、<b>$3</b> の処理に $4 秒かかりました。',
	'pdtest_title'        => '比較元のページタイトル:',
	'pdtest_text'         => '比較するテキスト:',
	'pdtest_ok'           => '比較',
	'pdtest_get_text'     => 'ページからテキストを取得',
	'pdtest_diff'         => '差異',
	'pdtest_side_by_side' => '出力の比較',
	'pdt_comparing_page'  => '[[$1]]のパーサー出力を比較',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'pdtest_page_missing' => 'Déi gefrote Säit gouf net an der Datebank fonnt.',
	'pdtest_no_changes'   => 'Keng Ännerung fonnt.',
	'pdtest_time_report'  => '<b>$1</b> huet $2 Sekonne gedauert, <b>$3</b> huet $4 Sekonne gedauert.',
	'pdtest_ok'           => 'ok',
	'pdtest_diff'         => 'Ënnerscheed',
);

/** Limburgish (Limburgs)
 * @author Ooswesthoesbes
 * @author Matthias
 */
$messages['li'] = array(
	'parserdifftest'      => 'Parserversjèlletes',
	'pdtest-desc'         => '[[Special:ParserDiffTest|Speciale pagina]] voor het vergelijken van de uitvoer van twee verschillende parsers',
	'pdtest_no_target'    => 'Gein doel aangegaeve.',
	'pdtest_page_missing' => 'De aangegaeve pazjena is neet aangetroffe inne database.',
	'pdtest_no_changes'   => 'Gein verangeringe vasgesteld.',
	'pdtest_time_report'  => '<b>$1</b> doerde $2 second, <b>$3</b> doerde $4 second.',
	'pdtest_title'        => 'Contekstitel:',
	'pdtest_text'         => 'Inveurteks:',
	'pdtest_ok'           => 'ok',
	'pdtest_get_text'     => 'Teks van pazjena ophaole',
	'pdtest_diff'         => 'Versjèlle',
	'pdtest_side_by_side' => 'Oetveurverglieking',
	'pdt_comparing_page'  => 'Parseroetveur van [[$1]] verglieke',
);

/** Lithuanian (Lietuvių)
 * @author Matasg
 */
$messages['lt'] = array(
	'pdtest_ok' => 'Gerai',
);

/** Dutch (Nederlands)
 * @author Siebrand
 * @author SPQRobin
 */
$messages['nl'] = array(
	'parserdifftest'      => 'Parserverschillentest',
	'pdtest-desc'         => '[[Special:ParserDiffTest|Speciale pagina]] voor het vergelijken van de uitvoer van twee verschillende parsers',
	'pdtest_no_target'    => 'Geen doel aangegeven.',
	'pdtest_page_missing' => 'De aangegeven pagina is niet aangetroffen in de database.',
	'pdtest_no_changes'   => 'Geen wijzigingen vastgesteld.',
	'pdtest_time_report'  => '<b>$1</b> duurde $2 seconden, <b>$3</b> duurde $4 seconden.',
	'pdtest_title'        => 'Contexttitel:',
	'pdtest_text'         => 'Invoertekst:',
	'pdtest_ok'           => 'OK',
	'pdtest_get_text'     => 'Tekst van pagina ophalen',
	'pdtest_diff'         => 'Verschillen',
	'pdtest_side_by_side' => 'Uitvoervergelijking',
	'pdt_comparing_page'  => 'Parseruitvoer van [[$1]] vergelijken',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Eirik
 */
$messages['nn'] = array(
	'parserdifftest'      => 'Test av parserskilnader',
	'pdtest_no_target'    => 'Inga målside er oppgjeve.',
	'pdtest_page_missing' => 'Den oppgjevne sida fanst ikkje i databasen.',
	'pdtest_no_changes'   => 'Ingen endringar vart oppdaga.',
	'pdtest_time_report'  => '<b>$1</b> tok $2 sekund, <b>$3</b> tok $4 sekund.',
	'pdtest_title'        => 'Sidetittel:',
	'pdtest_text'         => 'Tekst som skal parsast:',
	'pdtest_ok'           => 'Sjå skilnader',
	'pdtest_get_text'     => 'Hent tekst frå side',
	'pdtest_diff'         => 'Skilnader',
	'pdtest_side_by_side' => 'Samanlikning av resultatet',
	'pdt_comparing_page'  => 'Samanliknar parserresultat frå [[$1]]',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'parserdifftest'      => 'Parserdifftest',
	'pdtest-desc'         => '[[Special:ParserDiffTest|Spesialside]] for sammenligning av resultatet fra to forskjellige parsere',
	'pdtest_no_target'    => 'Intet mål oppgitt.',
	'pdtest_page_missing' => 'Den angitte siden ble ikke funnet i databasen.',
	'pdtest_no_changes'   => 'Ingen endringer oppdaget.',
	'pdtest_time_report'  => '<b>$1</b> tok $2 sekunder, <b>$3</b> tok $4 sekunder.',
	'pdtest_title'        => 'Konteksttittel:',
	'pdtest_text'         => 'Skriv inn tekst:',
	'pdtest_ok'           => 'OK',
	'pdtest_get_text'     => 'Få tekst fra siden',
	'pdtest_diff'         => 'Forskjeller',
	'pdtest_side_by_side' => 'Resultatsammenligning',
	'pdt_comparing_page'  => 'Sammeligner parserresultat fra [[$1]]',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'parserdifftest'      => 'Tèst de dif per parser',
	'pdtest-desc'         => 'Pagina especiala per comparar la sortida de dos parsaires diferents.',
	'pdtest_no_target'    => "Cap de cibla pas d'indicada",
	'pdtest_page_missing' => 'La pagina especificada es pas estada trobada dins la banca de donadas.',
	'pdtest_no_changes'   => 'Cap de cambiament es pas estat detectat.',
	'pdtest_time_report'  => '<b>$1</b> a pres $2 segondas, <b>$3</b> a pres $4 segondas.',
	'pdtest_title'        => 'Títol del contèxt :',
	'pdtest_text'         => "Tèxt d'entrada :",
	'pdtest_ok'           => "D'acòrdi",
	'pdtest_get_text'     => 'Obténer lo tèxt dempuèi la pagina',
	'pdtest_diff'         => 'Diferéncias',
	'pdtest_side_by_side' => 'Comparason en sortida',
	'pdt_comparing_page'  => 'Comparason del parser en sortida a partir de [[$1]]',
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'pdtest-desc'         => '[[Special:ParserDiffTest|Strona specjalna]] pozwalająca porównać wyniki oraz skuteczność dwóch różnych analizatorów składni',
	'pdtest_no_target'    => 'Nie określono obiektu działania',
	'pdtest_page_missing' => 'Wybranej strony nie odnaleziono w bazie danych.',
	'pdtest_no_changes'   => 'Nie wykryto zmian.',
	'pdtest_time_report'  => 'Analizator składni <b>$1</b> potrzebował $2 sekund, a <b>$3</b> potrzebował $4 sekund.',
	'pdtest_text'         => 'Tekst wejściowy:',
	'pdtest_ok'           => 'OK',
	'pdtest_get_text'     => 'Pobierz tekst ze strony',
	'pdtest_diff'         => 'Różnice',
	'pdtest_side_by_side' => 'Porównanie wyników',
	'pdt_comparing_page'  => 'Porównuję wynik działania analizatora składni z [[$1]]',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'pdtest_text' => 'ځايونکی متن:',
	'pdtest_ok'   => 'ښه/هو',
	'pdtest_diff' => 'توپيرونه',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'parserdifftest'      => 'Teste de diferenças do analisador "parser"',
	'pdtest-desc'         => '[[Special:ParserDiffTest|Página especial]] para comparar o resultado de dois analisadores "parser" diferentes',
	'pdtest_no_target'    => 'Nenhum alvo foi especificado.',
	'pdtest_page_missing' => 'A página especificada não foi encontrada na base de dados.',
	'pdtest_no_changes'   => 'Nenhuma alteração detectada.',
	'pdtest_time_report'  => '<b>$1</b> demorou $2 segundos, <b>$3</b> demorou $4 segundos.',
	'pdtest_title'        => 'Título do contexto:',
	'pdtest_text'         => 'Texto de entrada:',
	'pdtest_ok'           => 'OK',
	'pdtest_get_text'     => 'Obter texto da página',
	'pdtest_diff'         => 'Diferenças',
	'pdtest_side_by_side' => 'Comparação de resultado',
	'pdt_comparing_page'  => 'Comparação do resultado do analisador "parser" de [[$1]]',
);

/** Russian (Русский)
 * @author .:Ajvol:.
 */
$messages['ru'] = array(
	'parserdifftest'      => 'Проверка изменений синтаксического анализатора',
	'pdtest-desc'         => '[[Special:ParserDiffTestSpecial|Служебная страница]] для сравнения вывода двух разных парсеров',
	'pdtest_no_target'    => 'Не указана цель.',
	'pdtest_page_missing' => 'Указанная страница не найдена в базе данных.',
	'pdtest_no_changes'   => 'Изменений не обнаружено.',
	'pdtest_time_report'  => '<b>$1</b> заняло $2 секунд, <b>$3</b> заняло $4 секунд.',
	'pdtest_title'        => 'Название страницы:',
	'pdtest_text'         => 'Входной текст:',
	'pdtest_ok'           => 'OK',
	'pdtest_get_text'     => 'Получить текст со страницы',
	'pdtest_diff'         => 'Различия',
	'pdtest_side_by_side' => 'Сравнение вывода',
	'pdt_comparing_page'  => 'Сравнение вывода синтаксического анализатора для [[$1]]',
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'parserdifftest'      => 'Синтаксис анализаторын (парсер) уларыйыытын көрүү',
	'pdtest-desc'         => 'Икки тус туспа парсердар таһаарыыларын уратытын көрдөрөр [[Special:ParserDiffTestSpecial|аналлаах сирэй]]',
	'pdtest_no_target'    => 'Соруга ыйыллыбатах.',
	'pdtest_page_missing' => 'Ыйыллыбыт сирэй билэ тиһигэр көстүбэтэ.',
	'pdtest_no_changes'   => 'Уларыйыы көстүбэтэ.',
	'pdtest_time_report'  => '<b>$1</b>$2 сөкүүндэ иһигэр, <b>$3</b> $4 $2 сөкүүндэ иһигэр.',
	'pdtest_title'        => 'Сирэй аата:',
	'pdtest_text'         => 'Киирэр тиэкис:',
	'pdtest_ok'           => 'ОК',
	'pdtest_get_text'     => 'Сирэйтэн тиэкиһи ыларга',
	'pdtest_diff'         => 'Араастара',
	'pdtest_side_by_side' => 'Тахсыбыты тэҥнээһин',
	'pdt_comparing_page'  => 'Синтаксис анализаторын (парсер) таһаарыытын манна [[$1]] анаан тэҥнээһин',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'parserdifftest'      => 'Test rozdielov syntaktického analyzátora',
	'pdtest-desc'         => '[[Special:ParserDiffTest|Špeciálna stránka]] na porovnanie výstupov dvoch rozdielnych syntaktických analyzátorov',
	'pdtest_no_target'    => 'Nebol zadaný cieľ.',
	'pdtest_page_missing' => 'Zadaná stránka nebola nájdená v databáze.',
	'pdtest_no_changes'   => 'Neboli zistené žiadne zmeny.',
	'pdtest_time_report'  => '<b>$1</b> trval $2 sekúnd, <b>$3</b> trval $4 sekúnd.',
	'pdtest_title'        => 'Kontextový názov:',
	'pdtest_text'         => 'Vstupný text:',
	'pdtest_ok'           => 'OK',
	'pdtest_get_text'     => 'Získať text zo stránky',
	'pdtest_diff'         => 'Rozdiely',
	'pdtest_side_by_side' => 'Porovnanie výstupu',
	'pdt_comparing_page'  => 'Porovnáva sa výstup syntaktického analyzátora z [[$1]]',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'parserdifftest'      => 'Parser-Differenz-Test',
	'pdtest_no_target'    => 'Neen Siel anroat.',
	'pdtest_page_missing' => 'Ju anroate Siede wuude nit in de Doatenboank fuunen.',
	'pdtest_no_changes'   => 'Neen Unnerscheede fuunen.',
	'pdtest_time_report'  => '<b>$1</b> bruukte $2 Sekunden, <b>$3</b> bruukte $4 Sekunden.',
	'pdtest_title'        => 'Kontexttittel:',
	'pdtest_text'         => 'Iengoawe:',
	'pdtest_ok'           => 'OK',
	'pdtest_get_text'     => 'Hoalje Text fon Siede',
	'pdtest_diff'         => 'Unnerscheede',
	'pdtest_side_by_side' => 'Uutgoawe in dän Fergliek',
	'pdt_comparing_page'  => 'Fergliek Parser-Uutgoawe foar [[$1]]',
);

/** Swedish (Svenska)
 * @author Lejonel
 * @author M.M.S.
 */
$messages['sv'] = array(
	'parserdifftest'      => 'Parserskillnadstest',
	'pdtest-desc'         => '[[Special:ParserDiffTest|Specialsida]] för att jämföra resultaten från två olika parsrar.',
	'pdtest_no_target'    => 'Ingen målsida angavs.',
	'pdtest_page_missing' => 'Den angivna sidan hittades inte i databasen.',
	'pdtest_no_changes'   => 'Inga ändringar hittades.',
	'pdtest_time_report'  => '<b>$1</b> tog $2 sekunder, <b>$3</b> tog $4 sekunder.',
	'pdtest_title'        => 'Sidtitel:',
	'pdtest_text'         => 'Text som ska parsas:',
	'pdtest_ok'           => 'Jämför',
	'pdtest_get_text'     => 'Hämta text från sida',
	'pdtest_diff'         => 'Skillnader',
	'pdtest_side_by_side' => 'Jämförelse av resultat',
	'pdt_comparing_page'  => 'Jämför parsningsresultat av [[$1]]',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'pdtest-desc'         => 'రెండు వేర్వేరు పార్సర్ల యొక్క ఔట్&zwnj;పుట్&zwnj;ని పోల్చిచూసేందుకు [[Special:ParserDiffTest|ప్రత్యేక పేజీ]]',
	'pdtest_no_target'    => 'లక్ష్యం ఏమీ ఇవ్వలేదు.',
	'pdtest_page_missing' => 'చెప్పిన ఆ పేజీ డాటాబేసులో కనబడలేదు.',
	'pdtest_no_changes'   => 'మార్పులేమీ కనబడలేదు.',
	'pdtest_time_report'  => '<b>$1</b> $2 క్షణాలు తీసుకుంది, <b>$3</b> $4 క్షణాలు తీసుకుంది.',
	'pdtest_title'        => 'సందర్భపు శీర్షిక:',
	'pdtest_text'         => 'ఇన్&zwnj;పుట్ పాఠ్యం:',
	'pdtest_ok'           => 'సరే',
	'pdtest_get_text'     => 'పేజీనుండి పాఠ్యాన్ని పొందండి',
	'pdtest_diff'         => 'తేడాలు',
	'pdtest_side_by_side' => 'అవుట్&zwnj;పుట్ పోలిక',
	'pdt_comparing_page'  => '[[$1]] నుండి పార్సర్ అవుట్&zwnj;పుట్&zwnj;ని పోలుస్తున్నాం',
);

/** Tajik (Тоҷикӣ)
 * @author Ibrahim
 */
$messages['tg'] = array(
	'pdtest_no_changes'   => 'Ҳеҷ тағйир ҳазф нашуд.',
	'pdtest_get_text'     => 'Дарёфти матн аз саҳифа',
	'pdtest_diff'         => 'Фарқиятҳо',
	'pdtest_side_by_side' => 'Муқоисаи хуруҷӣ',
);

/** Turkish (Türkçe)
 * @author Erkan Yilmaz
 */
$messages['tr'] = array(
	'pdtest_ok' => 'Tamam',
);

/** Vietnamese (Tiếng Việt)
 * @author Vinhtantran
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'parserdifftest'      => 'So sánh kiểm thử bằng hàm phân tích',
	'pdtest-desc'         => '[[Special:ParserDiffTest|Trang đặc biệt]] để so sánh mã nguồn được sản xuất bởi hai bộ xử lý khác nhau',
	'pdtest_no_target'    => 'Chưa xác định mục tiêu',
	'pdtest_page_missing' => 'Trang chỉ định không tìm thấy trong cơ sở dữ liệu.',
	'pdtest_no_changes'   => 'Không tìm thấy sự khác biệt nào.',
	'pdtest_time_report'  => '<b>$1</b> mất $2 giây, <b>$3</b> mất $4 giây.',
	'pdtest_title'        => 'Tựa đề văn cảnh:',
	'pdtest_text'         => 'Văn bản nhập:',
	'pdtest_ok'           => 'OK',
	'pdtest_get_text'     => 'Lấy văn bản từ trang',
	'pdtest_diff'         => 'Khác biệt',
	'pdtest_side_by_side' => 'So sánh đầu ra',
	'pdt_comparing_page'  => 'Đang so sánh đầu ra hàm phân tích từ [[$1]]',
);

/** Volapük (Volapük)
 * @author Smeira
 * @author Malafaya
 */
$messages['vo'] = array(
	'pdtest_page_missing' => 'Pad pavilöl no patuvon.',
	'pdtest_no_changes'   => 'Votükams nonik petuvons.',
	'pdtest_get_text'     => 'Getolöd vödemi se pad',
	'pdtest_diff'         => 'Difs',
	'pdtest_side_by_side' => 'Jonolöd leigodi',
);

