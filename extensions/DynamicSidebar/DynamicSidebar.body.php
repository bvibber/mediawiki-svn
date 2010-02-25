<?php

if (!defined('MEDIAWIKI')) die();

class DynamicSidebar {

	/**
	 * Called from SkinBeforeParseSidebar hook. Modifies the sidebar
	 * via callbacks.
	 *
	 * @param Skin $skin
	 * @param string $sidebar
	 * @access public
	 */
	public function modifySidebarContent( $skin, &$sidebar ) {
		$dynamicsidebar = new DynamicSidebar();
		$sidebar = $dynamicsidebar->modifySidebar( $skin, $sidebar );

		return true;
	}

	/**
	 * Internal function called to modify the sidebar via callbacks.
	 *
	 * @param Skin $skin
	 * @param string $sidebar
	 * @access private
	 * @return string
	 */
	private function modifySidebar( $skin, $sidebar ) {
		global $egDynamicSidebarUseGroups, $egDynamicSidebarUseUserpages;
		global $egDynamicSidebarUseCategories;

		if ( $egDynamicSidebarUseGroups ) {
			$sidebar = preg_replace_callback( "/\* GROUP-SIDEBAR/", array( &$this, 'doGroupSidebar' ), $sidebar );
		}
		if ( $egDynamicSidebarUseUserpages ) {
			$sidebar = preg_replace_callback( "/\* USER-SIDEBAR/", array( &$this, 'doUserSidebar' ), $sidebar );
		}
		if ( $egDynamicSidebarUseCategories ) {
			$sidebar = preg_replace_callback( "/\* CATEGORY-SIDEBAR/", array( &$this, 'doCategorySidebar' ), $sidebar );
		}

		return $sidebar;
	}

	/**
	 * Callback function, replaces $matches with the contents of
	 * User:<username>/Sidebar
	 *
	 * @param array $matches
	 * @access private
	 * @return string
	 */
	private function doUserSidebar( $matches ) {
		global $wgUser;
		
		$username = $wgUser->getName();

		$title = Title::makeTitle( NS_USER, $username . '/Sidebar' );
		$a = new Article( $title );
		
		// does '<username>/Sidebar' page exist?
		if ( ( $a === null ) || ( $a->getID() === 0 ) ) {
			// Remove this sidebar if not
			return '';
		}

		$text = $a->getContent();

		return $text;
	}

	/**
	 * Callback function, replaces $matches with the contents of
	 * MediaWiki:Sidebar/<group>, based on the current logged in user's
	 * groups.
	 *
	 * @param array $matches
	 * @access private
	 * @return string
	 */
	private function doGroupSidebar( $matches ) {
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
			$a = new Article( $title );

			// Is the corresponding page found?
			if ( ( $a === null ) || ( $a->getID() === 0 ) ) {
				continue;
			}

			$text .= $a->getContent() . "\n";

		}

		return $text;
	}

	/**
	 * Callback function, replaces $matches with the contents of
	 * MediaWiki:Sidebar/<category>, based on the current logged in user's
	 * userpage categories.
	 *
	 * @param array $matches
	 * @access private
	 * @return string
	 */
	private function doCategorySidebar( $matches ) {
		global $wgUser;

		$username = $wgUser->getName();
		self::printDebug( "User name: $username" );
		$userpage = Title::makeTitle( NS_USER, $username );
		$categories = $userpage->getParentCategories();

		// Did we find any categories?
		if ( count( $categories ) == 0 ) {
			// Remove this sidebar if not.
			return '';
		}

		$text = '';

		// getParentCategories() returns categories in the form:
		// [ParentCategory] => page
		// We only care about the parent category
		foreach ( $categories as $category => $userpage ) {
			// $category is in form Category:<category>
			// We need <category>.
			$category = explode( ":", $category );
			$category = $category[1];
			self::printDebug( "Checking category: $category" );

			// Form the path to the article:
			// MediaWiki:Sidebar/<category>
			$title = Title::makeTitle( NS_MEDIAWIKI, 'Sidebar/' . $category );
			$a = new Article( $title );

			// Is the corresponding page found?
			if ( ( $a === null ) || ( $a->getID() === 0 ) ) {
				continue;
			}

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
		global $egDynamicSidebarDebug;

		if ( $egDynamicSidebarDebug ) {
			if ( isset( $debugArr ) ) {
				$text = $debugText . " " . implode( "::", $debugArr );
				wfDebugLog( 'dynamic-sidebar', $text, false );
			} else {
				wfDebugLog( 'dynamic-sidebar', $debugText, false );
			}
		}
	}

}
