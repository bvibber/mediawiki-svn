<?php

/**
 * This special page lists all defined user groups and the associated rights.
 * See also @ref $wgGroupPermissions.
 *
 * @ingroup SpecialPage
 * @author Petr Kadlec <mormegil@centrum.cz>
 */
class SpecialListGroupRights extends SpecialPage {

	var $skin;

	/**
	 * Constructor
	 */
	function __construct() {
		global $wgUser;
		parent::__construct( 'Listgrouprights' );
		$this->skin = $wgUser->getSkin();
	}

	/**
	 * Show the special page
	 */
	public function execute( $par ) {
		global $wgOut, $wgImplicitGroups, $wgMessageCache;
		global $wgAddGroups, $wgRemoveGroups;
		$wgMessageCache->loadAllMessages();
		
		$rm = new RightsManagerMulti;
		$groupPerms = $rm->getAllGroupPermissions();

		$this->setHeaders();
		$this->outputHeader();

		$wgOut->addHTML(
			Xml::openElement( 'table', array( 'class' => 'mw-listgrouprights-table' ) ) .
				'<tr>' .
					Xml::element( 'th', null, wfMsg( 'listgrouprights-group' ) ) .
					Xml::element( 'th', null, wfMsg( 'listgrouprights-rights' ) ) .
				'</tr>'
		);

		foreach( $groupPerms as $group => $permissions ) {
			$groupname = ( $group == '*' ) ? 'all' : htmlspecialchars( $group ); // TODO: Replace * with a more descriptive groupname

			$msg = wfMsg( 'group-' . $groupname );
			if ( wfEmptyMsg( 'group-' . $groupname, $msg ) || $msg == '' ) {
				$groupnameLocalized = $groupname;
			} else {
				$groupnameLocalized = $msg;
			}

			$msg = wfMsgForContent( 'grouppage-' . $groupname );
			if ( wfEmptyMsg( 'grouppage-' . $groupname, $msg ) || $msg == '' ) {
				$grouppageLocalized = MWNamespace::getCanonicalName( NS_PROJECT ) . ':' . $groupname;
			} else {
				$grouppageLocalized = $msg;
			}

			if( $group == '*' ) {
				// Do not make a link for the generic * group
				$grouppage = $groupnameLocalized;
			} else {
				$grouppage = $this->skin->makeLink( $grouppageLocalized, $groupnameLocalized );
			}

			if ( !in_array( $group, $wgImplicitGroups ) ) {
				$grouplink = '<br />' . $this->skin->makeKnownLinkObj( SpecialPage::getTitleFor( 'Listusers' ), wfMsgHtml( 'listgrouprights-members' ), 'group=' . $group );
			} else {
				// No link to Special:listusers for implicit groups as they are unlistable
				$grouplink = '';
			}

			$changeableGroups = array();

			global $wgRightsManagers;
			foreach( $wgRightsManagers as $rmClass ) {
				$rm = new $rmClass;
				$changeableGroups = array_merge_recursive( $rm->getChangeableGroups( array( $group ) ), $changeableGroups );
			}

			$wgOut->addHTML(
				'<tr>
					<td>' .
						$grouppage . $grouplink .
					'</td>
					<td>' .
						self::formatPermissions( $permissions, $changeableGroups ) .
					'</td>
				</tr>'
			);
		}
		$wgOut->addHTML(
			Xml::closeElement( 'table' ) . "\n"
		);
	}

	/**
	 * Create a user-readable list of permissions from the given array.
	 *
	 * @param $permissions Array of permission => bool (from $wgGroupPermissions items)
	 * @param $changeableGroups Array of action => list of groups (from RightsManager::getChangeableGroups)
	 * @return string List of all granted permissions, separated by comma separator
	 */
	 private static function formatPermissions( $permissions, $changeableGroups ) {
	 	global $wgLang;
		$r = array();
		foreach( $permissions as $permission => $granted ) {
			if ( $granted ) {
				$description = wfMsgExt( 'listgrouprights-right-display', array( 'parseinline' ),
					User::getRightDescription( $permission ),
					$permission
				);
				$r[] = $description;
			}
		}
		sort( $r );
		
		// Get addable/removable groups.
		$groupActions = array( 'add-self' => 'addself', 'remove-self' => 'removeself', 'add' => 'add', 'remove' => 'remove' );
		
		foreach( $groupActions as $key => $value ) {
			if ( !isset($changeableGroups[$key]) || !is_array($changeableGroups[$key]) || !count($changeableGroups[$key]) ) {
				// Do nothing.
			} elseif ( !count( array_diff( $changeableGroups[$key], array_diff( User::getAllGroups(), User::getImplicitGroups() ) ) ) ) {
				// i.e. User can change *all* groups.
				$r[] = wfMsgExt( "listgrouprights-{$value}group-all", array( 'escape' ) );
			} elseif ( count( $changeableGroups[$key] ) ) {
				$r[] = wfMsgExt( "listgrouprights-{$value}group", array( 'parseinline' ), $wgLang->listToText( array_map( array( 'User', 'makeGroupLinkWiki' ), $changeableGroups[$key] ) ), count( $changeableGroups[$key] ) );
			}
		}
	
		if( empty( $r ) ) {
			return '';
		} else {
			return '<ul><li>' . implode( "</li>\n<li>", $r ) . '</li></ul>';
		}
	}
}
