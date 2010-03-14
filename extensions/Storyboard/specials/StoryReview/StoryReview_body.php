<?php
/**
 * File holding the SpecialStoryReview class that allows reviewers to moderate the submitted stories.
 *
 * @file StoryReview_body.php
 * @ingroup Storyboard
 * @ingroup SpecialPage
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class SpecialStoryReview extends SpecialPage {

	public function __construct() {
		parent::__construct( 'StoryReview', 'storyreview' );
	}

	public function execute( $language ) {
		wfProfileIn( __METHOD__ );
		
		global $wgUser;
		if ( $this->userCanExecute( $wgUser ) ) {
			// If the user has the storyreview permission and is not blocked, show the regular output.
			$this->addOutput();
		} else {
			// If the user is not authorized, show an error.
			$this->displayRestrictionError();
		}
		
		wfProfileOut( __METHOD__ );
	}

	private function addOutput() {
		global $wgOut;
		
		$wgOut->setPageTitle( wfMsg( 'storyboard-storyreview' ) );
		
		$wgOut->includeJQuery();
		
		// Get a slave db object to do read operations against.
		$dbr = wfGetDB( DB_SLAVE );
		
		// Create a query to retrieve information about all non hidden stories.
		$stories = $dbr->select(
			Storyboard_TABLE,
			array(
				'story_id',
				'story_author_name',
				'story_title',
				'story_text',
				'story_is_published'
			),
			array( 'story_is_hidden' => 0 )
		);
		
		// String to hold the html for both the unreviewed and reviewed stories.
		$unreviewed = '';
		$reviewed = '';
		
		// Loop through all stories, get their html, and add it to the appropriate string.
		while ( $story = $dbr->fetchObject( $stories ) ) {
			if ( $story->story_is_published ) {
				$reviewed .= $this->getStorySegments( $story, $story->story_is_published  );
			}
			else {
				$unreviewed .= $this->getStorySegments( $story, $story->story_is_published  );
			}
		}

		$unrevMsg = wfMsg( 'storyboard-unreviewed' );
		$revMsg = wfMsg( 'storyboard-reviewed' );
		
		// Output the html for the stories.
		$wgOut->addHTML( <<<EOT
		<h2>$unrevMsg</h2>
		<table width="100%">
		$unreviewed
		</table>
		<h2>$revMsg</h2>
		<table width="100%">
		$reviewed
		</table>		
EOT
		);
	}
	
	/**
	 * Returns the html segments for a single story.
	 * 
	 * @param $story
	 * 
	 * @return string
	 */
	private function getStorySegments( $story, $published ) {
		$imageSrc = 'http://upload.wikimedia.org/wikipedia/mediawiki/9/99/SemanticMaps.png'; // TODO: get cropped image here
		$title = htmlspecialchars( $story->story_title );
		$text = htmlspecialchars( $story->story_text );
		$publish = $published ? wfMsg( 'storyboard-unpublish' ) : wfMsg( 'storyboard-publish' );
		$edit = wfMsg( 'edit' );
		$hide = wfMsg( 'hide' );
		
		return <<<EOT
		<tr>
			<td>
				<table width="100%" border="1">
					<tr>
						<td rowspan="2" width="200px">
							<img src="$imageSrc" />
						</td>
						<td>
							<b>$title</b>
							<br />$text
						</td>
					</tr>
					<tr>
						<td align="center" height="35">
							<button type="button">$publish</button>&nbsp;&nbsp;&nbsp;
							<button type="button">$edit</button>&nbsp;&nbsp;&nbsp;
							<button type="button">$hide</button>
						</td>
					</tr>
				</table>
			</td>
		</tr>
EOT;
	}
}