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
	'cb_requires_javascript' => 'The Category Browser extension requires JavaScript to be enabled in the browser.',
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
	'cb_previous_items_stats' => ' ($1 - $2)',
	'cb_previous_items_line' => '$1 $2',
	'cb_next_items_link' => 'Next',
	'cb_next_items_stats' => ' (from $1)',
	'cb_next_items_line' => '$1 $2',
	'cb_cat_subcats' => 'subcategories',
	'cb_cat_pages' => 'pages',
	'cb_cat_files' => 'files',
	'cb_apply_button' => 'Apply',
	'cb_op1_template' => '$1[$2]',
	'cb_op2_template' => '$1 $2 $3',
	'cb_all_op' => 'All',
	'cb_lbracket_op' => '(',
	'cb_rbracket_op' => ')',
	'cb_or_op' => 'or',
	'cb_and_op' => 'and',
	'cb_ge_op' => '>=',
	'cb_le_op' => '<=',
	'cb_eq_op' => '=',
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
 * @author QuestPC
 */
$messages['qqq'] = array(
	'cb_cat_name_filter_ci' => 'Dialog string for case insensitive category name search.',
	'cb_op1_template' => 'One operand operation visualisation template. Argument 1 is an operator name. Argument 2 is operation\'s parameter.',
	'cb_op2_template' => 'Two operands operation visualisation template. Argument 1 is first parameter. Argument 2 is operator name. Argument 3 is second parameter.',
	'cb_all_op' => 'Operator to select all categories available.',
	'cb_lbracket_op' => 'Opening bracket, allows to change execution order.',
	'cb_rbracket_op' => 'Closing bracket, allows to change execution order.',
	'cb_or_op' => 'Name of logical "or" operator in your language. Short (mathematical) form is preferred.',
	'cb_and_op' => 'Name of logical "and" operator in your language. Short (mathematical) form is preferred.',
	'cb_ge_op' => 'Name of comparsion operator "greater or equal" in your language. Short (mathematical) form is preferred.',
	'cb_le_op' => 'Name of comparsion operator "less or equal" in your language. Short (mathematical) form is preferred.',
	'cb_eq_op' => 'Name of comparsion operator "equal" in your language. Short (mathematical) form is preferred.',
);

/** Russian (Русский)
 * @author QuestPC
 */
$messages['ru'] = array(
	'categorybrowser' => 'Просмотр категорий',
	'categorybrowser-desc' => 'Предоставляет специальную страницу [[Служебная:CategoryBrowser]] для выбора наиболее ёмких категорий вики сайта с целью последующей навигации с использованием AJAX-интерфейса',
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
