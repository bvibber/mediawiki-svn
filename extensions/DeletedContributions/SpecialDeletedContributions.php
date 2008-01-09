<?php
/**
 @ Extension based on SpecialContributions for archived revisions
 @ Modifications made to SpecialContributions.php
 @ copyright © 2007 Aaron Schulz
 */

$wgExtensionCredits['specialpage'][] = array(
	'author' => 'Aaron Schulz',
	'version' => '2008-01-09',
	'name' => 'Deleted user contributions',
	'url' => 'http://www.mediawiki.org/wiki/Extension:DeletedContributions',
	'description' => 'Gives sysops the ability to browse a user\'s deleted edits.'
);

# Internationalisation
$wgExtensionMessagesFiles['DeletedContributions'] = dirname(__FILE__) . '/DeletedContributions.i18n.php';
$wgExtensionFunctions[] = 'efLoadDeletedContribsMessages';

global $wgHooks;
$wgHooks['ContributionsToolLinks'][] = 'wfLoadContribsLink';

/**
 * Add a "Deleted contributions" link to Special:Contributions for sysops.
 */
function wfLoadContribsLink( $id, $nt, &$links ) {
	global $wgUser;
	if( $wgUser->isAllowed( 'deletedhistory' ) ) {
		$links[] = $wgUser->getSkin()->makeKnownLinkObj(
			SpecialPage::getTitleFor( 'DeletedContributions', $nt->getDBkey() ),
			wfMsgHtml( 'deletedcontributions' )
		);
	}
	return true;
}

function efLoadDeletedContribsMessages() {
	require( dirname( __FILE__ ) . '/DeletedContributions_body.php' );
	wfLoadExtensionMessages( 'DeletedContributions' );
}

$wgSpecialPages['DeletedContributions'] = array( 'SpecialPage', 'DeletedContributions', 'deletedhistory',
		/*listed*/ true, /*function*/ false, /*file*/ false );
