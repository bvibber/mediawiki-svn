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
	'masseditregex' => 'Mass edit using regular expressions',
	'masseditregex-desc' => 'Use regular expressions to [[Special:MassEditRegex|edit many pages in one operation]]',
	'masseditregextext' => 'Enter one or more regular expressions (one per line) for matching, and one or more expressions to replace each match with.
The first match-expression, if successful, will be replaced with the first replace-expression, and so on.
See the PHP function preg_replace() for details.',
	'masseditregex-pagelisttxt' => 'Pages to edit:',
	'masseditregex-matchtxt' => 'Search for:',
	'masseditregex-replacetxt' => 'Replace with:',
	'masseditregex-executebtn' => 'Execute',
	'masseditregex-err-nopages' => 'You must specify at least one page to change.',

	'masseditregex-before' => 'Before',
	'masseditregex-after' => 'After',
	'masseditregex-max-preview-diffs' => 'Preview has been limited to the first $1 {{PLURAL:$1|match|matches}}.',
	'masseditregex-num-changes' => '$1: $2 {{PLURAL:$2|change|changes}}',
	'masseditregex-page-not-exists' => '$1 does not exist',
	'masseditregex-num-articles-changed' => '$1 {{PLURAL:$1|page|pages}} edited',
	'masseditregex-view-full-summary' => 'View full edit summary',

	'masseditregex-hint-intro' => 'Here are some hints and examples for accomplishing common tasks:',
	'masseditregex-hint-headmatch' => 'Match',
	'masseditregex-hint-headreplace' => 'Replace',
	'masseditregex-hint-headeffect' => 'Effect',
	'masseditregex-hint-toappend' => 'Append some text to the end of the page - great for adding pages to categories',
	'masseditregex-hint-remove' => 'Remove some text from all the pages in the list',
	'masseditregex-hint-removecat' => 'Remove all categories from an page (note the escaping of the square brackets in the wikicode.)
The replacement values should not be escaped.'
);
