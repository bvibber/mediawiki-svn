<?php

/**
 * Internationalisation file for the New User Email Notification extension
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */

function efNewUserNotifMessages() {
	$messages = array(
	
'en' => array(
	'newusernotifsubj' => 'New User Notification for $1',
	'newusernotifbody' => "Hello $1,\n\nA new user account, $2, has been created on $3 at $4.",
),

'de' => array(
	'newusernotifsubj' => 'Benachrichtung für $1 über die Einrichtung eines neuen Benutzerskontos',
	'newusernotifbody' => "Hallo $1,\n\nEin neues Benutzerkonto, $2, wurde am $4 auf $3 angelegt.",
),
	
	);
	return $messages;
}