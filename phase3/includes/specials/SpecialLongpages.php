<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 *
 * @ingroup SpecialPage
 */
class LongPagesPage extends ShortPagesPage {

	function __construct() {
		SpecialPage::__construct( 'Longpages' );
	}

	function sortDescending() {
		return true;
	}
}
