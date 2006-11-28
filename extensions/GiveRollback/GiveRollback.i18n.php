<?php

/**
 * Internationalisation file for the GiveRollback extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0 or later
 */

function efGiveRollbackMessages() {
	return array(
	
'giverollback' => 'Grant or revoke rollback rights',
'giverollback-header' => "'''A local bureaucrat can use this page to grant or revoke [[Help:Rollback|rollback rights]] to another user account.'''<br />This can be used to allow non-sysops to revert vandalism quickly. This should be done in accordance with applicable policies.",
'giverollback-username' => 'Username:',
'giverollback-search' => 'Go',

'giverollback-hasrb' => '[[User:$1|$1]] has rollback rights.',
'giverollback-norb' => '[[User:$1|$1]] does not have rollback rights.',
'giverollback-toonew' => '[[User:$1|$1]] is too new, and cannot be given rollback rights.',
'giverollback-sysop' => '[[User:$1|$1]] is a sysop, and already has rollback permissions.',
'giverollback-change' => 'Change status:',
'giverollback-grant' => 'Grant',
'giverollback-revoke' => 'Revoke',
'giverollback-comment' => 'Comment:',
'giverollback-granted' => '[[User:$1|$1]] now has rollback rights.',
'giverollback-revoked' => '[[User:$1|$1]] no longer has rollback rights.',

'giverollback-logpage' => 'Rollback rights log',
'giverollback-logpagetext' => 'This is a log of changes to non-sysops\' [[Help:Rollback|rollback]] rights.',
'giverollback-logentrygrant' => 'granted rollback rights to [[$1]]',
'giverollback-logentryrevoke' => 'removed rollback rights from [[$1]]',

	);
}

?>