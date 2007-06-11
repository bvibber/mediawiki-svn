<?php

/**
 * Internationalisation file for the MediaFunctions extension
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 * @version 1.0
 */

/**
 * Get translated magic words, if available
 *
 * @param string $lang Language code
 * @return array
 */
function efMediaFunctionsWords( $lang ) {
	$words = array();
	
	/**
	 * English
	 */
	$words['en'] = array(
		'mediamime' 	=> array( 0, 'mediamime' ),
		'mediasize' 	=> array( 0, 'mediasize' ),
		'mediaheight' 	=> array( 0, 'mediaheight' ),
		'mediawidth' 	=> array( 0, 'mediawidth' ),
	);
	
	# English is used as a fallback, and the English synonyms are
	# used if a translation has not been provided for a given word
	return ( $lang == 'en' || !isset( $words[$lang] ) )
		? $words['en']
		: array_merge( $words['en'], $words[$lang] );
}

/**
 * Get error message translations
 *
 * @return array
 */
function efMediaFunctionsMessages() {
	$messages = array(

'en' => array(
'mediafunctions-invalid-title' => '"$1" is not a valid title',
'mediafunctions-not-exist'     => '"$1" does not exist',
),

'de' => array(
'mediafunctions-invalid-title' => '„$1“ ist kein gültiger Name',
'mediafunctions-not-exist'     => '„$1“ ist nicht vorhanden',
),
	
	);
	return $messages;
}

?>