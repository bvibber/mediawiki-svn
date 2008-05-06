<?php
/**
 @ Extension based on SpecialContributions for archived revisions
 @ Modifications made to SpecialContributions.php
 @ copyright © 2007 Aaron Schulz
 */

$wgExtensionCredits['specialpage'][] = array(
	'author' => 'Aaron Schulz',
	'svn-date' => '$LastChangedDate$',
	'svn-revision' => '$LastChangedRevision$',
	'name' => 'Deleted user contributions',
	'url' => 'http://www.mediawiki.org/wiki/Extension:DeletedContributions',
	'description' => 'Gives sysops the ability to browse a user\'s deleted edits',
	'descriptionmsg' => 'deletedcontributions-desc',
);

# Internationalisation
$wgExtensionMessagesFiles['DeletedContributions'] = dirname(__FILE__) . '/DeletedContributions.i18n.php';
$wgAutoloadClasses['DeletedContributionsPage'] 
	= $wgAutoloadClasses['DeletedContributionsPage']
	= dirname(__FILE__) . '/DeletedContributions_body.php';

$wgHooks['ContributionsToolLinks'][] = 'wfLoadContribsLink';
$wgSpecialPages['DeletedContributions'] = 'DeletedContributionsPage';
$wgSpecialPageGroups['DeletedContributions'] = 'users';

/**
 * Add a "Deleted contributions" link to Special:Contributions for sysops.
 */
function wfLoadContribsLink( $id, $nt, &$links ) {
	global $wgUser;
	if( $wgUser->isAllowed( 'deletedhistory' ) ) {
		wfLoadExtensionMessages( 'DeletedContributions' );

		$links[] = $wgUser->getSkin()->makeKnownLinkObj(
			SpecialPage::getTitleFor( 'DeletedContributions', $nt->getDBkey() ),
			wfMsgHtml( 'deletedcontributions' )
		);
	}
	return true;
}

