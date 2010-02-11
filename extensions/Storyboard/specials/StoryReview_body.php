<?php
/**
 * File holding the SpecialStoryReview class that allows reviewers to moderate the submitted stories.
 *
 * @file StoryReview_body.php
 * @ingroup Storyboard
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class SpecialStoryReview extends IncludableSpecialPage {

	function __construct() {
		parent::__construct( 'StoryReview' );

		wfLoadExtensionMessages( 'Storyboard' );
	}

	function execute( $language ) {
		global $wgOut;
	}
}
