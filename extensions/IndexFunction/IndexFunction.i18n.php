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
	'indexfunc-editwarn' => 'Warning: This title is an index title for [[$1]].
Be sure the page you are about to create does not already exist under a different title.
If you create this page, remove this title from the <nowiki>{{#index:}}</nowiki> on $1.',
	'indexfunc-index-exists' => 'The page "$1" already exists',
	'indexfunc-index-taken' => '"$1" is already used as an index by "$2"',

	'index' => 'Index',
	'index-legend' => 'Search the index',
	'index-search' => 'Search:',
	'index-submit' => 'Submit',
	'index-disambig-start' => "'''$1''' may refer to several pages:",
	'index-exclude-categories' => '', # List of categories to exclude from the auto-disambig pages
	'index-missing-param' => 'This page cannot be used with no parameters',
	'index-emptylist' => 'There are no pages associated with "$1"',
);

/** Message documentation (Message documentation)
 * @author Fryed-peach
 * @author Purodha
 * @author Raymond
 */
$messages['qqq'] = array(
	'indexfunc-desc' => 'Short description of this extension, shown in [[Special:Version]]. Do not translate or change links or tag names.',
	'index' => 'This is either the name of the parser function, to be used inside the wiki code, or not used, if I got it right. --[[User:Purodha|Purodha Blissenbach]] 00:13, 15 July 2009 (UTC)',
	'index-legend' => 'Used in [[Special:Index]].',
	'index-search' => '{{Identical|Search}}',
	'index-submit' => '{{Identical|Submit}}',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'indexfunc-desc' => 'Функцыя парсэра для стварэньня аўтаматычных перанакіраваньняў і старонак неадназначнасьцяў',
	'indexfunc-badtitle' => 'Няслушная назва: «$1»',
	'indexfunc-editwarn' => 'Папярэджаньне: гэтая назва зьяўляецца індэкснай для [[$1]].
Упэўніцеся, што старонка, якую Вы спрабуеце стварыць, не існуе над іншай назвай.
Калі Вы стварыце гэтую старонку, выдаліце гэтую назву з <nowiki>{{#index:}}</nowiki> на $1.',
	'indexfunc-index-exists' => 'Старонка «$1» ужо існуе',
	'indexfunc-index-taken' => '«$1» ужо выкарыстоўваецца як індэкс для «$2»',
	'index' => 'Індэкс',
	'index-legend' => 'Пошук у індэксе',
	'index-search' => 'Пошук:',
	'index-submit' => 'Адправіць',
	'index-disambig-start' => "'''$1''' можа адносіцца да некалькіх старонак:",
	'index-missing-param' => 'Гэтая старонка ня можа выкарыстоўвацца без парамэтраў',
	'index-emptylist' => 'Няма старонак зьвязаных з «$1»',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'indexfunc-desc' => 'Parserska funkcija za pravljenje automatskih preusmjerenja i čvor stranica',
	'indexfunc-badtitle' => 'Nevaljan naslov: "$1"',
	'indexfunc-editwarn' => 'Upozorenje: Ovaj naslov je naslov indeksa za [[$1]].
Provjerite da li stranica koju namjeravate napraviti već ne postoji pod drugim naslovom.
Ako napravite ovu stranicu, uklonite ovaj naslov iz <nowiki>{{#index:}}</nowiki> na $1.',
	'indexfunc-index-exists' => 'Stranica "$1" već postoji',
	'indexfunc-index-taken' => '"$1" je već iskorišten kao indeks u "$2"',
	'index' => 'Indeks',
	'index-legend' => 'Pretraživanje indeksa',
	'index-search' => 'Traži:',
	'index-submit' => 'Pošalji',
	'index-disambig-start' => "'''$1''' se može odnositi na nekoliko stranica:",
	'index-missing-param' => 'Ova stranica ne može biti korištena bez parametara',
	'index-emptylist' => 'Nema stranica povezanih sa "$1"',
);

/** Spanish (Español)
 * @author Crazymadlover
 */
$messages['es'] = array(
	'indexfunc-desc' => 'Función analizadora para crear redirecciones y páginas de desambiguación',
	'indexfunc-badtitle' => 'Título inválido: "$1"',
	'indexfunc-index-exists' => 'La página "$1" ya existe',
	'indexfunc-index-taken' => '"$1" ya es usada como un índice por "$2"',
	'index' => 'Índice',
	'index-legend' => 'Buscar el índice',
	'index-search' => 'Buscar:',
	'index-submit' => 'Enviar',
	'index-disambig-start' => "'''$1''' puede referir a varias páginas:",
	'index-missing-param' => 'Esta página no puede ser usada sin parámetros',
	'index-emptylist' => 'No hay páginas asociadas con "$1"',
);

/** French (Français)
 * @author Crochet.david
 * @author IAlex
 */
$messages['fr'] = array(
	'indexfunc-desc' => "Fonction du parseur pour créer des pages de redirection et d'homonymie automatiquement",
	'indexfunc-badtitle' => 'Titre invalide : « $1»',
	'indexfunc-editwarn' => 'Attention : Ce titre est un titre d’index pour [[$1]].
Assurez-vous que la page que vous vous apprêtez à créer n’existe pas déjà sous un autre titre.
Si vous créez cette page, supprimez ce titre de la <nowiki>{{#index:}}</nowiki> sur $1.',
	'indexfunc-index-exists' => 'La page « $1 » existe déjà',
	'indexfunc-index-taken' => '« $1 » est déjà utilisé comme un index par « $2 »',
	'index' => 'Index',
	'index-legend' => 'Rechercher dans l’index',
	'index-search' => 'Chercher:',
	'index-submit' => 'Envoyer',
	'index-disambig-start' => "'''$1''' peut se référer à plusieurs pages :",
	'index-missing-param' => 'Cette page ne peut pas être utilisée sans paramètre',
	'index-emptylist' => 'Il n’y a pas de pages liées à « $1 »',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'indexfunc-desc' => 'Funcións analíticas para crear redireccións automáticas e páxinas de homónimos',
	'indexfunc-badtitle' => 'Título inválido: "$1"',
	'indexfunc-editwarn' => 'Aviso: este título é un título de índice para "[[$1]]".
Asegúrese de que a páxina que está a piques de crear aínda non existe cun título diferente.
Se crea esta páxina, elimine este título de <nowiki>{{#index:}}</nowiki> en "$1".',
	'indexfunc-index-exists' => 'A páxina "$1" xa existe',
	'indexfunc-index-taken' => '"$1" xa se usa como índice de "$2"',
	'index' => 'Índice',
	'index-legend' => 'Procurar no índice',
	'index-search' => 'Procurar:',
	'index-submit' => 'Enviar',
	'index-disambig-start' => "'''$1''' pódese referir a varias páxinas:",
	'index-missing-param' => 'Esta páxina non se pode usar sen parámetros',
	'index-emptylist' => 'Non hai páxinas asociadas con "$1"',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'indexfunc-desc' => 'Parserfunktion go automatischi Wyterleitige un Begriffsklärige aalege',
	'indexfunc-badtitle' => 'Nit giltige Titel „$1“',
	'indexfunc-editwarn' => 'Warnig: Dää Titel isch e Verzeichnis-Titel fir [[$1]].
Stell sicher, ass es die Syte, wu Du grad witt aalege, nit scho unter eme andere Titel git.
Wänn Du die Syte aaleisch, nimm dää Titel us em <nowiki>{{#index:}}</nowiki> uf $1 uuse.',
	'indexfunc-index-exists' => 'D Syte „$1“ git s scho.',
	'indexfunc-index-taken' => '„$1“ wird scho as Verzeichnis brucht dur „$2“',
	'index' => 'Verzeichnis',
	'index-legend' => 'S Verzeichnis dursueche',
	'index-search' => 'Suech:',
	'index-submit' => 'Abschicke',
	'index-disambig-start' => "'''$1''' cha zue verschidene Syte ghere:",
	'index-missing-param' => 'Die Syte cha nit brucht wäre, ohni ass Parameter aagee sin',
	'index-emptylist' => 'S git kei Syte, wu zue „$1“ ghere',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'indexfunc-desc' => 'Parserowa funkcija za wutworjenje awtomatiskich daleposrědkowanjow a rozjasnjenjow wjacezmyslnosćow',
	'indexfunc-badtitle' => 'Njepłaćiwy titul: "$1"',
	'indexfunc-editwarn' => 'Warnowanje: Tutón titul je indeksowy titul za [[$1]].
Přeswědč so, zo strona, kotruž chceš wutworić, pod druhim titulom njeeksistuje.
Jeli tutu stronu tworiš, wotstroń tutón titul z <nowiki>{{#index:}}</nowiki> na $1.',
	'indexfunc-index-exists' => 'Strona "$1" hižo eksistuje',
	'indexfunc-index-taken' => '"$1" so hižo wot "$2" jako indeks wužiwa',
	'index' => 'Indeks',
	'index-legend' => 'Indeks přepytać',
	'index-search' => 'Pytać:',
	'index-submit' => 'Wotpósłać',
	'index-disambig-start' => "'''$1''' móže so na wjacore strony poćahować:",
	'index-missing-param' => 'Tuta strona njeda so bjez parametrow wužiwać',
	'index-emptylist' => 'Njejsu strony, kotrež su z "$1" zwjazane.',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'indexfunc-desc' => 'Function del analysator syntactic pro le creation automatic de redirectiones e paginas de disambiguation',
	'indexfunc-badtitle' => 'Titulo invalide: "$1"',
	'indexfunc-editwarn' => 'Attention: Iste titulo es un titulo de indice pro [[$1]].
Assecura te que le pagina que tu va crear non ja existe sub un altere titulo.
Si tu crea iste pagina, remove iste titulo del <nowiki>{{#index:}}</nowiki> in $1.',
	'indexfunc-index-exists' => 'Le pagina "$1" ja existe',
	'indexfunc-index-taken' => '"$1" es ja usate como indice per "$2"',
	'index' => 'Indice',
	'index-legend' => 'Cercar in le indice',
	'index-search' => 'Cerca:',
	'index-submit' => 'Submitter',
	'index-disambig-start' => "'''$1''' pote referer a plure paginas:",
	'index-missing-param' => 'Iste pagina non pote esser usate sin parametros',
	'index-emptylist' => 'Il non ha paginas associate con "$1"',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 */
$messages['id'] = array(
	'index-submit' => 'Kirim',
);

/** Japanese (日本語)
 * @author Fryed-peach
 * @author Hosiryuhosi
 * @author 青子守歌
 */
$messages['ja'] = array(
	'indexfunc-desc' => '自動的なリダイレクトや曖昧さ回避ページを作成するためのパーサー関数',
	'indexfunc-badtitle' => '不正なタイトル:「$1」',
	'indexfunc-editwarn' => '警告: このタイトルは[[$1]]の索引タイトルです。あなたが作成しようとしているページが既に別のタイトルで存在していないことを確認してください。このページを作成したら、このタイトルを$1の <nowiki>{{#index:}}</nowiki> から除去してください。',
	'indexfunc-index-exists' => 'ページ「$1」は既に存在します。',
	'indexfunc-index-taken' => '「$1」は既に索引として「$2」に使われています',
	'index' => '索引',
	'index-legend' => '索引の検索',
	'index-search' => '検索:',
	'index-submit' => '送信',
	'index-disambig-start' => "「'''$1'''」はいくつかのページを指す可能性があります:",
	'index-missing-param' => 'このページは引数なしで使用できません',
	'index-emptylist' => '「$1」と関連付けられたページはありません',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'indexfunc-desc' => 'Paaserfunxjuhn för Ömleijdunge un „Watt ėßß datt?“-Sigge automattesch jemaat ze krijje.',
	'indexfunc-badtitle' => '„$1“ es ene onjöltije Sigge-Tittel.',
	'indexfunc-editwarn' => 'Opjepaß: Dä Tittel hät en automattesche „Watt ėßß datt?“-Sigg udder en automattesche Ömleijdung op de Sigg „[[$1]]“.
Beß sescher, dat di Sigg, di heh aanlääje wells nit ald onger enem andere Nahme aanjelaat es.
Wann De heh di Sigg aanlääß, donn dä iere Tittel uß dämm <code lang="en"><nowiki>{{#index:}}</nowiki></code> op dä Sigg „$1“ eruß nämme.',
	'indexfunc-index-exists' => 'Di Sigg „$1“ jitt et ald.',
	'indexfunc-index-taken' => '„$1“ weed ald als för automattesche „Watt ėßß datt?“-Sigg udder en automattesche Ömleijdung en dä Sigg „$2“ jebruch.',
	'index' => 'index',
	'index-legend' => 'Donn en de automatesche Ömleidunge un „Watt ėßß datt?“-Leßte söhke',
	'index-search' => 'Söhk noh:',
	'index-submit' => 'Lohß Jonn!',
	'index-disambig-start' => "Dä Tittel '''$1''' deiht op ongerscheidlijje Sigge paße:",
	'index-missing-param' => 'Heh di Sigg kann nit der oohne ene Parremeter jebruch wääde.',
	'index-emptylist' => 'Mer han kein Sigge, di met „$1“ verbonge wöhre.',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'indexfunc-desc' => 'Parser-Fonctioun fir Viruleedungen an Homonymie-Säiten automatesch unzeleeën',
	'indexfunc-badtitle' => 'Net valabelen Titel: "$1"',
	'indexfunc-index-exists' => 'D\'Säit "$1" gëtt et schonn',
	'indexfunc-index-taken' => '"$1" gëtt schonn als Index vum "$2" benotzt',
	'index' => 'Index',
	'index-legend' => 'Am Index sichen',
	'index-search' => 'Sichen:',
	'index-missing-param' => 'Dës Säit kann net ouni Parameter benotzt ginn',
	'index-emptylist' => 'Et gëtt keng Säiten déi mat "$1" assoziéiert sinn',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'indexfunc-desc' => "Parserfunctie om automatisch doorverwijzingen en doorverwijspagina's aan te maken",
	'indexfunc-badtitle' => 'Ongeldige paginanaam: "$1"',
	'indexfunc-editwarn' => 'Waarschuwing: Deze paginanaam is een indexnaam voor [[$1]].
Zorg dat de pagina die u aan gaat maken niet al bestaan onder een andere naam.
Als u deze pagina aanmaakt, verwijder deze pagina dan uit de <nowiki>{{#index:}}</nowiki> op $1.',
	'indexfunc-index-exists' => 'De pagina "$1" bestaat al',
	'indexfunc-index-taken' => '"$1" wordt al gebruikt als een index bij "$2"',
	'index' => 'Index',
	'index-legend' => 'De index doorzoeken',
	'index-search' => 'Zoeken:',
	'index-submit' => 'OK',
	'index-disambig-start' => "'''$1''' kan verwijzen naar meerdere pagina's:",
	'index-missing-param' => 'Deze pagina kan niet gebruikt worden zonder parameters',
	'index-emptylist' => 'Er zijn geen pagina\'s geassocieerd met "$1"',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'indexfunc-desc' => "Foncion del parser per crear de paginas de redireccion e d'omonimia automaticament",
	'indexfunc-badtitle' => 'Títol invalid : « $1»',
	'indexfunc-editwarn' => 'Atencion : Aqueste títol es un títol d’indèx per [[$1]].
Asseguratz-vos que la pagina que sètz a mand de crear existís pas ja jos un autre títol.
Se creatz aquesta pagina, suprimissètz aqueste títol de la <nowiki>{{#index:}}</nowiki> sus $1.',
	'indexfunc-index-exists' => 'La pagina « $1 » existís ja',
	'indexfunc-index-taken' => '« $1 » ja es utilizat coma un indèx per « $2 »',
	'index' => 'Indèx',
	'index-legend' => 'Recercar dins l’indèx',
	'index-search' => 'Cercar :',
	'index-submit' => 'Mandar',
	'index-disambig-start' => "'''$1''' se pòt referir a mai d'una pagina :",
	'index-missing-param' => 'Aquesta pagina pòt pas èsser utilizada sens paramètre',
	'index-emptylist' => 'I a pas de paginas ligadas a « $1 »',
);

/** Russian (Русский)
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'indexfunc-desc' => 'Функция парсера для создания автоматических перенаправлений и страниц неоднозначностей',
	'indexfunc-badtitle' => 'Ошибочный заголовок «$1»',
	'indexfunc-editwarn' => 'Предупреждение. Это название является индексным названием [[$1]].
Убедитесь, что страницы, которую вы собираетесь создать, не существует под другим названием.
Если создаёте эту страницу, удалите это название из <nowiki>{{#index:}}</nowiki> на $1.',
	'indexfunc-index-exists' => 'Страница «[[$1]]» уже существует',
	'indexfunc-index-taken' => '«$1» уже используется как индекс для «$2»',
	'index' => 'Индекс',
	'index-legend' => 'Поиск по индексу',
	'index-search' => 'Поиск:',
	'index-submit' => 'Отправить',
	'index-disambig-start' => "'''$1''' может относиться к нескольким страницам:",
	'index-missing-param' => 'Эта страница не может быть использована без каких-либо параметров',
	'index-emptylist' => 'Нет страниц, связанных с «$1»',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'indexfunc-desc' => 'Funkcia syntaktického analyzátora na automatickú tvorbu presmerovaní a rozlišovacích stránok',
	'indexfunc-badtitle' => 'Neplatný názov: „$1“',
	'indexfunc-editwarn' => 'Upozornenie: Tento názov je indexový názov pre [[$1]].
Uistite sa, že stránka, ktorú sa chystáte vytvoriť už neexistuje pod iným názvom.
Ak vytvoríte túto stránku, odstráňte tento názov z <nowiki>{{#index:}}</nowiki> na $1.',
	'indexfunc-index-exists' => 'Stránka „$1“ už existuje',
	'indexfunc-index-taken' => '„$1“ sa už používa ako index v „$2“',
	'index' => 'Index',
	'index-legend' => 'Hľadať v indexe',
	'index-search' => 'Hľadať:',
	'index-submit' => 'Odoslať',
	'index-disambig-start' => "'''$1''' môže odkazovať na niekoľko stránok:",
	'index-missing-param' => 'Túto stránku nemožno použiť bez zadania parametrov',
	'index-emptylist' => 'S „$1“ nesúvisia žiadne stránky',
);

