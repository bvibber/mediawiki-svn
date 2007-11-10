<?php

/**
 * Internationalisation file for the MediaFunctions extension
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 * @version 1.1
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
		'mediamime' 		=> array( 0, 'mediamime' ),
		'mediasize' 		=> array( 0, 'mediasize' ),
		'mediaheight' 		=> array( 0, 'mediaheight' ),
		'mediawidth' 		=> array( 0, 'mediawidth' ),
		'mediadimensions'	=> array( 0, 'mediadimensions' ),
		'mediaexif'			=> array( 0, 'mediaexif' ),
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

'ar' => array(
	'mediafunctions-invalid-title' => '"$1" ليس عنوانا صحيحا',
	'mediafunctions-not-exist'     => '"$1" غير موجود',
),

'de' => array(
	'mediafunctions-invalid-title' => '„$1“ ist kein gültiger Name',
	'mediafunctions-not-exist'     => '„$1“ ist nicht vorhanden',
),

'fr' => array(
	'mediafunctions-invalid-title' => '« $1 » n\'est pas un titre valide.',
	'mediafunctions-not-exist'     => '« $1 » n\'existe pas.',
),

'gl' => array(
	'mediafunctions-invalid-title' => '"$1" non é un título válido',
	'mediafunctions-not-exist'     => '"$1" non existe',
),

'hsb' => array(
	'mediafunctions-invalid-title' => '"$1" płaćiwe mjeno njeje.',
	'mediafunctions-not-exist'     => '"1" njeeksistuje',
),

'nds' => array(
	'mediafunctions-invalid-title' => '„$1“ is keen validen Titel',
	'mediafunctions-not-exist'     => '„$1“ gifft dat nich',
),

'nl' => array(
	'mediafunctions-invalid-title' => '"$1" is geen geldige titel',
	'mediafunctions-not-exist'     => '"$1" bestaat niet',
),

'no' => array(
	'mediafunctions-invalid-title' => '«$1» er en ugyldig tittel',
	'mediafunctions-not-exist'     => '«$1» eksisterer ikke',
),

'oc' => array(
	'mediafunctions-invalid-title' => '« $1 » es pas un títol valid.',
	'mediafunctions-not-exist'     => '« $1 » existís pas.',
),

'pl' => array(
	'mediafunctions-invalid-title' => '"$1" nie jest poprawnym tytułem',
	'mediafunctions-not-exist'     => '"$1" nie istnieje',
),

'pms' => array(
	'mediafunctions-invalid-title' => '"$1" a va nen bin për tìtol',
	'mediafunctions-not-exist'     => '"$1" a-i é pa',
),

'yue' => array(
	'mediafunctions-invalid-title' => '"$1" 唔係一個有效嘅標題',
	'mediafunctions-not-exist'     => '"$1" 唔存在',
),

'zh-hans' => array(
	'mediafunctions-invalid-title' => '"$1" 不是一个有效的标题',
	'mediafunctions-not-exist'     => '"$1" 不存在',
),

'zh-hant' => array(
	'mediafunctions-invalid-title' => '"$1" 不是一個有效的標題',
	'mediafunctions-not-exist'     => '"$1" 不存在',
),
	
	);

	/* Chinese defaults, fallback to zh-hans or zh-hant */
	$messages['zh'] = $messages['zh-hans'];
	$messages['zh-cn'] = $messages['zh-hans'];
	$messages['zh-hk'] = $messages['zh-hant'];
	$messages['zh-tw'] = $messages['zh-hans'];
	$messages['zh-sg'] = $messages['zh-hant'];
	/* Cantonese default, fallback to yue */
	$messages['zh-yue'] = $messages['yue'];

	return $messages;
}
