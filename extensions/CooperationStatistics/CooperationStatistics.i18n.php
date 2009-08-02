<?php
/**
 * Internationalisation file for extension cooperationstatistics.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'cooperationstatistics' => 'Cooperation statistics',
	'cooperationstatistics-desc' => 'Show [[Special:CooperationStatistics|cooperation statistics on main namespace]].',
	'cooperationstatistics-text' => 'Show cooperation statistics on main namespace.
From [[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]] to [[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}}+]] revisors.<br />
See also the [[Special:MostRevisors|\'\'\'pages with the most revisors\'\'\']] and [[Special:MostRevisions|pages with the most revisions]].',
	'cooperationstatistics-tablearticle' => 'Page count',
	'cooperationstatistics-tablevalue' => 'Number of editors',
	'cooperationstatistics-articles' => '$1 {{PLURAL:$1|page|pages}}',
	'cooperationstatistics-nbusers' => '{{PLURAL:$2|has|have}} $1 {{PLURAL:$1|editor|editors}}',
	'cooperationstatistics-nblessusers' => '{{PLURAL:$2|has|have}} $1 {{PLURAL:$1|editor|or less editors}}',
	'cooperationstatistics-nbmoreusers' => '{{PLURAL:$2|has|have}} $1 or more editors',
	'cooperationstatistics-legendmore' => 'or more editors.',

	// Settings. Do not translate
	'cooperationstatistics-users' => 'editors',
	'cooperationstatistics-limit-few-revisors' => '1',
	'cooperationstatistics-limit-many-revisors' => '5',
);

/** Message documentation (Message documentation)
 * @author Fryed-peach
 * @author Purodha
 * @author Siebrand
 */
$messages['qqq'] = array(
	'cooperationstatistics-desc' => '{{desc}}',
	'cooperationstatistics-text' => 'Consider translating the + sign to " or more" if "number+" is not a commonplace notation in your language.',
	'cooperationstatistics-articles' => 'This message supports PLURAL.',
	'cooperationstatistics-nbusers' => 'This message supports PLURAL.
* $1 is the number of editors
* $2 is the number of pages in the previous column',
	'cooperationstatistics-nblessusers' => 'This message supports PLURAL.
* $1 is the number of editors
* $2 is the number of pages in the previous column',
	'cooperationstatistics-nbmoreusers' => 'This message supports PLURAL.
* $1 is the number of editors
* $2 is the number of pages in the previous column',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'cooperationstatistics' => 'Статыстыка супрацоўніцтва',
	'cooperationstatistics-tablearticle' => 'Лічыльнік старонак',
	'cooperationstatistics-tablevalue' => 'Колькасьць рэдактараў',
	'cooperationstatistics-articles' => '$1 {{PLURAL:$1|старонка|старонкі|старонак}}',
	'cooperationstatistics-nbusers' => 'мае $1 {{PLURAL:$1|рэдактара|рэдактараў|рэдактараў}}',
	'cooperationstatistics-nblessusers' => 'мае $1 ці меней рэдактараў',
	'cooperationstatistics-nbmoreusers' => 'мае $1 ці болей рэдактараў',
	'cooperationstatistics-legendmore' => 'ці болей рэдактараў.',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'cooperationstatistics' => 'Statistike kooperacije',
	'cooperationstatistics-desc' => 'Prikazuje [[Special:CooperationStatistics|statistike saradnje u glavnom imenskom prostoru]].',
	'cooperationstatistics-tablearticle' => 'Broj stranica',
	'cooperationstatistics-articles' => '$1 {{PLURAL:$1|stranica|stranice|stranica}}',
	'cooperationstatistics-nbusers' => '{{PLURAL:$2|ima|imaju}} $1 {{PLURAL:$1|uređivač|uređivača}}',
	'cooperationstatistics-legendmore' => 'ili više urednika.',
);

/** German (Deutsch)
 * @author Pill
 */
$messages['de'] = array(
	'cooperationstatistics-tablevalue' => 'Zahl der Bearbeiter',
	'cooperationstatistics-articles' => '$1 {{PLURAL:$1|Seite|Seiten}}',
	'cooperationstatistics-nbusers' => '{{PLURAL:$2|hat|haben}} $1 {{PLURAL:$1|Bearbeiter|Bearbeiter}}',
	'cooperationstatistics-nblessusers' => '{{PLURAL:$2|hat|haben}} $1 {{PLURAL:$1|Bearbeiter|oder weniger Bearbeiter}}',
	'cooperationstatistics-nbmoreusers' => '{{PLURAL:$2|hat|haben}} $1 oder mehr Bearbeiter',
	'cooperationstatistics-legendmore' => 'oder mehr Bearbeiter.',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'cooperationstatistics' => 'Kooperaciska statistika',
	'cooperationstatistics-desc' => '[[Special:CooperationStatistics|Kooperacisku statistiku wó głownem mjenjowem rumje]] pokazaś.',
	'cooperationstatistics-text' => "Kooperacisku statistiku wó głownem mjenjowem rumje pokazaś.
Wót [[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]] do [[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}}+]] pśeglědarjow.<br />
Glědaj teke [[Special:MostRevisors|'''boki z nejwěcej pśeglědarjami''']] a [[Special:MostRevisions|boki z nejwěcej wersijami]].",
	'cooperationstatistics-tablearticle' => 'Licenje bokow',
	'cooperationstatistics-tablevalue' => 'Licba wobźěłarjow',
	'cooperationstatistics-articles' => '$1 {{PLURAL:$1|bok|boka|boki|bokow}}',
	'cooperationstatistics-nbusers' => '{{PLURAL:$2|ma|matej|maju|ma}} $1 {{PLURAL:$1|wobźěłarja|wobźěłarjowu|wobźěłarjow|wobźěłarjow}}',
	'cooperationstatistics-nblessusers' => '{{PLURAL:$2|ma|matej|maju|ma}} $1 {{PLURAL:$1|wobźěłarja|wobźěłarjowu|wobźěłarjow|wobźěłarjow}} abo mjenje',
	'cooperationstatistics-nbmoreusers' => '{{PLURAL:$2|ma|matej|maju|ma}} $1 {{PLURAL:$1|wobźěłarja|wobźěłarjowu|wobźěłarjow|wobźěłarjow}} abo wěcej',
	'cooperationstatistics-legendmore' => 'abo wobźěłarjow.',
);

/** Basque (Euskara)
 * @author Kobazulo
 */
$messages['eu'] = array(
	'cooperationstatistics-articles' => '{{PLURAL:$1|Orrialde bat|$1 orrialde}}',
);

/** French (Français)
 * @author IAlex
 */
$messages['fr'] = array(
	'cooperationstatistics' => 'Statistiques de coopération',
	'cooperationstatistics-desc' => "Affiche les [[Special:CooperationStatistics|statistiques de coopération de l'espace de noms principal]].",
	'cooperationstatistics-text' => "Affiche les statistiques de coopération de l'espace de noms principal.
De [[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]] à [[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}}+]] relecteurs<br />
Voyez aussi les [[Special:MostRevisors|'''pages avec le plus de relecteurs''']] et les [[Special:MostRevisions|pages avec le plus de révisions]].",
	'cooperationstatistics-tablearticle' => 'Nombre de pages',
	'cooperationstatistics-tablevalue' => "Nombre d'éditeurs",
	'cooperationstatistics-articles' => '$1 pages',
	'cooperationstatistics-nbusers' => 'ont $1 éditeurs',
	'cooperationstatistics-nblessusers' => 'ont $1 éditeurs ou moins',
	'cooperationstatistics-nbmoreusers' => 'ont $1 éditeurs ou moins',
	'cooperationstatistics-legendmore' => "ou plus d'éditeurs.",
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'cooperationstatistics' => 'Estatísticas de cooperación',
	'cooperationstatistics-desc' => 'Mostra as [[Special:CooperationStatistics|estatísticas de cooperación do espazo de nomes principal]].',
	'cooperationstatistics-text' => "Mostra as estatísticas de cooperación do espazo de nomes principal.
De [[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]] a
[[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}}+]] revisores)<br />
Olle tamén as [[Special:MostRevisors|'''páxinas con máis revisores''']] e as [[Special:MostRevisions|páxinas con máis revisións]].",
	'cooperationstatistics-tablearticle' => 'Número de páxinas',
	'cooperationstatistics-tablevalue' => 'Número de editores',
	'cooperationstatistics-articles' => '$1 páxinas',
	'cooperationstatistics-nbusers' => 'ten $1 editores',
	'cooperationstatistics-nblessusers' => 'ten $1 ou menos editores',
	'cooperationstatistics-nbmoreusers' => 'ten $1 ou máis editores',
	'cooperationstatistics-legendmore' => 'ou máis editores.',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'cooperationstatistics' => 'Zämmearbetsstatischtik',
	'cooperationstatistics-desc' => '[[Special:CooperationStatistics|Zämmearbetsstatischtik fir dr Hauptnamensruum]] zeige.',
	'cooperationstatistics-text' => "Zämmearbetsstatischtik fir dr Hauptnamensruum zeige.
Vu dr [[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]] bis zue dr [[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}}+]]-Bearbeiter.<br />
Luege au d [[Special:MostRevisors|'''Syte mit dr meischte Bearbeiter''']] un d [[Special:MostRevisions|Syte mit dr meischte Bearbeitige]].",
	'cooperationstatistics-tablearticle' => 'Sytezeller',
	'cooperationstatistics-tablevalue' => 'Zahl vu Bearbeiter',
	'cooperationstatistics-articles' => '$1 Syte',
	'cooperationstatistics-nbusers' => 'hän $1 Bearbeiter',
	'cooperationstatistics-nblessusers' => 'hän $1 oder weniger Bearbeiter',
	'cooperationstatistics-nbmoreusers' => 'hän $1 oder meh Bearbeiter',
	'cooperationstatistics-legendmore' => 'oder meh Bearbeiter',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'cooperationstatistics' => 'Kooperaciska statistika',
	'cooperationstatistics-desc' => '[[Special:CooperationStatistics|Kooperacisku statistiku wo hłownym mjenowym rumje]] pokazać.',
	'cooperationstatistics-text' => "Kooperacisku statistiku wo hłownym mjenowym rumje pokazać.
Z [[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]] do [[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}}+]] kontrolerow.<br />
Hlej tež [[Special:MostRevisors|'''strony z najwjace kontrolerami''']] a [[Special:MostRevisions|strony z najwjace wersijemi]].",
	'cooperationstatistics-tablearticle' => 'Ličenje stronow',
	'cooperationstatistics-tablevalue' => 'Ličba wobdźěłarjow',
	'cooperationstatistics-articles' => '$1 {{PLURAL:$1|strona|stronje|strony|stronow}}',
	'cooperationstatistics-nbusers' => 'maja $1 {{PLURAL:$1|wobdźěłarja|wobdźěłarjow|wobdźěłarjow|wobdźěłarjow}}',
	'cooperationstatistics-nblessusers' => 'maja $1 {{PLURAL:$1|wobdźěłarja|wobdźěłarjow|wobdźěłarjow|wobdźěłarjow}} abo mjenje',
	'cooperationstatistics-nbmoreusers' => 'maja $1 {{PLURAL:$1|wobdźěłarja|wobdźěłarjow|wobdźěłarjow|wobdźěłarjow}} abo wjace',
	'cooperationstatistics-legendmore' => 'abo wjace wobdźěłarjow.',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'cooperationstatistics' => 'Statisticas de cooperation',
	'cooperationstatistics-desc' => 'Monstra [[Special:CooperationStatistics|statisticas de cooperation in le spatio de nomines principal]].',
	'cooperationstatistics-text' => "Monstra statisticas de cooperation in le spatio de nomines principal.
De [[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]] a[[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}}+]] contributores.<br />
Vide etiam le [[Special:MostRevisors|'''paginas con le plus contributores''']] e le [[Special:MostRevisions|paginas con le plus versiones]].",
	'cooperationstatistics-tablearticle' => 'Numero de paginas',
	'cooperationstatistics-tablevalue' => 'Numero de contributores',
	'cooperationstatistics-articles' => '$1 paginas',
	'cooperationstatistics-nbusers' => 'ha $1 contributores',
	'cooperationstatistics-nblessusers' => 'ha $1 contributores o minus',
	'cooperationstatistics-nbmoreusers' => 'ha $1 contributores o plus',
	'cooperationstatistics-legendmore' => 'o plus contributores.',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 */
$messages['id'] = array(
	'cooperationstatistics' => 'Statistik kerjasama',
	'cooperationstatistics-desc' => 'Menampilkan [[Special:CooperationStatistics|statistik kerjasama di ruang nama utama]].',
	'cooperationstatistics-tablearticle' => 'Jumlah halaman',
	'cooperationstatistics-tablevalue' => 'Jumlah penyunting',
	'cooperationstatistics-articles' => '$1 halaman',
	'cooperationstatistics-nbusers' => 'punya $1 penyunting',
	'cooperationstatistics-nblessusers' => 'punya kurang dari $1 penyunting',
	'cooperationstatistics-nbmoreusers' => 'punya lebih dari $1 penyunting',
	'cooperationstatistics-legendmore' => 'penyunting atau lebih.',
);

/** Italian (Italiano)
 * @author Darth Kule
 */
$messages['it'] = array(
	'cooperationstatistics' => 'Statistiche di cooperazione',
	'cooperationstatistics-desc' => 'Mostra [[Special:CooperationStatistics|statistiche di cooperazione nel namespace principale]].',
	'cooperationstatistics-text' => "Mostra statistiche di cooperazione nel namespace principale.
Da [[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]] a [[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}}+]] revisori)<br />
Consultare anche le [[Special:MostRevisors|'''pagine con più revisori''']] e le [[Special:MostRevisions|pagine con più revisioni]].",
	'cooperationstatistics-tablearticle' => 'Numero di pagine',
	'cooperationstatistics-tablevalue' => 'Numero di autori',
	'cooperationstatistics-articles' => '$1 pagine',
	'cooperationstatistics-nbusers' => 'hanno $1 autori',
	'cooperationstatistics-nblessusers' => 'hanno $1 autori o meno',
	'cooperationstatistics-nbmoreusers' => 'hanno $1 autori o più',
	'cooperationstatistics-legendmore' => 'o più autori.',
);

/** Japanese (日本語)
 * @author Fryed-peach
 * @author 青子守歌
 */
$messages['ja'] = array(
	'cooperationstatistics' => '協力状況',
	'cooperationstatistics-desc' => '[[Special:CooperationStatistics|標準名前空間における編集協力の状況]]を表示する。',
	'cooperationstatistics-text' => "標準名前空間における編集協力の状況を表示する。編集者が[[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]]から[[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}}人以上]]までのページ。<br />[[Special:MostRevisors|'''最も編集者の多いページ''']]および[[Special:MostRevisions|最も版の多いページ]]も参照してください。",
	'cooperationstatistics-tablearticle' => '合計ページ数',
	'cooperationstatistics-tablevalue' => '編集者の人数',
	'cooperationstatistics-articles' => '$1ページ',
	'cooperationstatistics-nbusers' => 'には$1人の編集者がいます',
	'cooperationstatistics-nblessusers' => 'には$1人以下の編集者がいます',
	'cooperationstatistics-nbmoreusers' => 'には$1人以上の編集者がいます',
	'cooperationstatistics-legendmore' => '以上の編集者がいます。',
);

/** Georgian (ქართული)
 * @author David1010
 */
$messages['ka'] = array(
	'cooperationstatistics' => 'თანამშრომლობის სტატისტიკა',
	'cooperationstatistics-tablearticle' => 'გვერდების რაოდენობა',
	'cooperationstatistics-tablevalue' => 'რედაქტორების რიცხვი',
	'cooperationstatistics-legendmore' => 'ან მეტი რედაქტორი.',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'cooperationstatistics' => 'Statistike vum Zesammewerke',
	'cooperationstatistics-desc' => 'Zeish en [[Special:CooperationStatistics|Shtatistik övver et Zosammewerke aam Houp_Appachtemang]].',
	'cooperationstatistics-text' => "Statistike övver de Zosammeärbeit aan de Sigge em Houp-Appachtemang.
Vun [[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]] bes [[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}} udder mieh]] Schriiver.<br />
Loor och  noh de [[Special:MostRevisors|'''Sigge met de miehßte Schriiver''']] un de [[Special:MostRevisions|Sigge met de miehßte Änderunge]].",
	'cooperationstatistics-tablearticle' => 'Aanzahl Sigge',
	'cooperationstatistics-tablevalue' => 'Aanzahl Schriiver',
	'cooperationstatistics-articles' => '{{PLURAL:$1|Ein Sigg|$1 Sigge|Kei Sigg}}',
	'cooperationstatistics-nbusers' => '{{PLURAL:$2|hät|hann|hät}} {{PLURAL:$|$1 Schriever}}',
	'cooperationstatistics-nblessusers' => '{{PLURAL:$2|hät|hann|hät}} {{PLURAL:$1|$1 Schriever|$1 udder winnijer Schriever|keine Schriiver}}',
	'cooperationstatistics-nbmoreusers' => '{{PLURAL:$2|hät|hann|hät}} $1 udder mieh Schriever',
	'cooperationstatistics-legendmore' => 'udder mieh Schriiver.',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'cooperationstatistics' => 'Statistike vun der Zesummenaarbecht',
	'cooperationstatistics-desc' => '[[Special:CooperationStatistics|Statistike vun der Zesummenaarbecht am Haaptnummraum]] weisen.',
	'cooperationstatistics-text' => "Statistike vun der Zesummenaarbecht am Haaptnummraum weisen.
Vu(n) [[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]] bis [[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}} oder méi]] Reviseuren<br />
Kuckt och d'[[Special:MostRevisors|'''Säite mat de meeschte Reviseuren''']] an d'[[Special:MostRevisions|Säite mat de meeschte Versiounen]].",
	'cooperationstatistics-tablearticle' => 'Säitenzuel',
	'cooperationstatistics-tablevalue' => 'Zuel vun Editeuren',
	'cooperationstatistics-articles' => '$1 Säiten',
	'cooperationstatistics-nbusers' => 'hunn $1 Editeuren',
	'cooperationstatistics-nblessusers' => 'hunn $1 oder manner Editeuren',
	'cooperationstatistics-nbmoreusers' => 'hunn $1 oder méi Editeuren',
	'cooperationstatistics-legendmore' => 'oder méi Editeuren.',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'cooperationstatistics' => 'Samenwerkingsstatistieken',
	'cooperationstatistics-tablearticle' => "Aantal pagina's",
	'cooperationstatistics-tablevalue' => 'Aantal bewerkers',
	'cooperationstatistics-articles' => "$1 {{PLURAL:$1|pagina|pagina's}}",
	'cooperationstatistics-nbusers' => '{{PLURAL:$2|heeft}} $1 {{PLURAL:$1|bewerker|bewerkers}}',
	'cooperationstatistics-nblessusers' => '{{PLURAL:$2|heeft}} $1 {{PLURAL:$1|bewerker|of minder bewerkers}}',
	'cooperationstatistics-nbmoreusers' => '{{PLURAL:$2|heeft}} $1 of meer bewerkers',
	'cooperationstatistics-legendmore' => 'of meer bewerkers.',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'cooperationstatistics' => 'Estatisticas de cooperacion',
	'cooperationstatistics-desc' => "Aficha las [[Special:CooperationStatistics|estatisticas de cooperacion de l'espaci de noms principal]].",
	'cooperationstatistics-text' => "Aficha las estatisticas de cooperacion de l'espaci de noms principal.
De [[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]] a [[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}}+]] relectors<br />
Vejatz tanben las [[Special:MostRevisors|'''paginas amb lo mai de relectors''']] e las [[Special:MostRevisions|paginas amb lo mai de revisions]].",
	'cooperationstatistics-tablearticle' => 'Nombre de paginas',
	'cooperationstatistics-tablevalue' => "Nombre d'editors",
	'cooperationstatistics-articles' => '$1 paginas',
	'cooperationstatistics-nbusers' => 'an $1 editors',
	'cooperationstatistics-nblessusers' => 'an $1 editors o mens',
	'cooperationstatistics-nbmoreusers' => 'an $1 editors o mens',
	'cooperationstatistics-legendmore' => "o mai d'editors.",
);

/** Russian (Русский)
 * @author Ferrer
 * @author Lockal
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'cooperationstatistics' => 'Статистика сотрудничества',
	'cooperationstatistics-desc' => 'Показывает [[Special:CooperationStatistics|статистику сотрудничества в основном пространстве имён]].',
	'cooperationstatistics-text' => "Показать статистику сотрудничества в основном пространстве имён.
От [[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]] до [[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}}+]] редакторов.<br />
См. также [[Special:MostRevisors|'''страницы с наибольшим количеством редакторов''']] и [[Special:MostRevisions|страницы с наибольшим количеством редакций]].",
	'cooperationstatistics-tablearticle' => 'Число страниц',
	'cooperationstatistics-tablevalue' => 'Число редакторов',
	'cooperationstatistics-articles' => '$1 {{PLURAL:$1|страница|страницы|страниц}}',
	'cooperationstatistics-nbusers' => 'имеет $1 редакторов',
	'cooperationstatistics-nblessusers' => 'имеет $1 или меньше редакторов',
	'cooperationstatistics-nbmoreusers' => 'имеет $1 или больше редакторов',
	'cooperationstatistics-legendmore' => 'или больше редакторов.',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'cooperationstatistics' => 'Štatistika spolupráce',
	'cooperationstatistics-desc' => 'Zobraziť [[Special:CooperationStatistics|štatistiku spolupráce v hlavnom mennom priestore]].',
	'cooperationstatistics-text' => "Zobraziť štatistiku spolupráce v hlavnom mennom priestore.
Od [[MediaWiki:Cooperationstatistics-limit-few-revisors|{{MediaWiki:Cooperationstatistics-limit-few-revisors}}]] do [[MediaWiki:Cooperationstatistics-limit-many-revisors|{{MediaWiki:cooperationstatistics-limit-many-revisors}}+]] revízorov.<br />
Pozri aj [[Special:MostRevisors|'''stránky s najväčším počtom kontrolórov''']] a [[Special:MostRevisions|stránky s najväčším počtom revízií]].",
	'cooperationstatistics-tablearticle' => 'Počet stránok',
	'cooperationstatistics-tablevalue' => 'Počet redaktorov',
	'cooperationstatistics-articles' => '$1 {{PLURAL:$1|stránka|stránky|stránok}}',
	'cooperationstatistics-nbusers' => '{{PLURAL:$2|má|majú}} $1 {{PLURAL:$1|redaktora|redaktorov}}',
	'cooperationstatistics-nblessusers' => '{{PLURAL:$2|má|majú}} $1 {{PLURAL:$1|redaktora|alebo menej redaktorov}}',
	'cooperationstatistics-nbmoreusers' => '{{PLURAL:$2|má|majú}} $1 alebo viac redaktorov.',
	'cooperationstatistics-legendmore' => 'alebo viac redaktorov.',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'cooperationstatistics' => 'సహకార గణాంకాలు',
	'cooperationstatistics-articles' => '$1 {{PLURAL:$1|పేజీ|పేజీలు}}',
);

