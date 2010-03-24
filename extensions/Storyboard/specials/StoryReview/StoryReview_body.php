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
		
		global $wgUser, $wgOut;
		
		$wgOut->setPageTitle( wfMsg( 'storyboard-storyreview' ) );
		
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
		global $wgOut, $egStoryboardScriptPath;
		
		$wgOut->addStyle( $egStoryboardScriptPath . '/storyboard.css' );
		$wgOut->includeJQuery();
		$wgOut->addScriptFile( $egStoryboardScriptPath . '/storyboard.js' );
		
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
				'story_is_published',
				'story_image_hidden'
			),
			array( 'story_is_hidden' => 0 )
		);
		
		// String to hold the html for both the unreviewed and reviewed stories.
		$unreviewed = '';
		$reviewed = '';
		
		// Loop through all stories, get their html, and add it to the appropriate string.
		while ( $story = $dbr->fetchObject( $stories ) ) {
			if ( $story->story_is_published ) {
				$reviewed .= $this->getStorySegments( $story );
			}
			else {
				$unreviewed .= $this->getStorySegments( $story );
			}
		}

		$unrevMsg = wfMsg( 'storyboard-unreviewed' );
		$revMsg = wfMsg( 'storyboard-reviewed' );
		
		// Output the html for the stories.
		$wgOut->addHTML( <<<EOT
		<h2>$unrevMsg</h2>
		$unreviewed
		<h2>$revMsg</h2>
		$reviewed		
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
	private function getStorySegments( $story ) {
		$imageSrc = 'http://upload.wikimedia.org/wikipedia/mediawiki/9/99/SemanticMaps.png'; // TODO: get cropped image here
		
		$title = htmlspecialchars( $story->story_title );
		$text = htmlspecialchars( $story->story_text );
		
		// TODO: htmlspecialchars the messages?
		
		$publishAction = $story->story_is_published ? 'unpublish' : 'publish';
		// Uses storyboard-unpublish or storyboard-publish
		$publishMsg = wfMsg( "storyboard-$publishAction" );
		
		$imageAction = $story->story_image_hidden ? 'unhideimage' : 'hideimage';
		// Uses storyboard-unhideimage or storyboard-hideimage
		$imageMsg = wfMsg( "storyboard-$imageAction" );
		
		$editMsg = wfMsg( 'edit' );
		$hideMsg = wfMsg( 'hide' );
		$deleteImageMsg = wfMsg( 'storyboard-deleteimage' );
		
		return <<<EOT
		<table width="100%" border="1" id="story_$story->story_id">
			<tr>
				<td>
					<div class="story">
						<img src="http://upload.wikimedia.org/wikipedia/mediawiki/9/99/SemanticMaps.png" class="story-image">
						<div class="story-title">$title</div><br />
						$text
					</div>
				</td>
			</tr>
			<tr>
				<td align="center" height="35">
					<button type="button" onclick="stbDoStoryAction( this, $story->story_id, '$publishAction' )">$publishMsg</button>&nbsp;&nbsp;&nbsp;
					<button type="button" onclick="">$editMsg</button>&nbsp;&nbsp;&nbsp;
					<button type="button" onclick="stbDoStoryAction( this, $story->story_id, 'hide' )">$hideMsg</button>&nbsp;&nbsp;&nbsp;
					<button type="button" onclick="stbDoStoryAction( this, $story->story_id, '$imageAction' )">$imageMsg</button>&nbsp;&nbsp;&nbsp;
					<button type="button" onclick="stbDeleteStoryImage( this, $story->story_id )">$deleteImageMsg</button>
				</td>
			</tr>
		</table>
EOT;
	}
}