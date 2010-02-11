<?php
/**
 * Chick: A lightweight Monobook skin with no sidebar, the sidebar links are
 * given at the bottom of the page instead, as in the unstyled MySkin.
 *
 * @file
 * @ingroup Skins
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

/** */
require_once( dirname( __FILE__ ) . '/MonoBook.php' );

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @ingroup Skins
 */
class SkinChick extends SkinMonoBook {

	/**
	 * We don't want common/wikiprintable.css.
	 */
	public function commonPrintStylesheet() {
		return false;
	}

	/** @return string path to the skin stylesheet */
	public function getStylesheet() {
		return 'chick/main.css';
	}

	/** @return string skin name */
	public function getSkinName() {
		return 'chick';
	}

	function setupSkinUserCss( OutputPage $out ) {
		$out->addStyle( 'common/shared.css' );
		$out->addStyle( 'common/commonPrint.css', 'print' );
		$out->addStyle( $this->getStylesheet() );
		$out->addStyle( 'common/common_rtl.css', '', '', 'rtl' );
		// Append to the default screen common & print styles...
		$out->addStyle( 'chick/main.css', 'screen,handheld' );
		$out->addStyle( 'chick/IE60Fixes.css', 'screen,handheld', 'IE 6' );
	}
}
