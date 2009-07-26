<?php
/**
 * Internationalization file for Transliterator
 */
$messages = array();

/**
 * English
 * @author: Conrad.Irwin
 * @author: Purodha
 */
$messages['en'] = array(
	'transliterator-desc' => "Provides a configurable parser function for transliteration",
	'transliterator-invoke' => 'transliterate', // {{#transliterate:blah}}
	'transliterator-prefix' => 'Transliterator:', // [[MediaWiki:Transliterator:blah]] NOTE: changing this requires moving all maps
	// $1 is the line from the map, 'a => z', $2 is the map-page including prefix.
	'transliterator-error-ambiguous' => "Ambiguous rule '$1' in [[MediaWiki:$2]]",
	'transliterator-error-syntax' => "Invalid syntax '$1' in [[MediaWiki:$2]]",
	// $1 is the limit on number of rules
	'transliterator-error-rulecount' => "More than $1 {{PLURA:$1|rule|rules}} in [[MediaWiki:$2]]",
	// $3 is the limit on the length of the left hand side (e.g. 'alpha => beta' has 5)
	'transliterator-error-rulesize' => "Rule '$1' has more than $3 {{PLURAL:$3|character|characters}} on the left in [[MediaWiki:$2]]",
);

/**
 * Message Documentation.
 * @author: Purodha
 */
$messages['qqq'] = array(
	'transliterator-desc' => 'This is a short description of the extension. It is shown in [[Special:Version]].', 
	'transliterator-invoke' => "This is the name of a parserfunction: {<nowiki />{#transliterate:''blah''}}", 
	'transliterator-prefix' => "This is a prefix for the transliteration maps, used in the MediaWiki namespace like [<nowiki />[MediaWiki:Transliterator:''blah'']]. Changing this requires moving all maps.", 
	'transliterator-error-ambiguous' => 'Parameters:
* $1 is the line from the map, such as: <code>a => z</code>
* $2 is the map-page including thr prefix {{msg-mw|transliterator-invoke}}',
	'transliterator-error-syntax' => 'Parameters:
* $1 is the line from the map, such as: <code>a => z</code>
* $2 is the map-page including thr prefix {{msg-mw|transliterator-invoke}}',
	'transliterator-error-rulecount' => 'Parameters:
* $1 is the limit on number of rules
* $2 is the map-page including thr prefix {{msg-mw|transliterator-invoke}}',
	'transliterator-error-rulesize' => 'Parameters:
* $1 is the line from the map, such as: <code>a => z</code>
* $2 is the map-page including thr prefix {{msg-mw|transliterator-invoke}}
* $3 is the limit on the length of the left hand side (e.g. <code>alpha => beta</code> has 5)',
);

/**
 * German (Deutsch)
 * @author: Purodha
 */
$messages['de'] = array(
	'transliterator-desc' => 'Stellt eine konfigurierbare Parserfunktion zur Transliteration bereit.',
	'transliterator-invoke' => 'transliterate', 
	'transliterator-prefix' => 'Transliterator:',
	'transliterator-error-ambiguous' => 'Mehrdeutige Regel <code>$1</code> in [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'Fehlerhafte Syntax in Regel <code>$1</code> in [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Mehr als die {{PLURAL:$1|erlaubte eine Regel|die erlabubten $1 Regeln}} in [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => 'In der Regel <code>$1</code> {{PLURAL:$3|ist|sind}} mehr als $3 Zeichen auf der ligken Seite in [[MediaWiki:$2]]',
);

/**
 * Ripuarian (Ripoaresch)
 * @author: Purodha
 */
$messages['ksh'] = array(
	'transliterator-desc' => 'Deiht en ennstellbaa Paaserfunxjuhn en et Wiki, di Boochshtabe tuusche kann.', 
	'transliterator-invoke' => 'transliterate', 
	'transliterator-prefix' => 'Transliterator:',
	'transliterator-error-ambiguous' => 'En unkloh Rejel <code>$1</code> es en [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'En kappodde Syntax <code>$1</code> es en [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Et {{PLURAL:$1|es mieh wi ein Rejel|sinn_er mieh wi $1 Rejelle|es kei Rejel}} en [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => 'En de Rejel <code>$1</code> {{PLURAL:$3|es|sinn_er}} mieh wi $3 Zeische op de lengke Sigg, en [[MediaWiki:$2]]',
);

