<?php
/**
 * MySkin: Monobook without the CSS. The idea is that you
 * customise it using user or site CSS
 *
 * @file
 * @ingroup Skins
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

/**
 * Inherit main code from SkinMonoBook.
 * @ingroup Skins
 */
class SkinMySkin extends SkinMonoBook {

	/**
	 * We don't want common/wikiprintable.css.
	 */
	public function commonPrintStylesheet() {
		return false;
	}

	/** @return string skin name */
	public function getSkinName() {
		return 'myskin';
	}

	function setupSkinUserCss( OutputPage $out ) {
		$out->addStyle( 'common/shared.css' );
		$out->addStyle( 'common/commonPrint.css', 'print' );
		$out->addStyle( 'common/common_rtl.css', '', '', 'rtl' );
	}
}
