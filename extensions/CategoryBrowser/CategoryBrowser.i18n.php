<?php
/**
 * ***** BEGIN LICENSE BLOCK *****
 * This file is part of CategoryBrowser.
 *
 * CategoryBrowser is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * CategoryBrowser is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CategoryBrowser; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * ***** END LICENSE BLOCK *****
 *
 * CategoryBrowser is an AJAX-enabled category filter and browser for MediaWiki.
 *
 * To activate this extension :
 * * Create a new directory named CategoryBrowser into the directory "extensions" of MediaWiki.
 * * Place the files from the extension archive there.
 * * Add this line at the end of your LocalSettings.php file :
 * require_once "$IP/extensions/CategoryBrowser/CategoryBrowser.php";
 *
 * @version 0.3.1
 * @link http://www.mediawiki.org/wiki/Extension:CategoryBrowser
 * @author Dmitriy Sintsov <questpc@rambler.ru>
 * @addtogroup Extensions
 */

/**
 * Messages list.
 */

$messages = array();

/** English (English)
 * @author QuestPC
 */
$messages['en'] = array(
	'categorybrowser' => 'Category browser',
	'categorybrowser-desc' => 'Provides a [[Special:CategoryBrowser|special page]] to filter out most populated categories and to navigate them using an AJAX interface',
	'cb_requires_javascript' => 'The category browser extension requires JavaScript to be enabled in the browser.',
	'cb_ie6_warning' => 'The condition editor does not work in Internet Explorer 6.0 or earlier versions.
However, browsing of pre-defined conditions should work normally.
Please change or upgrade your browser, if possible.',
	'cb_show_no_parents_only' => 'Show only categories which has no parents',
	'cb_cat_name_filter' => 'Search for category by name:',
	'cb_cat_name_filter_clear' => 'Press to clear category name filter',
	'cb_cat_name_filter_ci' => 'Case insensitive',
	'cb_copy_line_hint' => 'Use the [+] and [>+] buttons to copy and paste operators into the selected expression',
	'cb_has_subcategories' => '$1 {{PLURAL:$1|subcategory|subcategories}}',
	'cb_has_pages' => '$1 {{PLURAL:$1|page|pages}}',
	'cb_has_files' => '$1 {{PLURAL:$1|file|files}}',
	'cb_has_parentcategories' => 'parent categories (if any)',
	'cb_previous_items_link' => 'Previous',
	'cb_previous_items_stats' => ' ($1 - $2)', # only translate this message to other languages if you have to change it
	'cb_previous_items_line' => '$1 $2', # do not translate or duplicate this message to other languages
	'cb_next_items_link' => 'Next',
	'cb_next_items_stats' => ' (from $1)',
	'cb_next_items_line' => '$1 $2', # do not translate or duplicate this message to other languages
	'cb_cat_subcats' => 'subcategories',
	'cb_cat_pages' => 'pages',
	'cb_cat_files' => 'files',
	'cb_apply_button' => 'Apply',
	'cb_op1_template' => '$1[$2]', # do not translate or duplicate this message to other languages
	'cb_op2_template' => '$1 $2 $3', # do not translate or duplicate this message to other languages
	'cb_all_op' => 'All',
	'cb_lbracket_op' => '(', # do not translate or duplicate this message to other languages
	'cb_rbracket_op' => ')', # do not translate or duplicate this message to other languages
	'cb_or_op' => 'or',
	'cb_and_op' => 'and',
	'cb_ge_op' => '>=', # do not translate or duplicate this message to other languages
	'cb_le_op' => '<=', # do not translate or duplicate this message to other languages
	'cb_eq_op' => '=', # do not translate or duplicate this message to other languages
	'cb_edit_left_hint' => 'Move left, if possible',
	'cb_edit_right_hint' => 'Move right, if possible',
	'cb_edit_remove_hint' => 'Delete, if possible',
	'cb_edit_copy_hint' => 'Copy operator to clipboard',
	'cb_edit_append_hint' => 'Insert operator to last position',
	'cb_edit_clear_hint' => 'Clear current expression (select all)',
	'cb_edit_paste_hint' => 'Paste operator into current position, if possible',
	'cb_edit_paste_right_hint' => 'Paste operator into next position, if possible',
);

/** Message documentation (Message documentation)
 * @author EugeneZelenko
 * @author QuestPC
 */
$messages['qqq'] = array(
	'cb_cat_name_filter_ci' => 'Dialog string for case insensitive category name search.',
	'cb_has_pages' => '{{Identical|Page}}',
	'cb_has_files' => '{{Identical|File}}',
	'cb_previous_items_link' => '{{Identical|Previous}}',
	'cb_next_items_link' => '{{Identical|Next}}',
	'cb_cat_pages' => '{{Identical|Pages}}',
	'cb_cat_files' => '{{Identical|File}}',
	'cb_all_op' => 'Operator to select all categories available.
{{Identical|All}}',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'categorybrowser' => 'Kategorie-blaaier',
	'cb_cat_name_filter' => 'Soek vir kategorie met die naam:',
	'cb_cat_name_filter_ci' => 'Kas onsensitief',
	'cb_has_subcategories' => '$1 {{PLURAL:$1|subkategorie|subkategorieë}}',
	'cb_has_pages' => '$1 {{PLURAL:$1|bladsy|bladsye}}',
	'cb_has_files' => '$1 {{PLURAL:$1|lêer|lêers}}',
	'cb_has_parentcategories' => 'boonste kategorieë (indien enige)',
	'cb_previous_items_link' => 'Vorige',
	'cb_next_items_link' => 'Volgende',
	'cb_next_items_stats' => '(vanaf $1)',
	'cb_cat_subcats' => 'subkategorië',
	'cb_cat_pages' => 'bladsye',
	'cb_cat_files' => 'lêers',
	'cb_apply_button' => 'Pas toe',
	'cb_all_op' => 'Alle',
	'cb_edit_left_hint' => 'Skuif na links, indien moontlik',
	'cb_edit_right_hint' => 'Skuif na regs, indien moontlik',
	'cb_edit_remove_hint' => 'Verwyder, indien moontlik',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'categorybrowser' => 'Прагляд катэгорыяў',
	'categorybrowser-desc' => 'Дадае [[Special:CategoryBrowser|спэцыяльную старонку]] для выбару найбольш поўных катэгорыяў для іх навігацыі з выкарыстаньнем AJAX-інтэрвэйсу',
	'cb_requires_javascript' => 'Пашырэньне для прагляду катэгорыяў патрабуе ўключэньне JavaScript у браўзэры.',
	'cb_ie6_warning' => 'Рэдактар станаў не працуе ў Internet Explorer 6.0 ці больш раньніх вэрсіях.
Тым ня менш, прагляд ужо вызначаных станаў павінен працаваць нармальна.
Калі ласка, зьмяніце ці абнавіце Ваш браўзэр, калі гэта магчыма.',
	'cb_show_no_parents_only' => 'Паказваць толькі катэгорыі без бацькоўскіх',
	'cb_cat_name_filter' => 'Пошук катэгорыяў па назьве:',
	'cb_cat_name_filter_clear' => 'Націсьніце для ачысткі фільтру назваў катэгорыяў',
	'cb_cat_name_filter_ci' => 'Без уліку рэгістру',
	'cb_copy_line_hint' => 'Выкарыстоўвайце кнопкі [+] і [>+] для капіяваньня і ўстаўкі апэратара ў выбраны выраз',
	'cb_has_subcategories' => '$1 {{PLURAL:$1|падкатэгорыя|падкатэгорыі|падкатэгорыяў}}',
	'cb_has_pages' => '$1 {{PLURAL:$1|старонка|старонкі|старонак}}',
	'cb_has_files' => '$1 {{PLURAL:$1|файл|файлы|файлаў}}',
	'cb_has_parentcategories' => 'бацькоўскія катэгорыі (калі ёсьць)',
	'cb_previous_items_link' => 'Папярэднія',
	'cb_next_items_link' => 'Наступныя',
	'cb_next_items_stats' => '(ад $1)',
	'cb_cat_subcats' => 'падкатэгорыі',
	'cb_cat_pages' => 'старонкі',
	'cb_cat_files' => 'файлы',
	'cb_apply_button' => 'Ужыць',
	'cb_all_op' => 'Усе',
);

/** Breton (Brezhoneg)
 * @author Y-M D
 */
$messages['br'] = array(
	'cb_has_subcategories' => '$1 {{PLURAL:$1|isrummad|isrummad}}',
	'cb_has_pages' => '$1 {{PLURAL:$1|pajenn|pajenn}}',
	'cb_has_files' => '$1 {{PLURAL:$1|restr|restr}}',
	'cb_previous_items_link' => 'Kent',
	'cb_next_items_link' => "War-lerc'h",
	'cb_next_items_stats' => '(eus $1)',
	'cb_cat_subcats' => 'isrummadoù',
	'cb_cat_pages' => 'pajennoù',
	'cb_cat_files' => 'restroù',
	'cb_apply_button' => 'Arloañ',
	'cb_all_op' => 'An holl',
);

/** German (Deutsch)
 * @author Kghbln
 * @author The Evil IP address
 */
$messages['de'] = array(
	'categorybrowser' => 'Kategorienbrowser',
	'categorybrowser-desc' => 'Ergänzt eine [[Special:CategoryBrowser|Spezialseite]] mit der die umfangreichsten Kategorien ausgewählt und in ihnen über ein Ajax-Interface navigiert werden kann',
	'cb_requires_javascript' => 'Um den Kategorienbrowser nutzen zu können, muss JavaScript im Browser aktiviert sein.',
	'cb_ie6_warning' => 'Der Editor für Bedingungen funktioniert nicht beim Internet Explorer 6.0 oder einer früheren Version.
Allerdings sollte das Browsen mit vordefinierten Bedingungen normalerweise funktionieren.
Dennoch sollte, sofern irgend möglich, der Browser aktualisiert oder gewechselt werden.',
	'cb_show_no_parents_only' => 'Nur Kategorien ohne übergeordnete Kategorie anzeigen',
	'cb_cat_name_filter' => 'Suche einer Kategorie anhand deren Namen:',
	'cb_cat_name_filter_clear' => 'Zum Zurücksetzen des Filters nach Kategoriename anklicken',
	'cb_cat_name_filter_ci' => 'Schreibungsunabhängig',
	'cb_copy_line_hint' => 'Zum Kopieren und Einfügen von Operatoren in die ausgewählten Ausdrücke, die Schaltflächen [+] und [>+] verwenden',
	'cb_has_subcategories' => '$1 {{PLURAL:$1|Unterkategorie|Unterkategorien}}',
	'cb_has_pages' => '$1 {{PLURAL:$1|Seite|Seiten}}',
	'cb_has_files' => '$1 {{PLURAL:$1|Datei|Dateien}}',
	'cb_has_parentcategories' => 'übergeordneten Kategorien (falls vorhanden)',
	'cb_previous_items_link' => 'Vorherige',
	'cb_next_items_link' => 'Nächste',
	'cb_next_items_stats' => ' (ab $1)',
	'cb_cat_subcats' => 'Unterkategorien',
	'cb_cat_pages' => 'Seiten',
	'cb_cat_files' => 'Dateien',
	'cb_apply_button' => 'Anwenden',
	'cb_all_op' => 'Alle',
	'cb_or_op' => 'oder',
	'cb_and_op' => 'und',
	'cb_edit_left_hint' => 'Nach links bewegen (sofern möglich)',
	'cb_edit_right_hint' => 'Nach rechts bewegen (sofern möglich)',
	'cb_edit_remove_hint' => 'Löschen (sofern möglich)',
	'cb_edit_copy_hint' => 'Operator in die Zwischenablage kopieren',
	'cb_edit_append_hint' => 'Operator an letzter Position einfügen',
	'cb_edit_clear_hint' => 'Aktuelle Ausdrücke entfernen (alle auswählen)',
	'cb_edit_paste_hint' => 'Operator an aktueller Position einfügen (sofern möglich)',
	'cb_edit_paste_right_hint' => 'Operator an nächstmöglicher Position einfügen (sofern möglich)',
);

/** Spanish (Español)
 * @author Danke7
 */
$messages['es'] = array(
	'cb_previous_items_link' => 'Anterior',
	'cb_next_items_link' => 'Siguiente',
	'cb_next_items_stats' => '(de $1)',
	'cb_cat_subcats' => 'subcategorías',
	'cb_cat_pages' => 'páginas',
	'cb_cat_files' => 'archivos',
	'cb_apply_button' => 'Aplicar',
	'cb_all_op' => 'Todos',
	'cb_edit_left_hint' => 'Mover a la izquierda, si es posible',
	'cb_edit_right_hint' => 'Mover a la derecha, si es posible',
	'cb_edit_remove_hint' => 'Borrar, si es posible',
);

/** French (Français)
 * @author The Evil IP address
 */
$messages['fr'] = array(
	'cb_or_op' => 'ou',
	'cb_and_op' => 'et',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'categorybrowser' => 'Navegador de categorías',
	'categorybrowser-desc' => 'Proporciona unha [[Special:CategoryBrowser|páxina especial]] para filtrar as categorías máis populares e navegar por elas a través dunha interface AJAX',
	'cb_requires_javascript' => 'A extensión do navegador de categorías necesita que o navegador teña o JavaScript activado.',
	'cb_ie6_warning' => 'O editor de condicións non funciona no Internet Explorer 6.0 ou calquera versión anterior.
Porén, a navegación polas condicións predefinidas debería funcionar correctamente.
Cambie ou actualice o seu navegador, se fose posible.',
	'cb_show_no_parents_only' => 'Mostrar unicamente as categorías que non colgan de ningunha outra',
	'cb_cat_name_filter' => 'Procurar por nome de categoría:',
	'cb_cat_name_filter_clear' => 'Prema para limpar o filtro de nome de categoría',
	'cb_cat_name_filter_ci' => 'Sen distinción entre maiúsculas e minúsculas',
	'cb_copy_line_hint' => 'Empregue os botóns [+] e [>+] para copiar e pegar os operadores na expresión seleccionada',
	'cb_has_subcategories' => '$1 {{PLURAL:$1|subcategoría|subcategorías}}',
	'cb_has_pages' => '$1 {{PLURAL:$1|páxina|páxinas}}',
	'cb_has_files' => '$1 {{PLURAL:$1|ficheiro|ficheiros}}',
	'cb_has_parentcategories' => 'categorías das que colga (se houbese algunha)',
	'cb_previous_items_link' => 'Anterior',
	'cb_next_items_link' => 'Seguinte',
	'cb_next_items_stats' => ' (de $1)',
	'cb_cat_subcats' => 'subcategorías',
	'cb_cat_pages' => 'páxinas',
	'cb_cat_files' => 'ficheiros',
	'cb_apply_button' => 'Aplicar',
	'cb_all_op' => 'Todas',
	'cb_edit_left_hint' => 'Mover á esquerda, se fose posible',
	'cb_edit_right_hint' => 'Mover á dereita, se fose posible',
	'cb_edit_remove_hint' => 'Borrar, se fose posible',
	'cb_edit_copy_hint' => 'Copiar o operador na memoria',
	'cb_edit_append_hint' => 'Inserir o operador na última posición',
	'cb_edit_clear_hint' => 'Limpar a expresión actual (selecciona todas)',
	'cb_edit_paste_hint' => 'Pegar o operador na posición actual, se fose posible',
	'cb_edit_paste_right_hint' => 'Pegar o operador na seguinte posición, se fose posible',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'categorybrowser' => 'Navigator de categorias',
	'categorybrowser-desc' => 'Provide un [[Special:CategoryBrowser|pagina special]] pro filtrar le categorias le plus popular e pro navigar per illos usante un interfacie AJAX',
	'cb_requires_javascript' => 'Le extension de navigator de categorias require JavaScript pro esser activate in le navigator del web.',
	'cb_ie6_warning' => 'Le editor de conditiones non functiona in Internet Explorer 6.0 o versiones anterior.
Nonobstante, le navigation de categorias predefinite debe functionar normalmente.
Per favor cambia o actualisa le navigator del web, si possibile.',
	'cb_show_no_parents_only' => 'Monstrar solmente categorias sin categoria superior',
	'cb_cat_name_filter' => 'Cerca un categoria per nomine:',
	'cb_cat_name_filter_clear' => 'Preme pro rader le filtro de nomine de categoria',
	'cb_cat_name_filter_ci' => 'Non distingue inter majusculas e minusculas',
	'cb_copy_line_hint' => 'Usa le buttones [+] and [>+] pro copiar e collar operatores in le expression seligite',
	'cb_has_subcategories' => '$1 {{PLURAL:$1|subcategoria|subcategorias}}',
	'cb_has_pages' => '$1 {{PLURAL:$1|pagina|paginas}}',
	'cb_has_files' => '$1 {{PLURAL:$1|file|files}}',
	'cb_has_parentcategories' => 'categorias superior (si existe)',
	'cb_previous_items_link' => 'Precedente',
	'cb_next_items_link' => 'Sequente',
	'cb_next_items_stats' => ' (ab $1)',
	'cb_cat_subcats' => 'subcategorias',
	'cb_cat_pages' => 'paginas',
	'cb_cat_files' => 'files',
	'cb_apply_button' => 'Applicar',
	'cb_all_op' => 'Totes',
	'cb_edit_left_hint' => 'Displaciar a sinistra, si possibile',
	'cb_edit_right_hint' => 'Displaciar a dextra, si possibile',
	'cb_edit_remove_hint' => 'Deler, si possibile',
	'cb_edit_copy_hint' => 'Copiar le operator al area de transferentia',
	'cb_edit_append_hint' => 'Inserer le operator al ultime position',
	'cb_edit_clear_hint' => 'Rader le actual expression (seliger toto)',
	'cb_edit_paste_hint' => 'Collar le operator in le actual position, si possibile',
	'cb_edit_paste_right_hint' => 'Collar le operator in le sequente position, si possibile',
);

/** Japanese (日本語)
 * @author Yanajin66
 */
$messages['ja'] = array(
	'categorybrowser' => 'カテゴリーブラウザ',
	'cb_requires_javascript' => 'カテゴリーブラウザの拡張にはブラウザ上で有効にされたJavaScriptが必要である。',
	'cb_show_no_parents_only' => '親のないカテゴリーのみを表示する',
	'cb_cat_name_filter' => '名前によるカテゴリーの検索',
	'cb_copy_line_hint' => '選択された式に演算子をコピーまたはペーストする場合は、[+] と[>+]ボタンを使用する',
	'cb_previous_items_link' => '前',
	'cb_next_items_link' => '次',
	'cb_next_items_stats' => '（$1から）',
	'cb_cat_subcats' => 'サブカテゴリ',
	'cb_cat_pages' => 'ページ',
	'cb_cat_files' => 'ファイル',
	'cb_apply_button' => '適用',
	'cb_all_op' => 'すべて',
	'cb_or_op' => 'または',
	'cb_and_op' => 'および',
	'cb_edit_left_hint' => '可能であれば、左に移動',
	'cb_edit_right_hint' => '可能であれば、右に移動',
	'cb_edit_remove_hint' => '可能であれば、削除する',
	'cb_edit_copy_hint' => 'クリップボードに演算子をコピーする',
	'cb_edit_append_hint' => '最後の位置に演算子を挿入する',
	'cb_edit_clear_hint' => '現在の式をクリアする（選択したすべての）',
	'cb_edit_paste_hint' => '可能であれば、現在の位置に演算子を貼付ける',
	'cb_edit_paste_right_hint' => '可能であれば、次の位置に演算子を貼付ける',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'categorybrowser' => 'Kategoriebrowser',
	'cb_show_no_parents_only' => 'Nëmme Kategorie weisen déi keng Kategorie driwwer hunn',
	'cb_cat_name_filter' => 'Sich no enger Kategorie nom Numm:',
	'cb_cat_name_filter_clear' => 'Dréckt fir de Filter vum Kategoriennumm eidelzemaachen',
	'cb_cat_name_filter_ci' => 'Ënnerscheed tëschent groussen a klenge Buschtawen',
	'cb_has_subcategories' => '$1 {{PLURAL:$1|Ënnerkategorie|Ënnerkategorien}}',
	'cb_has_pages' => '$1 {{PLURAL:$1|Säit|Säiten}}',
	'cb_has_files' => '$1 {{PLURAL:$1|Fichier|Fichieren}}',
	'cb_previous_items_link' => 'Vireg',
	'cb_next_items_link' => 'Nächst',
	'cb_next_items_stats' => '(vu(n) $1)',
	'cb_cat_subcats' => 'Ënnerkategorien',
	'cb_cat_pages' => 'Säiten',
	'cb_cat_files' => 'Fichieren',
	'cb_apply_button' => 'Applizéieren',
	'cb_all_op' => 'All',
	'cb_edit_left_hint' => 'No lénks réckelen, wa méiglech',
	'cb_edit_right_hint' => 'No riets réckelen, wa méiglech',
	'cb_edit_remove_hint' => 'Läschen, wa méiglech',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'categorybrowser' => 'Прелистувач на категории',
	'categorybrowser-desc' => 'Дава [[Special:CategoryBrowser|специјална страница]] за филтрирање на најисполнети категории и движење низ истите со помош на AJAX-посредник',
	'cb_requires_javascript' => 'Додатокот за прелистување на категории бара прелистувачот да има овозможено JavaScript',
	'cb_ie6_warning' => 'Уредникот на услови не работи на Internet Explorer 6.0 и постари верзии.
Меѓутоа прелистувањето на предодредени услови би требало да функционира нормално.
Сменете си го прелистувачот или подновете го.',
	'cb_show_no_parents_only' => 'Прикажувај само категории без матични категории',
	'cb_cat_name_filter' => 'Пребарување на категорија по име:',
	'cb_cat_name_filter_clear' => 'Притиснете тука за да го исчистите полето за пребарување категории по име',
	'cb_cat_name_filter_ci' => 'Не разликува големи/мали букви',
	'cb_copy_line_hint' => 'Користете ги копчињата [+] и [>+] за да копирање и лепење оператори во избраниот израз',
	'cb_has_subcategories' => '$1 {{PLURAL:$1|поткатегорија|поткатегории}}',
	'cb_has_pages' => '$1 {{PLURAL:$1|страница|страници}}',
	'cb_has_files' => '$1 {{PLURAL:$1|податотека|податотеки}}',
	'cb_has_parentcategories' => 'матични категории (ако има)',
	'cb_previous_items_link' => 'Претходни',
	'cb_next_items_link' => 'Следни',
	'cb_next_items_stats' => ' (од $1)',
	'cb_cat_subcats' => 'поткатегории',
	'cb_cat_pages' => 'страници',
	'cb_cat_files' => 'податотеки',
	'cb_apply_button' => 'Примени',
	'cb_all_op' => 'Сè',
	'cb_or_op' => 'или',
	'cb_and_op' => 'и',
	'cb_edit_left_hint' => 'Премести лево, ако може',
	'cb_edit_right_hint' => 'Премести десно, ако може',
	'cb_edit_remove_hint' => 'Избриши, ако може',
	'cb_edit_copy_hint' => 'Ископирај го операторот во оставата за копии',
	'cb_edit_append_hint' => 'Вметни го операторот во последната позиција',
	'cb_edit_clear_hint' => 'Исчисти го тековниот израз (избери сè)',
	'cb_edit_paste_hint' => 'Залепи го операторот во тековната позиција, ако може',
	'cb_edit_paste_right_hint' => 'Залепи го операторот во следната позиција, ако може',
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'categorybrowser' => 'Przeglądarka kategorii',
);

/** Piedmontese (Piemontèis)
 * @author Dragonòt
 */
$messages['pms'] = array(
	'categorybrowser' => 'Browser dle categorìe',
	'categorybrowser-desc' => "A dà na [[Special:CategoryBrowser|pàgina special]] për filtré le categorìe pi popolar e për scorrje an dovrand n'antërfacia AJAX",
	'cb_requires_javascript' => "L'estension browser ëd categorìa a ciama che JavaScript a sia abilità ant ël browser.",
	'cb_ie6_warning' => "L'editor ëd condission a travaja pa an Internet Explorer 6.0 o vërsion pi veje.
An tùit ij cas, lë scoriment ëd condission predefinìe a dovrìa travajé normalment.
Për piasì cangia o modìfica tò browser, se possìbil.",
	'cb_show_no_parents_only' => "Mosta mach categorìe ch'a l'han pa ëd parent",
	'cb_cat_name_filter' => 'Serca categorìe për nòm:',
	'cb_cat_name_filter_clear' => 'Sgnaca për scanselé ij filtr ëd nòm ëd categorìa',
	'cb_cat_name_filter_ci' => 'Pa sensìbil a minùscol/maiùscol',
	'cb_copy_line_hint' => "Dòvra ij boton [+] and [>+] për copié e ancolé j'operador ant l'espression selessionà",
	'cb_has_subcategories' => '$1 {{PLURAL:$1|sotcategorìa|sotcategorìe}}',
	'cb_has_pages' => '$1 {{PLURAL:$1|pàgina|pàgine}}',
	'cb_has_files' => '$1 {{PLURAL:$1|archivi|archivi}}',
	'cb_has_parentcategories' => 'categorìe parente (se quaidun-e)',
	'cb_previous_items_link' => 'Prima',
	'cb_next_items_link' => 'Apress',
	'cb_next_items_stats' => '(da $1)',
	'cb_cat_subcats' => 'sotcategorìe',
	'cb_cat_pages' => 'pàgine',
	'cb_cat_files' => 'archivi',
	'cb_apply_button' => 'Fà',
	'cb_all_op' => 'Tùit',
	'cb_or_op' => 'o',
	'cb_and_op' => 'e',
	'cb_edit_left_hint' => 'Va a snista, se possìbil',
	'cb_edit_right_hint' => 'Va a drita, se possìbil',
	'cb_edit_remove_hint' => 'Scansela, se possìbil',
	'cb_edit_copy_hint' => 'Còpia operador a la tastera',
	'cb_edit_append_hint' => "Ansëriss operador ant l'ùltima posission",
	'cb_edit_clear_hint' => 'Scansela espression corenta (selession-a tut)',
	'cb_edit_paste_hint' => 'Còpia operador ant la posission corenta, se possìbil',
	'cb_edit_paste_right_hint' => "Còpia operador ant la posission d'apress, se possìbil",
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Giro720
 */
$messages['pt-br'] = array(
	'cb_cat_name_filter' => 'Procurar categoria por  nome:',
	'cb_cat_name_filter_ci' => 'Não diferenciar maiúsculas/minúsculas',
	'cb_has_subcategories' => '$1 {{PLURAL:$1|subcategoria|subcategorias}}',
	'cb_has_pages' => '$1 {{PLURAL:$1|página|páginas}}',
	'cb_has_files' => '$1 {{PLURAL:$1|arquivo|arquivos}}',
	'cb_previous_items_link' => 'Anteriores',
	'cb_next_items_link' => 'Próximos',
	'cb_next_items_stats' => '$1 (de $2)',
	'cb_cat_subcats' => 'subcategorias',
	'cb_cat_pages' => 'páginas',
	'cb_cat_files' => 'arquivos',
	'cb_apply_button' => 'Aplicar',
	'cb_all_op' => 'Todos',
	'cb_edit_left_hint' => 'Mover para esquerda, se possível',
	'cb_edit_right_hint' => 'Mover para direita, se possível',
	'cb_edit_remove_hint' => 'Apagar, se possível',
);

/** Russian (Русский)
 * @author MaxSem
 * @author QuestPC
 */
$messages['ru'] = array(
	'categorybrowser' => 'Просмотр категорий',
	'categorybrowser-desc' => 'Предоставляет [[Special:CategoryBrowser|специальную страницу]] для выбора наиболее ёмких категорий вики-сайта с целью последующей навигации с использованием AJAX-интерфейса',
	'cb_requires_javascript' => 'Расширение для просмотра категорий требует включения поддержки Javascript в браузере',
	'cb_ie6_warning' => 'Редактор выражений не поддерживается в Internet Explorer версии 6.0 или более ранних.
Возможен лишь просмотр предопределенных выражений.
Пожалуйста поменяйте или обновите ваш браузер.',
	'cb_show_no_parents_only' => 'Показывать только категории без родителей',
	'cb_cat_name_filter' => 'Поиск категории по имени:',
	'cb_cat_name_filter_clear' => 'Нажмите здесь для очистки поля поиска категории по имени',
	'cb_cat_name_filter_ci' => 'Без учёта регистра',
	'cb_copy_line_hint' => 'Используйте кнопки [+] и [>+] для копирования оператора в выбранное выражение',
	'cb_has_subcategories' => '$1 {{PLURAL:$1|подкатегория|подкатегории|подкатегорий}}',
	'cb_has_pages' => '$1 {{PLURAL:$1|страница|страницы|страниц}}',
	'cb_has_files' => '$1 {{PLURAL:$1|файл|файла|файлов}}',
	'cb_has_parentcategories' => 'родительские категории (если есть)',
	'cb_previous_items_link' => 'Предыдущие',
	'cb_next_items_link' => 'Следующие',
	'cb_next_items_stats' => ' (начиная с $1)',
	'cb_cat_subcats' => 'подкатегорий',
	'cb_cat_pages' => 'страниц',
	'cb_cat_files' => 'файлов',
	'cb_apply_button' => 'Применить',
	'cb_all_op' => 'Все',
	'cb_or_op' => 'или',
	'cb_and_op' => 'и',
	'cb_edit_left_hint' => 'Переместить влево, если возможно',
	'cb_edit_right_hint' => 'Переместить вправо, если возможно',
	'cb_edit_remove_hint' => 'Удалить, если возможно',
	'cb_edit_copy_hint' => 'Скопировать оператор в буфер обмена',
	'cb_edit_append_hint' => 'Вставить оператор в последнюю позицию',
	'cb_edit_clear_hint' => 'Очистить текущее выражение (выбрать всё)',
	'cb_edit_paste_hint' => 'Вставить оператор в текущую позицию, если возможно',
	'cb_edit_paste_right_hint' => 'Вставить оператор в следующую позицию, если возможно',
);

