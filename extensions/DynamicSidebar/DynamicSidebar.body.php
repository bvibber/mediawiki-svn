<?php
class DynamicSidebar {
	/**
	 * Called through $wgExtensionFunctions. Disables sidebar cache if necessary
	 */
	public static function setup() {
		global $wgUser, $wgEnableSidebarCache;

		// Don't pollute the sidebar cache for non-logged-in users
		// Also ensure that logged-in users are getting dynamic content
		// FIXME: Only do this for users who should actually get the non-standard sidebar
		if ( $wgUser->isLoggedIn() ) {
			$wgEnableSidebarCache = false;
		}
		return true;
	}

	/**
	 * Called from SkinBeforeParseSidebar hook. Modifies the sidebar
	 * via callbacks.
	 *
	 * @param Skin $skin
	 * @param string $sidebar
	 */
	private static function modifySidebar( $skin, &$sidebar ) {
		global $wgDynamicSidebarUseGroups, $wgDynamicSidebarUseUserpages;
		global $wgDynamicSidebarUseCategories;

		if ( $wgDynamicSidebarUseGroups && isset( $sidebar['GROUP-SIDEBAR'] ) ) {
			// Replace the GROUP-SIDEBAR entry with the group's sidebar
			$groupSB = array();
			$skin->addToSidebarPlain( $groupSB, self::doGroupSidebar() );
			array_splice( $sidebar, array_search( 'GROUP-SIDEBAR', $sidebar ), 1, $groupSB );
		}
		if ( $wgDynamicSidebarUseUserpages && isset( $sidebar['USER-SIDEBAR'] ) ) {
			// Replace the USER-SIDEBAR entry with the user's sidebar
			$userSB = array();
			$skin->addToSidebarPlain( $userSB, self::doUserSidebar() );
			array_splice( $sidebar, array_search( 'USER-SIDEBAR', $sidebar ), 1, $userSB );
		}
		if ( $wgDynamicSidebarUseCategories && isset( $sidebar['CATEGORY-SIDEBAR'] ) ) {
			$catSB = array();
			$skin->addToSidebarPlain( $catSB, self::doCategorySidebar() );
			array_splice( $sidebar, array_search( 'CATEGORY-SIDEBAR', $sidebar ), 1, $catSB );
		}
		return true;
	}

	/**
	 * Grabs the sidebar for the current user
	 * User:<username>/Sidebar
	 *
	 * @access private
	 * @return string
	 */
	private static function doUserSidebar() {
		global $wgUser;
		$username = $wgUser->getName();
		
		// does 'User:<username>/Sidebar' page exist?
		$title = Title::makeTitle( NS_USER, $username . '/Sidebar' );
		if ( !$title->exists() ) {
			// Remove this sidebar if not
			return '';
		}

		$a = new Article( $title );
		return $a->getContent();
	}

	/**
	 * Grabs the sidebar for the current user's groups
	 *
	 * @access private
	 * @return string
	 */
	private static function doGroupSidebar( $matches ) {
		global $wgUser;
		
		// Get group membership array.
		$groups = $wgUser->getEffectiveGroups();
		// Did we find any groups?
		if ( count( $groups ) == 0 ) {
			// Remove this sidebar if not
			return '';
		}

		$text = '';
		foreach ( $groups as $group ) {
			// Form the path to the article:
			// MediaWiki:Sidebar/<group>
			$title = Title::makeTitle( NS_MEDIAWIKI, 'Sidebar/' . $group );
			if ( !$title->exists() ) {
				continue;
			}
			$a = new Article( $title );
			$text .= $a->getContent() . "\n";

		}
		return $text;
	}

	/**
	 * Grabs the sidebar for the current user's categories
	 *
	 * @access private
	 * @return string
	 */
	private static function doCategorySidebar( $matches ) {
		global $wgUser;

		self::printDebug( "User name: {$wgUser->getName()}" );
		$categories = $wgUser->getUserPage()->getParentCategories();

		// Did we find any categories?
		if ( count( $categories ) == 0 ) {
			// Remove this sidebar if not.
			return '';
		}

		$text = '';
		// getParentCategories() returns categories in the form:
		// [ParentCategory] => page
		// We only care about the parent category
		foreach ( $categories as $category => $unused ) {
			// $category is in form Category:<category>
			// We need <category>.
			$category = explode( ":", $category );
			$category = $category[1];
			self::printDebug( "Checking category: $category" );

			// Form the path to the article:
			// MediaWiki:Sidebar/<category>
			$title = Title::makeTitle( NS_MEDIAWIKI, 'Sidebar/' . $category );
			if ( !$title->exists() ) {
				continue;
			}
			$a = new Article( $title );
			$text .= $a->getContent() . "\n";
		}
		return $text;
	}

	/**
	 * Prints debugging information. $debugText is what you want to print, $debugArr
	 * will expand into arrItem::arrItem2::arrItem3::... and is appended to $debugText
	 *
	 * @param string $debugText
	 * @param array $debugArr
	 * @access private
	 */
	private static function printDebug( $debugText, $debugArr = null ) {
		if ( isset( $debugArr ) ) {
			$text = $debugText . " " . implode( "::", $debugArr );
			wfDebugLog( 'dynamic-sidebar', $text, false );
		} else {
			wfDebugLog( 'dynamic-sidebar', $debugText, false );
		}
	}
}
