<?php
/**
 * Internationalisation file for IndexFunction extension.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'indexfunc-desc' => 'Parser function to create automatic redirects and disambiguation pages',

	'indexfunc-badtitle' => 'Invalid title: "$1"',
	'indexfunc-editwarning' => 'Warning:
This title is an index title for the following {{PLURAL:$2|page|pages}}:
$1
Be sure the page you are about to create does not already exist under a different title.
If you create this page, remove this title from the <nowiki>{{#index:}}</nowiki> on the above {{PLURAL:$2|page|pages}}.',
	'indexfunc-index-exists' => 'The page "$1" already exists',
	'indexfunc-movewarn' => 'Warning:
"$1" is an index title for the following {{PLURAL:$3|page|pages}}:
$2
Please remove "$1" from the <nowiki>{{#index:}}</nowiki> on the above {{PLURAL:$3|page|pages}}.',

	'index' => 'Index search',
	'index-legend' => 'Search the index',
	'index-search' => 'Search:',
	'index-submit' => 'Submit',
	'index-disambig-start' => "'''$1''' may refer to several pages:",
	'index-exclude-categories' => '', # List of categories to exclude from the auto-disambig pages
	'index-emptylist' => 'There are no pages associated with "$1"',
	'index-expand-detail' => 'Show pages indexed under this title',
	'index-hide-detail' => 'Hide the list of pages',
	'index-no-results' => 'The search returned no results',	
	'index-search-explain' => 'This page uses a prefix search. 
	
Type the first few characters and press the submit button to search for page titles and index entries that start with the search string',
	'index-details-explain' => 'Entries with arrows are index entries.
Click the arrow to show all pages indexed under that title.',
);

/** Message documentation (Message documentation)
 * @author Bennylin
 * @author Fryed-peach
 * @author Purodha
 * @author Raymond
 */
$messages['qqq'] = array(
	'indexfunc-desc' => '{{desc}}',
	'indexfunc-badtitle' => '{{Identical|Invalid title}}',
	'index' => 'This is either the name of the parser function, to be used inside the wiki code, or not used, if I got it right. --[[User:Purodha|Purodha Blissenbach]] 00:13, 15 July 2009 (UTC)
{{Identical|Index}}',
	'index-legend' => 'Used in [[Special:Index]].',
	'index-search' => '{{Identical|Search}}',
	'index-submit' => '{{Identical|Submit}}',
	'index-search-explain' => 'If your language permits, you can replace <code>submit</code> with <code>{<nowiki />{int:{{msg-mw|index-submit}}}}</code> for the button label.',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'indexfunc-badtitle' => 'Ongeldige bladsynaam: "$1"',
	'index' => 'Indeks',
	'index-legend' => 'Die indeks deursoek',
	'index-search' => 'Soek:',
	'index-submit' => 'OK',
);

/** Arabic (العربية)
 * @author OsamaK
 */
$messages['ar'] = array(
	'indexfunc-badtitle' => 'عنوان غير صالح: "$1"',
	'indexfunc-editwarning' => 'تحذير:
هذا العنوان عنوان فهرس {{PLURAL:$2||للصفحة التالية|للصفحتين التاليتين|للصفحات التالية}}:
$1
تأكد م أن الصفحة التي أنت بصدد إنشائها غير موجودة أصلًا تحت عنوان مختلف.
إذا أنشأت هذه الصفحة، فأزل هذا العنوان من <nowiki>{{#index:}}</nowiki> في {{PLURAL:$2||الصفحة|الصفحتين|الصفحات}} أعلاه.',
	'indexfunc-index-exists' => 'الصفحة "$1" موجودة بالفعل',
	'indexfunc-movewarn' => 'تحذير:
"$1" عنوان فهرس {{PLURAL:$3||للصفحة التالية|للصفحتين التاليتين|للصفحات التالية}}:
$2
من فضلك أزل "$1" من <nowiki>{{#index:}}</nowiki> في {{PLURAL:$2||الصفحة|الصفحتين|الصفحات}} أعلاه.',
	'index' => 'البحث في الفهرس',
	'index-legend' => 'ابحث في الفهرس',
	'index-search' => 'ابحث:',
	'index-submit' => 'أرسل',
	'index-disambig-start' => "'''$1''' يمكن أن يشير إلى صفحات عديدة:",
	'index-emptylist' => 'لا توجد أي صفحات مربوطة ب"$1"',
	'index-expand-detail' => 'أظهر الصفحات المفهرسة تحت هذا العنوان',
	'index-hide-detail' => 'أخفِ قائمة الصفحات',
	'index-no-results' => 'لم يرجع البحث بأي نتيجة',
	'index-search-explain' => 'تستخدم هذه الصفحة البحث ببادئة.

اطبع الحروف الأولى ثم انقر زر الإرسال للبحث عن عناوين الصفحات ومدخلات الفهرس التي تبدأ بعبارة البحث.',
	'index-details-explain' => 'المدخلات ذات الأسهم تمثل مدخلات فهرس.
انقر على السهم لعرض كل الصفحات المفهرسة تحت ذلك العنوان.',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'indexfunc-desc' => 'Функцыя парсэра для стварэньня аўтаматычных перанакіраваньняў і старонак неадназначнасьцяў',
	'indexfunc-badtitle' => 'Няслушная назва: «$1»',
	'indexfunc-editwarning' => 'Папярэджаньне: Гэтая назва зьяўляецца індэкснай для {{PLURAL:$2|наступнай старонкі|наступных старонак}}: $1
Упэўніцеся, што старонка, якую Вы зьбіраецеся стварыць, яшчэ не існуе зь іншай назвай.
Калі Вы створыце гэту старонку, выдаліце гэту назву з <nowiki>{{#index:}}</nowiki> у {{PLURAL:$2|наступнай старонцы|наступных старонках}}.',
	'indexfunc-index-exists' => 'Старонка «$1» ужо існуе',
	'indexfunc-movewarn' => 'Папярэджаньне: «$1» зьяўляецца індэкснай назвай для {{PLURAL:$3|наступнай старонкі|наступных старонак}}: $2
Калі ласка, выдаліце «$1» з <nowiki>{{#index:}}</nowiki> у {{PLURAL:$3|наступнай старонцы|наступных старонках}}.',
	'index' => 'Індэкс',
	'index-legend' => 'Пошук у індэксе',
	'index-search' => 'Пошук:',
	'index-submit' => 'Адправіць',
	'index-disambig-start' => "'''$1''' можа адносіцца да некалькіх старонак:",
	'index-emptylist' => 'Няма старонак зьвязаных з «$1»',
	'index-expand-detail' => 'Паказаць старонкі праіндэксаваныя пад гэтай назвай',
	'index-hide-detail' => 'Схаваць сьпіс старонак',
	'index-no-results' => 'Пошук не прынёс выніках',
	'index-search-explain' => 'Гэта старонка выкарыстоўвае прэфіксны пошук.

Увядзіце першыя некалькі сымбаляў і націсьніце кнопку для пошуку назваў старонак і індэксных запісаў, якія пачынаюцца з пошукавага радку',
	'index-details-explain' => 'Запісы са стрэлкамі зьяўляюцца індэкснымі, націсьніце на стрэлку для паказу ўсіх старонак праіндэксаваных пад гэтай назвай.',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'indexfunc-desc' => "Arc'hwel eus ar parser evit sevel pajennoù adkas ha diforc'hañ ent emgefre",
	'indexfunc-badtitle' => 'Titl direizh : "$1"',
	'indexfunc-editwarning' => "Diwallit :
Un titl meneger evit ar {{PLURAL:$2|bajenn|pajenn}}-mañ eo an titl-mañ :
$1
Gwiriit mat n'eo ket bet savet c'hoazh, gant un titl all, ar bajenn emaoc'h en sell da grouiñ.
Mar savit ar bajenn-mañ, tennit an titl eus ar <nowiki>{{#index:}}</nowiki> {{PLURAL:$2|bajenn|pajenn}} a-us.",
	'indexfunc-index-exists' => 'Bez\' ez eus eus ar bajenn "$1" c\'hoazh',
	'indexfunc-movewarn' => 'Diwallit :
Un titl meneger evit ar {{PLURAL:$3|bajenn |pajenn}} eo $1 :
$2
Tennit "$1" eus ar <nowiki>{{#index:}}</nowiki> {{PLURAL:$3|bajenn|pajenn}} a-us.',
	'index' => 'Meneger klask',
	'index-legend' => 'Klask er meneger',
	'index-search' => 'Klask :',
	'index-submit' => 'Kas',
	'index-disambig-start' => "Gallout a ra '''$1''' ober dave da meur a bajenn :",
	'index-emptylist' => 'N\'eus pajenn ebet liammet ouzh "$1"',
	'index-expand-detail' => 'Diskouez ar pajennoù menegeret dindan an titl-mañ',
	'index-hide-detail' => 'Kuzhat roll ar pajennoù',
	'index-no-results' => "N'eus bet kavet disoc'h ebet",
	'index-search-explain' => 'Ober a ra ar bajenn-mañ gant ur rakger klask.

Merkit an nebeud arouezennoù kentañ ha pouezit war ar bouton klask evit kavout titloù ar pajennoù a grog gant an neudennad klask-se',
	'index-details-explain' => 'Monedoù meneger eo ar monedoù gant biroù. 
Klikit war ar bir evit gwelet an holl bajennoù menegeret dindan an titl-se.',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'indexfunc-desc' => 'Parserska funkcija za pravljenje automatskih preusmjerenja i čvor stranica',
	'indexfunc-badtitle' => 'Nevaljan naslov: "$1"',
	'indexfunc-editwarning' => 'Upozorenje: Ovaj naslov je naslov indeksa za {{PLURAL:$2|slijedeću stranicu|slijedeće stranice}}:
$1
Provjerite da stranica koju namjeravate napraviti ranije već ne postoji pod drugim naslovom.
Ako napravite ovu stranicu, uklonite ovaj naslov iz <nowiki>{{#index:}}</nowiki> sa {{PLURAL:$2|gornje stranice|gornjih stranica}}.',
	'indexfunc-index-exists' => 'Stranica "$1" već postoji',
	'indexfunc-movewarn' => 'Upozorenje: "$1" je naslov indeksa za {{PLURAL:$3|slijedeću stranicu|slijedeće stranice}}:
$2
Molimo uklonite "$1" iz <nowiki>{{#index:}}</nowiki> sa {{PLURAL:$3|gornje stranice|gornjih stranica}}.',
	'index' => 'Indeks',
	'index-legend' => 'Pretraživanje indeksa',
	'index-search' => 'Traži:',
	'index-submit' => 'Pošalji',
	'index-disambig-start' => "'''$1''' se može odnositi na nekoliko stranica:",
	'index-emptylist' => 'Nema stranica povezanih sa "$1"',
	'index-expand-detail' => 'Prikaži stranice koje su indeksirane pod tim naslovom',
	'index-hide-detail' => 'Sakrij spisak stranica',
	'index-no-results' => 'Pretraga nije dala rezultata',
	'index-search-explain' => 'Ova stranica koristi pretragu po prefiksima.

Upišite prvih par znakova i pritisnite dugme Pošalji za traženje naslova stranica i indeksiranih stavki koje počinju sa traženim izrazom',
	'index-details-explain' => 'Stavke sa strelicama su stavke indeksa.
Kliknite na strelicu za prikaz svih stranica indeksiranih pod tim naslovom.',
);

/** Catalan (Català)
 * @author Paucabot
 */
$messages['ca'] = array(
	'index-search' => 'Cerca:',
	'index-submit' => 'Envia',
	'index-hide-detail' => 'Oculta la llista de pàgines',
);

/** German (Deutsch)
 * @author Imre
 * @author MF-Warburg
 */
$messages['de'] = array(
	'indexfunc-badtitle' => 'Ungültiger Titel: „$1“',
	'indexfunc-index-exists' => 'Die Seite „$1“ ist bereits vorhanden',
	'index' => 'Indexsuche',
	'index-legend' => 'Den Index durchsuchen',
	'index-search' => 'Suche:',
	'index-submit' => 'Senden',
	'index-disambig-start' => "'''$1''' steht für:",
	'index-emptylist' => 'Es gibt keine Seiten, die mit „$1“ verbunden sind',
	'index-expand-detail' => 'Zeige Seiten, die unter diesem Titel indiziert sind',
	'index-hide-detail' => 'Seitenliste verstecken',
	'index-no-results' => 'Die Suche ergab keine Ergebnisse',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'indexfunc-desc' => 'Parserowa funkcija za napóranje awtomatiskich dalejpósrědnjenjow a bokow za rozjasnjenje zapśimjeśow',
	'indexfunc-badtitle' => 'Njepłaśiwy titel: "$1"',
	'indexfunc-editwarning' => 'Warnowanje: Toś ten titel jo indeksowy titel za {{PLURAL:$2|slědujucy bok|slědujucej boka|slědujuce boki|slědujuce boki}}: $1
Wobwěsć se, až bok, kótaryž coš napóraś, hyšći njeeksistěrujo pód drugim titelom.
Jolic napórajoš toś ten bok, wótpóraj toś ten titel z <nowiki>{{#index:}}</nowiki> na {{PLURAL:$2|górjejcnem boku|górjejcnyma bokoma|górjejcnych bokach|górjejcnych bokach}}.',
	'indexfunc-index-exists' => 'Bok "$1" južo eksistěrujo',
	'indexfunc-movewarn' => 'Warnowanje: "$2" jo indeksowy titel za {{PLURAL:$3|slědujucy bok|slědujucej boka|slědujuce boki|slědujuce boki}}: $2
Pšosym wótpóraj "$1" z <nowiki>{{#index:}}</nowiki> na {{PLURAL:$3|górjejcnem boku|górjejcnyma bokoma|górjejcnych bokach|górjejcnych bokach}}.',
	'index' => 'Indeksowe pytanje',
	'index-legend' => 'Indeks pśepytaś',
	'index-search' => 'Pytaś:',
	'index-submit' => 'Wótpósłaś',
	'index-disambig-start' => "'''$1''' móžo se na někotare boki póśěgnuś:",
	'index-emptylist' => 'Njejsu boki zwězane z "$1"',
	'index-expand-detail' => 'Boki, kótarež su pód toś tym titelom indicěrowane, pokazaś',
	'index-hide-detail' => 'Lisćinu bokow schowaś',
	'index-no-results' => 'Pytanje njejo wuslědki wrośiło',
	'index-search-explain' => 'Toś ten bok wužywa prefiksowe pytanje.

Zapódaj nejpjerwjej někotare znamuška a klikni tłocašk {{int:index-submit}}, aby titele bokow a indeksowe zapiski pytał, kótarež zachopinaju se z pytańskim wurazom',
	'index-details-explain' => 'Zapiski ze šypkami su indeksowe zapiski, klikni na šypku, aby wše boki pokazali, kótarež su pód tym titelom indicěrowane.',
);

/** Greek (Ελληνικά)
 * @author Consta
 * @author Omnipaedista
 * @author ZaDiak
 */
$messages['el'] = array(
	'indexfunc-badtitle' => 'Μη έγκυρος τίτλος: "$1"',
	'indexfunc-index-exists' => 'Η σελίδα "$1" υπάρχει ήδη',
	'index' => 'Δείκτης αναζήτησης',
	'index-legend' => 'Αναζήτηση στο ευρετήριο',
	'index-search' => 'Αναζήτηση:',
	'index-submit' => 'Καταχώρηση',
	'index-hide-detail' => 'Απόκρυψη της λίστας σελίδων',
	'index-no-results' => 'Η αναζήτηση δεν επέστρεψε αποτελέσματα',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'indexfunc-index-exists' => 'La paĝo "$1" jam ekzistas',
	'index' => 'Indeksa serĉo',
	'index-legend' => 'Serĉi la indekson',
	'index-search' => 'Serĉi',
	'index-submit' => 'Enmeti',
	'index-hide-detail' => 'Kaŝi la liston de paĝoj',
);

/** Spanish (Español)
 * @author Crazymadlover
 */
$messages['es'] = array(
	'indexfunc-desc' => 'Función analizadora para crear redirecciones y páginas de desambiguación',
	'indexfunc-badtitle' => 'Título inválido: "$1"',
	'indexfunc-index-exists' => 'La página "$1" ya existe',
	'index' => 'Índice',
	'index-legend' => 'Buscar el índice',
	'index-search' => 'Buscar:',
	'index-submit' => 'Enviar',
	'index-disambig-start' => "'''$1''' puede referir a varias páginas:",
	'index-emptylist' => 'No hay páginas asociadas con "$1"',
);

/** Basque (Euskara)
 * @author Kobazulo
 */
$messages['eu'] = array(
	'indexfunc-badtitle' => 'Izenburu baliogabea: "$1"',
	'index-search' => 'Bilatu:',
	'index-submit' => 'Bidali',
);

/** Finnish (Suomi)
 * @author Cimon Avaro
 * @author Crt
 */
$messages['fi'] = array(
	'index-search' => 'Etsi:',
	'index-submit' => 'Lähetä',
);

/** French (Français)
 * @author Crochet.david
 * @author IAlex
 */
$messages['fr'] = array(
	'indexfunc-desc' => "Fonction du parseur pour créer des pages de redirection et d'homonymie automatiquement",
	'indexfunc-badtitle' => 'Titre invalide : « $1»',
	'indexfunc-editwarning' => "Attention : ce titre est un titre d'index pour {{PLURAL:$2|la page suivante|les pages suivantes}} :
$1
Soyez sûr que la page que vous êtes sur le point de créer n'existe pas sous un autre titre.
Si vous créez cette page, retirez-là de <nowiki>{{#index:}}</nowiki> {{PLURAL:$2|de la page|des pages}} ci-dessus.",
	'indexfunc-index-exists' => 'La page « $1 » existe déjà',
	'indexfunc-movewarn' => "Attention : « $1 »  est un titre d'index pour {{PLURAL:$3|la page suivante|les pages suivantes}} :
$2
Enlevez « $1 » de <nowiki>{{#index:}}</nowiki> {{PLURAL:$3|de la page|des pages}} ci-dessus.",
	'index' => 'Index',
	'index-legend' => 'Rechercher dans l’index',
	'index-search' => 'Chercher:',
	'index-submit' => 'Envoyer',
	'index-disambig-start' => "'''$1''' peut se référer à plusieurs pages :",
	'index-emptylist' => 'Il n’y a pas de pages liées à « $1 »',
	'index-expand-detail' => 'Afficher les pages indexées sous ce titre',
	'index-hide-detail' => 'Masque la liste des pages',
	'index-no-results' => "La recherche n'a retourné aucun résultat",
	'index-search-explain' => 'Cette page utilise une recherche par préfixe.

Tapez les premiers caractères et pressez sur le bouton de soumission pour chercher les titres des pages qui débutent avec chaîne de recherche.',
	'index-details-explain' => "Les entrées avec des flèches sont des entrées d'index, cliquez sur la flèche pour voir toutes les pages indexées sous ce titre.",
);

/** Franco-Provençal (Arpetan)
 * @author Cedric31
 */
$messages['frp'] = array(
	'index-search' => 'Chèrchiér :',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'indexfunc-desc' => 'Funcións analíticas para crear redireccións automáticas e páxinas de homónimos',
	'indexfunc-badtitle' => 'Título inválido: "$1"',
	'indexfunc-editwarning' => 'Aviso: este título é un título de índice para {{PLURAL:$2|a seguinte páxina|as seguintes páxinas}}:
$1
Asegúrese de que a páxina que está a piques de crear aínda non foi creada cun título diferente.
Se crea esta páxina, elimine este título de <nowiki>{{#index:}}</nowiki> {{PLURAL:$2|na páxina de enriba|nas páxinas de enriba}}.',
	'indexfunc-index-exists' => 'A páxina "$1" xa existe',
	'indexfunc-movewarn' => 'Aviso: "$1" é un título de índice para {{PLURAL:$3|a seguinte páxina|as seguintes páxinas}}:
$2
Por favor, elimine "$1" de <nowiki>{{#index:}}</nowiki> {{PLURAL:$3|na páxina de enriba|nas páxinas de enriba}}.',
	'index' => 'Índice',
	'index-legend' => 'Procurar no índice',
	'index-search' => 'Procurar:',
	'index-submit' => 'Enviar',
	'index-disambig-start' => "'''$1''' pódese referir a varias páxinas:",
	'index-emptylist' => 'Non hai páxinas asociadas con "$1"',
	'index-expand-detail' => 'Mostrar as páxinas indexadas baixo este título',
	'index-hide-detail' => 'Agochar a lista de páxinas',
	'index-no-results' => 'A procura non devolveu resultados',
	'index-search-explain' => 'Esta páxina usa unha procura por prefixos.  

Insira os primeiros caracteres e prema o botón "Enviar" para buscar títulos de páxinas e entradas de índice que comezan coa secuencia de procura',
	'index-details-explain' => 'As entradas con frechas son entradas de índice.
Prema na frecha para mostrar todas as páxinas indexadas con ese título.',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'indexfunc-desc' => 'Parserfunktion go automatischi Wyterleitige un Begriffsklärige aalege',
	'indexfunc-badtitle' => 'Nit giltige Titel „$1“',
	'indexfunc-editwarning' => 'Warnig: Dää Titel isch e Verzeichnis-Titel fir die {{PLURAL:$2|Syte|Syte}}:
$1
Stell sicher, ass es d Syte, wu Du grad aaleisch, nonig unter eme andere Titel git.
Wänn Du die Syte aaleisch, no nimm dää Titel us em <nowiki>{{#index:}}</nowiki> uf dr obe ufgfierte  {{PLURAL:$2|Syte|Syte}} use.',
	'indexfunc-index-exists' => 'D Syte „$1“ git s scho.',
	'indexfunc-movewarn' => 'Warnig: „$1“ isch e Verzeichnis-Titel fir die {{PLURAL:$3|Syte|Syte}}:
$2
Bitte nimm „$1“ us em <nowiki>{{#index:}}</nowiki> uf dr obe ufgfierte  {{PLURAL:$3|Syte|Syte}} use.',
	'index' => 'Verzeichnis',
	'index-legend' => 'S Verzeichnis dursueche',
	'index-search' => 'Suech:',
	'index-submit' => 'Abschicke',
	'index-disambig-start' => "'''$1''' cha zue verschidene Syte ghere:",
	'index-emptylist' => 'S git kei Syte, wu zue „$1“ ghere',
	'index-expand-detail' => 'Syte aazeige, wu unter däm Titel ufglischtet sin',
	'index-hide-detail' => 'D Sytelischt verstecke',
	'index-no-results' => 'D Suechi het kei Ergebnis brocht',
	'index-search-explain' => 'Die Syte verwändet e Präfixsuechi.  

Tipp di erschte paar Buehcstabe yy un druck dr „Abschicke“-Chnopf go Sytetitel un Verzeichnisyytreg suech, wu mit däre Zeichechette aafange',
	'index-details-explain' => 'Yytreg mit Bege sin Verzeichnisyytreg.
Druck uf dr Boge go alli Syte aazeige, wu unter däm Titel ufglischtet sin.',
);

/** Hebrew (עברית)
 * @author Rotemliss
 * @author YaronSh
 */
$messages['he'] = array(
	'indexfunc-badtitle' => 'כותרת בלתי תקינה: "$1"',
	'indexfunc-index-exists' => 'הדף "$1" כבר קיים',
	'index' => 'חיפוש באינדקס',
	'index-legend' => 'חיפוש באינדקס',
	'index-search' => 'חיפוש:',
	'index-disambig-start' => "המונח '''$1''' עשוי להתייחס למספר דפים:",
	'index-hide-detail' => 'הסתרת רשימת הדפים',
	'index-no-results' => 'החיפוש לא החזיר תוצאות',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'indexfunc-desc' => 'Parserowa funkcija za wutworjenje awtomatiskich daleposrědkowanjow a rozjasnjenjow wjacezmyslnosćow',
	'indexfunc-badtitle' => 'Njepłaćiwy titul: "$1"',
	'indexfunc-editwarning' => 'Kedźbu: Tutón titul je indeksowy titul za {{PLURAL:$2|slědowacu stronu|slědowacej stronje|slědowace strony|slědowace strony}}: $1
Přeswědčče so, zo strona, kotruž chceš wutworić, pod druhim titulom hišće njeeksistuje.
Jeli tutu stronu wutworiš, wotstroń tutón titul z <nowiki>{{#index:}}</nowiki> na {{PLURAL:$2|hornjej stronje|hornimaj stronomaj|hornich stronach|hornich stronach}}.',
	'indexfunc-index-exists' => 'Strona "$1" hižo eksistuje',
	'indexfunc-movewarn' => 'Kedźbu: "$1" je indeksowy titul za {{PLURAL:$3|slědowacu stronu|slědowacej stronje|slědowace strony|slědowace strony}}: $2
Prošu wotstroń "$1" z <nowiki>{{#index:}}</nowiki> na {{PLURAL:$3|hornjej stronje|hornimaj stronomaj|hornich stronach|hornich stronach}}.',
	'index' => 'Indeks',
	'index-legend' => 'Indeks přepytać',
	'index-search' => 'Pytać:',
	'index-submit' => 'Wotpósłać',
	'index-disambig-start' => "'''$1''' móže so na wjacore strony poćahować:",
	'index-emptylist' => 'Njejsu strony, kotrež su z "$1" zwjazane.',
	'index-expand-detail' => 'Strony pokazać, kotrež su pod tutym titulom indikowane',
	'index-hide-detail' => 'Lisćinu stronow schować',
	'index-no-results' => 'Pytanje njeje žane wuslědki přinjesło',
	'index-search-explain' => 'Tuta strona prefiksowe pytanje wužiwa.

Zapodaj najprjedy někotre znamješka a klikń na tłóčatko {{int:index-submit}}, zo by titule stronow a indeksowe zapiski pytał, kotrež so z pytanskim tekstom započinaja',
	'index-details-explain' => 'Zapiski z šipkami su indeksowe zapiski, klikń na šipk, zo by wšě strony pokazał, kotrež su pod tym titulom indikowane.',
);

/** Hungarian (Magyar)
 * @author Glanthor Reviol
 */
$messages['hu'] = array(
	'index-search' => 'Keresés:',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'indexfunc-desc' => 'Function del analysator syntactic pro le creation automatic de redirectiones e paginas de disambiguation',
	'indexfunc-badtitle' => 'Titulo invalide: "$1"',
	'indexfunc-editwarning' => 'Attention: Iste titulo es un titulo de indice pro le sequente {{PLURAL:$2|pagina|paginas}}:
$1
Assecura te que le pagina que tu va crear non ja existe sub un altere titulo.
Si tu crea iste pagina, remove iste titulo del <nowiki>{{#index:}}</nowiki> in le {{PLURAL:$2|pagina|paginas}} ci supra.',
	'indexfunc-index-exists' => 'Le pagina "$1" ja existe',
	'indexfunc-movewarn' => 'Attention: Iste titulo es un titulo de indice pro le sequente {{PLURAL:$3|pagina|paginas}}:
$2
Per favor remove "$1" del <nowiki>{{#index:}}</nowiki> in le {{PLURAL:$3|pagina|paginas}} ci supra.',
	'index' => 'Indice',
	'index-legend' => 'Cercar in le indice',
	'index-search' => 'Cerca:',
	'index-submit' => 'Submitter',
	'index-disambig-start' => "'''$1''' pote referer a plure paginas:",
	'index-emptylist' => 'Il non ha paginas associate con "$1"',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 * @author Irwangatot
 */
$messages['id'] = array(
	'indexfunc-desc' => 'Fungsi parser untuk membuat pengalihan otomatis dan halaman disambiguasi',
	'indexfunc-badtitle' => 'Judul tidak sah: "$1"',
	'indexfunc-editwarning' => 'Peringatan:
Judul ini adalah judul indeks {{PLURAL:$2|halaman|halaman}} berikut :
$1  
Pastikan halaman yang akan Anda  buat tidak ada pada judul yang berbeda.  
Jika Anda membuat halaman ini, hapus halaman ini dari <nowiki>{{#index:}}</nowiki> di atas {{PLURAL:$2|halaman|halaman}}.',
	'indexfunc-index-exists' => 'Halaman "$1" sudah ada',
	'indexfunc-movewarn' => 'Peringatan:
"$1" adalah judul indeks {{PLURAL:$3|halaman|halaman}} berikut :  
$2
Hapus "$1" dari <nowiki>{{#index:}}</nowiki> di atas {{PLURAL:$3|halaman|halaman}}.',
	'index' => 'Indeks',
	'index-legend' => 'Cari di indeks',
	'index-search' => 'Cari:',
	'index-submit' => 'Kirim',
	'index-disambig-start' => "'''$1''' dapat mengacu kepada:",
	'index-emptylist' => 'Tidah ada halaman yang berhubungan dengan "$1"',
	'index-expand-detail' => 'Lihat indek halaman dibawah judul ini',
	'index-hide-detail' => 'Sembunyikan daftar halaman',
	'index-no-results' => 'Pencarian, tidak ada hasil',
	'index-search-explain' => 'Halaman ini menggunakan pencarian prefix.

ketikan beberapa karakter pertama dan tekan tombol kirim untuk mencari judul halaman dan masukan indek yang dimulai dengan kata pencarian',
	'index-details-explain' => 'Masukan dengan panah adalah masukan indek.
Clik panah untuk melihat semua halaman indek dibawah judul itu.',
);

/** Italian (Italiano)
 * @author Darth Kule
 */
$messages['it'] = array(
	'indexfunc-desc' => 'Funzione del parser per creare redirect automatici e pagine di disambiguazione',
	'indexfunc-badtitle' => 'Titolo non valido: "$1"',
	'indexfunc-editwarning' => 'Attenzione: questo titolo è il titolo di un indice per {{PLURAL:$2|la seguente pagina|le seguenti pagine}}: $1. Assicurasi che la pagina che si sta per creare non esista già con un altro titolo.
Se si crea questa pagina, rimuovere questo titolo dal <nowiki>{{#index:}}</nowiki> {{PLURAL:$2|nella pagina precedente|nelle pagine precedenti}}.',
	'indexfunc-index-exists' => 'La pagina "$1" esiste già',
	'indexfunc-movewarn' => 'Attenzione: "$1" è un titolo di un indice per {{PLURAL:$3|la seguente pagina|le seguenti pagine}}: $2. Rimuovere "$1" dal <nowiki>{{#index:}}</nowiki> {{PLURAL:$2|nella pagina precedente|nelle pagine precedenti}}.',
	'index-legend' => "Cerca l'indice",
	'index-search' => 'Ricerca:',
	'index-submit' => 'Invia',
	'index-disambig-start' => "'''$1''' può riferirsi a più pagine:",
	'index-emptylist' => 'Non ci sono pagine associate con "$1"',
	'index-expand-detail' => 'Visualizza le pagine indicizzate sotto questo titolo',
	'index-hide-detail' => "Nascondi l'elenco delle pagine",
	'index-no-results' => 'La ricerca non ha restituito risultati',
	'index-search-explain' => 'Questa pagina utilizza una ricerca per prefissi.

Digitare i primi caratteri e premere il pulsante Invia per la ricerca di titoli di pagine e voci che iniziano con la stringa di ricerca',
	'index-details-explain' => "Le voci con le frecce sono voci dell'indice.
Fare clic sulla freccia per visualizzare tutte le pagine indicizzate sotto quel titolo.",
);

/** Japanese (日本語)
 * @author Fryed-peach
 * @author Hosiryuhosi
 * @author 青子守歌
 */
$messages['ja'] = array(
	'indexfunc-desc' => '自動的なリダイレクトや曖昧さ回避ページを作成するためのパーサー関数',
	'indexfunc-badtitle' => '不正なページ名:「$1」',
	'indexfunc-editwarning' => '警告: このページ名は以下の{{PLURAL:$2|ページ}}用の索引名となっています。
$1
あなたが作成しようとしているページが既に別の名前で存在していないことを確認してください。
このページを作成する場合、前掲の{{PLURAL:$2|ページ}}内の <nowiki>{{#index:}}</nowiki> からこのページ名を除去してください。',
	'indexfunc-index-exists' => 'ページ「$1」は既に存在します。',
	'indexfunc-movewarn' => '警告: 「$1」は以下の{{PLURAL:$3|ページ}}の索引名となっています。
$2
前掲の{{PLURAL:$3|ページ}}内の <nowiki>{{#index:}}</nowiki> から「$1」を除去してください。',
	'index' => '索引検索',
	'index-legend' => '索引の検索',
	'index-search' => '検索:',
	'index-submit' => '送信',
	'index-disambig-start' => "「'''$1'''」はいくつかのページを指す可能性があります:",
	'index-emptylist' => '「$1」と関連付けられたページはありません',
	'index-expand-detail' => 'この名前で索引付けされたページを表示する',
	'index-hide-detail' => 'ページの一覧を表示しない',
	'index-no-results' => '検索結果はありません',
	'index-search-explain' => 'このページは前方一致検索を用います。

先頭の数文字を入力して送信ボタンを押すと、検索文字列から始まるページ名および索引項目を探します。',
	'index-details-explain' => '矢印の付いた項目は索引項目で、矢印をクリックするとその名前で索引に載っているすべてのページを表示します。',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'indexfunc-desc' => 'Paaserfunxjuhn för Ömleijdunge un „Watt ėßß datt?“-Sigge automattesch jemaat ze krijje.',
	'indexfunc-badtitle' => '„$1“ es ene onjöltije Sigge-Tittel.',
	'indexfunc-editwarning' => 'Opjepaß: Dat es ene Tittel för en automattesche Ömleijdung udder „Watt ėßß datt?“-Sigg, för heh di {{PLURAL:$2|Sigg |Sigge|Fähler!}}:
$1
Bes sescher, dat et di Sigg, di De aanlääje wells, anderswoh nit ald onger enem andere Tittel jitt.
Wann De heh di Sigg aanlääje wells, dann nemm op dä {{PLURAL:$2|Sigg|Sigge|Fähler}} bovve heh dä Tittel uß däm <nowiki>{{#index:}}</nowiki> eruß!',
	'indexfunc-index-exists' => 'Di Sigg „$1“ jitt et ald.',
	'indexfunc-movewarn' => 'Opjepaß: „$1“ es ene Tittel för en automattesche Ömleijdung udder „Watt ėßß datt?“-Sigg, för heh di {{PLURAL:$3|Sigg |Sigge|Fähler!}}:
$2
Nemm op dä {{PLURAL:$3|Sigg|Sigge|Fähler}} dat „$1“ uß däm <nowiki>{{#index:}}</nowiki> eruß!',
	'index' => 'index',
	'index-legend' => 'Donn en de automatesche Ömleidunge un „Watt ėßß datt?“-Leßte söhke',
	'index-search' => 'Söhk noh:',
	'index-submit' => 'Lohß Jonn!',
	'index-disambig-start' => "Dä Tittel '''$1''' deiht op ongerscheidlijje Sigge paße:",
	'index-emptylist' => 'Mer han kein Sigge, di met „$1“ verbonge wöhre.',
	'index-expand-detail' => 'Zeijsch all di automattesche Ömleijdunge un automattesche „Watt ėßß datt?“-Sigge onger däm Tittel',
	'index-hide-detail' => 'Donn de Sigge-Leß vershteishe',
	'index-no-results' => 'Bei däm Söke es nix eruß jekumme',
	'index-search-explain' => 'Heh di Sigg beedt et Söhke noh Aanfäng.

Donn de eezte pa Bochshtave udder Zeijsche tippe, un donn dann dä Knopp „{{int:index-submit}}“ dröcke, öm noh Sigge ier Tittelle un noh Endrääsch för automattesche Ömleijdunge un automattesche „Watt ėßß datt?“-Sigge ze söhke, di met jenou dä Bochshtave udder Zeijsche aanfange.',
	'index-details-explain' => 'Endrääsch met piele en för för automattesche Ömleijdunge un automattesche „Watt ėßß datt?“-Sigge. Donn op dä Piel klecke, öm all di automattesche Ömleijdunge un automattesche „Watt ėßß datt?“-Sigge jezeijsch ze krijje, di dä Tittel han.',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'indexfunc-desc' => 'Parser-Fonctioun fir Viruleedungen an Homonymie-Säiten automatesch unzeleeën',
	'indexfunc-badtitle' => 'Net valabelen Titel: "$1"',
	'indexfunc-index-exists' => 'D\'Säit "$1" gëtt et schonn',
	'index' => 'Index',
	'index-legend' => 'Am Index sichen',
	'index-search' => 'Sichen:',
	'index-submit' => 'Schécken',
	'index-disambig-start' => "'''$1''' ka sech op méi Säite bezéien:",
	'index-emptylist' => 'Et gëtt keng Säiten déi mat "$1" assoziéiert sinn',
	'index-expand-detail' => 'Déi Säite weisen déi ënner dësem Titel indexéiert sinn',
	'index-hide-detail' => "D'Lëscht vu Säite verstoppen",
	'index-no-results' => "D'Sich hat keng Resultater",
	'index-search-explain' => 'Dës Säit benotzt Prefix-Sich.

Tippt déi éischt Buchstawen an dréckt op de {{int:index-submit ("Schécken")}} Knäppchen fir no Säitentitelen ze sichen déi mat dem ufänken wat Dir aginn hutt.',
	'index-details-explain' => "D'Donnéeë mat Feiler sinn Index-Donnéeën.
Klickt op de Feil fir all Säiten ze gesinn déi ënner deem Titel indexéiert sinn.",
);

/** Mongolian (Монгол)
 * @author Chinneeb
 */
$messages['mn'] = array(
	'index-search' => 'Хайх:',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'indexfunc-desc' => "Parserfunctie om automatisch doorverwijzingen en doorverwijspagina's aan te maken",
	'indexfunc-badtitle' => 'Ongeldige paginanaam: "$1"',
	'indexfunc-editwarning' => "Waarschuwing: deze pagina is een indexpagina voor de volgende {{PLURAL:$2|pagina|pagina's}}:
$1
Zorg ervoor dat de pagina die u wilt aanmaken niet al bestaat onder een andere naam.
Als u deze pagina aanmaakt, verwijder deze dan uit de <nowiki>{{#index:}}</nowiki> in de bovenstaande {{PLURAL:$2|pagina|pagina's}}.",
	'indexfunc-index-exists' => 'De pagina "$1" bestaat al',
	'indexfunc-movewarn' => 'Waarschuwing: "$1" is een indexpagina voor de volgende {{PLURAL:$3|pagina|pagina\'s}}:
$2
Verwijder "$1" uit de <nowiki>{{#index:}}</nowiki> op de bovenstaande {{PLURAL:$3|pagina|pagina\'s}}.',
	'index' => 'Index',
	'index-legend' => 'De index doorzoeken',
	'index-search' => 'Zoeken:',
	'index-submit' => 'OK',
	'index-disambig-start' => "'''$1''' kan verwijzen naar meerdere pagina's:",
	'index-emptylist' => 'Er zijn geen pagina\'s geassocieerd met "$1"',
	'index-expand-detail' => "Onder deze naam geïndexeerde pagina's weergeven",
	'index-hide-detail' => "Lijst met pagina' verbergen",
	'index-no-results' => 'De zoekopdracht heeft geen resultaten opgeleverd',
	'index-search-explain' => 'Deze pagina maakt gebruik van zoeken op voorvoegsel.

Voer de eerste paar letters in en druk op de verzendknop om te zoeken naar paginanamen en trefwoorden die beginnen met de opgegeven zoekreeks',
	'index-details-explain' => "Trefwoorden met pijlen komen uit de index.
Klik op de pijl om alle onder die paginaam geïndexeerde pagina's weer te geven.",
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'indexfunc-desc' => "Foncion del parser per crear de paginas de redireccion e d'omonimia automaticament",
	'indexfunc-badtitle' => 'Títol invalid : « $1»',
	'indexfunc-editwarning' => "Atencion : aqueste títol es un títol d'indèx per {{PLURAL:$2|la pagina seguenta|las paginas seguentas}} :
$1
Siatz segur(a) que la pagina que sètz a mand de crear existís pas jos un autre títol.
Se creatz aquesta pagina, levatz-la de <nowiki>{{#index:}}</nowiki> {{PLURAL:$2|de la pagina|de las paginas}} çaisús.",
	'indexfunc-index-exists' => 'La pagina « $1 » existís ja',
	'indexfunc-movewarn' => "Atencion : « $1 »  es un títol d'indèx per {{PLURAL:$3|la pagina seguenta|las paginas seguentas}} :
$2
Levatz « $1 » de <nowiki>{{#index:}}</nowiki> {{PLURAL:$3|de la pagina|de las paginas}} çaisús.",
	'index' => 'Indèx',
	'index-legend' => 'Recercar dins l’indèx',
	'index-search' => 'Cercar :',
	'index-submit' => 'Mandar',
	'index-disambig-start' => "'''$1''' se pòt referir a mai d'una pagina :",
	'index-emptylist' => 'I a pas de paginas ligadas a « $1 »',
	'index-expand-detail' => 'Afichar las paginas indexadas jos aqueste títol',
	'index-hide-detail' => 'Amaga la lista de las paginas',
	'index-no-results' => 'La recèrca a pas tornat cap de resultat',
	'index-search-explain' => 'Aquesta pagina utiliza una recèrca per prefix.

Picatz los primièrs caractèrs e quichatz sul boton de somission per cercar los títols de las paginas que començan amb la cadena de recèrca.',
	'index-details-explain' => "Las entradas amb de sagetas son d'entradas d'indèx, clicatz sus la sageta per veire totas las paginas indexadas jos aqueste títol.",
);

/** Romanian (Română)
 * @author Firilacroco
 * @author KlaudiuMihaila
 */
$messages['ro'] = array(
	'indexfunc-badtitle' => 'Titlu invalid: "$1"',
	'indexfunc-index-exists' => 'Pagina "$1" există deja',
	'index-search' => 'Căutare:',
	'index-disambig-start' => "'''$1''' se poate referi la mai multe pagini:",
	'index-emptylist' => 'Nu există pagini asociate cu "$1"',
	'index-expand-detail' => 'Arată paginile indexate sub acest titlu',
	'index-hide-detail' => 'Ascunde lista paginilor',
	'index-no-results' => 'Căutarea nu a returnat rezultate',
);

/** Russian (Русский)
 * @author Ferrer
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'indexfunc-desc' => 'Функция парсера для создания автоматических перенаправлений и страниц неоднозначностей',
	'indexfunc-badtitle' => 'Ошибочный заголовок «$1»',
	'indexfunc-editwarning' => 'Предупреждение. Это название является индексным для {{PLURAL:$2|следующей страницы|следующих страниц}}:
$1
Убедитесь, что страница, которую вы собираетесь создать, не существует под другим названием.
Если вы создаёте эту страницу, удалите это название из <nowiki>{{#index:}}</nowiki> на {{PLURAL:$2|указанной выше странице|указанных выше страницах}}.',
	'indexfunc-index-exists' => 'Страница «[[$1]]» уже существует',
	'indexfunc-movewarn' => 'Предупреждение. «$1» является индексным названием для {{PLURAL:$3|следующей страницы|следующих страниц}}:
$2
Пожалуйста, удалите «$1» из <nowiki>{{#index:}}</nowiki> на {{PLURAL:$2|указанной выше странице|указанных выше страницах}}.',
	'index' => 'Индекс',
	'index-legend' => 'Поиск по индексу',
	'index-search' => 'Поиск:',
	'index-submit' => 'Отправить',
	'index-disambig-start' => "'''$1''' может относиться к нескольким страницам:",
	'index-emptylist' => 'Нет страниц, связанных с «$1»',
	'index-expand-detail' => 'Показать страницы, проиндексированные под этим заголовком',
	'index-hide-detail' => 'Скрыть список страниц',
	'index-no-results' => 'Поиск не дал результатов',
	'index-search-explain' => 'Эта страница осуществляет префиксный поиск.

Введите несколько первых символов и нажмите кнопку отправки запроса, чтобы осуществить поиск по заголовкам страниц и индексным записям, начинающимся с заданной строки',
	'index-details-explain' => 'Элементы со стрелками являются индексными записями, нажмите на стрелку, чтобы показать все страницы, проиндексированные в соответствии с этим названием.',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'indexfunc-desc' => 'Funkcia syntaktického analyzátora na automatickú tvorbu presmerovaní a rozlišovacích stránok',
	'indexfunc-badtitle' => 'Neplatný názov: „$1“',
	'indexfunc-editwarning' => 'Upozornenie: Tento názov je indexový názov {{PLURAL:$2|nasledovnej stránky|nasledovných stránok}}:
$1
Uistite sa, že stránka, ktorú sa chystáte vytvoriť už neexistuje pod iným názvom.
Ak túto stránku vytvoríte, odstráňte jej názov z <nowiki>{{#index:}}</nowiki> v hore {{PLURAL:$2|uvedenej stránke|uvedených stránkach}}.',
	'indexfunc-index-exists' => 'Stránka „$1“ už existuje',
	'indexfunc-movewarn' => 'Upozornenie: „$1“ je indexový názov {{PLURAL:$3|nasledovnej stránky|nasledovných stránok}}:
$2
Prosím, odstráňte „$1“ z <nowiki>{{#index:}}</nowiki> v hore {{PLURAL:$3|uvedenej stránke|uvedených stránkach}}.',
	'index' => 'Index',
	'index-legend' => 'Hľadať v indexe',
	'index-search' => 'Hľadať:',
	'index-submit' => 'Odoslať',
	'index-disambig-start' => "'''$1''' môže odkazovať na niekoľko stránok:",
	'index-emptylist' => 'S „$1“ nesúvisia žiadne stránky',
	'index-expand-detail' => 'Zobraziť stránky indexované pod týmto názvom',
	'index-hide-detail' => 'Skryť zoznam stránok',
	'index-no-results' => 'Hľadanie nevrátilo žiadne výsledky',
	'index-search-explain' => 'Táto stránka používa predponu vyhľadávania.

Napíšte niekoľko prvých znakov a stlačte tlačidlo odoslať. Vyhľadajú sa názvy stránok a položky indexu začínajúce zadaným reťazcom.',
	'index-details-explain' => 'Položky s šípkami sú položky indexu.
Po kliknutí na šípku sa zobrazia všetky stránky indexované pod daným názvom.',
);

/** Swedish (Svenska)
 * @author Rotsee
 */
$messages['sv'] = array(
	'indexfunc-desc' => 'Parser-funktion för att skapa automatiska omdirigeringar och förgreningssidor',
	'indexfunc-badtitle' => 'Ogiltig titel: "$1"',
	'indexfunc-editwarning' => 'Varning:
Den här titeln används som innehållsförteckningstitel för följande {{PLURAL:$2|sida|sidor}}:
$1
Försäkra dig om att sida du försöker skapa inte redan finns under en annan titel.
Om du skapar den här sidan, ta bort den här titeln från <nowiki>{{#index:}}</nowiki> {{PLURAL:$2|sidan|sidorna}} ovan.',
	'indexfunc-index-exists' => 'Sidan "$1" finns redan',
	'indexfunc-movewarn' => 'Varning:
"$1" är en innehållsförteckningstitel för följande {{PLURAL:$3|sida|sidor}}:
$2
Ta bort "$1" från <nowiki>{{#index:}}</nowiki> {{PLURAL:$3|sidan|sidorna}} ovan.',
	'index' => 'Sök',
	'index-legend' => 'Sök i innehållsförteckningen',
	'index-search' => 'Sök:',
	'index-submit' => 'Skicka',
	'index-disambig-start' => "'''$1''' kan syfta på flera saker:",
	'index-emptylist' => 'Det finns inga sidor kopplade till "$1"',
	'index-expand-detail' => 'Visa sidor som listas under den här rubriken',
	'index-hide-detail' => 'Göm sidlistan',
	'index-no-results' => 'Inga träffar',
	'index-search-explain' => 'Den här sidan använder prefix-sökning.

Skriv några inledande tecken och klicka på {{int:index-submit ("<index-submit>")}} för att hitta sidor och stycken som inleds med din söksträng.',
	'index-details-explain' => 'Poster med pilar är innehållsförteckningar.
Klicka på pilen för att se hela innehållsförteckningen.',
);

/** Telugu (తెలుగు)
 * @author Kiranmayee
 */
$messages['te'] = array(
	'index-search' => 'వెతుకు:',
	'index-submit' => 'దాఖలుచెయ్యి',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Wrightbus
 */
$messages['zh-hant'] = array(
	'index-search' => '搜尋：',
	'index-submit' => '遞交',
	'index-hide-detail' => '隱藏頁面清單',
);

