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
		'url' => 'http://www.mediawiki.org/wiki/Extension:Automatic_Groups',
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
	 *
	 * See README for more information and examples
	 */
	$wgAutomaticGroups = array();
	
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