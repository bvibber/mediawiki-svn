<?php
/**
 * Internationalisation file for LuceneSearch extension.
 *
 * @addtogroup Extensions
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
Search for $3 $9 $10",
	'lucenefallback'        => "There was a problem with the wiki search.
This is probably temporary; try again in a few moments,
or you can search the wiki through an external search service:\n",
	'searchexactcase'       => 'Exact-case search',
	'searchall'             => 'all',
	'searchincategory'      => 'incategory',
	'searchaliases'         => ""
);

$wgLuceneSearchMessages['af'] = array(
	'searchnumber'          => '<strong>Resultate $1-$2 van $3</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>Vorige</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Volgende</span> &#x00BB;',
	'searchscore'           => 'Relevansie: $1',
	'searchsize'            => '$1KG ($2 woorde)',
	'searchdidyoumean'      => 'Het u "<a href="$1">$2</a>" bedoel?',
	'searchnoresults'       => 'Jammer, daar was niks wat u navraag presies pas nie.',
	'searchnearmatches'     => '<b>Hierdie bladsye het titels soortgelyk aan u navraag:</b>',
	'lucenepowersearchtext' => 'Soek in naamruimtes:
<br />
$1
<br />Soek vir $3 $9',
);

$wgLuceneSearchMessages['ar'] = array(
	'searchnumber'          => '<strong>النتائج $1-$2 ل$3</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>السابق</span>',
	'searchnext'            => '<span style=\'font-size: small\'>التالي</span> &#x00BB;',
	'searchscore'           => 'الارتباط: $1',
	'searchsize'            => '$1 كيلوبايت ($2 كلمة)',
	'searchdidyoumean'      => 'هل تقصد: "<a href="$1">$2</a>"?',
	'searchnoresults'       => 'عذرا، لم توجد نتائج مطابقة لاستعلامك.',
	'searchnearmatches'     => '<b>هذه الصفحات لها عناوين مشابهة لاستعلامك:</b>',
	'lucenepowersearchtext' => 'ابحث في النطاقات:

$1

ابحث عن $3 $9',
	'lucenefallback'        => 'هناك خلل في نظام البحث في ويكي. غالبا ما تكون هذه مشكلة مؤقتة، بإمكانك أن تنتظر لفترة قليلة ثم تعاود المحاولة، و يمكن أن تبحث في الويكي باستخدام خدمة بحث خارجة:',
	'searchexactcase'       => 'بحث مطابق',
	'searchall'             => 'الكل',
	'searchincategory'      => 'في التصنيف',
);

$wgLuceneSearchMessages['bcl'] = array(
	'searchsize'            => '$1 KB ($2 mga tataramon)',
	'searchdidyoumean'      => 'An gusto mo: "<a href="$1">$2</a>"?',
	'searchall'             => 'gabos',
	'searchincategory'      => 'sakategorya',
);

$wgLuceneSearchMessages['ca'] = array(
	'searchnumber'          => '<strong>Resultats $1-$2 de $3</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>Anterior</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Següent</span> &#x00BB;',
	'searchscore'           => 'Rellevància: $1',
	'searchsize'            => '$1 KB ($2 paraules)',
	'searchdidyoumean'      => 'Potser volíeu dir «<a href="$1">$2</a>»?',
	'searchnoresults'       => 'Malauradament no s\'ha trobat cap resultat per aquesta cerca.',
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
Hledat $3 $9 $10",
	'lucenefallback'        => 'Při hledání došlo k chybě. Problém je pravděpodobně dočasný, zkuste hledání později, případně můžete vyzkoušet externí vyhledávač:\n'
);
$wgLuceneSearchMessages['de'] = array(
	'searchnumber'		=> "<strong>Ergebnisse $1–$2 von $3</strong>",
	'searchprev'            => "&larr; <span style='font-size: smaller'>Vorherige</span>",
	'searchnext'            => "<span style='font-size: smaller'>Nächste</span> &rarr;",
	'searchscore'           => "Relevanz: $1",
	'searchsize'            => "$1 kB ($2 Wörter)",
	'searchdidyoumean'      => "Meintest du „<a href=\"$1\">$2</a>“?",
	'searchnoresults'       => "Es wurden keine passenden Seiten für deine Suchanfrage gefunden.",
	'searchnearmatches'     => "<b>Diese Seiten haben zu der Suchanfrage ähnliche Titel:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "Suche in den Namensräumen $1<br />Suchbegriff: $3 $9 $10",
	'lucenefallback'        => "Bei der {{SITENAME}}-internen Suche ist ein Problem aufgetreten.
Dies ist normalerweise ein vorübergehendes Problem. Bitte versuche es nochmal.
Alternativ kannst du auch die externen Suchmöglichkeiten nutzen:\n",
	'searchexactcase'       => 'Exakte Groß-/Kleinschreibungssuche',
	'searchall'             => 'alle',
	'searchincategory'      => 'in Kategorie',

);
$wgLuceneSearchMessages['el'] = array(
	'lucenefallback'        => 'Υπήρξε κάποιο πρόβλημα με την αναζήτηση στο wiki. Αυτό είναι μάλλον προσωρινό. Προσπαθήστε ξανά αργότερα ή πραγματοποιήστε την αναζήτησή σας στο wiki μέσω μιας εξωτερικής υπηρεσίας αναζήτησης:',
);
$wgLuceneSearchMessages['eo'] = array(
	'searchnumber'          => '<strong>Rezultoj $1-$2 el $3</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>Antaŭa</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Sekvanta</span> &#x00BB;',
	'searchscore'           => 'Kongrueco: $1',
	'searchsize'            => '$1 kB ($2 vortoj)',
	'searchdidyoumean'      => 'Ĉu vi celis : "<a href="$1">$2</a>"?',
	'searchnoresults'       => 'Bedaŭrinde ne estas precize kongrua rezulto por via serĉo.',
	'searchnearmatches'     => '<b>Ĉi tiuj paĝoj havas titolojn similajn al via serĉo:</b>
',
	'lucenepowersearchtext' => '
Serĉo en nomspacoj:

$1

Serĉo de $3 $9 $10',
	'lucenefallback'        => 'Estis problemo kun la serĉilo de ĉi vikio.
Estas verŝajne nur portempa; bonvolu provi denove post iom da tempo
aŭ vi povas esplori la vikion per eksteraj serĉservoj.
',
);
$wgLuceneSearchMessages['es'] = array(
	'searchnumber'          => '<strong>Resultados $1-$2 de $3</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>Previo</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Siguiente</span> &#x00BB;',
	'searchscore'           => 'Relevancia: $1',
	'searchsize'            => '$1 KB ($2 palabras)',
	'searchdidyoumean'      => '¿Quisiste decir «<a href="$1">$2</a>»?',
	'searchnoresults'       => 'Lo sentimos, no hay ningún artículo con el texto buscado.',
	'searchnearmatches'     => '<b>Estas páginas tienen títulos similares a tu consulta:</b>',
	'lucenepowersearchtext' => 'Buscar sólo en los espacios de nombre:

$1

Buscar $3 $9',
	'lucenefallback'        => 'Ha habido un problema con la búsqueda.
Probablemente sea temporal. Intentelo de nuevo en unos minutos, 
o emplee algún buscador externo para explorar la wiki.',
	'searchexactcase'       => 'Búsqueda sensible a mayúsculas',
	'searchall'             => 'todos',
	'searchincategory'      => 'en categoría',
);

$wgLuceneSearchMessages['ext'] = array(
	'searchall'             => 'tó',
);

$wgLuceneSearchMessages['fi'] = array(
	'searchnumber'          => '<strong>Tulokset $1–$2 (yhteensä $3)</strong>',
	'searchprev'            => '← <span style=\'font-size: small\'>edelliset</span>',
	'searchnext'            => '<span style=\'font-size: small\'>seuraavat</span> →',
	'searchscore'           => 'Asiaankuuluvuus: $1',
	'searchsize'            => '$1 KiB ($2 sanaa)',
	'searchdidyoumean'      => 'Tarkoititko: <a href="$1">$2</a>',
	'searchnoresults'       => 'Tarkkoja osumia ei löytynyt.',
	'searchnearmatches'     => '<b>Vastaavannimisiä sivuja:</b>',
	'lucenepowersearchtext' => 'Etsi nimiavaruuksista

$1

Hakulauseke $3 $9 $10',
	'lucenefallback'        => 'Haku epäonnistui. Yritä hetken päästä uudelleen tai käytä ulkoisia hakukoneita:',
	'searchall'             => 'kaikki',
	'searchincategory'      => 'luokassa',
);
$wgLuceneSearchMessages['fo'] = array(
	'searchnoresults'       => 'Tíverri vóru ongar síður, ið luku tíni leitikrøv.',
	'lucenepowersearchtext' => 'Leita í navnaøki: $1 Leita eftur $3 $9',
);
$wgLuceneSearchMessages['fr'] = array(
	'searchnumber'          => '<strong>Résultats $1 – $2 sur $3</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>Préc.</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Suiv.</span> &#x00BB;',
	'searchscore'           => 'Pertinence : $1',
	'searchsize'            => '$1 ko ($2 mots)',
	'searchdidyoumean'      => 'Pensiez-vous à : « <a href="$1">$2</a> » ?',
	'searchnoresults'       => 'Désolé, il n’existe aucune correspondance exacte à votre requête.',
	'searchnearmatches'     => '<strong>Ces pages ont un titre similaire à votre requête.</strong>',
	'lucenepowersearchtext' => '
Rechercher dans les espaces : $1 <br/>
Texte à rechercher : $3 $9 $10',
	'lucenefallback'        => 'Un problème est survenu avec la recherche wiki. Ce souci est probablement temporaire ; merci de réessayer dans un instant ou d’utiliser un service de recherche externe.',
	'searchexactcase'       => 'Rechercher (sensible à la casse)',
	'searchall'             => 'Tous',
	'searchincategory'      => 'Chercher dans les catégories',
);
$wgLuceneSearchMessages['gl'] = array(
	'searchnumber'          => '<strong>Resultados $1-$2 de $3</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>Anterior</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Seguinte</span> &#x00BB;',
	'searchscore'           => 'Relevancia: $1',
	'searchsize'            => '$1 KB ($2 palabras)',
	'searchdidyoumean'      => 'Quixo dicir: "<a href="$1">$2</a>"?',
	'searchnoresults'       => 'Sentímolo, non hai coincidencias exactas para a súa procura.',
	'searchnearmatches'     => '<b>Estas páxinas teñen títulos semellantes aos da súa procura:</b>',
	'lucenepowersearchtext' => 'Procura en espazos de nomes:

$1

Procura para $3 $9 $10',
	'lucenefallback'        => 'Houbo un problema coa procura wiki.
É probabelmente temporal; ténteo de novo nunhos minutos,
ou vostede pode buscar o wiki a través dun servizo de procura externa:',
	'searchexactcase'       => 'Caso-exacto de procura',
	'searchall'             => 'todo',
	'searchincategory'      => 'na categoría',
);
$wgLuceneSearchMessages['hak'] = array(
	'searchnumber'          => '<strong>Khiung-yû $3 hong chhìm-chhut ke kiet-kó, yî-ha he thi $1-$2 hong ke kiet-kó</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>Song yit-hong</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Hâ yit-hong</span> &#x00BB;',
	'searchscore'           => 'Kôan-lièn-thu：$1',
	'searchsize'            => '$1KB ($2-ke sṳ)',
	'searchdidyoumean'      => 'He "<a href="$1">$2</a>" mâ?',
	'searchnoresults'       => 'Tui-put-hí, vù-nèn chhìm-cháu-to khi̍p ngì phit-phi ke chhà-chhìm.',
	'searchnearmatches'     => '<b>Yî-ha hong-mien lâu ngì chhà-chhìm ke nui-yùng yû siong-sṳ ke phêu-thì:</b>',
);
$wgLuceneSearchMessages['he'] = array(
	'searchnumber'          => "<strong>תוצאות $1-$2 מתוך $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>הקודם</span>",
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
חיפוש של $3 $9 $10",
	'lucenefallback'        => "יש בעיה עם מנוע הוויקי.
סביר להניח שהיא זמנית; אנא נסו שנית בעוד מספר דקות.
באפשרותכם גם לחפש בוויקי באמצעות שירותי חיפוש חיצוניים:\n",
	'searchexactcase'       => 'חיפוש תלוי־רישיות',
	'searchall'             => 'הכל',
	'searchincategory'      => 'בקטגוריה',
);

$wgLuceneSearchMessages['hsb'] = array(
	'searchnumber'          => '<strong>Wuslědki $1–$2 z $3</strong>',
	'searchprev'            => '&larr; <span style=\'font-size: smaller\'>Předchadny</span>',
	'searchnext'            => '<span style=\'font-size: smaller\'>Přichodny</span> &rarr;',
	'searchscore'           => 'Relewanca: $1',
	'searchsize'            => '$1 KB ($2 słowow)',
	'searchdidyoumean'      => 'Měnješe ty „<a href="$1">$2</a>“?',
	'searchnoresults'       => 'Bohužel njebuchu přihódne strony za twoje pytanske naprašowanje namakane.',
	'searchnearmatches'     => '<b>Tute strony maja k pytanskemu naprašowanju podobne titule:</b>',
	'lucenepowersearchtext' => 'W mjenowych rumach pytać: $1<br />Pytać za: $3 $9 $10',
	'lucenefallback'        => '<br /> ---- <br />
Je nachwilny problem z nutřkownej pytawu. Spytaj za někotre wokomiki hišće raz abo přepytuj Wikipediju z eksternej pytawu:',
	'searchexactcase'       => 'Wulko-/małopisanje při pytanju wobkedźbować',
	'searchall'             => 'wšě',
	'searchincategory'      => 'w kategoriji',
);

$wgLuceneSearchMessages['hu'] = array(
	'searchnumber'          => "<strong>$1-$2, összesen: $3 találat</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>előző</span>",
	'searchnext'            => "<span style='font-size: small'>következő</span> &#x00BB;",
	'searchscore'           => "Relevancia: $1",
	'searchsize'            => "$1KB ($2 szó)",
	'searchdidyoumean'      => "Erre gondoltál: \"<a href=\"$1\">$2</a>\"?",
	'searchnearmatches'     => "<b>Ezeknek a lapoknak hasonlít a címe a keresett kifejezésre:</b>",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "Keresés a névterekben:

$1

$3 $9 $10",
	'lucenefallback'        => "Hiba adódott a wiki keresés során.
A hiba átmeneti; próbáld újra néhány másodperc múlva vagy kereshetsz a wikin egy külső keresőszolgáltatáson keresztül is:"
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
Pencarian terhadap $3 $9 $10",
	'lucenefallback'        => "Ada masalah pada pencarian wiki.
Masalah ini mungkin hanya sementara; silakan coba lagi dalam beberapa saat,
atau gunakan layanan pencari eksternal:\n"
);

$wgLuceneSearchMessages['is'] = array(
	'searchdidyoumean'      => 'Áttirðu við: "<a href="$1">$2</a>"?',
	'lucenepowersearchtext' => 'Leita í eftirfarandi nafnrýmum:
$1
Leita að $3 $9',
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
Testo da ricercare $3 $9 $10",
	'lucenefallback'        => "Il motore di ricerca interno ha un problema. 
	Probabilmente si tratta di un errore temporaneo, destinato a risolversi in breve tempo. 
	Nel frattempo, si consiglia di riprovare tra qualche istante o di utilizzare un motore di ricerca esterno:\n"
);

$wgLuceneSearchMessages['ja'] = array(
	'searchnumber'          => '<strong>$3 件中 $1 - $2 件目</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>前へ</span>',
	'searchnext'            => '<span style=\'font-size: small\'>次へ</span> &#x00BB;',
	'searchscore'           => '関連性：$1',
	'searchsize'            => '$1 kB（$2語）',
	'searchdidyoumean'      => 'もしかして: "<a href="$1">$2</a>"',
	'searchnoresults'       => '該当するページが見つかりませんでした。',
	'searchnearmatches'     => '<b>タイトルが検索語に近い項目:</b>
',
	'lucenepowersearchtext' => '
検索する名前空間:

$1

検索語: $3 $9 $10',
	'lucenefallback'        => '検索中に一時的な問題が発生しました。しばらく経ってから再度検索するか、外部の検索サービスを使用してください:
',
);

$wgLuceneSearchMessages['kk-kz'] = array(
	'searchnumber'          => "<strong>$3 ішіндегі $1—$2 нәтиже</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Алдыңғыға</span>",
	'searchnext'            => "<span style='font-size: small'>Келесіге</span> &#x00BB;",
	'searchscore'           => "Сәйкестігі: $1",
	'searchsize'            => "$1 KB ($2 сөз)",
	'searchdidyoumean'      => "Мынау деп есепке алдыңыз ба: «<a href=\"$1\">$2</a>»?",
	'searchnoresults'       => "Ғафу етіңіз, сұранысынызға нақты сәйкесі бар еш нәтиже табылмады.",
	'searchnearmatches'     => "<b>Мына бет атауларында сұранысыңызға ұқсастығы бар:</b>\n",
	'searchnearmatch'       => "<li>$1</li><br />",
	'lucenepowersearchtext' => "
Мына есім аяларда іздеу:<br />
$1<br />
Іздестіру сұранысы: $3 $9 $10",
	'lucenefallback'        => "Мында «уики іздеу» дегенді пайдаланғанда шатақ шықты.
Бәлкім, бұл уақытша кедергі болды; біршама сәттен соң қайталаңыз,
немесе осы уикиден іздеу үшін сыртқы қызметтерін қолданыңыз:<br />",
	'searchexactcase'       => 'Бас-кіші әріпін ескеру',
	'searchall'             => 'барлығында',
	'searchincategory'      => 'санатта',
);

$wgLuceneSearchMessages['kk-tr'] = array(
	'searchnumber'          => "<strong>$3 işindegi $1—$2 nätïje</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Aldıñğığa</span>",
	'searchnext'            => "<span style='font-size: small'>Kelesige</span> &#x00BB;",
	'searchscore'           => "Säýkestigi: $1",
	'searchsize'            => "$1 KB ($2 söz)",
	'searchdidyoumean'      => "Mınaw dep esepke aldıñız ba: «<a href=\"$1\">$2</a>»?",
	'searchnoresults'       => "Ğafw etiñiz, suranısınızğa naqtı säýkesi bar eş nätïje tabılmadı.",
	'searchnearmatches'     => "<b>Mına bet atawlarında suranısıñızğa uqsastığı bar:</b>\n",
	'searchnearmatch'       => "<li>$1</li><br />",
	'lucenepowersearchtext' => "
Mına esim ayalarda izdew:<br />
$1<br />
İzdestirw suranısı: $3 $9 $10",
	'lucenefallback'        => "Mında «wïkï izdew» degendi paýdalanğanda şataq şıqtı.
Bälkim, bul waqıtşa kedergi boldı; birşama sätten soñ qaýtalañız,
nemese osı wïkïden izdew üşin sırtqı qızmetterin qoldanıñız:<br />",
	'searchexactcase'       => 'Bas-kişi äripin eskerw',
	'searchall'             => 'barlığında',
	'searchincategory'      => 'sanatta',
);

$wgLuceneSearchMessages['kk-cn'] = array(
	'searchnumber'          => "<strong>$3 ٸشٸندەگٸ $1—$2 نٵتيجە</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>الدىڭعىعا</span>",
	'searchnext'            => "<span style='font-size: small'>كەلەسٸگە</span> &#x00BB;",
	'searchscore'           => "سٵيكەستٸگٸ: $1",
	'searchsize'            => "$1 KB ($2 سٶز)",
	'searchdidyoumean'      => "مىناۋ دەپ ەسەپكە الدىڭىز با: «<a href=\"$1\">$2</a>»؟",
	'searchnoresults'       => "عافۋ ەتٸڭٸز, سۇرانىسىنىزعا ناقتى سٵيكەسٸ بار ەش نٵتيجە تابىلمادى.",
	'searchnearmatches'     => "<b>مىنا بەت اتاۋلارىندا سۇرانىسىڭىزعا ۇقساستىعى بار:</b>\n",
	'searchnearmatch'       => "<li>$1</li><br />",
	'lucenepowersearchtext' => "
مىنا ەسٸم ايالاردا ٸزدەۋ:<br />
$1<br />
ٸزدەستٸرۋ سۇرانىسى: $3 $9 $10",
	'lucenefallback'        => "مىندا «ۋيكي ٸزدەۋ» دەگەندٸ پايدالانعاندا شاتاق شىقتى.
بٵلكٸم, بۇل ۋاقىتشا كەدەرگٸ بولدى; بٸرشاما سٵتتەن سوڭ قايتالاڭىز,
نەمەسە وسى ۋيكيدەن ٸزدەۋ ٷشٸن سىرتقى قىزمەتتەرٸن قولدانىڭىز:<br />",
	'searchexactcase'       => 'باس-كٸشٸ ٵرٸپٸن ەسكەرۋ',
	'searchall'             => 'بارلىعىندا',
	'searchincategory'      => 'ساناتتا',
);

$wgLuceneSearchMessages['kk'] = $wgLuceneSearchMessages['kk-kz'];

$wgLuceneSearchMessages['lo'] = array(
	'searchdidyoumean'      => "ຫຼື ທ່ານໝາຍຄວາມວ່າ",
	'searchnoresults'       => "ຂໍໂທດຫຼາຍໆ, ບໍ່ມີ ບົດຄວາມ ທີ່ມີຊື່ກົງກັບສິ່ງທີ່ ທ່ານ ຊອກຫາແບບສົມບູນ.",
);
$wgLuceneSearchMessages['lt'] = array(
	'searchnumber'          => "<strong>Rezultatai $1-$2 iš $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Atgal</span>",
	'searchnext'            => "<span style='font-size: small'>Pirmyn</span> &#x00BB;",
	'searchscore'           => "Panašumas: $1",
	'searchsize'            => "$1 KB ($2 žodžiai)",
	'searchdidyoumean'      => "Galbūt norėjote: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Atsiprašome, jūsų užklausai nėra jokių tikslių atitikmenų.",
	'searchnearmatches'     => "<b>Šie puslapiai turi panašius pavadinimus į jūsų užklausą:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
Ieškoti vardų srityse:\n
$1\n
Ieškoma $3 $9 $10",
	'lucenefallback'        => "Buvo problemų su projekto paieška.
Tai turbūt laikina; pamėginkite šiek tiek vėliau,
arba galite mėginti ieškoti projekte per išorines paieškos paslaugas:\n"
);
$wgLuceneSearchMessages['nan'] = array(
	'searchnumber'          => "<strong>$3 hāng kiat-kó ê tē $1-$2 hāng</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Thâu-chêng</span>",
	'searchnext'            => "<span style='font-size: small'>Āu-piah</span> &#x00BB;",
	'searchscore'           => "Koan-liân-sèng: $1",
	'searchsize'            => "$1KB ($2 jī)",
	'searchnoresults'       => "Chhōe bô tùi-tâng ê bûn-jī.",
	'lucenepowersearchtext' => "Chhiau chia ê miâ-khong-kan:\n
$1\n
Chhōe $3 $9 $10",
	'lucenefallback'        => "Wiki ê chhiau-chhoē kong-lêng chhut būn-tê, chiām-sî bē iōng tit; chhiáⁿ liâm-mī koh chhì. Mā thang ēng goā-chām ê kiám-sek ho̍k-bū chhiau-chhoē khoàⁿ-māi:\n",
);
$wgLuceneSearchMessages['nds'] = array(
	'searchnumber'          => '<strong>Resultaten $1-$2 vun $3</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>Vörig</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Tokamen</span> &#x00BB;',
	'searchscore'           => 'Relevanz: $1',
	'searchsize'            => '$1 KB ($2 Wöör)',
	'searchdidyoumean'      => 'Hest du „<a href="$1">$2</a>“ meent?',
	'searchnoresults'       => 'Deit uns leed, gifft nix, wat akraat to dien Söökanfraag passt.',
	'searchnearmatches'     => '\'\'\'Disse Sieden hebbt Titels, de dien Söökanfraag ähnelt:\'\'\'',
	'lucenepowersearchtext' => 'Söken in Naamrüüm: $1 Söök na $3 $9 $10',
	'lucenefallback'        => 'Dat geev en Problem mit de Söök in dit Wiki. Dat is wohrschienlich blot för en Tiet; versöök dat later noch wedder oder söök mit en annere Söökmaschien in dat Wiki:',
	'searchexactcase'       => 'nipp un nau op Lütt- un Grootschrieven achten',
	'searchall'             => 'all',#identical but defined
	'searchincategory'      => 'inKategorie',
);
$wgLuceneSearchMessages['nl'] = array(
	'searchnumber'          => '<strong>Resultaten $1-$2 van de $3</strong>',
	'searchprev'            => '« <span style=\'font-size: small\'>Vorige</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Volgende</span> »',
	'searchscore'           => 'Relevantie: $1',
	'searchsize'            => '$1 KB ($2 woorden)',
	'searchdidyoumean'      => 'Bedoelde u: "<a href="$1">$2</a>"?',
	'searchnoresults'       => 'Sorry, uw zoekopdracht heeft geen resultaten opgeleverd.',
	'searchnearmatches'     => '<b>Deze paginanamen komen overeen met uw zoekopdracht:</b>',
	'lucenepowersearchtext' => '
Zoek in de volgende naamruimten:

$1

Zoek naar $3 $9 $10',
	'lucenefallback'        => 'Er is een storing in de wikizoekmachine.
Deze is waarschijnlijk tijdelijk van aard; probeer het over enige tijd opnieuw
of doorzoek de wiki via een externe zoekmachine:',
	'searchexactcase'       => 'Hoofdlettergevoelig zoeken',
	'searchall'             => 'alle',
	'searchincategory'      => 'binnen categorie',
);
$wgLuceneSearchMessages['no'] = array(
	'searchnumber'          => '<strong>Resultat $1–$2 av $3</strong>',
	'searchprev'            => '« <span style="font-size:small;">Forrige</span>',
	'searchnext'            => '<span style="font-size:small;">Neste</span> »',
	'searchscore'           => 'Relevans: $1',
	'searchsize'            => '$1&nbsp;kB ($2 ord)',
	'searchdidyoumean'      => 'Mente du: «<a href="$1">$2</a>»?',
	'searchnoresults'       => 'Beklager, det var ingen eksakte treff på ditt søk.',
	'searchnearmatches'     => '<b>Disse sidene har titler som ligner ditt søk:</b>',
	'lucenepowersearchtext' => 'Søk i navnerom:

$1

Søk etter $3 $9 $10',
	'lucenefallback'        => 'Det var et problem med søket. Dette er sannsynligvis midlertidig; prøv igjen om et øyeblikk, eller søk ved hjelp av en ekstern søketjeneste:',
);
$wgLuceneSearchMessages['oc'] = array(
	'searchnumber'          => '<strong>Resultats $1-$2 sus $3</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>Prec.</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Seg.</span> &#x00BB;',
	'searchscore'           => 'Pertinéncia : $1',
	'searchsize'            => '$1 ko ($2 mots)',
	'searchdidyoumean'      => 'Pensavetz a : « <a href="$1">$2</a> » ?',
	'searchnoresults'       => 'O planhem, existís pas cap de correspondéncia exacta a vòstra requèsta.',
	'searchnearmatches'     => '<strong>Aquestas paginas an un títol similar a vòstra requèsta.</strong>',
	'lucenepowersearchtext' => 'Recercar dins los espacis : $1<br />Tèxt de recercar : $3 $9',
	'lucenefallback'        => 'Un problèma es subrevengut amb la recèrca wiki. Aqueste problèma es probablament temporari ; mercé de tornar ensajar dins un moment o d’utilizar un servici de recèrca extèrna.',
);
$wgLuceneSearchMessages['pl'] = array(
	'searchnumber'          => '<strong>Wyniki $1-$2 z $3</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>Poprzednia</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Następna</span> &#x00BB;',
	'searchscore'           => 'Trafność: $1',
	'searchsize'            => '$1 KB ($2 słów)',
	'searchdidyoumean'      => 'Może chodziło Ci o "<a href="1">$2</a>"?',
	'searchnoresults'       => 'Niestety nie znaleziono stron pasujących do podanych kryteriów wyszukiwania.',
	'searchnearmatches'     => '<b>Strony o podobnych nazwach:</b>
',
	'lucenepowersearchtext' => '
Szukaj w przestrzeniach nazw:

$1

Szukana fraza $3 $9 $10',
	'lucenefallback'        => 'Wystąpił błąd z wyszukiwaniem w wiki.
Jest to tymczasowe; spróbuj ponownie za parę chwil
lub przeszukaj wiki za pomocą zewnętrznych wyszukiwarek:
',
	'searchexactcase'       => 'Szukanie czułe na wielkość znaków',
	'searchall'             => 'wszystkie',
	'searchincategory'      => 'w kategorii',
);
/* Piedmontese (Bèrto 'd Sèra) */
$wgLuceneSearchMessages['pms'] = array(
	'searchnumber'          => '<strong>Arzultà $1-$2 ëd $3</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>andré</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Anans</span> &#x00BB;',
	'searchscore'           => 'Arlevansa: $1',
	'searchsize'            => '$1 KB ($2 paròle)',
	'searchdidyoumean'      => 'Veul-lo dì: "<a href="$1">$2</a>"?',
	'searchnoresults'       => 'Darmagi, a l\'é pa trovasse un chèich-còs ch\'a fussa giust ëscarpì lòn ch\'a sërcava chiel/chila.',
	'searchnearmatches'     => '<b>Ste pàgine-sì a l\'han un tìtol ch\'a-j ësmija a lòn ch\'a sërca chiel/chila:</b>',
	'lucenepowersearchtext' => 'Sërchè ant jë spassi nominaj:

$1

Sërchè $3 $9',
	'lucenefallback'        => 'Ël motor d\'arsërca a l\'ha chèich gran-a.
A l\'é belfé ch\'a sia già butasse a pòst daspërchiel; ch\'a lassa passé na minuta e peuj ch\'a preuva torna,
ò pura ch\'a preuva a sërché ant la wiki ën dovrand un motor d\'arsërca estern:',
	'searchexactcase'       => 'Sërché con maiùscole e minùscole giuste',
	'searchall'             => 'tuti',
	'searchincategory'      => 'ant la categorìa',
);
$wgLuceneSearchMessages['pt'] = array(
	'searchnumber'          => '<strong>Resultados $1-$2 de $3</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>Anterior</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Posterior</span> &#x00BB;',
	'searchscore'           => 'Relevância: $1',
	'searchdidyoumean'      => 'Você quis dizer: "<a href="$1">$2</a>"?',
	'searchnoresults'       => 'Desculpe, mas não existem resultados exatos correspondentes à sua procura.',
	'searchnearmatches'     => '<b>As páginas a seguir possuem títulos similares com o que você pesquisou:</b>
',
		'lucenepowersearchtext' => '
Pesquisar nos espaços nominais:

$1

Pesquisar por $3 $9 $10',
	'lucenefallback'        => 'Há um problema com a busca neste wiki.
Provavelmente seja apenas uma falha temporária; tente novamente mais tarde,
ou procure por conteúdos neste wiki através de um mecanismo externo:
',
	'searchexactcase'       => 'Pesquisar esta grafia exata',
	'searchall'             => 'tudo',
	'searchincategory'      => 'na categoria',
	
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
Поисковый запрос $3 $9 $10",
	'lucenefallback'        => "Возникла проблема с поиском по вики.
Вероятно, эта временная проблема, попробуйте ещё раз чуть позже,
либо воспользуйтесь поиском во внешних поисковых системах.\n"
);
$wgLuceneSearchMessages['sk'] = array(
	'searchnumber'          => '<strong>Výsledky $1-$2 zo $3</strong>',
	'searchprev'            => '&#x00AB; <span style=\'font-size: small\'>Späť</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Ďalej</span> &#x00BB;',
	'searchscore'           => 'Relevantnosť: $1',
	'searchsize'            => '$1KB ($2 slov)',
	'searchdidyoumean'      => 'Mali ste na mysli: "<a href="$1">$2</a>"?',
	'searchnoresults'       => 'Ľutujeme, vyhľadávanie nevrátilo na vašu požiadavku žiadne presné výsledky.',
	'searchnearmatches'     => '<b>Tieto stránky majú názvy podobné vašej požiadavke:</b>',
	'lucenepowersearchtext' => 'Vyhľadávanie v menných priestoroch: $1 Hľadanie $3 $9',
	'lucenefallback'        => 'S vyhľadávaním na wiki nastal problém. Je možné, že je to dočasné; o chvíľu to skúste znova alebo vyhľadávajte na wiki pomocou externej indexovacej služby:',
	'searchexactcase'       => 'Vyhľadávanie zohľadňujúce veľkosť písmen',
	'searchall'             => 'všetko',
	'searchincategory'      => 'v kategórii',
);
$wgLuceneSearchMessages['sq'] = array(
	'lucenepowersearchtext' => 'Kërko në hapësirat: $1 për $3 $9',
	'searchnext'            => "<span style='font-size: small'>Më pas</span> &#x00BB;",
	'searchnoresults'       => 'Nuk u gjet asnjë përputhje. Për ndihmë rreth kërkimit ju lutem shikoni Kërkime.',
	'searchnumber'          => '<strong>Rezultatet $1-$2 nga $3</strong>',
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Më parë</span>",
	'searchscore'           => 'Përkatësia: $1',
	'searchsize'            => '$1KB ($2 fjalë)',
);
$wgLuceneSearchMessages['sr-ec'] = array(
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
Претрага за $3 $9 $10",
	'lucenefallback'        => "Дошло је до проблема у вики претрази...
Ово је вероватно привремено; покушајте поново након неколико момената,
или претражите вики преко неког од спољних претраживачких сервиса:\n",
	'searchexactcase'       => 'Тражи са капитализацијом',
	'searchall'             => "све\nsve",
	'searchincategory'      => "укатегорији\nukategoriji"
);
$wgLuceneSearchMessages['sr-el'] = array(
	'searchnumber'          => "<strong>Rezultati $1-$2 od $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Prethodna</span>",
	'searchnext'            => "<span style='font-size: small'>Sledeća</span> &#x00BB;",
	'searchscore'           => "Sličnost: $1",
	'searchsize'            => "$1 KB ($2 words)",
	'searchdidyoumean'      => "Da li ste mislili: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Izvinjavamo se, ne postoje rezultati za vaš upit.",
	'searchnearmatches'     => "<b>Sledeće stranice imaju slične nazive kao što ste tražili:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
Pretraga u imenskim prostorima:\n
$1\n
Pretraga za $3 $9 $10",
	'lucenefallback'        => "Došlo je do problema u viki pretrazi...
Ovo je verovatno privremeno; pokušajte ponovo nakon nekoliko momenata,
ili pretražite viki preko nekog od spoljnih pretraživačkih servisa:\n",
	'searchexactcase'       => 'Traži sa kapitalizacijom',
	'searchall'             => "све\nsve",
	'searchincategory'      => "укатегорији\nukategoriji"
);
$wgLuceneSearchMessages['sr'] = $wgLuceneSearchMessages['sr-ec'];
$wgLuceneSearchMessages['sv'] = array(
	'searchnumber'          => "<strong>Resultat $1-$2 av $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Föregående</span>",
	'searchnext'            => "<span style='font-size: small'>Nästa</span> &#x00BB;",
	'searchscore'           => "Relevans: $1",
	'searchsize'            => "$1 kbyte ($2 ord)",
	'searchdidyoumean'      => "Menade du: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Sökningen gav tyvärr inga exakta träffar.",
	'searchnearmatches'     => "<b>Följande sidor har titlar som liknar din sökning:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "Sök i följande namnrymder:\n
$1\n
Sök efter $3 $9 $10",
	'lucenefallback'        => "På grund av ett problem kunde inte sökningen utföras. 
Det var troligen bara något tillfälligt; försök igen om en liten stund,
eller sök på wikin med någon extern söktjänst:\n"
);
$wgLuceneSearchMessages['ur'] = array(
	'searchnumber'          => '<strong><font face="times new roman, urdu naskh asiatype">نـتـائـج : $3 کے $1 تـا $2 </strong></font>',
	'searchprev'            => 'پیچھے',
	'searchnext'            => 'آگے',
	'searchscore'           => 'مشابہت: $1',
	'searchsize'            => '$1کلوبائٹ ($2 الفاظ)',
	'searchnoresults'       => 'بہ تاسف، کوئی ایسا صفحہ نہیں مـلا جو آپکی مطلوبہ تلاش کے عین مطابق ہو۔',
	'lucenepowersearchtext' => 'تلاش کریں، فضاۓ نام : $1 میں براۓ $3 $9 $10',
);
$wgLuceneSearchMessages['wa'] = array(
	'searchnumber'          => '<strong>Rizultats: $1-$2 di $3</strong>',
	'searchprev'            => '← <span style=\'font-size: small\'>Div.</span>',
	'searchnext'            => '<span style=\'font-size: small\'>Shuv.</span> →',
	'searchnoresults'       => 'Mande escuzes, mins i gn a rén ki corespond.',
	'searchnearmatches'     => '<b>Les pådjes shuvantes ont des tites ki ravizèt çou k\' vos avoz cwerou:</b>',
	'lucenepowersearchtext' => 'Cweraedje dins les espåces di lomaedje: $1 <br />
Cweraedje di: $3 $9 $10',
);
$wgLuceneSearchMessages['yue'] = array(
	'searchnumber'          => "<strong>一共有$3項搜尋結果，以下係第$1-$2項結果</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>上一版</span>",
	'searchnext'            => "<span style='font-size: small'>下一版</span> &#x00BB;",
	'searchscore'           => "關聯度：$1",
	'searchsize'            => "$1KB ($2 個字)",
	'searchdidyoumean'      => "你係唔係搵\"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "對唔住，搵唔到同你匹配嘅查詢。",
	'searchnearmatches'     => "<b>以下頁面同你查詢嘅内容有相似嘅標題：</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
響空間名度搵：\n
$1\n
搜尋：$3 $9 $10",
	'lucenefallback'        => "Wiki搜尋出咗問題。呢個可能係暫時性嘅，請稍後再試。你亦都可以通過利用外部搜尋服務來去搵呢個wiki：\n",
	'searchall'             => '全部',
	'searchincategory'      => '響分類度'
);
$wgLuceneSearchMessages['zh-hans'] = array(
	'searchnumber'          => "<strong>共有$3项搜索结果，以下是第$1-$2项结果</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>上一页</span>",
	'searchnext'            => "<span style='font-size: small'>下一页</span> &#x00BB;",
	'searchscore'           => "关联度：$1",
	'searchsize'            => "$1KB ($2 个字)",
	'searchdidyoumean'      => "是\"<a href=\"$1\">$2</a>\"吗?",
	'searchnoresults'       => "对不起，找不到和您匹配的查询。",
	'searchnearmatches'     => "<b>以下页面与你查询的内容有相似的标题：</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
在名字空间中搜索：\n
$1\n
搜索：$3 $9 $10",
	'lucenefallback'        => "系统搜索发生错误。这可能是暂时性的，请稍后重试。你也可以通过使用外部搜索服务搜索本站：\n",
	'searchall'             => '所有',
	'searchincategory'      => '在分类中'
);
$wgLuceneSearchMessages['zh-hant'] = array(
	'searchnumber'          => "<strong>共有$3項搜尋結果，以下是第$1-$2項結果</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>上一頁</span>",
	'searchnext'            => "<span style='font-size: small'>下一頁</span> &#x00BB;",
	'searchscore'           => "關聯度：$1",
	'searchsize'            => "$1KB ($2 個字)",
	'searchdidyoumean'      => "是\"<a href=\"$1\">$2</a>\"嗎?",
	'searchnoresults'       => "對不起，找不到和您匹配的查詢。",
	'searchnearmatches'     => "<b>以下頁面與你查詢的内容有相似的標題：</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
在名字空間中搜尋：\n
$1\n
搜尋：$3 $9 $10",
	'lucenefallback'        => "系統搜尋發生錯誤。這可能是暫時性的，請稍後重試。你也可以通過使用外部搜尋服務搜尋本站：\n",
	'searchall'             => '所有',
	'searchincategory'      => '在分類中'
);

$wgLuceneSearchMessages['zh'] = $wgLuceneSearchMessages['zh-hans'];
$wgLuceneSearchMessages['zh-cn'] = $wgLuceneSearchMessages['zh-hans'];
$wgLuceneSearchMessages['zh-hk'] = $wgLuceneSearchMessages['zh-hant'];
$wgLuceneSearchMessages['zh-min-nan'] = $wgLuceneSearchMessages['nan'];
$wgLuceneSearchMessages['zh-sg'] = $wgLuceneSearchMessages['zh-hans'];
$wgLuceneSearchMessages['zh-tw'] = $wgLuceneSearchMessages['zh-hant'];
$wgLuceneSearchMessages['zh-yue'] = $wgLuceneSearchMessages['yue'];
