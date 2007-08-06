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

'en' => array(
	'inputbox-error-no-type'  => 'You have not specified the type of input box to create.',
	'inputbox-error-bad-type' => 'Input box type "$1" not recognised. Please specify "create", "comment", "search" or "search2".',
),

'de' => array(
	'inputbox-error-no-type'  => 'Du hast keinen Inputbox-Typ angegeben.',
	'inputbox-error-bad-type' => 'Der Inputbox-Typ „$1“ ist unbekannt. Bitte gebe einen gültigen Typ an: „create“, „comment“, „search“ oder „search2“.',
),
	);
	return $messages;
}