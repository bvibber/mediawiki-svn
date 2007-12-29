<?php
#(c) Andrew Garrett 2007 GPL

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "FlaggedRevs extension\n";
	exit( 1 );
}

// So that extensions which optionally allow an invitation model will work
define('Invitations',1);

$wgExtensionCredits['specialpage'][] = array(
	'author' => 'Andrew Garrett',
	'name' => 'Invitations',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Invitations',
	'description' => 'Allows management of new features by restricting them to an invitation-based system.'
);

$wgSpecialPages['Invitations'] = 'SpecialInvitations';
$wgAutoloadClasses['SpecialInvitations'] = dirname(__FILE__) . '/Invitations_page.php';

$wgAutoloadClasses['Invitations'] = dirname(__FILE__) . '/Invitations_obj.php';

$wgInvitationTypes = array();

// Example: $wgInvitationTypes['centralauth'] = array( reserve => 5, limitedinvites => true, invitedelay => 24 * 3600 * 4 );
// Limits invites to 'centralauth' to 5 invites per inviter, which can be used 4 days after the user are invited.

# Add invite log
$wgLogTypes[] = 'invite';
$wgLogNames['invite'] = 'invite-logpage';
$wgLogHeaders['invite'] = 'invite-logpagetext';
$wgLogActions['invite/invite']  = 'invite-logentry';

