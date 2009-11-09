<?php
/**
 * ***** BEGIN LICENSE BLOCK *****
 * This file is part of QPoll.
 * Uses parts of code from Quiz extension (c) 2007 Louis-Rémi BABE. All rights reserved.
 *
 * QPoll is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * QPoll is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with QPoll; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * ***** END LICENSE BLOCK *****
 *
 * QPoll is a poll tool for MediaWiki.
 * 
 * To activate this extension :
 * * Create a new directory named QPoll into the directory "extensions" of MediaWiki.
 * * Place the files from the extension archive there.
 * * Add this line at the end of your LocalSettings.php file :
 * require_once "$IP/extensions/QPoll/qp_user.php";
 * 
 * @version 0.6.2
 * @link http://www.mediawiki.org/wiki/Extension:QPoll
 * @author QuestPC <questpc@rambler.ru>
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
	'pollresults' => 'Results of the polls on this site',
	'qp_desc' => 'Allows creation of polls',
	'qp_desc-sp' => '[[Special:PollResults|Special page]] for viewing results of the polls',
	'qp_result_NA' => 'Not answered',
	'qp_result_error' => 'Syntax error',
	'qp_vote_button' => 'Vote',
	'qp_vote_again_button' => 'Change your vote',
	'qp_polls_list' => 'List all polls',
	'qp_users_list' => 'List all users',
	'qp_browse_to_poll' => 'Browse to $1',
	'qp_browse_to_user' => 'Browse to $1',
	'qp_votes_count' => '$1 {{PLURAL:$1|vote|votes}}',
	'qp_source_link' => 'Source',
	'qp_stats_link' => 'Statistics',
	'qp_users_link' => 'Users',
	'qp_voice_link' => 'User voice',
	'qp_user_polls_link' => 'Participated in $1 polls',
	'qp_user_missing_polls_link' => 'No participation',
	'qp_not_participated_link' => 'Not participated',
	'qp_order_by_username' => 'Order by username',
	'qp_order_by_polls_count' => 'Order by polls count',
	'qp_spec_line' => 'Page $1 poll $2 $3 $4 $5 $6',
	'qp_export_to_xls' => 'Export statistics into XLS format',
	'qp_users_answered_questions' => 'users answered to the questions',
	'qp_func_no_such_poll' => 'No such poll ($1)',
	'qp_func_missing_question_id' => 'Please specify an existing question id (starting from 1) for the poll $1',
	'qp_func_invalid_question_id' => 'Invalid question id=$2 (not a number) for the poll $1',
	'qp_func_missing_proposal_id' => 'Please specify an existing proposal id (starting from 0) for the poll $1, question $2',
	'qp_func_invalid_proposal_id' => 'Invalid proposal id=$3 (not a number) for the poll $1, question $2',
	'qp_error_no_such_poll' => 'No such poll ($1).
Make sure that the poll declared and saved, also be sure to use address delimiter character #',
	'qp_error_id_in_stats_mode' => 'Cannot declare an ID of the poll in statistical mode',
	'qp_error_dependance_in_stats_mode' => 'Cannot declare dependance chain of the poll in statistical mode',
	'qp_error_no_stats' => 'No statistical data is available, because noone has voted for this poll, yet (address=$1)',
	'qp_error_address_in_decl_mode' => 'Cannot get an address of the poll in declaration mode',
	'qp_error_question_not_implemented' => 'Question of such type are not implemented: $1',
	'qp_error_invalid_question_type' => 'Invalid question type: $1',
	'qp_error_type_in_stats_mode' => 'Question type cannot be defined in statistical display mode: $1',
	'qp_error_no_poll_id'	=> 'Poll tag has no id attribute defined.',
	'qp_error_invalid_poll_id' => 'Invalid poll id (id=$1).
Poll id may contain only letters, numbers and space character',
	'qp_error_already_used_poll_id' => 'The poll id has already been used on this page (id=$1).',
	'qp_error_invalid_dependance_value' => 'The poll (id=$1) dependance chain has invalid value of dependance attribute (dependance="$2")',
	'qp_error_missed_dependance_title' => 'The poll (id=$1) is dependant on the another poll (id=$3) from page [[$2]], but the title [[$2]] was not found.
Either remove the dependance attribute, or restore [[$2]]',
	'qp_error_missed_dependance_poll' => 'The poll (id=$1) is dependant on the another poll (id=$3) on page $2, but that poll does not exists or has not been saved yet.
Either remove the dependance attribute, or create the poll with id=$3 at the page $2 and save it.
To save a poll, submit it while not answering to any proposal questions.',
	'qp_error_vote_dependance_poll' => 'Please vote for poll $1 first.',
	'qp_error_too_many_spans' => 'Too many category classes for the subcategories defined',
	'qp_error_unanswered_span' => 'Unanswered subcategory',
	'qp_error_non_unique_choice' => 'This question requires unique proposal answer',
	'qp_error_category_name_empty' => 'Category name is empty',
	'qp_error_proposal_text_empty' => 'Proposal text is empty',
	'qp_error_too_few_categories' => 'At least two categories must be defined',
	'qp_error_too_few_spans' => 'Every category class requires at least two possible answers defined',
	'qp_error_no_answer' => 'Unanswered proposal',
	'qp_error_unique' => 'Question of type unique() has more proposals than possible answers defined: impossible to complete'
);

/** Russian (Русский)
 * @author QuestPC
 */
$messages['ru'] = array(
	'pollresults' => 'Результаты опросов на сайте',
	'qp_desc' => 'Позволяет создавать опросы',
	'qp_result_NA' => 'Нет ответа',
	'qp_result_error' => 'Синтаксическая ошибка',
	'qp_vote_button' => "Проголосовать",
	'qp_vote_again_button' => "Переголосовать",
	'qp_polls_list' => 'Список всех опросов',
	'qp_users_list' => 'Список всех участников',
	'qp_browse_to' => "Перейти к $1",
	'qp_votes_count' => "$1 {{PLURAL:$1|голос|голоса|голосов}}",
	'qp_source_link' => 'Исходный код',
	'qp_stats_link' => 'Статистика',
	'qp_users_link' => 'Участники',
	'qp_voice_link' => 'Голос участника',
	'qp_user_polls_link' => 'Участвовал в $1 {{PLURAL:$1|опросе|опросах|опросах}}',
	'qp_user_missing_polls_link' => 'Неучастие в опросах',
	'qp_not_participated_link' => 'Список неучаствовавших',
	'qp_order_by_username' => 'Сортировать по имени пользователя',
	'qp_order_by_polls_count' => 'Сортировать по количеству опросов',
	'qp_spec_line' => 'Страница $1 Опрос $2 $3 $4 $5 $6',
	'qp_export_to_xls' => 'Экспортировать статистику в XLS формате',
	'qp_users_answered_questions' => '{{PLURAL:$1|пользователь|пользователя|пользователя}} {{PLURAL:$1|ответил|ответили|ответили}} на вопросы этого опроса',
	'qp_func_no_such_poll' => 'Опрос не найден ($1)',
	'qp_func_missing_question_id' => 'Укажите существующий идентификатор вопроса (начинающийся с единицы) для опроса $1',
	'qp_func_invalid_question_id' => 'Ошибочный идентификатор вопроса (question id=$2 - требуется числовое значение) для опроса $1',
	'qp_func_missing_proposal_id' => 'Укажите идентификатор строки (начинающийся с нуля) для опроса $1, вопроса $2',
	'qp_func_invalid_proposal_id' => 'Ошибочный идентификатор строки (proposal id=$3 - требуется числовое значение) для опроса $1, вопроса $2',
	'qp_error_no_such_poll' => 'Опрос не найден ($1). Убедитесь что заданный опрос определён и сохранён, а также что используется символ разделителя адреса #',
	'qp_error_id_in_stats_mode' => 'Недопустимо определять идентификатор опроса (id) в статистическом режиме вывода',
	'qp_error_dependance_in_stats_mode' => 'Недопустимо определять атрибут зависимости опроса (dependance) в статистическом режиме',
	'qp_error_no_stats' => 'Статистика голосования недоступна, так как еще никто не голосовал в этом опросе (address=$1)',
	'qp_error_address_in_decl_mode' => 'Недопустимо задавать адрес опроса (address) в режиме определения',
	'qp_error_invalid_question_header' => 'Неверный формат заголовка вопроса',
	'qp_error_question_not_implemented' => 'Вопросы данного типа не реализованы в коде расширения: $1',
	'qp_error_invalid_question_type' => 'Недопустимый тип вопроса: $1',
	'qp_error_type_in_stats_mode' => 'Недопустимо определять тип вопроса в статистическом режиме: $1',
	'qp_error_no_poll_id' => 'Тэг опроса не имеет атрибута id.',
	'qp_error_invalid_poll_id' => 'Недопустимый идентификатор опроса (id=$1). Идентификатор опроса может содержать только буквы, цифры и символ пробела',
	'qp_error_already_used_poll_id' => 'Установленный атрибут id опроса уже используется другим опросом на данной странице (id=$1).',
	'qp_error_invalid_dependance_value' => 'В цепочке зависимости опросов для опроса (id=$1) было найдено синтаксически неверное значение атрибута зависимости (dependance="$2")',
	'qp_error_missed_dependance_title'=> 'Опрос с идентификатором id=$1 имеет атрибут зависимости от другого опроса (id=$3), находящегося на отсутствующей странице [[$2]]. Необходимо убрать атрибут зависимости от другого опроса, либо восстановить страницу [[$2]]',
	'qp_error_missed_dependance_poll' => 'Опрос с идентификатором id=$1 требует прохождения другого опроса с идентификатором id=$3, находящегося на странице $2. Однако же, последний не был найден. Необходимо удалить атрибут зависимости из опроса (id=$1), либо создать опрос с идентификатором id=$3 на странице $2 и сохранить его. Для сохранения опроса будет достаточно нажать кнопку "Проголосовать", не отвечая ни на один вопрос.',
	'qp_error_vote_dependance_poll' => 'Пожалуйста ответьте сначала на опрос $1.',
	'qp_error_too_many_spans' => 'Определено слишком много классов для подкатегорий вопросов',
	'qp_error_unanswered_span' => 'Подкатегория вопроса без ответа',
	'qp_error_non_unique_choice' => 'Данный вопрос требует чтобы выбранный вариант ответа не использовался ранее',
	'qp_error_category_name_empty' => 'Отсутствует название варианта ответа',
	'qp_error_proposal_text_empty' => 'Отсутствует текст строки вопроса',
	'qp_error_too_few_categories' => 'Каждый вопрос должен иметь по крайней мере два варианта ответа',
	'qp_error_too_few_spans' => 'Каждая подкатегория вопроса требует по меньшей мере два варианта ответа',
	'qp_error_no_answer' => 'Нет ответа на вопрос',
	'qp_error_unique' => 'Опрос, имеющий тип unique(), не должен иметь больше ответов чем вопросов'
);
