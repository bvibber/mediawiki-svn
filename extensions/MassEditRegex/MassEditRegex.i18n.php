<?php
/**
 * Internationalisation file for MassEditRegex extension
 *
 * @addtogroup Extensions
 */

$messages = array();

/** English
 * @author Adam Nielsen
 */
$messages['en'] = array(
	'masseditregex'      => 'Mass Edit using Regular Expressions',
	'masseditregex-desc' => 'Use regular expressions to [[Special:MassEditRegex|edit many pages in one operation]]',
	'masseditregextext'  => 'Enter one or more regular expressions (one per line) for matching, and one or more expressions to replace each match with.  The first match-expression, if successful, will be replaced with the first replace-expression, and so on.  See the PHP function preg_replace() for details.',
	'pagelisttxt'        => 'Pages to edit:',
	'matchtxt'           => 'Search for:',
	'replacetxt'         => 'Replace with:',
	'executebtn'         => 'Execute',
	'err-nopages'        => 'You must specify at least one page to change.',

	'before'             => 'Before',
	'after'              => 'After',
	'max-preview-diffs'  => 'Preview has been limited to the first $1 matches.',

	'num-changes'        => 'changes', // e.g. "5 changes", can't use $1 or it'll be too slow
	'num-articles-changed' => '$1 articles edited',
	'view-full-summary'  => 'View full edit summary',

	'hint-intro'         => 'Here are some hints and examples for accomplishing common tasks:',
	'hint-headmatch'     => 'Match',
	'hint-headreplace'   => 'Replace',
	'hint-headeffect'    => 'Effect',
	'hint-toappend'      => 'Append some text to the end of the article - great for adding pages to categories',
	'hint-remove'        => 'Remove some text from all the pages in the list',
	'hint-removecat'     => 'Remove all categories from an article (note the escaping of the square brackets in the wikicode.)  The replacement values should not be escaped.'
);
