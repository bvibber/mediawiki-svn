<?php
/**
 * File holding the SpecialStoryReview class that allows reviewers to moderate the submitted stories.
 *
 * @file StoryReview_body.php
 * @ingroup Storyboard
 * @ingroup SpecialPage
 *
 * @author Jeroen De Dauw
 * 
 * TODO: implement eternal load (or paging) stuff for each list
 * TODO: fix layout
 * TODO: ajax load tab contents?
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
		global $wgOut, $wgRequest, $wgJsMimeType, $wgContLanguageCode, $egStoryboardScriptPath;
		
		efStoryboardAddJSLocalisation();
		$wgOut->addStyle( $egStoryboardScriptPath . '/storyboard.css' );
		$wgOut->includeJQuery();
		$wgOut->addScriptFile( $egStoryboardScriptPath . "/jquery/jquery.ajaxscroll.js" );
		$wgOut->addScriptFile( $egStoryboardScriptPath . '/storyboard.js' );
		// jQuery UI core and Tabs.
		$wgOut->addScriptFile( $egStoryboardScriptPath . '/jquery/jquery-ui-1.7.2.custom.min.js' );
		$wgOut->addStyle( $egStoryboardScriptPath . '/jquery/css/jquery-ui-1.7.2.custom.css' );
		
		$unpublished = htmlspecialchars( wfMsg( 'storyboard-unpublished' ) );
		$published = htmlspecialchars( wfMsg( 'storyboard-published' ) );
		$hidden = htmlspecialchars( wfMsg( 'storyboard-hidden' ) );
		
		$language = $wgRequest->getText( 'language', false );
		if ( !$language ) $language = $wgContLanguageCode;
		
		$html = <<<EOT
<div id="storyreview-tabs">
	<ul>
		<li><a href="#$unpublished" id="$unpublished-tab">$unpublished</a></li>
		<li><a href="#$published" id="$published-tab">$published</a></li>
		<li><a href="#$hidden" id="$hidden-tab">$hidden</a></li>
	</ul>
	<div id="$unpublished"></div>
	<div id="$published"></div>
	<div id="$hidden"></div>
</div>

<script type="$wgJsMimeType">
	var storyboardLanguage = "$language";

	jQuery( function() {
		jQuery( "#storyreview-tabs" ).tabs();
	});
	
	jQuery('#storyreview-tabs').bind( 'tabsshow', function( event, ui ) {
		stbShowReviewBoard( jQuery( ui.panel ), ui.index );
	});
</script>	
EOT;
	
	$wgOut->addHTML( $html );
	}
	
	/**
	 * Returns the html segments for a single story.
	 * 
	 * @param $story
	 * 
	 * @return string
	 */
	private function getStoryBlock( $story, $storyState ) {
		global $wgTitle;
		
		$editUrl = SpecialPage::getTitleFor( 'story', $story->story_title )->getFullURL( 'action=edit&returnto=' . $wgTitle->getPrefixedText() );
		$editUrl = Xml::escapeJsString( $editUrl );
		
		$title = htmlspecialchars( $story->story_title );
		$text = htmlspecialchars( $story->story_text );
		
		$publishAction = $storyState == Storyboard_STORY_PUBLISHED ? 'unpublish' : 'publish';
		// Uses storyboard-unpublish or storyboard-publish.
		$publishMsg = htmlspecialchars( wfMsg( "storyboard-$publishAction" ) );
		
		$editMsg = htmlspecialchars( wfMsg( 'edit' ) );
		$hideMsg = htmlspecialchars( wfMsg( 'hide' ) );
		
		$imageHtml = '';
		
		$buttons = array();
		
		if ( $storyState != Storyboard_STORY_PUBLISHED ) {
			$buttons[] = $this->getStateActionButton( $story->story_id, 'publish', 'storyboard-publish' );
		}

		if ( $storyState != Storyboard_STORY_UNPUBLISHED ) {
			$buttons[] = $this->getStateActionButton( $story->story_id, 'unpublish', 'storyboard-unpublish' );
		}

		if ( $storyState != Storyboard_STORY_HIDDEN ) {
			$buttons[] = $this->getStateActionButton( $story->story_id, 'hide', 'storyboard-hide' );
		}
		
		$buttons[] = <<<EOT
		<button type="button" onclick="window.location='$editUrl'">$editMsg</button>
EOT;
		
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
			
			$buttons[] = <<<EOT
				<button type="button" onclick="stbDoStoryAction( this, $story->story_id, '$imageAction' )"
					id="image_button_$story->story_id">$imageMsg</button>
EOT;
			$buttons[] = <<<EOT
				<button type="button" onclick="stbDeleteStoryImage( this, $story->story_id )">$deleteImageMsg</button>
EOT;
		}
		
		$buttonHtml = implode( '&nbsp;&nbsp;&nbsp;', $buttons );
		
		return <<<EOT
		<table width="100%" border="1" id="story_$story->story_id">
			<tr>
				<td>
					<div class="story">
						$imageHtml
						<div class="story-title">$title</div><br />
						<div class="story-text">$text</div>
					</div>
				</td>
			</tr>
			<tr>
				<td align="center" height="35">
					$buttonHtml
				</td>
			</tr>
		</table>		
EOT;
	}
	
	private function getStateActionButton( $storyId, $action, $messageKey ) {
		$message = htmlspecialchars( wfMsg( $messageKey ) );
		$storyId = Xml::escapeJsString( $storyId );
		$action = Xml::escapeJsString( $action );
		return <<<EOT
				<button type="button" onclick="stbDoStoryAction( this, $storyId, '$action' )">$message</button>
EOT;
	}
}