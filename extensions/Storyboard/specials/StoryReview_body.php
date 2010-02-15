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

	public function __construct() {
		parent::__construct( 'StoryReview' );

		wfLoadExtensionMessages( 'Storyboard' );
	}

	public function execute( $language ) {
		global $wgUser;
		if ($wgUser->isAllowed('storyreview') && !$wgUser->isBlocked()) {
			$this->addOutput();
		}
		else {
			global $wgOut;
			$wgOut->permissionRequired( 'storyreview' );
		}
	}
	
	private function addOutput() {
		global $wgOut;
		$wgOut->includeJQuery();		
	}
}
