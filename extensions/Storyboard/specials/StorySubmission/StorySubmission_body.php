<?php
/**
 * File holding the SpecialStorySubmission class defining a special page to save submitted stories and display a success message.
 *
 * @file StorySubmission_body.php
 * @ingroup Storyboard
 * @ingroup SpecialPage
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class SpecialStorySubmission extends UnlistedSpecialPage {

	public function __construct() {
		parent::__construct( 'StorySubmission' );
	}

	public function execute( $title ) {
		global $wgOut, $wgRequest, $wgUser;
		
		if ( $wgRequest->wasPosted() && $wgUser->matchEditToken( $wgRequest->getVal( 'wpStoryEditToken' ) ) ) {
			$this->saveStory();
			$this->displayResult();
		} else {
			$wgOut->returnToMain();
		}
	}
	
	/**
	 * Store the submitted story in the database, and return a page telling the user his story has been submitted.
	 */
	private function saveStory() {
		global $wgRequest, $wgUser;
		
		$dbw = wfGetDB( DB_MASTER );

		$title = $wgRequest->getText( 'storytitle' );

		$story = array(
			'story_lang_code' => $wgRequest->getText( 'lang' ),
			'story_author_name' => $wgRequest->getText( 'name' ),
			'story_author_location' => $wgRequest->getText( 'location' ),
			'story_author_occupation' => $wgRequest->getText( 'occupation' ),
			'story_author_email' => $wgRequest->getText( 'email' ),
			'story_title' => $title,
			'story_text' => $wgRequest->getText( 'storytext' ),
			'story_created' => $dbw->timestamp( time() ),
			'story_modified' => $dbw->timestamp( time() ),
		);

		// If the user is logged in, also store his user id.
		if ( $wgUser->isLoggedIn() ) {
			$story[ 'story_author_id' ] = $wgUser->getId();
		}

		// TODO: email confirmation would be nice

		$dbw->insert( 'storyboard', $story );
	}
	
	private function displayResult() {
		global $wgOut;
		
		$wgOut->setPageTitle( wfMsg( 'storyboard-submissioncomplete' ) );
		
		$storyboardLink = ''; // TODO: create html link to the page containing stories. 

		$wgOut->addWikiText( wfMsgExt( 'storyboard-createdsucessfully', 'parsemag', $storyboardLink ) );
	}
	
}