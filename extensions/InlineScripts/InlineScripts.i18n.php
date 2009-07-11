<?php
/**
 * Internationalisation file for extension InlineScripts.
 *
 * @addtogroup Extensions
 */

$messages = array();

/** English
 * @author Victor Vasiliev
 */
$messages['en'] = array(
	'inlinescripts-desc' => 'Provides inline script interpreter',

	'inlinescripts-exception-unclosedstring' => 'Unclosed string at char $1',
	'inlinescripts-exception-unrecognisedtoken' => 'Unrecognized token at char $1',
	'inlinescripts-exception-toomanytokens' => 'Exceeded tokens limit',
	'inlinescripts-exception-toomanyevals' => 'Exceeded evaluations limit',
	'inlinescripts-exception-recoverflow' => 'Too deep abstract syntax tree',
	'inlinescripts-exception-expectingdata' => 'Unexpected token at char $1',
	'inlinescripts-exception-expectingoperator' => 'Unexpected token at char $1',
	'inlinescripts-exception-cantchangeconst' => 'Cannot assign value to a constant at char $1',
	'inlinescripts-exception-expectednotfound' => 'Expected $2, but not found at char $1',
	'inlinescripts-exception-unbalancedbraces' => 'Unbalanced parenthesis at char $1',
	'inlinescripts-exception-notanarray' => 'Tried to get an element of a non-array at char $1',
	'inlinescripts-exception-outofbounds' => 'Got out of array bounds at char $1',
	'inlinescripts-exception-invalidforeach' => 'Invalid argument supplied for foreach at char $1',
	'inlinescripts-exception-unexceptedop' => 'Unexpected operator $2',
	'inlinescripts-exception-notenoughargs' => 'Not enough arguments for function at char $1',
	'inlinescripts-exception-notenoughopargs' => 'Not enough aruments for operator at char $1',
	'inlinescripts-exception-dividebyzero' => 'Division by zero at char $1',
	'inlinescripts-exception-break' => '"break" called outside of foreach at char $1',
	'inlinescripts-exception-continue' => '"continue" called outside of foreach at char $1',
);

// == Magic words ==

$magicWords = array();

$magicWords['en'] = array(
	'script' => array( 0, 'script' ),
	'inline' => array( 0, 'inline' ),
);
