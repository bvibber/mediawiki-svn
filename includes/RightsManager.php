<?php

if ( !defined( 'MEDIAWIKI' ) )
	die();
	
abstract class RightsManager {

	/**
	 * Assign a set of rights to a group.
	 * @param $group \type{\string} The group to assign rights to.
	 * @param $rights \type{\arrayof{\string}} The rights to assign.
	 */
	function assignRights( $group, $rights ) {
		$this->setRightsStatus( $group, array_fill_keys( $rights, true ) );
	}
	
	/**
	 * Invalidate the cache of rights for a group.
	 * @param $group \type{\string} The group to invalidate the cache for.
	 */
	function invalidateGroupCache( $group ) {}
	
	/**
	 * Invalidate the cache of rights for a user.
	 * @param $user \type{\object{User}} The user to invalidate the cache for.
	 */
	function invalidateUserCache( $user ) {}
	
	/**
	 * Revoke a set of rights from a group.
	 * @param $group \type{\string} The group to revoke rights from.
	 * @param $rights \type{\arrayof{\string}} The rights to revoke.
	 */
	function revokeRights( $group, $rights ) {
		$this->setRightsStatus( $group, array_fill_keys( $rights, false ) );
	}
	
	/**
	 * Set the status of rights.
	 * @param $group \type{\string} The group to set rights status for
	 * @param $rights \type{\array} An array with key=right, value=whether or not the right is assigned.
	 */
	abstract function setRightsStatus( $group, $rights );
	
	/**
	 * Modify the changeable groups for a group.
	 * @param $group \type{\string} The group to assign edit changeable groups for.
	 * @param $addable \type{\arrayof{\string}} The groups to be addable.
	 * @param $removable \type{\arrayof{\string}} The groups to be removable.
	 * @param $addtoself \type{\arrayof{\string}} The groups to be addable by the user in question only.
	 * @param $removefromself \type{\arrayof{\string}} The groups to be removable by the user in question only.
	 */
	abstract function setChangeableGroups( $group, $addable, $removable, $addtoself, $removefromself );

	/**
	 * Get a list of all group permissions.
	 * @return \type{\array} Array of group => (permission => allowed, ...), ...
	 */
	abstract function getAllGroupPermissions();
	
	/**
	 * Determine whether a given user is allowed to change group rights.
	 * @param $user \type{\object{User}} The user to check.
	 * @return \type{\bool} Whether or not the user is allowed to change group rights.
	 */
	abstract function canEditRights( $user );
	
	/**
	 * Add a user to a group or groups.
	 * @param $user \type{\object{User}}
	 * @param $groups \type{\arrayof{\string}}
	 */
	abstract function addUserGroups( $user, $groups );
	
	/**
	 * Remove a user from a group or groups.
	 * @param $user \type{\object{User}}
	 * @param $groups \type{\arrayof{\string}}
	 */
	abstract function removeUserGroups( $user, $groups );
	
	/**
	 * Make a list of group names, for the log.
	 */
	function makeGroupNameListForLog( $ids ) {
		if( empty( $ids ) ) {
			return wfMsgForContent( 'rightsnone' );
		} else {
			return implode( ', ', $ids );
		}
	}
	
	/**
	 * Make a list of rights, for the log.
	 */
	function makeRightsList( $ids ) {
		return (bool)count($ids) ? implode( ', ', $ids ) : wfMsgForContent( 'rightsnone' );
	}
	
	function changeUserGroups( $user, $addgroups, $removegroups, $reason, $doer ) {
		// Validate input set...
		$changeable = $this->getChangeableGroupsForUser( $user );
		$isself = $this->userEquals( $user, $doer );
		
		// Allow adding to self
		$addable = array_merge( $changeable['add'], $isself ? $changeable['add-self'] : array() );
		$removable = array_merge( $changeable['remove'], $isself ? $changeable['remove-self'] : array() );

		// Filter for what we can actually change
		$removegroups = array_unique(
			array_intersect( (array)$removegroups, $removable ) );
		$addgroups = array_unique(
			array_intersect( (array)$addgroups, $addable ) );
			
		if (!count(array_merge( $removegroups, $addgroups ) ) )
			return;
			
		// Only add groups user doesn't have, remove groups user does have.
		$oldGroups = $this->getUserGroups( $user );
		$removegroups = array_intersect( $removegroups, $oldGroups );
		$addgroups = array_diff( $addgroups, array_intersect( $addgroups, $oldGroups ) );
	
		// The actual business end.
		$this->addUserGroups( $user, $addgroups );
		$this->removeUserGroups( $user, $removegroups );
		
		// Ensure that caches are cleared
		$this->invalidateUserCache( $user );
		
		// Log it
		$this->addUserGroupsLogEntry( $user, $addgroups, $removegroups, $reason, $doer );
	}
	
	/**
	 * Get a list of changeable groups.
	 * @return \type{\array} Array of group => array( group1, group2, ... )
	 */
	abstract function getAllChangeableGroups();
	
	/**
	 * Checks if two user objects are equal
	 */
	function userEquals( $a, $b ) {
		return $a->getId() == $b->getId();
	}
	
	abstract function fetchUser( $username );
	
	/**
	 * Get a list of groups changeable by a particular group.
	 * @param $groups \type{string} Group or array of groups to check.
	 * @return \type{\arrayof{\string}} Array of action => groups changeable by this group/groups
	 */
	function getChangeableGroups( $groups ) {
		$changeable = $this->getAllChangeableGroups();
		$result = array('add' => array(), 'remove' => array(), 'addself' => array(), 'removeself' => array());
		
		foreach( $groups as $group ) {
			if ( isset( $changeable[$group] ) && is_array( $changeable[$group] ) )
				$result = array_merge_recursive( $result, $changeable[$group] );
		}
		
		return $result;
	}
	
	/**
	 * Get a list of groups changeable by a particular user.
	 * @param $user \type{\object{User}} User to check.
	 * @return \type{\array} Array of action => groups
	 */
	function getChangeableGroupsForUser( $user ) {
		$groups = $this->getUserGroups( $user );
		return $this->getChangeableGroups( $groups );
	}
	
	/**
	 * Get a list of all defined groups.
	 * @return \type{\arrayof{\string}}
	 */
	function getAllGroups() {
		$groupPerms = $this->getAllGroupPermissions();
		$changeableGroups = $this->getAllChangeableGroups();
		
		return array_unique( array_merge( array_keys( $groupPerms ), array_keys( $changeableGroups ) ) );
	}
	
	/**
	 * Get a list of groups assigned to a user account.
	 * @param $user \type{\object{User}}
	 * @return \type{\arrayof{\string}}
	 */
	abstract function getUserGroups( $user );
	 
	 /**
	  * Get a list of permissions assigned to a user account.
	  * @param $user \type{\object{User}}
	  * @return \type{\arrayof{\string}}
	  */
	function getPermissionsForUser( $user ) {
		$groups = $this->getUserGroups( $user );
		return $this->getGroupPermissions( $groups );
	}
	
	/**
	 * Get a list of all defined groups with a given permission.
	 * @param $permission \type{\string} The permission to check for.
	 * @return \type{\arrayof{\string}} List of group names with that permission.
	 */
	function getGroupsWithPermission( $permission ) {
		$groupPerms = $this->getAllGroupPermissions();
		
		$allowedGroups = array();
		foreach ( $groupPerms as $group => $rights ) {
			if ( isset( $rights[$permission] ) && $rights[$permission] ) {
				$allowedGroups[] = $group;
			}
		}
		return $allowedGroups;
	}
	
	/**
	 * Get a list of all permissions assigned to the groups given.
	 * @param $groups \type{\arrayof{\string}} Array of groups to check.
	 * @return \type{\arrayof{\string}} List of permissions assigned to those groups.
	 */
	function getGroupPermissions( $groups ) {
		$groupPerms = $this->getAllGroupPermissions();
		$rights = array();
		foreach( $groups as $group ) {
			if( isset( $groupPerms[$group] ) ) {
				$rights = array_merge( $rights,
					array_keys( array_filter( $groupPerms[$group] ) ) );
			}
		}
		return $rights;
	}
	
	/**
	 * Show the logs for recent changes for a given group.
	 * @param $group \type{\string} The group to show changes for.
	 * @param $output \type{\object{OutputPage}} The OutputPage to print output to.
	 */
	function showGroupLogFragment( $group, $output ) {
		$title = SpecialPage::getTitleFor( 'ListUsers', $group );
		$output->addHTML( Xml::element( 'h2', null, LogPage::logName( 'rights' ) . "\n" ) );
		LogEventsList::showLogExtract( $output, 'rights', $title->getPrefixedText() );
	}
	
	function showUserLogFragment( $user, $output ) {
		$output->addHtml( Xml::element( 'h2', null, LogPage::logName( 'rights' ) . "\n" ) );
		LogEventsList::showLogExtract( $output, 'rights', $user->getUserPage()->getPrefixedText() );
	}
	
	/**
	 * Add a log entry for group rights changes.
	 * @param $group \type{\string} The group in question.
	 * @param $addRights \type{\arrayof{\string}} The rights added.
	 * @param $removeRights \type{\arrayof{\string}} The rights removed.
	 * @param $reason \type{\string} The comment attached to the change.
	 * @param $user \type{\object{User}} The user doing the change (Optional)
	 */
	abstract function addGroupLogEntry( $group, $addRights, $removeRights, $reason, $user );
	
	/**
	 * Add a log entry for user groups changes.
	 * @param $user \type{\object{User}} The user
	 * @param $addgroups \type{\arrayof{\string}} The groups added.
	 * @param $removegroups \type{\arrayof{\string}} The groups removed.
	 */
	abstract function addUserGroupsLogEntry( $user, $addgroups, $removegroups, $reason, $doer );
	
	/**
	 * Checks whether a given right on a given group may be modified.
	 * @param $group \type{\string} The group in question.
	 * @param $right \type{\string} The right in question.
	 * @return \type{\bool} Whether or not the given group-right may be modified.
	 */
	function rightEditable( $group, $right ) { return true; }
	
	/**
	 * Add any extra desired fields onto the group edit page.
	 * @param &$fields \type{\array} Pre-filled array of fields ready to be passed to Xml::buildForm.
	 * @param $group \type{\string} The group in question.
	 */
	function doExtraGroupForm( &$fields, $group ) {}
	
	/**
	 * Does any necessary extra processing for the group editing form.
	 * @param $group \type{\string} The group in question.
	 * @param $reason \type{\string} The reason given for the change.
	 * @param $request \type{\string} The HTTP request for the submission.
	 */
	 function doExtraGroupSubmit( $group, $reason, $request ) {}
}

// Derive from this class to produce a rights manager which is read-only.
abstract class RightsManagerReadOnly extends RightsManager {
	// These functions are disabled.
	function setChangeableGroups( $group, $add, $remove, $addself, $removeself ) { return false; }
	function setRightsStatus( $group, $rights ) { return false; }
	function addUserGroups( $user, $groups ) { return false; }
	function removeUserGroups( $user, $groups ) { return false; }
	function canEditRights( $user ) {return false;}
}

// Pseudo-concrete implementation of a rights manager
// -Pools rights and groups from multiple sources.
class RightsManagerMulti extends RightsManagerReadOnly {

	// Functions left unimplemented because it makes no sense.
	function fetchUser( $username ) { return null; }
	function addGroupLogEntry( $group, $addRights, $removeRights, $reason, $user ) { return false; }
	function addUserGroupsLogEntry( $user, $addgroups, $removegroups, $reason, $doer ) { return false; }
	function getAllChangeableGroups() { return false; }

	function getAllGroupPermissions() {
		global $wgRightsManagers;
		
		$groupPerms = array();
		foreach( $wgRightsManagers as $rmClass ) {
			$rm = new $rmClass;
			
			$rights = $rm->getAllGroupPermissions();
			
			foreach ($rights as $group => $grouprights) {
				if ( !isset( $groupPerms[$group] ) )
					$groupPerms[$group] = array();
				
				$grouprights = array_keys( array_filter( $grouprights ) );
				foreach( $grouprights as $right ) {
					$groupPerms[$group][$right] = true;
				}
			}
		}
		
		return $groupPerms;
	}
	
	function getGroupPermissions( $groups ) {
		global $wgRightsManagers;
		
		if (!is_array( $wgRightsManagers ) ) {
			die( var_dump( $wgRightsManagers ) );
		}
		
		$perms = array();
		foreach( $wgRightsManagers as $rmClass ) {
			$rm = new $rmClass;
			
			$perms = array_merge( $perms, $rm->getGroupPermissions( $groups ) );
		}
		
		return array_unique( $perms );
	}
	
	function getUserGroups( $user ) {
		global $wgRightsManagers;
		
		$groups = array();
		foreach( $wgRightsManagers as $rmClass ) {
			$rm = new $rmClass;
			
			$groups = array_merge( $groups, $rm->getUserGroups( $user ) );
		}
		
		return array_unique( $groups );
	}
	
	function getPermissionsForUser( $user ) {
		global $wgRightsManagers;
		
		$perms = array();
		foreach( $wgRightsManagers as $rmClass ) {
			$rm = new $rmClass;
			
			$perms = array_merge( $perms, $rm->getPermissionsForUser( $user ) );
		}
		
		return array_unique( $perms );
	}
	
	function getAllGroups() {
		global $wgRightsManagers;
		
		$groups = array();
		foreach( $wgRightsManagers as $rmClass ) {
			$rm = new $rmClass;
			
			$groups = array_merge( $groups, $rm->getAllGroups( ) );
		}
		
		return array_unique( $groups );
	}
}

// Concrete implementation of a rights manager - uses the default DB and configuration setup.
class RightsManagerConfigDB extends RightsManager {
	function getAllGroupPermissions() {
		global $wgGroupPermissions,$wgAllowDBRightSubtraction;
		$groupPerms = $wgGroupPermissions;
		$dbGroupPerms = $this->loadGroupPermissions();
		
		foreach( $dbGroupPerms as $group => $rights ) {
			foreach( $rights as $right => $value ) {
				if ($value || $wgAllowDBRightSubtraction)
					$groupPerms[$group][$right] = $value;
			}
		}
		
		return $groupPerms;
	}
	
	function canEditRights( $user ) {
		return $user->isAllowed( 'grouprights' );
	}
	
	/**
	 * Load all group permissions from the database.
	 */
	function loadGroupPermissions( ) {
		static $groupPerms = null;
		
		// In-process caching...
		if ( is_array($groupPerms) ) {
			return $groupPerms;
		}
		
		// Memcached caching
		global $wgMemc;
		$groupPerms = $wgMemc->get( wfMemcKey( 'grouprights' ) );
		if ( is_array( $groupPerms ) ) {
			return $groupPerms;
		}
		
		// Fetch from DB
		
		$groupPerms = array();
		
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'group_rights', '*', array(), __METHOD__ );
		
		while ($row = $dbr->fetchObject( $res ) ) {
			$groupPerms[$row->gr_group][$row->gr_right] = (bool)$row->gr_enabled;
		}
		
		$wgMemc->set( wfMemcKey( 'grouprights' ), $groupPerms );
		
		return $groupPerms;
	}
	
	function getUserGroups( $user ) {
		if ( !isset( $user->mGroups ) || is_null( $user->mGroups ) ) {
			$dbr = wfGetDB( DB_MASTER );
			$res = $dbr->select( 'user_groups',
				array( 'ug_group' ),
				array( 'ug_user' => $user->getId() ),
				__METHOD__ );
			$this->mGroups = array();
			while( $row = $dbr->fetchObject( $res ) ) {
				$user->mGroups[] = $row->ug_group;
			}
			
			$user->mGroups = array_unique( array_filter( $user->mGroups ) );
		}
		
		return $user->mGroups;
	}
	
	function getAllChangeableGroups() {
		static $changeableGroups = null;
		
		// In-process caching
		if ( is_array( $changeableGroups ) ) {
			return $changeableGroups;
		}
		
		global $wgMemc;
		$changeableGroups = $wgMemc->get( wfMemcKey( 'changeablegroups' ) );
		if ( is_array( $changeableGroups ) ) {
			return $changeableGroups;
		}
		
		// Fetch from DB
		$changeableGroups = array();
		$groupTemplate = array( 'add' => array(), 'remove' => array(), 'add-self' => array(), 'remove-self' => array() );
		
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'changeable_groups', '*', array(), __METHOD__ );
		
		while ( $row = $dbr->fetchObject( $res ) ) {
			$changer = $row->cg_changer;
			$group = $row->cg_group;
			$action = $row->cg_action;
			
			if ( !isset( $changeableGroups[$changer] ) || !is_array( $changeableGroups[$changer] ) ) {
				$changeableGroups[$changer] = $groupTemplate;
			}
			
			if ($changer && $group && $action)
				$changeableGroups[$changer][$action][] = $group;
		}

		// People who can change all groups.
		foreach( $this->getGroupsWithPermission( 'userrights' ) as $group ) {
			$changeableGroups[$group] = array_fill_keys( array( 'add', 'remove' ), array_diff( $this->getAllGroups(), User::getImplicitGroups() ) );
			$changeableGroups[$group] = array_merge_recursive( $groupTemplate, $changeableGroups[$group] );
		}
		
		$wgMemc->set( wfMemcKey('changeablegroups'), $changeableGroups );
		
		return $changeableGroups;
	}
	
	function setChangeableGroups( $group, $addable, $removable, $addself, $removeself ) {
		$orig = $this->getChangeableGroups( array( $group ));
		
		$rows = array();
		$delete = array();
		
		if ( count( array_diff( $addable, $orig['add'] ) ) ) {
			$delete[] = 'add';
			
			foreach( $addable as $cgroup ) {
				$rows[] = array( 'cg_changer' => $group, 'cg_group' => $cgroup, 'cg_action' => 'add' );
			}
		}
		
		if ( count( array_diff( $removable, $orig['remove'] ) ) ) {
			$delete[] = 'remove';
			
			foreach( $removable as $cgroup ) {
				$rows[] = array( 'cg_changer' => $group, 'cg_group' => $cgroup, 'cg_action' => 'remove' );
			}
		}
		
		if ( count( array_diff( $addself, $orig['add-self'] ) ) ) {
			$delete[] = 'add-self';
			
			foreach( $addself as $cgroup ) {
				$rows[] = array( 'cg_changer' => $group, 'cg_group' => $cgroup, 'cg_action' => 'add-self' );
			}
		}
		
		if ( count( array_diff( $removeself, $orig['remove-self'] ) ) ) {
			$delete[] = 'remove-self';
			
			foreach( $removeself as $cgroup ) {
				$rows[] = array( 'cg_changer' => $group, 'cg_group' => $cgroup, 'cg_action' => 'remove-self' );
			}
		}
		
		$dbw = wfGetDB( DB_MASTER );
		
		$dbw->begin();
		$dbw->delete( 'changeable_groups', array( 'cg_changer' => $group, 'cg_action' => $delete ), __METHOD__ );
		$dbw->insert( 'changeable_groups', $rows, __METHOD__ );
		$dbw->commit();
		$this->invalidateGroupCache( $group );
	}
	
	function setRightsStatus( $group, $rights ) {
		$dbw = wfGetDB( DB_MASTER );
		
		$toDelete = array();
		$toSet = array();
		foreach( $rights as $right => $status ) {
			if ($this->rightInConfig( $group, $right ) == $status) {
				$toDelete[] = $right;
			} else {
				$toSet[] = array( 'gr_group' => $group, 'gr_right' => $right, 'gr_enabled' => $status );
			}
		}
		
		if ( count($toDelete) )
			$dbw->delete( 'group_rights', array( 'gr_group' => $group, 'gr_right' => $toDelete), __METHOD__ );
		
		global $wgAllowDBRightSubtraction;
		if ( $wgAllowDBRightSubtraction && count($toSet) ) {
			$dbw->replace( 'group_rights', 'gr_group,gr_right', $toSet, __METHOD__ );
		}
		
		$this->invalidateGroupCache( $group );
	}
	
	protected function rightInConfig( $group, $right ) {
		global $wgGroupPermissions;
		
		return isset($wgGroupPermissions[$group]) && isset($wgGroupPermissions[$group][$right]) && $wgGroupPermissions[$group][$right];
	}
	
	function changeUserGroups( $user, $addgroups, $removegroups, $reason, $doer ) {
		parent::changeUserGroups( $user, $addgroups, $removegroups, $reason, $doer );
		wfRunHooks( 'UserRights', array( &$user, $addgroups, $removegroups, $reason, $doer ) );
	}
	
	function addUserGroups( $user, $groups ) {
		$groups = array_filter( $groups );
		
		if (!count($groups))
			return;
			
		$dbw = wfGetDB( DB_MASTER );
		
		$rows = array();
		
		foreach( $groups as $group ) {
			$rows[] = array( 'ug_user' => $user->getId(), 'ug_group' => $group );
		}
		
		$dbw->insert( 'user_groups',
			$rows,
			__METHOD__,
			array( 'IGNORE' ) );
	}
	
	function removeUserGroups( $user, $groups ) {
		$groups = array_filter( $groups );
		
		if (!count($groups))
			return;
	
		$dbw = wfGetDB( DB_MASTER );
		$dbw->delete( 'user_groups',
			array(
				'ug_user'  => $user->getID(),
				'ug_group' => $groups,
			),
			__METHOD__ );

		$user->invalidateCache();
	}
	
	function invalidateGroupCache( $group ) {
		global $wgMemc;
		
		$wgMemc->delete( wfMemcKey( 'changeablegroups' ) );
		$wgMemc->delete( wfMemcKey( 'grouprights' ) );
	}
	
	function invalidateUserCache( $user ) {
		$user->invalidateCache();
	}
	
	function rightEditable( $group, $right ) {
		global $wgAllowDBRightSubtraction;
		
		return $wgAllowDBRightSubtraction || !$this->rightInConfig( $group, $right );
	}
	
	function addGroupLogEntry( $group, $addRights, $removeRights, $reason, $user ) {
		global $wgRequest,$wgUser;
		
		if ($user == null)
			$user = $wgUser;
		
		$log = new LogPage( 'rights' );

		$log->addEntry( 'grprights',
			SpecialPage::getTitleFor( 'ListUsers', $group ),
			$reason,
			array(
				$this->makeRightsList( $addRights ),
				$this->makeRightsList( $removeRights )
			)
		, $user);
	}
	
	function addUserGroupsLogEntry( $user, $addgroups, $removegroups, $reason, $doer ) {
		global $wgRequest;
		$log = new LogPage( 'rights' );
	
		$log->addEntry( 'rights2',
			$user->getUserPage(),
			$reason,
			array(
				$this->makeGroupNameListForLog( $addgroups ),
				$this->makeGroupNameListForLog( $removegroups )
			)
		);
	}
	
	function makeGroupNameListForLog( $ids ) {
		if( empty( $ids ) ) {
			return '';
		} else {
			return implode( ', ', $ids );
		}
	}
	
	function fetchUser( $username ) {
		if( $username{0} == '#' ) {
			// Numeric ID can be specified...
			// We'll do a lookup for the name internally.
			$id = intval( substr( $username, 1 ) );

			$username = User::whoIs( $id );

			if( !$username ) {
				return null;
			}
		}
		
		$user = User::newFromName( $username );
		
		if (!$user || $user->isAnon()) {
			return null;
		}
		
		return $user;
	}
	
	function getChangeableGroupsForUser( $user ) {
		$cg = parent::getChangeableGroupsForUser( $user );
		
		$allgroups = array_diff( $this->getAllGroups(), User::getImplicitGroups() );
		
		if ($user->isAllowed( 'userrights' )) {
			$cg = array_merge_recursive( $cg, array_fill_keys(  array( 'add', 'remove', 'add-self', 'remove-self' ), $allgroups ) );
		}
		
		return $cg;
	}
}
