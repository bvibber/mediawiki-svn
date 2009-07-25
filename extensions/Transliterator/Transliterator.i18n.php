<?php
/**
 * Internationalization file for Transliterator
 */
$messages = array();

/**
 * English
 */
$messages['en'] = array(
	'transliterator-invoke' => 'transliterate', // {{#transliterate:blah}}
	'transliterator-prefix' => 'Transliterator:', // [[MediaWiki:Transliterator:blah]] NOTE: changing this requires moving all maps
	// $1 is the line from the map, 'a => z', $2 is the map-page including prefix.
	'transliterator-error-ambiguous' => "Ambiguous rule '$1' in [[MediaWiki:$2]]",
	'transliterator-error-syntax' => "Invalid syntax '$1' in [[MediaWiki:$2]]",
	// $1 is the limit on number of rules
	'transliterator-error-rulecount' => "More than $1 rules in [[MediaWiki:$2]]",
	// $2 is the limit on the length of the left hand side (e.g. 'alpha => beta' has 5)
	'transliterator-error-rulesize' => "Rule '$1' has more than $2 characters on the left in [[MediaWiki:$3]]",
	'transliterator-description' => "Provides a configurable parser function for transliteration"
);
