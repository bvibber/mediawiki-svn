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

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'replaceset-error-calllimit' => 'De limiet van het aantal aanroepen van ReplaceSet is bereikt.',
	'replaceset-error-regexnoend' => 'Het in het patroon "$1" van de reguliere expressie mist het sluitteken "$2"',
	'replaceset-error-regexbadmodifier' => 'De modifier "$1" van de reguliere expressie is niet geldig.',
);

