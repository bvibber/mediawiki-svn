<?php

/**
 * Messages file for the InputBox extension
 *
 * @addtogroup Extensions
 */

/**
 * Get all extension messages
 *
 * @return array
 */
function efInputBoxMessages() {
	$messages = array(
	
/**
 * English
 */
'en' => array(
	'inputbox-error-no-type' => 'You have not specified the type of input box to create.',
	'inputbox-error-bad-type' => 'Input box type "$1" not recognised. Please specify "create", "comment", "search" or "search2".',
),

	);
	return $messages;
}