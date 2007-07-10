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
	
	);
	return $messages;
}