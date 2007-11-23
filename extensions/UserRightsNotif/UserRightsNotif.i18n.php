<?php
/**
 * Internationalisation file for extension UserRightsNotif.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'userrightsnotifysubject' => 'Group membership change on $1',
	'userrightsnotifybody'    => "Hello $1\n\nThis is to inform you that your group memberships on $2 were changed by $3 at $4.\n\nAdded: $5\nRemoved: $6\n\nWith regards,\n\n$2",
);

$messages['fr'] = array(
	'userrightsnotifysubject' => 'Changement d’appartenance à des groupes d’utilisateurs sur $1',
	'userrightsnotifybody'    => "Bonjour $1,\n\nJ’ai l'honneur de vous informer que votre appartenance aux groupes d'utilisateurs sur $2 a été modifiée par $3 le $4.\n\nAjouté : $5\nRetiré : $6\n\nCordialement,\n\n$2",
);
