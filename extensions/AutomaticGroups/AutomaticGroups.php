<?php

/**
 * Extension provides convenient configuration of additional
 * effective groups based on a user's account age and edit count
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionCredits['other'][] = array(
		'name' => 'Automatic Groups',
		'author' => 'Rob Church',
		'url' => '',
		'description' => 'Provides a convenient means to configure automatic group
			membership based on user account age and edit count',
	);

	/**
	 * Register hook callback
	 */
	$wgHooks['UserEffectiveGroups'][] = 'efAutomaticGroups';
	
	/**
	 * Automatic group configuration
	 *
	 * Index is the group being assigned, with a second array
	 * of account properties; acceptable keys are 'age' and 'edits'
	 */
	$wgAutomaticGroups = array();
	// Example: "autoconfirmed" for accounts which are 4 days old
	//$wgAutomaticGroups['autoconfirmed'] = array( 'age' => 86400 * 4 );
	// Example: "patroller" for accounts with 250 edits
	//$wgAutomaticGroups['patroller'] = array( 'edits' => 250 );
	
	/**
	 * Main execution function
	 *
	 * @param User $user User to set groups for
	 * @param array $groups User's explicit groups
	 * @return bool
	 */
	function efAutomaticGroups( $user, &$groups ) {
		global $wgAutomaticGroups;
		$age = time() - wfTimestampOrNull( TS_UNIX, $user->getRegistration() );
		foreach( $wgAutomaticGroups as $group => $criteria ) {
			if( isset( $criteria['age'] ) && $age < $criteria['age'] )
				continue;
			if( isset( $criteria['edits'] ) && $user->getEditCount() < $criteria['edits'] )
				continue;
			# User qualifies for this group
			$groups[] = $group;
		}
		return true;
	}

} else {
	echo( "This file is an extension to MediaWiki and cannot be used standalone.\n" );
	exit( 1 );
}

?>