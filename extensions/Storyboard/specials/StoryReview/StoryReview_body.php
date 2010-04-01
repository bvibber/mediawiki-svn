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
		
		$wgOut->setPageTitle( wfMsg( 'storyreview' ) );
		
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
				'story_author_image',
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
		global $wgTitle;
		
		$editUrl = SpecialPage::getTitleFor( 'story', $story->story_title )->getFullURL('action=edit&returnto=' . $wgTitle->getPrefixedText() );
		$editUrl = Xml::escapeJsString( $editUrl );
		
		$title = htmlspecialchars( $story->story_title );
		$text = htmlspecialchars( $story->story_text );
		
		$publishAction = $story->story_is_published ? 'unpublish' : 'publish';
		// Uses storyboard-unpublish or storyboard-publish.
		$publishMsg = htmlspecialchars( wfMsg( "storyboard-$publishAction" ) );		
		
		$editMsg = htmlspecialchars( wfMsg( 'edit' ) );
		$hideMsg = htmlspecialchars( wfMsg( 'hide' ) );		
		
		$imageHtml = '';
		$imageButtonsHtml = '';
		
		if ( $story->story_author_image ) {
			$imageAction = $story->story_image_hidden ? 'unhideimage' : 'hideimage';
			// Uses storyboard-unhideimage or storyboard-hideimage.
			$imageMsg = htmlspecialchars(  wfMsg( "storyboard-$imageAction" ) );
			
			$deleteImageMsg = htmlspecialchars(  wfMsg( 'storyboard-deleteimage' ) );

			$imgAttribs = array(
				'src' => $story->story_author_image,
				'class' => 'story-image',
				'id' => "story_image_$story->story_id",
				'title' => $title,
				'alt' => $title
			);

			if ( $story->story_image_hidden ) {
				$imgAttribs['style'] = 'display:none;';
			}
			
			$imageHtml = Html::element( 'img', $imgAttribs );
			
			$imageButtonsHtml = <<<EOT
				&nbsp;&nbsp;&nbsp;<button type="button" 
					onclick="stbDoStoryAction( this, $story->story_id, '$imageAction' )" id="image_button_$story->story_id">$imageMsg</button>
				&nbsp;&nbsp;&nbsp;<button type="button" onclick="stbDeleteStoryImage( this, $story->story_id )">$deleteImageMsg</button>			
EOT;
		}
		
		return <<<EOT
		<table width="100%" border="1" id="story_$story->story_id">
			<tr>
				<td>
					<div class="story">
						$imageHtml
						<div class="story-title">$title</div><br />
						$text
					</div>
				</td>
			</tr>
			<tr>
				<td align="center" height="35">
					<button type="button" onclick="stbDoStoryAction( this, $story->story_id, '$publishAction' )">$publishMsg</button>&nbsp;&nbsp;&nbsp;
					<button type="button" onclick="window.location='$editUrl'">$editMsg</button>&nbsp;&nbsp;&nbsp;
					<button type="button" onclick="stbDoStoryAction( this, $story->story_id, 'hide' )">$hideMsg</button>$imageButtonsHtml
				</td>
			</tr>
		</table>
EOT;
	}
}