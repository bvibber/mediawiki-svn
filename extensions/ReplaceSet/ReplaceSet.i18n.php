<?php
/**
 * ReplaceSet
 * @package ReplaceSet
 * @author Daniel Friesen (http://mediawiki.org/wiki/User:Dantman) <mediawiki@danielfriesen.name>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

require_once( dirname(__FILE__) . '/ReplaceSet.i18n.magic.php' );

$messages = array();

$messages['en'] = array(
	'replaceset-error-calllimit' => 'The ReplaceSet call limit has been reached.',
	'replaceset-error-regexnoend' => 'The regex pattern "$1" is missing the ending delimiter \'$2\'',
	'replaceset-error-regexbadmodifier' => 'The regex modifier \'$1\' is not valid.'
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 */
$messages['be-tarask'] = array(
	'replaceset-error-calllimit' => 'Была дасягнутая мяжа выклікаў ReplaceSet.',
	'replaceset-error-regexnoend' => 'Ва ўзоры рэгулярнага выразу «$1» адсутнічае канчатковы падзяляльнік «$2»',
	'replaceset-error-regexbadmodifier' => 'Няслушны мадыфікатар рэгулярнага выразу «$1».',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'replaceset-error-calllimit' => 'Limit zawołanjow ReplaceSet jo dojśpjony.',
	'replaceset-error-regexnoend' => 'Mustroju regularnych wurazow "$1" falujo kóńcne źělatko \'$2\'',
	'replaceset-error-regexbadmodifier' => "Modifikator regularnych wurazow '$1' njejo płaśiwy.",
);

/** French (Français)
 * @author Verdy p
 */
$messages['fr'] = array(
	'replaceset-error-calllimit' => "La limite des appels à la fonction ''ReplaceSet'' a été atteinte.",
	'replaceset-error-regexnoend' => 'Le délimiteur de fin « $2 » est manquant dans le motif de l’expression régulière « $1 »',
	'replaceset-error-regexbadmodifier' => 'Le modificateur d’expression régulière  « $1 » est invalide.',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'replaceset-error-calllimit' => 'D Gränz vu dr Aaruef vu ReplaceSet isch erreicht wore.',
	'replaceset-error-regexnoend' => "Bim Regexbitmuschter „$1“ fählt s Änd-Begränzigszeiche '$2'",
	'replaceset-error-regexbadmodifier' => "Dr Regex-Modifier '$1' isch nit giltig.",
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'replaceset-error-calllimit' => 'Limit zawołanjow ReplaceSet je docpěty.',
	'replaceset-error-regexnoend' => 'Mustrej regularnych wurazow "$1" kónčne dźělatko \'$2\' pobrachuje',
	'replaceset-error-regexbadmodifier' => "Modifikator regularnych wurazow '$1' płaćiwy njeje.",
);

/** Italian (Italiano)
 * @author Darth Kule
 */
$messages['it'] = array(
	'replaceset-error-calllimit' => 'Il limite di chiamate ReplaceSet è stato raggiunto.',
	'replaceset-error-regexnoend' => 'Il delimitatore finale \'$2\' manca al pattern della regex "$1"',
	'replaceset-error-regexbadmodifier' => "Il modificatore della regex '$1' non è valido.",
);

/** Japanese (日本語)
 * @author Aotake
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'replaceset-error-calllimit' => 'ReplaceSet の呼び出し回数が上限に達しました。',
	'replaceset-error-regexnoend' => '指定した正規表現 "$1" には、終わりを区切る記号 \'$2\' が不足しています',
	'replaceset-error-regexbadmodifier' => "'$1' は正規表現の修飾子として無効です。",
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'replaceset-error-calllimit' => 'De Oproofjrenz för <i lang="en">ReplaceSet</i> es erreisch.',
	'replaceset-error-regexnoend' => 'En dä <i lang="en">regular expression</i> „$1“ fählt et Zeische för et Engk, wat eijentlesch e „$2“ sin mööt.',
	'replaceset-error-regexbadmodifier' => 'En dä <i lang="en">regular expression</i> es dat „$1“ e onjöltisch Zeijsche för en Veränderung udder en jenouere Beschtemmung.',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'replaceset-error-calllimit' => "D'Limit fir d'Opruffe vu ''ReplaceSet'' gouf erreecht.",
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'replaceset-error-calllimit' => 'De limiet van het aantal aanroepen van ReplaceSet is bereikt.',
	'replaceset-error-regexnoend' => 'Het in het patroon "$1" van de reguliere expressie mist het sluitteken "$2"',
	'replaceset-error-regexbadmodifier' => 'De modifier "$1" van de reguliere expressie is niet geldig.',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'replaceset-error-calllimit' => "Lo limit dels apèls a la foncion ''ReplaceSet'' es estat atench.",
	'replaceset-error-regexnoend' => 'Lo delimitador de fin « $2 » es mancant dins lo motiu de l’expression regulara « $1 »',
	'replaceset-error-regexbadmodifier' => 'Lo modificador d’expression regulara  « $1 » es invalid.',
);

/** Russian (Русский)
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'replaceset-error-calllimit' => 'Достигнут предел вызовов ReplaceSet.',
	'replaceset-error-regexnoend' => 'В шаблоне регулярного выражения «$1» отсутствует завершающий разделитель «$2»',
	'replaceset-error-regexbadmodifier' => 'Модификатор регулярного выражения «$1» недействителен.',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'replaceset-error-calllimit' => 'Bol dosiahnutý limit volaní ReplaceSet.',
	'replaceset-error-regexnoend' => 'Regulárnemu výrazu „$1“ chýba ukončovací oddeľovač „$2“',
	'replaceset-error-regexbadmodifier' => 'Modifikátor regulárneho výrazu „$1“ nie je platný.',
);

