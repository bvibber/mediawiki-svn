<?php

/**
 * Internationalisation file for the BackAndForth extension
 *
 * @author Rob Church <robchur@gmail.com>
 */

/**
 * Fetch extension messages indexed per language
 *
 * @return array
 */
function efBackAndForthMessages() {
	$messages = array(

/**
 * English
 */
'en' => array(
	'backforth-next' => 'Next ($1)',
	'backforth-prev' => 'Previous ($1)',
),

/**
 * Cantonese
 */
'yue' => array(
	'backforth-next' => '??? ($1)',
	'backforth-prev' => '??? ($1)',
),

/**
 * Chinese (Simplified)
 */
'zh-hans' => array(
	'backforth-next' => '??? ($1)',
	'backforth-prev' => '??? ($1)',
),

/**
 * Chinese (Traditional)
 */
'zh-hant' => array(
	'backforth-next' => '??? ($1)',
	'backforth-prev' => '??? ($1)',
),
	
	);

	/* Chinese defaults, fallback to zh-hans or zh-hant */
	$messages['zh-cn'] = $messages['zh-hans'];
	$messages['zh-hk'] = $messages['zh-hant'];
	$messages['zh-sg'] = $messages['zh-hans'];
	$messages['zh-tw'] = $messages['zh-hant'];
	/* Cantonese default, fallback to yue */
	$messages['zh-yue'] = $messages['yue'];

	return $messages;
}
