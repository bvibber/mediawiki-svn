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
	'qp_parentheses' => '($1)',
	'qp_full_category_name' => '$1($2)',
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
	'qp_voice_link_inv' => 'User voice?',
	'qp_user_polls_link' => 'Participated in $1 {{PLURAL:$1|poll|polls}}',
	'qp_user_missing_polls_link' => 'No participation',
	'qp_not_participated_link' => 'Not participated',
	'qp_order_by_username' => 'Order by username',
	'qp_order_by_polls_count' => 'Order by polls count',
	'qp_results_line_qupl' => 'Page "$1" Poll "$2": $3',
	'qp_results_line_qpl' => 'Page "$1" Poll "$2": $3, $4, $5, $6',
	'qp_header_line_qpul' => '$1 [ Page "$2" Poll "$3" ]',
	'qp_results_line_qpul' => '$1: $2',
	'qp_header_line_qucl' => '$1. $2<br />$3 ??? $4',
	'qp_results_line_qucl' => '$1: $2 $3',
	'qp_export_to_xls' => 'Export statistics into XLS format',
	'qp_users_answered_questions' => '$1 {{PLURAL:$1|user|users}} answered to the questions',
	'qp_func_no_such_poll' => 'No such poll ($1)',
	'qp_func_missing_question_id' => 'Please specify an existing question id (starting from 1) for the poll $1',
	'qp_func_invalid_question_id' => 'Invalid question id=$2 (not a number) for the poll $1',
	'qp_func_missing_proposal_id' => 'Please specify an existing proposal id (starting from 0) for the poll $1, question $2',
	'qp_func_invalid_proposal_id' => 'Invalid proposal id=$3 (not a number) for the poll $1, question $2',
	'qp_error_no_such_poll' => 'No such poll ($1).
Make sure that the poll declared and saved, also be sure to use address delimiter character #',
	'qp_error_id_in_stats_mode' => 'Cannot declare an ID of the poll in statistical mode',
	'qp_error_dependance_in_stats_mode' => 'Cannot declare dependance chain of the poll in statistical mode',
	'qp_error_no_stats' => 'No statistical data is available, because no one has voted for this poll, yet (address=$1)',
	'qp_error_address_in_decl_mode' => 'Cannot get an address of the poll in declaration mode',
	'qp_error_question_not_implemented' => 'Questions of such type are not implemented: $1',
	'qp_error_invalid_question_type' => 'Invalid question type: $1',
	'qp_error_type_in_stats_mode' => 'Question type cannot be defined in statistical display mode: $1',
	'qp_error_no_poll_id'	=> 'Poll tag has no id attribute defined.',
	'qp_error_invalid_poll_id' => 'Invalid poll id (id=$1).
Poll id may contain only letters, numbers and space character',
	'qp_error_already_used_poll_id' => 'The poll id has already been used on this page (id=$1).',
	'qp_error_invalid_dependance_value' => 'The poll (id=$1) dependance chain has invalid value of dependance attribute (dependance="$2")',
	'qp_error_missed_dependance_title' => 'The poll (id=$1) is dependant on the another poll (id=$3) from page [[$2]], but the title [[$2]] was not found.
Either remove the dependance attribute, or restore [[$2]]',
	'qp_error_missed_dependance_poll' => 'The poll (id=$1) is dependant on the another poll (id=$3) at page $2, but that poll does not exists or has not been saved yet.
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

/** Message documentation (Message documentation)
 * @author Fryed-peach
 * @author Siebrand
 */
$messages['qqq'] = array(
	'qp_desc' => '{{desc}}',
	'qp_result_error' => '{{Identical|Syntax error}}',
	'qp_vote_button' => '{{Identical|Vote}}',
	'qp_stats_link' => '{{Identical|Statistics}}',
	'qp_users_link' => '{{Identical|User}}',
	'qp_voice_link_inv' => "What '?' means?",
	'qp_user_polls_link' => 'Parameters:
* $1 is the number of polls participated in.',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'pollresults' => 'Resultate van die peilings op hierdie webwerf',
	'qp_desc' => 'Maak dit moontlik om peilings te skep',
	'qp_desc-sp' => '[[Special:PollResults|Speciale bladsy]] om die resultate van peilings te wys',
	'qp_result_NA' => 'Nie beantwoord nie',
	'qp_result_error' => 'Sintaksfout',
	'qp_vote_button' => 'Stem',
	'qp_vote_again_button' => 'Verander u stem',
	'qp_polls_list' => 'Wys alle peilings',
	'qp_users_list' => 'Lys alle gebruikers',
	'qp_browse_to_poll' => 'Wys peiling $1',
	'qp_browse_to_user' => 'Blaai na gebruiker $1',
	'qp_votes_count' => '$1 {{PLURAL:$1|stem|stemme}}',
	'qp_source_link' => 'Bron',
	'qp_stats_link' => 'Statistieke',
	'qp_users_link' => 'Gebruikers',
	'qp_voice_link' => 'Gebruikersstem',
	'qp_voice_link_inv' => 'Gebruikersstem',
	'qp_user_polls_link' => 'Het aan $1 {{PLURAL:$1|peiling|peilings}} deelgeneem',
	'qp_user_missing_polls_link' => 'Geen deelname',
	'qp_not_participated_link' => 'Nie deelgeneem nie',
	'qp_order_by_username' => 'Sorteer op gebruikersnaam',
	'qp_order_by_polls_count' => 'Sorteer op aantal peilings',
	'qp_results_line_qupl' => 'Bladsy "$1", peiling "$2": $3',
	'qp_results_line_qpl' => 'Bladsy "$1", peiling "$2": $3, $4, $5, $6',
	'qp_header_line_qpul' => '$1 [ bladsy "$2", peiling "$3" ]',
	'qp_export_to_xls' => 'Eksporteer statistieke na XLS-formaat',
	'qp_users_answered_questions' => '$1 {{PLURAL:$1|gebruiker|gebruikers}} het die vraag beantwoord',
	'qp_func_no_such_poll' => 'Die peiling bestaan nie ($1)',
	'qp_func_missing_question_id' => "Verskaf asseblief 'n vraag-ID (begin by 1) vir die peiling $1",
	'qp_func_invalid_question_id' => "Ongeldige vraag-ID ($2 - nie 'n getal nie) vir die peiling $1",
	'qp_func_missing_proposal_id' => "Verskaf asseblief 'n voorstel-ID (begin by 0) vir die peiling $1, vraag $2",
	'qp_func_invalid_proposal_id' => "Ongeldige voorstel-ID ($3 - nie 'n getal nie) vir die peiling $1, vraag $2",
	'qp_error_no_such_poll' => 'Die peiling bestaan nie ($1).
Sorg dat die peiling opgestel en gestoor is, en dat "#" as skeidingskarakter in die adres gebruik word.',
	'qp_error_id_in_stats_mode' => "Dit is nie moontlik om 'n ID vir die peiling in die statistiese modus te verklaar nie",
	'qp_error_dependance_in_stats_mode' => "Dit is nie moontlik om 'n afhanklikheid-ketting vir die peiling in die statistiese modus te verklaar nie",
	'qp_error_no_stats' => 'Daar is geen statistiese data beskikbaar nie omdat nog geen gebruikers in hierdie peiling gestem het nie (adres $1)',
	'qp_error_address_in_decl_mode' => "Dit is nie moontlik om 'n adres van die peiling in die verklaar-modus aan te vra nie.",
	'qp_error_question_not_implemented' => 'Vrae van die tipe is nie geïmplementeer nie: $1',
	'qp_error_invalid_question_type' => 'Ongeldige vraagtipe: $1',
	'qp_error_type_in_stats_mode' => 'Die vraagtipe kan nie in die statistiese vertoon-modus gedefinieer word nie: $1',
	'qp_error_no_poll_id' => 'Die eienskap "id" is nie vir die peiling gedefinieer nie.',
	'qp_error_invalid_poll_id' => 'Ongeldig peiling-ID ($1).
Die ID mag slegs letters, syfers en spasies bevat.',
	'qp_error_already_used_poll_id' => 'Die peilingsnommer word al reeds op hierdie bladsy gebruik (ID $1)',
	'qp_error_invalid_dependance_value' => 'Die peiling se afhanklikheid-ketting (ID $1) het \'n ongeldige waarde vir die afhanklikheid-eienskap (dependance "$2")',
	'qp_error_missed_dependance_title' => 'Die peiling (ID $1) is afhanklik van \'n ander peiling (ID $3) op bladsy [[$2]], maar die bladsy [[$2]] bestaan nie.
Verwyder die eienskap "dependance" of plaas die bladsy [[$2]] terug.',
	'qp_error_missed_dependance_poll' => 'Die peiling (ID $1) is afhanklik van \'n ander peiling (ID $3) op bladsy $2, maar die peiling bestaan nie of is nog nie geskep nie.
Verwyder die eienskap "dependance" of skep \'n peiling met ID $3 op bladsy $2.
Om \'n peiling te stoor, stuur dit sonder dat enig voorstel beantwoord word.',
	'qp_error_vote_dependance_poll' => 'Stem asseblief eers in die peiling $1.',
	'qp_error_too_many_spans' => 'Daar is te veel kategorieklasse vir die subkategorieë gedefinieer',
	'qp_error_unanswered_span' => 'Onbeantwoorde subkategorie',
	'qp_error_non_unique_choice' => "Hierdie vraag vereis 'n unieke voorstel-antwoord",
	'qp_error_category_name_empty' => 'Kategorie se naam is leeg',
	'qp_error_proposal_text_empty' => 'Geen voorstelteks is verskaf nie',
	'qp_error_too_few_categories' => 'Ten minste twee kategorieë moet gedefinieer word',
	'qp_error_too_few_spans' => 'Vir elke kategorieklas moet daar ten minste twee moontlike antwoorde gedefinieerd wees',
	'qp_error_no_answer' => 'Onbeantwoorde voorstel',
	'qp_error_unique' => 'Vir die vraag van die tipe unique() is daar meer voorstelle as moontlike antwoorde gedefinieer. Dit is nie reg voorberei nie.',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 */
$messages['be-tarask'] = array(
	'qp_stats_link' => 'Статыстыка',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'pollresults' => 'Wuslědki napšašowanjow na toś tom sedle',
	'qp_desc' => 'Zmóžnja napóranje napšašowanjow',
	'qp_desc-sp' => '[[Special:PollResults|Specialny bok]] za woglědowanje wuslědkow napšašowanjow',
	'qp_result_NA' => 'Njewótgronjony',
	'qp_result_error' => 'Syntaksowa zmólka',
	'qp_vote_button' => 'Wótgłosowaś',
	'qp_vote_again_button' => 'Twójo wótgłosowanje změniś',
	'qp_polls_list' => 'Wše napšašowanja nalicyś',
	'qp_users_list' => 'Wšych wužywarjow nalicyś',
	'qp_browse_to_poll' => 'Dalej k $1',
	'qp_browse_to_user' => 'Dalej k $1',
	'qp_votes_count' => '$1 {{PLURAL:$1|głos|głosa|głose|głosow}}',
	'qp_source_link' => 'Žrědło',
	'qp_stats_link' => 'Statistika',
	'qp_users_link' => 'Wužywarje',
	'qp_voice_link' => 'Wužywarski głos',
	'qp_voice_link_inv' => 'Wužywarski głos?',
	'qp_user_polls_link' => 'Jo se wobźělił na $1 {{PLURAL:$1|napšašowanju|napšašowanjoma|napšašowanjach|napšašowanjach}}',
	'qp_user_missing_polls_link' => 'Žedne wobźělenje',
	'qp_not_participated_link' => 'Njewobźělony',
	'qp_order_by_username' => 'Pórěd pó wužywarskem mjenju',
	'qp_order_by_polls_count' => 'Pórěd pó licbje napšašowanjow',
	'qp_results_line_qupl' => 'Bok "$1" napšašowanje "$2": $3',
	'qp_results_line_qpl' => 'Bok "$1" napšašowanje "$2": $3, $4, $5, $6',
	'qp_header_line_qpul' => '$1 [ bok "$2" napšašowanje "$3" ]',
	'qp_export_to_xls' => 'Statistiku do XLS-formata eksportěrowaś',
	'qp_users_answered_questions' => '$1 {{PLURAL:$1|wužywaŕ jo wótegronił|wužywarja stej wótegroniłej|wužywarje su wótegronili|wužywarjow jo wótegroniło}}',
	'qp_func_no_such_poll' => 'Take napšašowanje njejo ($1)',
	'qp_func_missing_question_id' => 'Pšosym pódaj eksistěrujucy ID pšašanja (zachopinajucy wót 1) za napšašowanje $1',
	'qp_func_invalid_question_id' => 'Njepłaśiwy ID=$2 pšašanja (žedna licba) za napšašowanje $1',
	'qp_func_missing_proposal_id' => 'Pšosym pódaj eksistěrujucy ID naraźenja (zachopinajucy wót 0) za napšašowanje $1, pšašanje $2',
	'qp_func_invalid_proposal_id' => 'Njepłaśiwy ID=$3 naraźenja (žedna licba) za napšašowanje $1, pšašanje $2',
	'qp_error_no_such_poll' => 'Take napšašowanje ($1) njejo.
Zawěsć, až napšašowanje se deklarěrujo a składujo, zawěsć se teke adresowe źěleńske znamuško # wužywaś',
	'qp_error_id_in_stats_mode' => 'Njejo móžno ID napšašowanja w statistiskem modusu deklarěrowaś',
	'qp_error_dependance_in_stats_mode' => 'Njejo móžno rjeśak wótwisnosći napšašowanja w statistiskem modusu deklarěrowaś',
	'qp_error_no_stats' => 'Žedne statistiske daty k dispoziciji, dokulaž nichten njejo wótgłosował za toś to napšašowanje (adresa=$1)',
	'qp_error_address_in_decl_mode' => 'Njejo móžno adresu napšašowanja w deklaraciskem modusu dostaś',
	'qp_error_question_not_implemented' => 'Pšašanja takego typa njejsu implementěrowane: $1',
	'qp_error_invalid_question_type' => 'Njepłaśiwy typ pšašanja: $1',
	'qp_error_type_in_stats_mode' => 'Typ pšašanja njedajo se w statistiskem zwobraznjeńskem modusu definěrowaś: $1',
	'qp_error_no_poll_id' => 'Toflicka Poll njejo atribut ID definěrowana.',
	'qp_error_invalid_poll_id' => 'Njepłaśiwy napšašowański ID (ID=$1).
Napšašowański ID smějo jano pismiki, licby a prozne znamje wopśimjeś',
	'qp_error_already_used_poll_id' => 'Napšašowański ID jo se južo wužył na toś tom boku (ID=$1).',
	'qp_error_invalid_dependance_value' => 'Rjeśazk wótwisnosći napšašowanja (ID=$1) ma njepłaśiwu gódnotu atributa dependance (dependance="$2")',
	'qp_error_missed_dependance_title' => 'Napšašowanje (ID=$1) jo wótwisne wót drugego napšašowanja (ID=$3) z boka [[$2]], ale titel [[$2]] njejo se namakał.
Wótpóraj pak atribut dependance pak wótnow [[$2]]',
	'qp_error_missed_dependance_poll' => 'Napšašowanje (ID=$1) jo wótwisne wót drugego napšašowanja (ID=$3) na boku $2, ale to napšašowanje njeeksistěrujo abo njejo se doněnta składło.
Wótpóraj pak atribut dependance pak napóraj napšašowanje z ID=$3 na boku $2 a składuj jo.
Aby składował napšašowanje, wótpósćel jo, mimo až sy wótegronił na naraźeńske pšašanja.',
	'qp_error_vote_dependance_poll' => 'Pšosym wótgłosuj nejpjerwjej za napšašowanje $1.',
	'qp_error_too_many_spans' => 'Pśewjele kategorijowych klasow za pódkategorije definěrowane',
	'qp_error_unanswered_span' => 'Pódkategorija bźez wótegrona',
	'qp_error_non_unique_choice' => 'Toś to pšašanje trjeba jadnorazne wótegrono naraźenja',
	'qp_error_category_name_empty' => 'Mě kategorije jo prozne',
	'qp_error_proposal_text_empty' => 'Tekst naraźenja jo prozny',
	'qp_error_too_few_categories' => 'Nanejmjenjej dwě kategorji musytej se definěrować',
	'qp_error_too_few_spans' => 'Kužda kategorijowa klasa trjeba nanejmjenjej dwě móžnej wótegronje',
	'qp_error_no_answer' => 'Naraźenje bźez wótegrona',
	'qp_error_unique' => 'Pšašanje typa unique() ma wěcej naraźenjow ako móžne wótegrona su definěrowane: njemóžno pókšacowaś',
);

/** Spanish (Español)
 * @author Translationista
 */
$messages['es'] = array(
	'qp_result_NA' => 'No respondido',
	'qp_result_error' => 'Error de sintaxis',
	'qp_vote_button' => 'Vota',
	'qp_vote_again_button' => 'Cambia tu voto',
	'qp_users_list' => 'Hacer una lista de todos los usuarios',
	'qp_source_link' => 'Fuente',
	'qp_stats_link' => 'Estadísticas',
	'qp_users_link' => 'Usuarios',
	'qp_order_by_username' => 'Organizar por nombre de usuario',
	'qp_export_to_xls' => 'Exportar estadísticas a formato XLS',
	'qp_error_invalid_question_type' => 'Tipo de pregunta inválido: $1',
	'qp_error_type_in_stats_mode' => 'El tipo de pregunta no puede definirse en modo de visualización estadística: $1',
	'qp_error_too_many_spans' => 'Muchas clases de categorías para las subcategorías definidas',
	'qp_error_unanswered_span' => 'Subcategoría sin responder',
	'qp_error_category_name_empty' => 'El nombre de categoría está vacío',
	'qp_error_proposal_text_empty' => 'El texto de propuesta está vacío',
	'qp_error_too_few_categories' => 'Se debe definir al menos dos categorías',
	'qp_error_too_few_spans' => 'Cada clase de categoría requiere de al menos dos respuestas possibles definidas',
	'qp_error_no_answer' => 'Propuesta no respondida',
);

/** French (Français)
 * @author IAlex
 * @author Jean-Frédéric
 * @author McDutchie
 * @author PieRRoMaN
 */
$messages['fr'] = array(
	'pollresults' => 'Résultats des sondages sur ce site',
	'qp_desc' => 'Permet la création de sondages',
	'qp_desc-sp' => '[[Special:PollResults|Page spéciale]] pour consulter les résultats des sondages',
	'qp_result_NA' => 'Pas de réponse',
	'qp_result_error' => 'Erreur de syntaxe',
	'qp_vote_button' => 'Vote',
	'qp_vote_again_button' => 'Changer votre vote',
	'qp_polls_list' => 'Lister tous les sondages',
	'qp_users_list' => 'Lister tous les utilisateurs',
	'qp_browse_to_poll' => "Aller jusqu'à $1",
	'qp_browse_to_user' => "Aller jusqu'à $1",
	'qp_votes_count' => '$1 {{PLURAL:$1|vote|votes}}',
	'qp_source_link' => 'Source',
	'qp_stats_link' => 'Statistiques',
	'qp_users_link' => 'Utilisateurs',
	'qp_voice_link' => "Voix de l'utilisateur",
	'qp_voice_link_inv' => "Voix de l'utilisateur ?",
	'qp_user_polls_link' => 'A participé à $1 {{PLURAL:$1|sondage|sondages}}',
	'qp_user_missing_polls_link' => 'Pas de participation',
	'qp_not_participated_link' => 'Pas de participation',
	'qp_order_by_username' => "Trier par nom d'utilisateur",
	'qp_order_by_polls_count' => 'Trier par nombre de sondages',
	'qp_results_line_qupl' => 'Page « $1 » Sondage « $2 » : $3',
	'qp_results_line_qpl' => 'Page « $1 » Sondage « $2 » : $3, $4, $5, $6',
	'qp_header_line_qpul' => '$1 [ Page « $2 » Sondage « $3 » ]',
	'qp_export_to_xls' => 'Exporter les statistiques au format XLS',
	'qp_users_answered_questions' => '$1 {{PLURAL:$1|utilisateur a|utilisateurs ont}} répondu aux questions',
	'qp_func_no_such_poll' => "Ce sondage n'existe pas ($1)",
	'qp_func_missing_question_id' => 'Veuillez spécifier un id question existant (à partir de 1) pour le sondage $1',
	'qp_func_invalid_question_id' => 'id question=$2 invalide (pas un nombre) pour le sondage $1',
	'qp_func_missing_proposal_id' => 'Veuillez spécifier un id proposition existant (à partir de 0) pour le sondage $1, question $2',
	'qp_func_invalid_proposal_id' => 'id proposition=$3 invalide (pas un nombre) pour le sondage $1, question $2',
	'qp_error_no_such_poll' => "Ce sondage n'existe pas ($1).
Assurez-vous que le sondage est déclaré et sauvegardé, vérifiez également que vous utilisez le caractère délimiteur d'adresse #",
	'qp_error_id_in_stats_mode' => 'Impossible de déclarer un ID du sondage dans le mode statistique',
	'qp_error_dependance_in_stats_mode' => 'Impossible de déclarer la chaîne de dépendance du sondage en mode statistique',
	'qp_error_no_stats' => "Aucune donnée statistique n'est disponible, car personne n'a voté pour ce sondage pour l'instant (address=$1)",
	'qp_error_address_in_decl_mode' => "Impossible d'obtenir une adresse du sondage en mode déclaratif",
	'qp_error_question_not_implemented' => 'Les questions de ce type ne sont pas implémentées : $1',
	'qp_error_invalid_question_type' => 'Type de question invalide : $1',
	'qp_error_type_in_stats_mode' => "Le type de question ne peut pas être défini en mode d'affichage statistique : $1",
	'qp_error_no_poll_id' => "Le tag du sondage n'a pas d'id attribut défini.",
	'qp_error_invalid_poll_id' => "Id sondage invalide (id=$1).
L'id sondage peut contenir uniquement des lettres, des nombres et le caractère espace",
	'qp_error_already_used_poll_id' => "L'id sondage a déjà été utilisé sur cette page (id=$1).",
	'qp_error_invalid_dependance_value' => 'La chaîne de dépendance du sondage (id=$1) a une valeur invalide pour l\'attribut de dépendance (dependance="$2")',
	'qp_error_missed_dependance_title' => "Le sondage (i=$1) est dépendant d'un autre sondage (id=$3) de la page [[$2]], mais le titre [[$2]] n'a pas été trouvé.
Supprimez l'attribut de dépendance, ou bien restaurez [[$2]]",
	'qp_error_missed_dependance_poll' => "Le sondage (id=$1) est dépendant d'un autre sondage (id=$3) à la page $2, mais ce sondage n'existe pas ou n'a pas encore été enregistré.
Supprimez l'attribut de dépendance, ou bien créez le sondage avec id=$3 à la page $2 et enregistrez-le.
Pour enregistrer un sondage, soumettez-le en ne répondant à aucune question de proposition.",
	'qp_error_vote_dependance_poll' => "Veuillez d'abord voter pour le sondage $1.",
	'qp_error_too_many_spans' => 'Trop de classes de catégories pour les sous-catégories définies',
	'qp_error_unanswered_span' => 'Sous-catégorie sans réponse',
	'qp_error_non_unique_choice' => 'Cette question nécessite une réponse de proposition unique',
	'qp_error_category_name_empty' => 'Le nom de la catégorie est vide',
	'qp_error_proposal_text_empty' => 'Le texte de la proposition est vide',
	'qp_error_too_few_categories' => 'Au moins deux catégories doivent être définies',
	'qp_error_too_few_spans' => 'Toute classe de catégorie nécessite au moins deux réponses possibles définies',
	'qp_error_no_answer' => 'Proposition sans réponse',
	'qp_error_unique' => "La question de type unique() a plus de propositions qu'il n'y a de réponses possibles définies : impossible de compléter",
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'pollresults' => 'Resultados das enquisas desta páxina',
	'qp_desc' => 'Permite a creación de enquisas',
	'qp_desc-sp' => '[[Special:PollResults|Páxina especial]] para ollar os resultados das enquisas',
	'qp_result_NA' => 'Sen resposta',
	'qp_result_error' => 'Erro de sintaxe',
	'qp_vote_button' => 'Votar',
	'qp_vote_again_button' => 'Cambiar o seu voto',
	'qp_polls_list' => 'Lista de todas as enquisas',
	'qp_users_list' => 'Lista de todos os usuarios',
	'qp_browse_to_poll' => 'Navegar ata $1',
	'qp_browse_to_user' => 'Navegar ata $1',
	'qp_votes_count' => '$1 {{PLURAL:$1|voto|votos}}',
	'qp_source_link' => 'Fonte',
	'qp_stats_link' => 'Estatísticas',
	'qp_users_link' => 'Usuarios',
	'qp_voice_link' => 'Voz do usuario',
	'qp_voice_link_inv' => 'Voz do usuario?',
	'qp_user_polls_link' => 'Participou {{PLURAL:$1|nunha enquisa|en $1 enquisas}}',
	'qp_user_missing_polls_link' => 'Non participou',
	'qp_not_participated_link' => 'Non participou',
	'qp_order_by_username' => 'Ordenar por nome de usuario',
	'qp_order_by_polls_count' => 'Ordenar por número de enquisa',
	'qp_results_line_qupl' => 'Páxina "$1", enquisa "$2": $3',
	'qp_results_line_qpl' => 'Páxina "$1", enquisa "$2": $3, $4, $5, $6',
	'qp_header_line_qpul' => '$1 [ Páxina "$2", enquisa "$3" ]',
	'qp_export_to_xls' => 'Exportar as estatísticas en formato XLS',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'pollresults' => 'Ergebnis vu dr Abstimmige uf däm Site',
	'qp_desc' => 'Erlaubt s Aalege vu Abstimmige',
	'qp_desc-sp' => '[[Special:PollResults|Spezialsyte]] zum Aalueg vu dr Ergebnis vu dr Abstimmige',
	'qp_result_NA' => 'Kei Antwort',
	'qp_result_error' => 'Syntaxfähler',
	'qp_vote_button' => 'Abstimme',
	'qp_vote_again_button' => 'Dyy Stimm ändere',
	'qp_polls_list' => 'Alli Abstimmige uflischte',
	'qp_users_list' => 'Alli Benutzer uflischte',
	'qp_browse_to_poll' => 'Wyter zue $1',
	'qp_browse_to_user' => 'Wyter zue $1',
	'qp_votes_count' => '$1 {{PLURAL:$1|Stimm|Stimme}}',
	'qp_source_link' => 'Quälle',
	'qp_stats_link' => 'Statischtik',
	'qp_users_link' => 'Benutzer',
	'qp_voice_link' => 'Benutzerstimm',
	'qp_voice_link_inv' => 'Benutzerstimm?',
	'qp_user_polls_link' => 'Mitgmacht bi $1 {{PLURAL:$1|Abstimmig|Abstimmige}}',
	'qp_user_missing_polls_link' => 'Niene mitgmacht',
	'qp_not_participated_link' => 'Niene mitgmacht',
	'qp_order_by_username' => 'No Benutzername gordnet',
	'qp_order_by_polls_count' => 'No Abstimmigsaazahl gordnet',
	'qp_results_line_qupl' => 'Syte „$1“ Abstimmig „$2“: $3',
	'qp_results_line_qpl' => 'Syte „$1“ Abstimmig „$2“: $3, $4, $5, $6',
	'qp_header_line_qpul' => '$1 [ Syte „$2“ Abstimmig „$3“ ]',
	'qp_header_line_qucl' => '$1. $2<br />$3 ??? $4',
	'qp_export_to_xls' => 'Statischtik im XLS-Format exportiere',
	'qp_users_answered_questions' => '$1 {{PLURAL:$1|Benutzer het|Benutzer hän}} Antworte uf d Froge gee',
	'qp_func_no_such_poll' => 'Kei sonigi Abstimmig ($1)',
	'qp_func_missing_question_id' => 'Bitte spezifizier e Froge-Chännnummere (mit 1 aafange) fir d Abstimmig $1',
	'qp_func_invalid_question_id' => 'Uugiltigi Froge-Chännnummere ($2, kei Zahl) fir d Abstimmig $1',
	'qp_func_missing_proposal_id' => 'Bitte spezifiezier e Vorschlags-Chännnummere, wu s scho git (mit 0 aafange) fir d Abstimmig $1, Frog $2',
	'qp_func_invalid_proposal_id' => 'Nit giltigi Vorschlags-Chännnummere ($3, kei Zahl) fir d Abstimmig $1, Frog $2',
	'qp_error_no_such_poll' => 'Kei sonigi Abstimmig ($1).
Stell sicher, ass d Abstimmig verchindet un gspycheret woren isch, stell au sicher, ass as Trännzeiche in dr Adräss alliwyl # brucht wird',
	'qp_error_id_in_stats_mode' => 'Cha dr Abstimmig kei Chännnummere zuewyyse im statistische Modus',
	'qp_error_dependance_in_stats_mode' => 'Cha d Abhägikeits-Chette vu dr Abstimmig nit verchinde im statistische Modus',
	'qp_error_no_stats' => 'Kei statistischi Date verfiegbar, wel no keini Benutzer abgstumme hän (Adräss $1)',
	'qp_error_address_in_decl_mode' => 'Cha kei Adräss vu dr Abstimmig iberchuu im Verchindigs-Modus',
	'qp_error_question_not_implemented' => 'Forge vu däm Typ sin nit vorgsäh: $1',
	'qp_error_invalid_question_type' => 'Nit giltige Frogetyp: $1',
	'qp_error_type_in_stats_mode' => 'Frogetyp cha nit im statistische Widergabmodus definiert wäre: $1',
	'qp_error_no_poll_id' => 'D Eigeschaft „id“ (Chännnummere) isch nit definiert fir d Abstimmig.',
	'qp_error_invalid_poll_id' => 'Nit giltigi Abstimmigs-Chännnummere ($1).
In dr Chännnummere derf s nume Buechstabe, Zahle un Läärstelle haa.',
	'qp_error_already_used_poll_id' => 'Die Abstimmigs-Chännnummere wird schoi rbuc ht uf däre Syte ($1)',
	'qp_error_invalid_dependance_value' => 'D Abhängigkeits-Chette vu dr Abstimmig (id=$1) het e nit giltige Wärt vu dr Abhängigkeitseigeschaft („$2“)',
	'qp_error_missed_dependance_title' => 'D Abstimmig ($1) hangt aqb vun ere andere Abstimmig ($3) vu dr Syte [[$2]], aber dr Titel [[$2]] isch nit gfunde wore.
Entwäder due d Abhängigkeitseigeschaft uuseneh oder spychere [[$2]] um',
	'qp_error_missed_dependance_poll' => 'D Abstimmig ($1) isch abhängig vun ere andere Abstimmig ($3) uf dr Syte $2, aber sälli Abstimmig git s nit oder si isch nonig gspycheret wore.
Entwäder due d Abhängigkeitseigeschaft uuseneh oder leg d Abstimmig aa mit dr Chännnummere $3 uf dr Syte $2 un due si spychere.
Go ne Abstimmig spychere due si ibertrage ohni ne Antwort gee uf irged e Vorschlagsfrog.',
	'qp_error_vote_dependance_poll' => 'Bitte stimm zerscht ab in dr Abstimmig $1.',
	'qp_error_too_many_spans' => 'S sin zvyl Kategorieklasse definiert fir d Unterkategorie',
	'qp_error_unanswered_span' => 'Unterkategori ohni Antwort',
	'qp_error_non_unique_choice' => 'D Frog brucht ei einzige Vorschlags-Antwort',
	'qp_error_category_name_empty' => 'Kei Kategoriname aagee',
	'qp_error_proposal_text_empty' => 'Kei Vorschlagstext aagee',
	'qp_error_too_few_categories' => 'Zmindescht zwo Kategorie mien definiert wäre',
	'qp_error_too_few_spans' => 'Fir jedi Kategorieklasse brucht s zmindescht zwo definierti Antworte',
	'qp_error_no_answer' => 'Vorschlag ohni Antwort',
	'qp_error_unique' => 'Fir d Frog vum Typ unique() git s meh Vorschleg wie Antworte definiert sin: cha nit abgschlosse wäre',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'pollresults' => 'Wuslědki wothłosowanjow na tutym sydle',
	'qp_desc' => 'Zmóžnja wutworjenje wothłosowanjow',
	'qp_desc-sp' => '[[Special:PollResults|Specialna strona]] za wobhladowanje wuslědkow wothłosowanjow',
	'qp_result_NA' => 'Njewotmołwjeny',
	'qp_result_error' => 'Syntaksowy zmylk',
	'qp_vote_button' => 'Hłosować',
	'qp_vote_again_button' => 'Twoje wothłosowanje změnić',
	'qp_polls_list' => 'Wšě wothłosowanja nalistować',
	'qp_users_list' => 'Wšěch wužiwarjow nalistować',
	'qp_browse_to_poll' => 'Dale k $1',
	'qp_browse_to_user' => 'Dale k $1',
	'qp_votes_count' => '$1 {{PLURAL:$1|hłós|hłosaj|hłosy|hłosow}}',
	'qp_source_link' => 'Žórło',
	'qp_stats_link' => 'Statistika',
	'qp_users_link' => 'Wužiwarjo',
	'qp_voice_link' => 'Wužiwarski hłós',
	'qp_voice_link_inv' => 'Wužiwarski hłós?',
	'qp_user_polls_link' => 'Wobdźěli so na $1 {{PLURAL:$1|wothłosownju|wothłosowanjomaj|wothłosowanjach|wothłosowanjach}}',
	'qp_user_missing_polls_link' => 'Žane wobdźělenje',
	'qp_not_participated_link' => 'Njewobdźěleny',
	'qp_order_by_username' => 'Porjad po wužiwarskim mjenje',
	'qp_order_by_polls_count' => 'Porjad po ličbje wothłosowanjow',
	'qp_results_line_qupl' => 'Strona "$1" wothłosowanje "$2": $3',
	'qp_results_line_qpl' => 'Strona "$1" wothłosowanje "$2": $3, $4, $5, $6',
	'qp_header_line_qpul' => '$1 [ strona "$2" wothłosowanje "$3" ]',
	'qp_export_to_xls' => 'Statistiku do XLS-formata eksportować',
	'qp_users_answered_questions' => '$1 {{PLURAL:$1|wužiwar je|wužiwarjej staj|wužiwarjo su|wužiwarjow je}} na prašenja {{PLURAL:$1|wotmołwił|wotmołwiłoj|wotmołwili|wotmołwiło}}',
	'qp_func_no_such_poll' => 'Žane tajke wothłosowanje ($1)',
	'qp_func_missing_question_id' => 'Prošu podaj eksistowacy prašenski ID (započinajo wot 1) za wothłosowanje $1',
	'qp_func_invalid_question_id' => 'Njepłaćiwe prašenje id=$2 (žana ličba) za wothłosowanje $1',
	'qp_func_missing_proposal_id' => 'Prošu podaj eksistowacy namjetowy ID (započinajo wot 0) za naprašowanje $1, prašenje $2',
	'qp_func_invalid_proposal_id' => 'Njepłaćiwy namjet id=$3 (žana ličba) za wothłosowanje $1, prašenje $2',
	'qp_error_no_such_poll' => 'Tajke wothłosowanje njeje ($1).
Zawěsć, zo wothłosowanje bu deklarowane a składowane, přeswědč so, zo wužiwaš adresowe dźělatko #',
	'qp_error_id_in_stats_mode' => 'Njeje móžno ID wothłosowanja w statistiskim modusu deklarowác',
	'qp_error_dependance_in_stats_mode' => 'Njeje móžno wotwisnosćowy rjećazk  wothłosowanja w statistiskim modusu deklarować',
	'qp_error_no_stats' => 'Žane statistiske daty k dispoziciji, dokelž dotal nichtó njeje za tute wothłosowanje hłosował (adresa=$1)',
	'qp_error_address_in_decl_mode' => 'Njeje móžno adresu wothłosowanja w deklaraciskim modusu dóstać',
	'qp_error_question_not_implemented' => 'Prašenja tutoho typa njejsu implementowane: $1',
	'qp_error_invalid_question_type' => 'Njepłaćiwy prašenski typ: $1',
	'qp_error_type_in_stats_mode' => 'Prašenski typ njeda so w statistiskim zwobraznjenskim modusu definować: $1',
	'qp_error_no_poll_id' => 'Taflička Poll njeje atribut ID definował.',
	'qp_error_invalid_poll_id' => 'Njepłaćiwy Id wothłosowanja (ID=$1).
ID wothłosowanja smě jenož pismiki, ličby a mjezeru wobsahować',
	'qp_error_already_used_poll_id' => 'ID wothłosowanja wužiwa so hižo na tutej stronje (ID=$1).',
	'qp_error_invalid_dependance_value' => 'Wotwisnosćowy rjećazk wothłosowanja (id=$1) ma njepłaćiwu hódnotu atributa dependance (dependance="$2")',
	'qp_error_missed_dependance_title' => 'Wothłosowanje (ID=$1) je wot druheho wothłosowanja (ID=$3) ze strony [[$2]] wotwisne, ale titul [[$2]] njebu namakany. Wotstroń pak atribut dependance pak wobnow [[$2]]',
	'qp_error_missed_dependance_poll' => 'Wothłosowanje (ID=$1) je wot druheho wothłosowanja (ID=$3) na stronje $2 wotwisne, ale te wothłosowanje njeeksistuje abo njeje so składowało.
Wotstroń pak atribut dependance pak wutwor wothłosowanje z ID=$3 na stronje $2 a składuj jo.
Zo by wothłosowanje składował, wotpósćel jo, bjeztoho zo by na namjetowe prašenja wotmołwił.',
	'qp_error_vote_dependance_poll' => 'Prošu hłosuj najprjedy za wothłosowanje $1.',
	'qp_error_too_many_spans' => 'Přewjele kategorijowych klasow za podkategorije definowane',
	'qp_error_unanswered_span' => 'Njewotmołwjena podkategorija',
	'qp_error_non_unique_choice' => 'Tute prašenje trjeba jónkróćnu namjetowu wotmołwu',
	'qp_error_category_name_empty' => 'Kategorijowe mjeno je prózdne',
	'qp_error_proposal_text_empty' => 'Namjetowy tekst je prózdny',
	'qp_error_too_few_categories' => 'Znajmjeńša dwě kategoriji dyrbitej so definować',
	'qp_error_too_few_spans' => 'Kóžda kategorijowa klasa trjeba znajmjeńša dwě móžnej definowanej wotmołwje',
	'qp_error_no_answer' => 'Njewotmołwjeny namjet',
	'qp_error_unique' => 'Prašenje typa unique() ma wjace namjetow hač su móžne wotmołwy definowane: njemóžno pokročować',
);

/** Hungarian (Magyar)
 * @author Dani
 */
$messages['hu'] = array(
	'pollresults' => 'Az oldal szavazásainak eredményei',
	'qp_desc' => 'Lehetővé teszi szavazások készítését',
	'qp_desc-sp' => '[[Special:PollResults|Speciális lap]] a szavazások eredményeinek megtekintésére',
	'qp_result_NA' => 'Nem válaszolt',
	'qp_result_error' => 'Szintaktikai hiba',
	'qp_vote_button' => 'Szavazás',
	'qp_vote_again_button' => 'Szavazat megváltoztatása',
	'qp_polls_list' => 'Szavazások listája',
	'qp_users_list' => 'Felhasználók listája',
	'qp_browse_to_poll' => 'Ugrás a szavazás helyére: $1',
	'qp_browse_to_user' => 'A felhasználó lapja: $1',
	'qp_votes_count' => '$1 szavazat',
	'qp_source_link' => 'Forrás',
	'qp_stats_link' => 'Statisztika',
	'qp_users_link' => 'Felhasználók',
	'qp_voice_link' => 'A felhasználó szavazatai',
	'qp_voice_link_inv' => 'A felhasználó szavazatai?',
	'qp_user_polls_link' => '$1 szavazáson vett részt',
	'qp_user_missing_polls_link' => 'Nem vett részt',
	'qp_not_participated_link' => 'Nem vett részt',
	'qp_order_by_username' => 'Rendezés felhasználónév szerint',
	'qp_order_by_polls_count' => 'Rendezés a szavazások száma szerint',
	'qp_results_line_qupl' => 'Lap: „$1”, szavazás: „$2”: $3',
	'qp_results_line_qpl' => 'Lap: „$1”, szavazás: „$2”: $3, $4, $5, $6',
	'qp_header_line_qpul' => '$1 [ Lap: „$2”, szavazás: „$3” ]',
	'qp_export_to_xls' => 'Statisztikák exportálása XLS-formátumban',
	'qp_users_answered_questions' => '$1 felhasználó válaszolt a kérdésekre',
	'qp_func_no_such_poll' => 'Nincs ilyen szavazás ($1)',
	'qp_func_missing_question_id' => 'Add meg egy létező kérdés azonosítóját (1-től kezdve) a(z) $1 szavazáshoz',
	'qp_func_invalid_question_id' => 'Érvénytelen kérdés id=$2 (nem szám) a(z) $1 szavazáshoz',
	'qp_func_missing_proposal_id' => 'Add meg egy létező javaslat azonosítóját (0-tól kezdve) a(z) $1 szavazás $2 kérdéséhez',
	'qp_func_invalid_proposal_id' => 'Érvénytelen javaslat id=$3 (nem szám) a(z) $1 szavazás $2 kérdéséhez',
	'qp_error_no_such_poll' => 'Nincs ilyen szavazás ($1).
Győződj meg róla, hogy a szavazás deklarálva van, és elmentetted, valamint hogy használtad-e a címhatároló karaktert (#)',
	'qp_error_id_in_stats_mode' => 'Nem deklarálható egy szavazás azonosítója statisztikai módban',
	'qp_error_dependance_in_stats_mode' => 'Nem deklarálható a szavazás függőségi lánca statisztikai módban',
	'qp_error_no_stats' => 'A statisztikai adatok nem elérhetőek, mert még senki sem szavazott ezen a szavazáson (address=$1)',
	'qp_error_address_in_decl_mode' => 'A szavazás címe nem kérhető le deklarációs módban',
	'qp_error_question_not_implemented' => 'Az ilyen típusú kérdések nincsenek implementálva: $1',
	'qp_error_invalid_question_type' => 'Érvénytelen kérdéstípus: $1',
	'qp_error_type_in_stats_mode' => 'A kérdés típusát nem lehet megadni statisztikai módban: $1',
	'qp_error_no_poll_id' => 'A szavazás tagnek nincs azonosító (id) attribútuma megadva.',
	'qp_error_invalid_poll_id' => 'Érvénytelen szavazásazonosító (id=$1).
A szavazásazonosító csak betűket, számokat és szóközt tartalmazhat',
	'qp_error_already_used_poll_id' => 'Ez a szavazásazonosító már használva volt korábban ezen a lapon (id=$1).',
	'qp_error_invalid_dependance_value' => 'A szavazás (id=$1) függőségi lánca érvénytelen függőségi attribútum-értékkel rendelkezik (dependance="$2")',
	'qp_error_missed_dependance_title' => 'A szavazás (id=$1) függ a(z) [[$2]] lapon található másik szavazástól (id=$3), de ez a lap nem található.
Vagy távolítsd el a függőségi attribútumot, vagy állítsd helyre a(z) [[$2]] lapot.',
	'qp_error_missed_dependance_poll' => 'A szavazás (id=$1) függ egy, a(z) $2 lapon található másik szavazástól  (id=$3), de ez a szavazás nem létezik, vagy még nem lett elmentve.
Távolítsd el a függőségi attribútumot, vagy készítsd el a(z) id=$3 azonosítóval rendelkező szavazást a(z) $2 lapon, és mentsd el.
A szavazás elmentéséhez küldd el azt anélkül, hogy válaszolnál bármelyik kérdésre.',
	'qp_error_vote_dependance_poll' => 'Először szavazz a(z) $1 szavazáson.',
	'qp_error_too_many_spans' => 'Túl sok kategóriaosztály lett az alkategóriák számára megadva.',
	'qp_error_unanswered_span' => 'Megválaszolatlan alkategória',
	'qp_error_non_unique_choice' => 'Ennek kérdésnek egyedi javasolt válaszra van szüksége',
	'qp_error_category_name_empty' => 'A kategória neve üres',
	'qp_error_proposal_text_empty' => 'A javaslat szövege üres',
	'qp_error_too_few_categories' => 'Legalább két kategóriát kell megadni',
	'qp_error_too_few_spans' => 'Mindegyik kategóriaosztályhoz meg kell adni legalább két lehetséges választ',
	'qp_error_no_answer' => 'Megválaszolatlan javaslat',
	'qp_error_unique' => 'A unique() típus kérdése több javaslattal rendelkezik, mint a megadott lehetséges válaszol száma: nem lehet befejezni',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'pollresults' => 'Resultatos del sondages in iste sito',
	'qp_desc' => 'Permitte le creation de sondages',
	'qp_desc-sp' => '[[Special:PollResults|Pagina special]] pro vider le resultatos del sondages',
	'qp_result_NA' => 'Sin responsa',
	'qp_result_error' => 'Error de syntaxe',
	'qp_vote_button' => 'Votar',
	'qp_vote_again_button' => 'Modificar tu voto',
	'qp_polls_list' => 'Listar tote le sondages',
	'qp_users_list' => 'Listar tote le usatores',
	'qp_browse_to_poll' => 'Navigar verso $1',
	'qp_browse_to_user' => 'Navigar verso $1',
	'qp_votes_count' => '$1 {{PLURAL:$1|voto|votos}}',
	'qp_source_link' => 'Origine',
	'qp_stats_link' => 'Statisticas',
	'qp_users_link' => 'Usatores',
	'qp_voice_link' => 'Voce del usator',
	'qp_voice_link_inv' => 'Voce de usator?',
	'qp_user_polls_link' => 'Participava in $1 {{PLURAL:$1||sondage|sondages}}',
	'qp_user_missing_polls_link' => 'Nulle participation',
	'qp_not_participated_link' => 'Non participate',
	'qp_order_by_username' => 'Ordinar per nomine de usator',
	'qp_order_by_polls_count' => 'Ordinar per numero de sondages',
	'qp_results_line_qupl' => 'Pagina "$1" Sondage "$2": $3',
	'qp_results_line_qpl' => 'Pagina "$1" Sondage "$2": $3, $4, $5, $6',
	'qp_header_line_qpul' => '$1 [ Pagina "$2" Sondage "$3" ]',
	'qp_export_to_xls' => 'Exportar statisticas in formato XLS',
	'qp_users_answered_questions' => 'usatores respondeva al questiones',
	'qp_func_no_such_poll' => 'Sondage non existe ($1)',
	'qp_func_missing_question_id' => 'Per favor specifica le ID de un question existente (a partir de 1) pro le sondage $1',
	'qp_func_invalid_question_id' => 'Question invalide id=$2 (non un numero) pro le sondage $1',
	'qp_func_missing_proposal_id' => 'Per favor specifica un ID de proposition existente (a partir de 0) pro le sondage $1, question $2',
	'qp_func_invalid_proposal_id' => 'ID de proposition invalide "$3" (non un numero) pro le sondage $1, question $2',
	'qp_error_no_such_poll' => 'Sondage non existe ($1).
Verifica que le sondage ha essite declarate e salveguardate, e que le character # es usate como delimitator de adresse.',
	'qp_error_id_in_stats_mode' => 'Non pote declarar un ID del sondage in modo statistic',
	'qp_error_dependance_in_stats_mode' => 'Non pote declarar le catena de dependentia del sondage in modo statistic',
	'qp_error_no_stats' => 'Nulle dato statistic es disponibile, proque necuno ha ancora votate pro iste sondage (adresse=$1)',
	'qp_error_address_in_decl_mode' => 'Non pote obtener un adresse del sondage in modo declarative',
	'qp_error_question_not_implemented' => 'Le questiones de iste typo non es implementate: $1',
	'qp_error_invalid_question_type' => 'Typo de question invalide: $1',
	'qp_error_type_in_stats_mode' => 'Le typo de question non pote esser definite in modo de presentation statistic: $1',
	'qp_error_no_poll_id' => 'Le etiquetta del sondage non ha un attributo "id" definite.',
	'qp_error_invalid_poll_id' => 'ID de sondage invalide (id=$1).
Le ID del sondage pote continer solmente litteras, numeros e le character de spatio.',
	'qp_error_already_used_poll_id' => 'Le ID del sondage ha ja essite usate in iste pagina (id=$1).',
	'qp_error_invalid_dependance_value' => 'Le catena de dependentia del sondage (id=$1) ha un valor invalide del attributo de dependentia (dependance="$2")',
	'qp_error_missed_dependance_title' => 'Le sondage (id=$1) depende de un altere sondage (id=$3) del pagina [[$2]], ma le titulo [[$2]] non ha essite trovate.
O remove le attributo de dependentia, o restaura [[$2]].',
	'qp_error_missed_dependance_poll' => 'Le sondage (id=$1) depende de un altere sondage (id=$3) al pagina $2, ma ille sondage non existe o non ha ancora essite salveguardate.
O remove le attributo de dependentia, o crea le sondage con id=$3 al pagina $2 e salveguarda lo.
Pro salveguardar un sondage, submitte lo sin responder a alcun question de proposition.',
	'qp_error_vote_dependance_poll' => 'Per favor vota primo pro le sondage $1.',
	'qp_error_too_many_spans' => 'Troppo de classes de categoria pro le subcategorias definite',
	'qp_error_unanswered_span' => 'Subcategoria sin responsa',
	'qp_error_non_unique_choice' => 'Iste question require un responsa de proposition unic',
	'qp_error_category_name_empty' => 'Le nomine del categoria es vacue',
	'qp_error_proposal_text_empty' => 'Le texto del proposition es vacue',
	'qp_error_too_few_categories' => 'Al minus duo categorias debe esser definite',
	'qp_error_too_few_spans' => 'Cata classe de categoria require le definition de al minus duo responsas possibile',
	'qp_error_no_answer' => 'Proposition sin responsa',
	'qp_error_unique' => 'Pro le question de typo unique() es definite plus propositiones que responsas possibile: non pote completar',
);

/** Japanese (日本語)
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'pollresults' => 'このサイトでの投票結果',
	'qp_desc' => '投票を実施できるようにする',
	'qp_desc-sp' => '投票結果を見るための[[Special:PollResults|特別ページ]]',
	'qp_result_NA' => '回答されていません',
	'qp_result_error' => '構文エラー',
	'qp_vote_button' => '投票',
	'qp_vote_again_button' => 'あなたの票を変更',
	'qp_polls_list' => '全投票一覧',
	'qp_users_list' => '全利用者一覧',
	'qp_browse_to_poll' => '$1 を閲覧',
	'qp_browse_to_user' => '$1 を閲覧',
	'qp_votes_count' => '$1{{PLURAL:$1|票}}',
	'qp_stats_link' => '統計',
	'qp_users_link' => '利用者',
	'qp_voice_link' => '利用者の声',
	'qp_voice_link_inv' => '利用者の声?',
	'qp_user_polls_link' => '$1件の{{PLURAL:$1|投票}}に参加',
	'qp_user_missing_polls_link' => '参加なし',
	'qp_not_participated_link' => '未参加',
	'qp_order_by_username' => '利用者名順に整列',
	'qp_order_by_polls_count' => '投票数順に整列',
	'qp_results_line_qupl' => 'ページ「$1」投票「$2」: $3',
	'qp_results_line_qpl' => 'ページ「$1」投票「$2」: $3、$4、$5、$6',
	'qp_header_line_qpul' => '$1 [ ページ「$2」投票「$3」]',
	'qp_export_to_xls' => '統計を XLS 形式でエクスポート',
	'qp_users_answered_questions' => '$1{{PLURAL:$1|人}}の利用者がこの質問に答えました',
	'qp_error_category_name_empty' => 'カテゴリー名が空です',
	'qp_error_proposal_text_empty' => '提案文が空です',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'pollresults' => 'Resultater vun der Ëmfro op dësem Site',
	'qp_desc' => "Erlaabt et Ëmfroen z'organiséieren",
	'qp_desc-sp' => "[[Special:PollResults|Spezialsäit]] fir d'Resultater vun der Ëmfro ze gesinn",
	'qp_result_NA' => 'Keng Äntwert',
	'qp_result_error' => 'Syntaxfeeler',
	'qp_vote_button' => 'Ofstëmmen',
	'qp_vote_again_button' => 'Ännert Är Ofstëmmung',
	'qp_polls_list' => 'All Ëmfroe weisen',
	'qp_users_list' => 'All Benotzer opzielen',
	'qp_browse_to_poll' => 'Op $1 goen',
	'qp_browse_to_user' => 'Bäi de(n) $1 goen',
	'qp_votes_count' => '$1 {{PLURAL:$1|Stëmm|Stëmmen}}',
	'qp_source_link' => 'Quell',
	'qp_stats_link' => 'Statistiken',
	'qp_users_link' => 'Benotzer',
	'qp_voice_link' => 'Stëmm vum Benotzer',
	'qp_voice_link_inv' => 'Stëmm vum Benotzer?',
	'qp_user_missing_polls_link' => 'Keng Bedeelegung',
	'qp_not_participated_link' => 'Net matgemaach',
	'qp_order_by_username' => 'Nom Benotzernumm zortéieren',
	'qp_results_line_qupl' => 'Säit "$1" Ëmfro "$2": $3',
	'qp_results_line_qpl' => 'Säit "$1" Ëmfro "$2": $3, $4, $5, $6',
	'qp_header_line_qpul' => '$1 [ Säit "$2" Ëmfro "$3" ]',
	'qp_export_to_xls' => "Exportéiert d'Statistiken am XLS-Format",
	'qp_users_answered_questions' => "$1 {{PLURAL:$1|Benotzer huet|Benotzer hun}} op d'Froe geäntwert",
	'qp_func_no_such_poll' => 'Et gëtt keng esou eng Ëmfro ($1)',
	'qp_error_no_stats' => 'Et gëtt keng statistesch Donnéeën, well bis elo (Adress=$1) kee fir dës Ëmfro gestëmmt huet.',
	'qp_error_already_used_poll_id' => "D'Ëmfro-Nummer (poll id) gouf op dëser Säit scho benotzt (id=$1).",
	'qp_error_vote_dependance_poll' => "Stëmmt w.e.g. fir d'éischt bäi der Ëmfro $1 of.",
	'qp_error_unanswered_span' => 'Ënnerkategorie ouni Äntwert',
	'qp_error_category_name_empty' => 'Den Numm vun der Kategorie ass eidel',
	'qp_error_proposal_text_empty' => 'Den Text vum Virschlag ass eidel',
	'qp_error_no_answer' => 'Propos ouni Äntwert',
);

/** Dutch (Nederlands)
 * @author McDutchie
 * @author Siebrand
 */
$messages['nl'] = array(
	'pollresults' => 'Resultaten van de stemmingen op deze site',
	'qp_desc' => 'Maakt het aanmaken van peilingen mogelijk',
	'qp_desc-sp' => '[[Special:PollResults|Speciale pagina]] voor het bekijken van de resultaten van peilingen',
	'qp_result_NA' => 'Niet beantwoord',
	'qp_result_error' => 'Er zit een fout in de syntaxis',
	'qp_vote_button' => 'Stemmen',
	'qp_vote_again_button' => 'Stem wijzigen',
	'qp_polls_list' => 'Alle peilingen weergeven',
	'qp_users_list' => 'Alle gebruikers weergeven',
	'qp_browse_to_poll' => 'Naar de peiling $1',
	'qp_browse_to_user' => 'Naar gebruiker $1',
	'qp_votes_count' => '$1 {{PLURAL:$1|stem|stemmen}}',
	'qp_source_link' => 'Bron',
	'qp_stats_link' => 'Statistieken',
	'qp_users_link' => 'Gebruikers',
	'qp_voice_link' => 'Gebruikersstem',
	'qp_voice_link_inv' => 'Gebruikersstem?',
	'qp_user_polls_link' => 'Heeft deelgenomen aan $1 {{PLURAL:$1|peiling|peilingen}}',
	'qp_user_missing_polls_link' => 'Geen deelname',
	'qp_not_participated_link' => 'Niet deelgenomen',
	'qp_order_by_username' => 'Sorteren op gebruikersnaam',
	'qp_order_by_polls_count' => 'Sorteren op peilingenaantal',
	'qp_results_line_qupl' => 'Pagina "$1", peiling "$2": $3',
	'qp_results_line_qpl' => 'Pagina "$1", peiling "$2": $3, $4, $5, $6',
	'qp_header_line_qpul' => '$1 [ pagina "$2", peiling "$3" ]',
	'qp_header_line_qucl' => '$1. $2<br />$3 ??? $4',
	'qp_export_to_xls' => 'Statistieken naar XLS-formaat exporteren',
	'qp_users_answered_questions' => '$1 {{PLURAL:$1|gebruiker heeft|gebruikers hebben}} de vragen beantwoord',
	'qp_func_no_such_poll' => 'Die peiling bestaat niet ($1)',
	'qp_func_missing_question_id' => 'Geef alstublieft een vraag-ID op (begin bij 1) voor de peiling $1',
	'qp_func_invalid_question_id' => 'Ongeldig vraag-ID ($2 - geen getal) voor de peiling $1',
	'qp_func_missing_proposal_id' => 'Geef alstublieft een voorstel-ID op (begin bij 0) voor de peiling $1, vraag $2',
	'qp_func_invalid_proposal_id' => 'Ongeldig voorstel-ID ($3 - geen getal) voor de peiling $1, vraag $2',
	'qp_error_no_such_poll' => 'Die peiling bestaat niet ($1).
Zorg dat de peiling is ingesteld en opgeslagen, en dat het adresscheidingsteken "#" is gebruikt.',
	'qp_error_id_in_stats_mode' => 'Het is niet mogelijk een ID voor de peiling te declareren in de statistische modus',
	'qp_error_dependance_in_stats_mode' => 'Het is niet mogelijk een afhankelijkheidsketen voor de peiling te declareren in de statistische modus',
	'qp_error_no_stats' => 'Er zijn geen statistische gegevens beschikbaar omdat er nog geen gebruikers hebben gestemd in deze peiling (adres $1)',
	'qp_error_address_in_decl_mode' => 'Het is niet mogelijk een adres van de peiling op te vragen in de declaratiemodus',
	'qp_error_question_not_implemented' => 'Vragen van dit type zijn niet beschikbaar: $1',
	'qp_error_invalid_question_type' => 'Ongeldig vraagtype: $1',
	'qp_error_type_in_stats_mode' => 'Het vraagtype kan niet gedefinieerd wordne in de statistische weergavemodus: $1',
	'qp_error_no_poll_id' => 'De eigenschap "id" is niet gedefinieerd voor de peiling.',
	'qp_error_invalid_poll_id' => 'Ongeldig peiling-ID ($1)
Het ID mag alleen letters, cijfers en spaties bevatten.',
	'qp_error_already_used_poll_id' => 'Het peilingsnummer wordt al gebruikt op deze pagina (ID $1)',
	'qp_error_invalid_dependance_value' => 'De peilingafhankelijkheidsketen (ID $1) heeft een ongeldige waarde voor de afhankelijkheidseigenschap (dependance "$2")',
	'qp_error_missed_dependance_title' => 'De peiling (ID $1) is afhankelijk van een andere peiling (ID $3) op pagina [[$2]], maar de pagina [[$2]] bestaat niet.
Verwijder de eigenschap "dependance" of plaats de pagina [[$2]] terug.',
	'qp_error_missed_dependance_poll' => 'De peiling (ID $1) is afhankelijk van een andere peiling (ID $3) op pagina $2, maar die peiling bestaat niet of is nog niet opgeslagen.
Verwijder de eigenschap "dependance" of maak een peiling aan met het ID $3 op pagina $2.
Sla een peiling op door deze op te slaan zonder dat enig voorstel is beantwoord.',
	'qp_error_vote_dependance_poll' => 'Stem alstublieft eerst in de peiling $1.',
	'qp_error_too_many_spans' => 'Er zijn te veel categorieklassen voor de subcategorieën gedefinieerd',
	'qp_error_unanswered_span' => 'Onbeantwoorde subcategorie',
	'qp_error_non_unique_choice' => 'Voor deze vraag is een uniek voorstelantwoord nodig',
	'qp_error_category_name_empty' => 'Er is geen categorienaam opgegeven',
	'qp_error_proposal_text_empty' => 'Er is geen voorsteltekst opgegeven',
	'qp_error_too_few_categories' => 'Er moeten tenminste twee categorieën gedefinieerd worden.',
	'qp_error_too_few_spans' => 'Voor iedere categorieklasse dienen tenminste twee mogelijk antwoorden gedefinieerd te zijn',
	'qp_error_no_answer' => 'Onbeantwoord voorstel',
	'qp_error_unique' => 'Voor de vraag van het type unique() zijn meer voorstellen dan mogelijke antwoorden gedefinieerd. Dat is niet recht te breien.',
);

/** Russian (Русский)
 * @author QuestPC
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'pollresults' => 'Результаты опросов на сайте',
	'qp_desc' => 'Позволяет создавать опросы',
	'qp_desc-sp' => '[[Special:PollResults|Специальная страница]] для просмотра результатов опросов',
	'qp_result_NA' => 'Нет ответа',
	'qp_result_error' => 'Синтаксическая ошибка',
	'qp_vote_button' => 'Проголосовать',
	'qp_vote_again_button' => 'Переголосовать',
	'qp_polls_list' => 'Список всех опросов',
	'qp_users_list' => 'Список всех участников',
	'qp_browse_to_poll' => 'Перейти к $1',
	'qp_browse_to_user' => 'Перейти к $1',
	'qp_votes_count' => '$1 {{PLURAL:$1|голос|голоса|голосов}}',
	'qp_source_link' => 'Исходный код',
	'qp_stats_link' => 'Статистика',
	'qp_users_link' => 'Участники',
	'qp_voice_link' => 'Голос участника',
	'qp_voice_link_inv' => 'Голос участника?',
	'qp_user_polls_link' => 'Участвовал в $1 {{PLURAL:$1|опросе|опросах|опросах}}',
	'qp_user_missing_polls_link' => 'Неучастие в опросах',
	'qp_not_participated_link' => 'Список неучаствовавших',
	'qp_order_by_username' => 'Сортировать по имени пользователя',
	'qp_order_by_polls_count' => 'Сортировать по количеству опросов',
	'qp_results_line_qupl' => 'Страница "$1" Опрос "$2": $3',
	'qp_results_line_qpl' => 'Страница "$1" Опрос "$2": $3, $4, $5, $6',
	'qp_header_line_qpul' => '$1 [ Страница "$2" Опрос "$3" ]',
	'qp_results_line_qpul' => '$1: $2',
	'qp_header_line_qucl' => '$1. $2<br />$3 ??? $4',
	'qp_results_line_qucl' => '$1: $2 $3',
	'qp_export_to_xls' => 'Экспортировать статистику в XLS формате',
	'qp_users_answered_questions' => 'На вопросы {{PLURAL:$1|ответил $1 участник|ответило $1 участника|ответили $1 участников}}',
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
	'qp_error_question_not_implemented' => 'Вопросы данного типа не реализованы в коде расширения: $1',
	'qp_error_invalid_question_type' => 'Недопустимый тип вопроса: $1',
	'qp_error_type_in_stats_mode' => 'Недопустимо определять тип вопроса в статистическом режиме: $1',
	'qp_error_no_poll_id' => 'Тэг опроса не имеет атрибута id.',
	'qp_error_invalid_poll_id' => 'Недопустимый идентификатор опроса (id=$1). Идентификатор опроса может содержать только буквы, цифры и символ пробела',
	'qp_error_already_used_poll_id' => 'Установленный атрибут id опроса уже используется другим опросом на данной странице (id=$1).',
	'qp_error_invalid_dependance_value' => 'В цепочке зависимости опросов для опроса (id=$1) было найдено синтаксически неверное значение атрибута зависимости (dependance="$2")',
	'qp_error_missed_dependance_title' => 'Опрос с идентификатором id=$1 имеет атрибут зависимости от другого опроса (id=$3), находящегося на отсутствующей странице [[$2]]. Необходимо убрать атрибут зависимости от другого опроса, либо восстановить страницу [[$2]]',
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
	'qp_error_unique' => 'Опрос, имеющий тип unique(), не должен иметь больше ответов чем вопросов',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'qp_votes_count' => '$1 {{PLURAL:$1|వోటు|వోట్లు}}',
	'qp_stats_link' => 'గణాంకాలు',
	'qp_users_link' => 'వాడుకరులు',
	'qp_error_category_name_empty' => 'వర్గం పేరు ఖాళీగా ఉంది',
);

/** Ukrainian (Українська)
 * @author NickK
 * @author Prima klasy4na
 */
$messages['uk'] = array(
	'qp_desc' => 'Дозволяє створювати опитування',
	'qp_users_answered_questions' => 'На питання {{PLURAL:$1|відповів $1 користувач|відповіли $1 користувачі|відповіли $1 користувачів}}',
);

