<?php
/**
 * Internationalisation file for LuceneSearch extension.
 *
 * @package MediaWiki
 * @subpackage Extensions
*/

$wgLuceneSearchMessages = array();

$wgLuceneSearchMessages['en'] = array(
	'searchnumber'          => "<strong>Results $1-$2 of $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Prev</span>",
	'searchnext'            => "<span style='font-size: small'>Next</span> &#x00BB;",
	'searchscore'           => "Relevance: $1",
	'searchsize'            => "$1 KB ($2 words)",
	'searchdidyoumean'      => "Did you mean: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Sorry, there were no exact matches to your query.",
	'searchnearmatches'     => "<b>These pages have similar titles to your query:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
Search in namespaces:\n
$1\n
Search for $3 $9",
	'lucenefallback'        => "There was a problem with the wiki search.
This is probably temporary; try again in a few moments,
or you can search the wiki through an external search service:\n"
);
$wgLuceneSearchMessages['cs'] = array(
	'searchnumber'          => '<strong>Výsledky $1–$2 z $3</strong>',
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Předchozí</span>",
	'searchnext'            => "<span style='font-size: small'>Následující</span> &#x00BB;",
	'searchscore'           => "Relevance: $1",
	'searchsize'            => "$1 KB ($2 slov)",
	'searchdidyoumean'      => 'Nehledáte „<a href="$1">$2</a>“?',
	'searchnoresults'       => 'Je mi líto, ale vašemu dotazu žádné stránky přesně neodpovídají.',
	'searchnearmatches'     => "<b>Následující stránky mají nadpis podobný vašemu dotazu:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
Hledat ve jmenných prostorech:\n
$1\n
Hledat $3 $9",
	'lucenefallback'        => 'Při hledání došlo k chybě. Problém je pravděpodobně dočasný, zkuste hledání později, případně můžete vyzkoušet externí vyhledávač:\n'
);
$wgLuceneSearchMessages['de'] = array(
	'searchnumber'		=> "<strong>Ergebnisse $1–$2 von $3</strong>",
	'searchprev'            => "&larr; <span style='font-size: smaller'>Vorherige</span>",
	'searchnext'            => "<span style='font-size: smaller'>Nächste</span> &rarr;",
	'searchscore'           => "Relevanz: $1",
	'searchsize'            => "$1 kB ($2 Wörter)",
	'searchdidyoumean'      => "Meinten Sie „<a href=\"$1\">$2</a>“?",
	'searchnoresults'       => "Es wurden keine passenden Seiten für Ihre Suchanfrage gefunden.",
	'searchnearmatches'     => "<b>Diese Seiten haben zu der Suchanfrage ähnliche Titel:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
Suche in Namensräumen:\n
$1\n
Suche nach $3 $9",
	'lucenefallback'        => "Bei der {{SITENAME}}-internen Suche ist ein Problem aufgetreten.
Dies ist normalerweise ein vorübergehendes Problem. Bitte versuchen Sie es nochmal.
Alternativ können Sie auch die externen Suchmöglichkeiten nutzen: :\n"
);
$wgLuceneSearchMessages['eo'] = array(
	'searchnumber'          => "<strong>Rezultoj $1-$2 el $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Antaŭa</span>",
	'searchnext'            => "<span style='font-size: small'>Sekvanta</span> &#x00BB;",
	'searchscore'           => "Kongrueco: $1",
	'searchsize'            => "$1 kB ($2 vortoj)",
	'searchdidyoumean'      => "Ĉu vi celis : \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Bedaŭrinde ne estas precize kongrua rezulto por via serĉo.",
	'searchnearmatches'     => "<b>Ĉi tiuj paĝoj havas titolojn similajn al via serĉo:</b>\n",
	'lucenepowersearchtext' => "
Serĉo en nomspacoj:\n
$1\n
Serĉo de $3 $9",
	'lucenefallback'        => "Estis problemo kun la serĉilo de ĉi vikio.
Estas verŝajne nur portempa; bonvolu provi denove post iom da tempo
aŭ vi povas esplori la vikion per eksteraj serĉservoj.\n"
);
$wgLuceneSearchMessages['he'] = array(
	'searchnumber'          => "<strong>תוצאות $1-$2 מתוך $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Prev</span>",
	'searchnext'            => "<span style='font-size: small'>הבא</span> &#x00BB;",
	'searchscore'           => "קשר: $1",
	'searchsize'            => "$1 קילובייטים ($2 מילים)",
	'searchdidyoumean'      => "האם התכוונתם ל: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "מצטערים, אין דפים עם הכותרת המדויקת שחיפשת.",
	'searchnearmatches'     => "<b>דפים אלו הם בעלי כותרת דומה למבוקשת:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
חיפוש במרחבי השם:\n
$1\n
חיפוש של $3 $9",
	'lucenefallback'        => "יש בעייה עם מנוע הוויקי.
סביר להניח שהיא זמנית; אנא נסו שנית בעוד מספר דקות.
באפשרותכם גם לחפש בוויקי באמצעות שירותי חיפוש חיצוניים:\n"
);
$wgLuceneSearchMessages['id'] = array(
	'searchnumber'          => "<strong>Hasil pencarian $1-$2 dari $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Sebelumnya</span>",
	'searchnext'            => "<span style='font-size: small'>Selanjutnya</span> &#x00BB;",
	'searchscore'           => "Relevansi: $1",
	'searchsize'            => "$1 KB ($2 kata)",
	'searchdidyoumean'      => "Apakah maksud Anda: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Maaf, tidak ditemukan hasil yang tepat sama dengan permintaan Anda.",
	'searchnearmatches'     => "<b>Halaman-halaman berikut mempunyai judul yang mirip dengan permintaan Anda:</b>\n",
	'lucenepowersearchtext' => "
Pencarian di namespace:\n
$1\n
Pencarian terhadap $3 $9",
	'lucenefallback'        => "Ada masalah pada pencarian wiki.
Masalah ini mungkin hanya sementara; silakan coba lagi dalam beberapa saat,
atau gunakan layanan pencari eksternal:\n"
);
$wgLuceneSearchMessages['it'] = array(
	'searchnumber'          => "<strong>Risultati da $1 a $2 su un totale di $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Precedente</span>",
	'searchnext'            => "<span style='font-size: small'>Successivo</span> &#x00BB;",
	'searchscore'           => "Pertinenza: $1",
	'searchsize'            => "$1 KB ($2 parole)",
	'searchdidyoumean'      => "Forse stavi cercando: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "La funzione di ricerca non ha trovato corrispondenze esatte con il testo cercato.",
	'searchnearmatches'     => "<b>Le pagine elencate di seguito hanno titoli simili al testo cercato:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
Cerca nei namespace selezionati:\n
$1\n
Testo da ricercare $3 $9",
	'lucenefallback'        => "Il motore di ricerca interno ha un problema. 
	Probabilmente si tratta di un errore temporaneo, destinato a risolversi in breve tempo. 
	Nel frattempo, si consiglia di riprovare tra qualche istante o di utilizzare un motore di ricerca esterno:\n"
);
$wgLuceneSearchMessages['kk-kz'] = array(
	'searchnumber'          => "<strong>$3 дегеннен табылған $1—$2 нәтиже</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Алдыңғыға</span>",
	'searchnext'            => "<span style='font-size: small'>Келесіге</span> &#x00BB;",
	'searchscore'           => "Сәйкестігі: $1",
	'searchsize'            => "$1 кБ ($2 сөз)",
	'searchdidyoumean'      => "«<a href=\"$1\">$2</a>» деп қалайсыз ба?",
	'searchnoresults'       => "Ғафу етіңіз, сұранысынызға қарай нақты сәйкесі бар еш нәтиже табылмады.",
	'searchnearmatches'     => "<b>Мына бет атауларында сұранысыңызға ұқсастығы бар:</b>\n",
	'lucenepowersearchtext' => "
Мына есім аяларда іздеу:\n
$1\n
Іздестіру сұранысы: $3 $9",
	'lucenefallback'        => "Іздеу кезінде мына уикиде шатақ шықты.
Бәлкім, бұл уақытша кедергі; біршама сәттен соң қайталаңыз,
немесе осы уикиден іздеу үшін сыртқы қызметтерін қолданыңыз:\n"
);
$wgLuceneSearchMessages['kk-tr'] = array(
	'searchnumber'          => "<strong>$3 degennen tabılğan $1—$2 nätïje</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Aldıñğığa</span>",
	'searchnext'            => "<span style='font-size: small'>Kelesige</span> &#x00BB;",
	'searchscore'           => "Säýkestigi: $1",
	'searchsize'            => "$1 kB ($2 söz)",
	'searchdidyoumean'      => "«<a href=\"$1\">$2</a>» dep qalaýsız ba?",
	'searchnoresults'       => "Ğafw etiñiz, suranısınızğa qaraý naqtı säýkesi bar eş nätïje tabılmadı.",
	'searchnearmatches'     => "<b>Mına bet atawlarında suranısıñızğa uqsastığı bar:</b>\n",
	'lucenepowersearchtext' => "
Mına esim ayalarda izdew:\n
$1\n
İzdestirw suranısı: $3 $9",
	'lucenefallback'        => "İzdew kezinde mına wïkïde şataq şıqtı.
Bälkim, bul waqıtşa kedergi; birşama sätten soñ qaýtalañız,
nemese osı wïkïden izdew üşin sırtqı qızmetterin qoldanıñız:\n"
);
$wgLuceneSearchMessages['kk-cn'] = array(
	'searchnumber'          => "<strong>$3 دەگەننەن تابىلعان $1—$2 نٴاتيجە</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>الدىڭعىعا</span>",
	'searchnext'            => "<span style='font-size: small'>كەلەسٴىگە</span> &#x00BB;",
	'searchscore'           => "سٴايكەستٴىگٴى: $1",
	'searchsize'            => "$1 كب ($2 سٴوز)",
	'searchdidyoumean'      => "«<a href=\"$1\">$2</a>» دەپ قالايسىز با?",
	'searchnoresults'       => "عافۋ ەتٴىڭٴىز, سۇرانىسىنىزعا قاراي ناقتى سٴايكەسٴى بار ەش نٴاتيجە تابىلمادى.",
	'searchnearmatches'     => "<b>مىنا بەت اتاۋلارىندا سۇرانىسىڭىزعا ۇقساستىعى بار:</b>\n",
	'lucenepowersearchtext' => "
مىنا ەسٴىم ايالاردا ٴىزدەۋ:\n
$1\n
ٴىزدەستٴىرۋ سۇرانىسى: $3 $9",
	'lucenefallback'        => "ٴىزدەۋ كەزٴىندە مىنا ۋيكيدە شاتاق شىقتى.
بٴالكٴىم, بۇل ۋاقىتشا كەدەرگٴى; بٴىرشاما سٴاتتەن سوڭ قايتالاڭىز,
نەمەسە وسى ۋيكيدەن ٴىزدەۋ ٴۇشٴىن سىرتقى قىزمەتتەرٴىن قولدانىڭىز:\n"
);
$wgLuceneSearchMessages['kk'] = $wgLuceneSearchMessages['kk-kz'];
$wgLuceneSearchMessages['nl'] = array(
	'searchnumber'          => "<strong>Resultaten $1-$2 van de $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Vorige</span>",
	'searchnext'            => "<span style='font-size: small'>Volgende</span> &#x00BB;",
	'searchscore'           => "Relevantie: $1",
	'searchsize'            => "$1 KB ($2 woorden)",
	'searchdidyoumean'      => "Bedoelde u: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Sorry, uw zoekopdracht heeft geen resultaten opgeleverd.",
	'searchnearmatches'     => "<b>Deze paginanamen komen overeen met uw zoekopdracht:</b>\n",
	'lucenepowersearchtext' => "
Zoek in de volgende naamruimten:\n
$1\n
Zoek naar $3 $9",
	'lucenefallback'        => "Er is een storing in de wikizoekmachine.
Deze is waarschijnlijk tijdelijk van aard; probeer het over enige tijd opnieuw
of doorzoek de wiki via een externe zoekmachine:\n"
);
$wgLuceneSearchMessages['pl'] = array(
	'searchnumber'          => "<strong>Wyniki $1-$2 z $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Poprzednia</span>",
	'searchnext'            => "<span style='font-size: small'>Następna</span> &#x00BB;",
	'searchscore'           => "Trafność: $1",
	'searchsize'            => "$1 KB ($2 słów)",
	'searchdidyoumean'      => "Może chodziło Ci o \"<a href=\"1\">$2</a>\"?",
	'searchnoresults'       => "Niestety nie znaleziono stron pasujących do podanych kryteriów wyszukiwania.",
	'searchnearmatches'     => "<b>Strony o podobnych nazwach:</b>\n",
	'lucenepowersearchtext' => "
Szukaj w przestrzeniach nazw:\n
$1\n
Szukana fraza $3 $9",
	'lucenefallback'        => "Wystąpił błąd z wyszukiwaniem w wiki.
Jest to tymczasowe; spróbuj ponownie za parę chwil
lub przeszukaj wiki za pomocą zewnętrznych wyszukiwarek:\n"
);
$wgLuceneSearchMessages['ru'] = array(
	'searchnumber'          => "<strong>Результаты $1—$2 из $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Пред.</span>",
	'searchnext'            => "<span style='font-size: small'>След.</span> &#x00BB;",
	'searchscore'           => "Релевантность: $1",
	'searchsize'            => "$1 кБ ($2 слов)",
	'searchdidyoumean'      => "Возможно, вы имели в виду «<a href=\"1\">$2</a>»?",
	'searchnoresults'       => "К сожалению, по вашему запросу не было найдено точных соответствий.",
	'searchnearmatches'     => "<b>Следующие страницы имеют заголовок, похожий на ваш запрос:</b>\n",
	'lucenepowersearchtext' => "
Поиск в пространствах имён:\n
$1\n
Поисковый запрос $3 $9",
	'lucenefallback'        => "Возникла проблема с поиском по вики.
Вероятно, эта временная проблема, попробуйте ещё раз чуть позже,
либо воспользуйтесь поиском во внешних поисковых системах.\n"
);
$wgLuceneSearchMessages['sk'] = array(
	'searchnumber'          => "<strong>Výsledky $1-$2 zo $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Späť</span>",
	'searchnext'            => "<span style='font-size: small'>Ďalej</span> &#x00BB;",
	'searchscore'           => "Relevantnosť: $1",
	'searchsize'            => "$1KB ($2 slov)",
	'searchdidyoumean'      => "Mali ste na mysli: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Ľutujeme, vyhľadávanie nevrátilo žiadne presné výsledky na Váš dotaz.",
	'searchnearmatches'     => "<b>Tieto stránky majú názvy podobné Vášmu dotazu:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
Vyhľadávanie v menných priestoroch:\n
$1\n
Hľadanie $3 $9",
	'lucenefallback'        => "S vyhľadávaním na wiki nastal problém.
Je možné, že je to dočasné; o chvíľu to skúste znova
alebo vyhľadávajte na wiki pomocou externej indexovacej služby:\n"
);
$wgLuceneSearchMessages['sr'] = array(
	'searchnumber'          => "<strong>Резултати $1-$2 од $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Претходна</span>",
	'searchnext'            => "<span style='font-size: small'>Следећа</span> &#x00BB;",
	'searchscore'           => "Сличност: $1",
	'searchsize'            => "$1 KB ($2 words)",
	'searchdidyoumean'      => "Да ли сте мислили: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Извињавамо се, не постоје резултати за ваш упит.",
	'searchnearmatches'     => "<b>Следеће странице имају сличне називе као што сте тражили:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
Претрага у именским просторима:\n
$1\n
Претрага за $3 $9",
	'lucenefallback'        => "Дошло је до проблема у вики претрази...
Ово је вероватно привремено; покушајте поново након неколико момената,
или претражите вики преко неког од спољних претраживачких сервиса:\n"
);
$wgLuceneSearchMessages['wa'] = array(
	'searchnumber'          => '<strong>Rizultats: $1-$2 di $3</strong>',
	'searchprev'            => '← <span style=\'font-size: small\'>Div.</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Shuv.</span> →',
	'searchnoresults'       => 'Mande escuzes, mins i gn a rén ki corespond.',
	'searchnearmatches'     => '<b>Les pådjes shuvantes ont des tites ki ravizèt çou k\' vos avoz cwerou:</b>',
	'lucenepowersearchtext' => 'Cweraedje dins les espåces di lomaedje: $1 <br />
Cweraedje di: $3 $9',
);
?>
