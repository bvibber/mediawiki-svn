<?php
/**
 * Internationalisation file for extension mostrevisors.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'mostrevisors' => 'Pages with the most revisors',
	'mostrevisors-desc' => 'List [[Special:MostRevisors|pages with the most revisors]]',
	'mostrevisors-header' => "'''This page lists the {{PLURAL:$1|page|$1 pages}} with most revisors on the wiki.'''",
	'mostrevisors-limitlinks' => 'Show up to $1 pages',
	'mostrevisors-namespace' => 'Namespace:',
	'mostrevisors-none' => 'No entries were found.',
	'mostrevisors-ns-header' => "'''This page lists the {{PLURAL:$1|page|$1 pages}} with most revisors in the $2 namespace.'''",
	'mostrevisors-showing' => 'Listing {{PLURAL:$1|page|$1 pages}}:',
	'mostrevisors-submit' => 'Go',
	'mostrevisors-showredir' => 'Show redirect pages',
	'mostrevisors-hideredir' => 'Hide redirect pages',
	'mostrevisors-users' => '- $1 {{PLURAL:$1|editor|editors}}',
	'mostrevisors-viewcontributors' => 'View main contributors',
	//'mostrevisors-text' => 'Show [[Special:MostRevisions|pages with the most revisors]], starting from [[MediaWiki:Mostrevisors-limit-few-revisors|{{MediaWiki:Mostrevisors-limit-few-revisors}} revisors]].',
	//'mostrevisors-text' => 'Show [[Special:MostRevisions|pages with the most revisors]], starting from [[MediaWiki:Mostrevisors-limit-few-revisors|{{MediaWiki:Mostrevisors-limit-few-revisors}} {{PLURAL:{{MediaWiki:Mostrevisors-limit-few-revisors}}|revisor|revisors}}]].',

	// Settings. Do not translate these messages.
	'mostrevisors-limit-few-revisors' => '1',
);

/** Message documentation (Message documentation)
 * @author Darth Kule
 * @author McDutchie
 * @author Purodha
 */
$messages['qqq'] = array(
	'mostrevisors' => 'The [http://www.mediawiki.org/wiki/Extension:MostRevisors documentation for this extension] seems to indicate that "revisor" here is another word for "editor" or "contributor".',
	'mostrevisors-desc' => 'Short description of the extension, shown on [[Special:Version]].',
	'mostrevisors-limitlinks' => '* $1 is a series of links for different numbers, separated by {{msg-mw|pipe-separator}}',
	'mostrevisors-namespace' => '{{Identical|Namespace}}',
	'mostrevisors-submit' => '{{Identical|Go}}',
	'mostrevisors-users' => '* $1 is the number of contributors to a page, it supports PLURAL.',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'mostrevisors' => 'Старонкі з найбольшай колькасьцю рэцэнзэнтаў',
	'mostrevisors-desc' => 'Сьпіс [[Special:MostRevisors|старонак з найбольшай колькасьцю рэцэнзэнтаў]]',
	'mostrevisors-header' => "'''На гэтай старонцы пададзены сьпіс $1 {{PLURAL:$1|старонкі|старонак|старонак}} з найбольшай колькасьцю рэцэнзэнтаў ва ўсёй {{GRAMMAR:месны|{{SITENAME}}}}.'''",
	'mostrevisors-limitlinks' => 'Паказваць да $1 {{PLURAL:$1|старонкі|старонак|старонак}}',
	'mostrevisors-namespace' => 'Прастора назваў:',
	'mostrevisors-none' => 'Запісы ня знойдзеныя.',
	'mostrevisors-showing' => 'Утрымлівае $1 {{PLURAL:$1|старонку|старонкі|старонак}}:',
	'mostrevisors-submit' => 'Паказаць',
	'mostrevisors-showredir' => 'Паказаць перанакіраваньні',
	'mostrevisors-hideredir' => 'Схаваць перанакіраваньні',
	'mostrevisors-viewcontributors' => 'Паказаць асноўных аўтараў',
);

/** German (Deutsch)
 * @author Pill
 */
$messages['de'] = array(
	'mostrevisors' => 'Seiten mit den meisten Bearbeitern',
	'mostrevisors-desc' => 'Zeigt die [[Special:MostRevisors|Seiten mit den meisten Bearbeitern]]',
	'mostrevisors-header' => "'''Diese Seite zeigt die {{PLURAL:$1|Seite|$1 Seiten}} mit den meisten Bearbeitern auf diesem Wiki an.'''",
	'mostrevisors-limitlinks' => 'Höchstens $1 Seiten anzeigen',
	'mostrevisors-namespace' => 'Namensraum:',
	'mostrevisors-none' => 'Es wurden keine Einträge gefunden.',
	'mostrevisors-ns-header' => "'''Diese Seite zeigt die {{PLURAL:$1|Seite|$1 Seiten}} mit den meisten Bearbeitern im Namensraum „$2“ an.'''",
	'mostrevisors-showing' => 'Zeige {{PLURAL:$1|Seite|$1 Seiten}}:',
	'mostrevisors-submit' => 'Los',
	'mostrevisors-showredir' => 'Weiterleitungen anzeigen',
	'mostrevisors-hideredir' => 'Weiterleitungen verstecken',
	'mostrevisors-users' => '- $1 {{PLURAL:$1|Bearbeiter|Bearbeiter}}',
);

/** French (Français)
 * @author IAlex
 */
$messages['fr'] = array(
	'mostrevisors' => 'Pages avec le plus des relecteurs',
	'mostrevisors-desc' => 'Liste les [[Special:MostRevisors|pages avec le plus de relecteurs]]',
	'mostrevisors-header' => "'''Cette page liste {{PLURAL:$1|la page|les $1 pages}} avec le plus de relecteurs sur ce wiki.'''",
	'mostrevisors-limitlinks' => "Afficher jusqu'à $1 pages",
	'mostrevisors-namespace' => 'Espace de noms :',
	'mostrevisors-none' => 'Aucune entrée trouvée.',
	'mostrevisors-ns-header' => "'''Cette page liste {{PLURAL:$1|la page|les $1 pages}} avec le plus de relecteurs sur ce wiki dans l'espace de noms $2.'''",
	'mostrevisors-showing' => 'Liste {{PLURAL:$1|de la page|des $1 pages}} :',
	'mostrevisors-submit' => 'Soumettre',
	'mostrevisors-showredir' => 'Afficher les pages de redirection',
	'mostrevisors-hideredir' => 'masquer les pages de redirection',
	'mostrevisors-users' => '- $1 {{PLURAL:$1|éditeur|éditeurs}}',
	'mostrevisors-viewcontributors' => 'Voir les contributeurs principaux',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'mostrevisors' => 'Syte mit dr meischte Priefer',
	'mostrevisors-desc' => '[[Special:MostRevisors|Syte mit dr meischte Priefer]] uflischte',
	'mostrevisors-header' => "'''Die Syte lischtet d {{PLURAL:$1|Syte|$1 Syte}} uf mit dr meischte Priefer in däm Wiki.'''",
	'mostrevisors-limitlinks' => 'Zeig bis zue $1 Syte',
	'mostrevisors-namespace' => 'Namensruum:',
	'mostrevisors-none' => 'Kei Yytreg gfunde.',
	'mostrevisors-ns-header' => "'''Die Syte lischtet d {{PLURAL:$1|Syte|$1 Syte}} uf mit dr meischte Priefer im $2-Namensruum.'''",
	'mostrevisors-showing' => 'Lischtet {{PLURAL:$1|Syte|$1 Syte}} uf:',
	'mostrevisors-submit' => 'Gang',
	'mostrevisors-showredir' => 'Wyterleitigssyte zeige',
	'mostrevisors-hideredir' => 'Wyterleitigssyte verstecke',
	'mostrevisors-viewcontributors' => 'Hauptbyyträger zeige',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'mostrevisors' => 'Strony z najwjace kontrolerami',
	'mostrevisors-desc' => '[[Special:MostRevisors|Strony z najwjace kontrolerami]] nalistować',
	'mostrevisors-header' => "'''Tuta strona nalistuje {{PLURAL:$1|stronu|$1 stronje|$1 strony|$1 stronow}} z najwjace kontrolerami we wikiju.'''",
	'mostrevisors-limitlinks' => 'Hač k $1 {{PLURAL:$1|stronje|stronomaj|stronam|stronam}} pokazać',
	'mostrevisors-namespace' => 'Mjenowy rum:',
	'mostrevisors-none' => 'Žane zapiski namakane.',
	'mostrevisors-ns-header' => "'''Tuta strona nalistuje {{PLURAL:$1|stronu|$1 stronje|$1 strony|$1 stronow}} z najwjace kontrolerami w mjenowym rumje $2.'''",
	'mostrevisors-showing' => '{{PLURAL:$1|$1 strona so pokazuje|$1 stronje so pokazujetej|$1 strony so pokazuja|$1 stronow so pokazuje}}:',
	'mostrevisors-submit' => 'Wotpósłać',
	'mostrevisors-showredir' => 'Daleposrědkowanske strony pokazać',
	'mostrevisors-hideredir' => 'Daleposrědkowanske strony schować',
	'mostrevisors-users' => '- $1 {{PLURAL:$1|wobdźěłar|wobdźěłarjej|wobdźěłarjo|wobdźěłarjow}}',
	'mostrevisors-viewcontributors' => 'Hłownych wobdźěłarjow sej wobhladać',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'mostrevisors' => 'Paginas con le plus contributores',
	'mostrevisors-desc' => 'Lista le [[Special:MostRevisors|paginas con le plus contributores]]',
	'mostrevisors-header' => "'''Iste pagina lista le {{PLURAL:$1|pagina|$1 paginas}} con le plus contributores in le wiki.'''",
	'mostrevisors-limitlinks' => 'Monstrar usque a $1 paginas',
	'mostrevisors-namespace' => 'Spatio de nomines:',
	'mostrevisors-none' => 'Nulle entrata ha essite trovate.',
	'mostrevisors-ns-header' => "'''Iste pagina lista le {{PLURAL:$1|pagina|$1 paginas}} con le plus contributores in le spatio de nomines $2.'''",
	'mostrevisors-showing' => 'Lista de {{PLURAL:$1|pagina|$1 paginas}}:',
	'mostrevisors-submit' => 'Ir',
	'mostrevisors-showredir' => 'Revelar paginas de redirection',
	'mostrevisors-hideredir' => 'Celar paginas de redirection',
	'mostrevisors-users' => '- $1 {{PLURAL:$1|contributor|contributores}}',
	'mostrevisors-viewcontributors' => 'Vider le contributores principal',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 */
$messages['id'] = array(
	'mostrevisors-namespace' => 'Ruang nama:',
);

/** Italian (Italiano)
 * @author Darth Kule
 */
$messages['it'] = array(
	'mostrevisors' => 'Pagine con più revisori',
	'mostrevisors-desc' => 'Elenca [[Special:MostRevisors|pagine con più revisori]]',
	'mostrevisors-header' => "'''In questa pagina {{PLURAL:$1|è elencata la pagina|sono elencate le $1 pagine}} con più revisori su questo sito.'''",
	'mostrevisors-limitlinks' => 'Mostra fino a $1 pagine',
	'mostrevisors-namespace' => 'Namespace:',
	'mostrevisors-none' => 'Nessuna pagina trovata.',
	'mostrevisors-ns-header' => "'''In questa pagina {{PLURAL:$1|è elencata la pagina|sono elencate le $1 pagine}} con più revisori nel namespace $2.'''",
	'mostrevisors-showing' => 'Elenco {{PLURAL:$1|pagina|$1 pagine}}:',
	'mostrevisors-submit' => 'Vai',
	'mostrevisors-showredir' => 'Mostra redirect',
	'mostrevisors-hideredir' => 'Nascondi redirect',
	'mostrevisors-viewcontributors' => 'Visualizza principali contributori',
);

/** Japanese (日本語)
 * @author Fryed-peach
 * @author Hosiryuhosi
 * @author 青子守歌
 */
$messages['ja'] = array(
	'mostrevisors' => '最も編集者の多いページ',
	'mostrevisors-desc' => '[[Special:MostRevisors|最も編集者の多いページ]]の一覧',
	'mostrevisors-header' => "'''このページは、ウィキ全体で最も編集者の多い$1ページの一覧です。'''",
	'mostrevisors-limitlinks' => '最大で$1件表示する',
	'mostrevisors-namespace' => '名前空間:',
	'mostrevisors-none' => 'ページは見つかりませんでした。',
	'mostrevisors-ns-header' => "'''このページは、$2名前空間の中で最も編集者の多い$1ページの一覧です。'''",
	'mostrevisors-showing' => '$1ページを列挙しています：',
	'mostrevisors-submit' => '表示',
	'mostrevisors-showredir' => 'リダイレクトページを表示',
	'mostrevisors-hideredir' => 'リダイレクトページを非表示',
	'mostrevisors-users' => '- $1{{PLURAL:$1|人の編集者}}',
	'mostrevisors-viewcontributors' => '主執筆者を見る',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'mostrevisors' => 'Sigge met de miehßte Schriiver',
	'mostrevisors-desc' => 'Kann de [[Special:MostRevisors|Sigge met de miehßte Schriiver]] opleßte.',
	'mostrevisors-header' => "'''Heh di Sigg deiht {{PLURAL:$1|di Sigg|de $1 Sigge|kein Sigge}} ussem Wiki met de mihßte Schriiver opleste.'''",
	'mostrevisors-limitlinks' => 'Nit mieh wi $1 Sigge aanzeije',
	'mostrevisors-namespace' => 'Appachtemang:',
	'mostrevisors-none' => 'Kein Enndrääsch jefonge.',
	'mostrevisors-ns-header' => "'''Heh di Sigg deiht {{PLURAL:$1|di Sigg|de $1 Sigge|kein Sigg}} ussem Appachtemang „$2“ met de mihßte Schriiver opleßte.'''",
	'mostrevisors-showing' => 'Hee {{PLURAL:$1|kütt ein Sigg:|kumme $1 Sigge:|sen kei Sigge.}}',
	'mostrevisors-submit' => 'Lohß jonn!',
	'mostrevisors-showredir' => 'Ömleidunge zeije',
	'mostrevisors-hideredir' => 'Ömleidunge fottlohße',
	'mostrevisors-users' => ' - {{PLURAL:$1|$1 Schriiver}}',
	'mostrevisors-viewcontributors' => 'Houpschriiver',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'mostrevisors-limitlinks' => 'Bis zu $1 Säite weisen',
	'mostrevisors-namespace' => 'Nummraum:',
	'mostrevisors-none' => 'Näischt fonnt.',
	'mostrevisors-showing' => '{{PLURAL:$1|Säit|$1 Säiten}} oplëschten:',
	'mostrevisors-submit' => 'Lass',
	'mostrevisors-showredir' => 'Viruleedungssäite weisen',
	'mostrevisors-hideredir' => 'Viruleedungssäite vestoppen',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'mostrevisors' => "Pagina's met de meeste bewerkers",
	'mostrevisors-desc' => "Geeft [[Special:MostRevisors|pagina's met de meeste bewerkers]] weer",
	'mostrevisors-header' => "'''Deze pagina bevat een lijst met de {{PLURAL:$1|pagina|$1 pagina's}} met de meeste bewerkers.'''",
	'mostrevisors-limitlinks' => "Maximaal $1 pagina's weergeven",
	'mostrevisors-namespace' => 'Naamruimte:',
	'mostrevisors-none' => "Geen pagina's gevonden.",
	'mostrevisors-ns-header' => "'''Deze pagina bevat een lijst met de {{PLURAL:$1|pagina|$1 pagina's}} met de meeste bewerkers in de naamruimte $2.'''",
	'mostrevisors-showing' => "Er {{PLURAL:$1|wordt één pagina|worden $1 pagina's}} weergegeven:",
	'mostrevisors-submit' => 'OK',
	'mostrevisors-showredir' => "Doorverwijspagina's weergeven",
	'mostrevisors-hideredir' => "Doorverwijspagina's verbergen",
	'mostrevisors-users' => '- $1 {{PLURAL:$1|bewerker|bewerkers}}',
	'mostrevisors-viewcontributors' => 'De grootste bijdragers bekijken',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'mostrevisors' => 'Paginas amb lo mai de relectors',
	'mostrevisors-desc' => 'Fa la lista de las [[Special:MostRevisors|paginas amb lo mai de relectors]]',
	'mostrevisors-header' => "'''Aquesta pagina fa la lista de {{PLURAL:$1|la pagina|las $1 paginas}} amb lo mai de relectors sus aqueste wiki.'''",
	'mostrevisors-limitlinks' => 'Afichar fins a $1 paginas',
	'mostrevisors-namespace' => 'Espaci de noms :',
	'mostrevisors-none' => "Cap d'entrada pas trobada.",
	'mostrevisors-ns-header' => "'''Aquesta pagina fa la lista de {{PLURAL:$1|la pagina|las $1 paginas}} amb lo mai de relectors sus aqueste wiki dins l'espaci de noms $2.'''",
	'mostrevisors-showing' => 'Lista {{PLURAL:$1|de la pagina|de las $1 paginas}} :',
	'mostrevisors-submit' => 'Sometre',
	'mostrevisors-showredir' => 'Afichar las paginas de redireccion',
	'mostrevisors-hideredir' => 'amagar las paginas de redireccion',
	'mostrevisors-viewcontributors' => 'Veire los contributors principals',
);

/** Russian (Русский)
 * @author EugeneZelenko
 * @author Ferrer
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'mostrevisors' => 'Страницы с наибольшим количеством редакторов',
	'mostrevisors-desc' => 'Список [[Special:MostRevisors|страниц с наибольшим количеством редакторов]]',
	'mostrevisors-header' => "'''На этой странице {{PLURAL:$1|приведена $1 страница|приведено $1 страницы|приведено $1 страниц}} с наибольшим количеством редакторов.'''",
	'mostrevisors-limitlinks' => 'Показать $1 страниц',
	'mostrevisors-namespace' => 'Пространство имён:',
	'mostrevisors-none' => 'Записей не найдено.',
	'mostrevisors-ns-header' => "'''На этой странице {{PLURAL:$1|приведена $1 страница|приведено $1 страницы|приведено $1 страниц}} с наибольшим количеством редакторов из пространства имён $2.'''",
	'mostrevisors-showing' => 'Содержит $1 {{PLURAL:$1|страницу|страницы|страниц}}:',
	'mostrevisors-submit' => 'Перейти',
	'mostrevisors-showredir' => 'Показать страницы перенаправлений',
	'mostrevisors-hideredir' => 'Скрыть страницы перенаправлений',
	'mostrevisors-viewcontributors' => 'Показать основных редакторов',
);

