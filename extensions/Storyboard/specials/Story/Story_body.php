<?php
/**
 * File holding the SpecialStory class defining a special page to view a specific story for permalink purpouses.
 *
 * @file Story_body.php
 * @ingroup Storyboard
 * @ingroup SpecialPage
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class SpecialStory extends IncludableSpecialPage {

	public function __construct() {
		parent::__construct( 'Story' );
	}

	public function execute( $title ) {
		wfProfileIn( __METHOD__ );
		global $wgOut, $wgRequest, $wgUser;
		
		$title = str_replace( '_', ' ', $title );
		
		if ( $wgRequest->wasPosted() && $wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ) ) ) {
			if ( $wgUser->isAllowed( 'storyreview' ) ) {
				// If the user is allowed to actually modify the story, save it.
				$this->saveStory();
				
				// Redirect the user when the redirect parameter is set.
				if ( $wgRequest->getVal( 'returnto' ) ) {
		 			$titleObj = Title::newFromText( $wgRequest->getVal( 'returnto' ) );
					$wgOut->redirect( $titleObj->getFullURL() );					
				}
			} else {
				// If the user is not allowed to modify stories, show an error.
				$wgOut->addWikiMsg( 'storyboard-cantedit' );
			}
		}
		
		if ( trim( $title ) != '' || $wgRequest->getIntOrNull( 'id' ) ) {
			$this->queryAndShowStory( $title );
		} else {
			$wgOut->setPageTitle( wfMsg( 'storyboard-viewstories' ) );
			$wgOut->addWikiMsg( 'storyboard-nostorytitle' );	
		}
		
		wfProfileOut( __METHOD__ );
	}
	
	/**
	 * Queries for the requested story and shows it in either display or edit mode when it's found.
	 */
	private function queryAndShowStory( $title ) {
		global $wgOut, $wgRequest, $wgUser;
		
		$hasTitle = trim( $title ) != '';
		
		$dbr = wfGetDB( DB_SLAVE );		
		
		// If an id is provided, query for the story title and redirect to have a nicer url,
		// or continue with function execution to display an error that there is no such story.
		if ( !$hasTitle ) {
			$story = $dbr->selectRow(
				'storyboard',
				array(
					'story_title',
				),
				array( 'story_id' => $wgRequest->getIntOrNull( 'id' ) )
			);	
			if ( $story ) {
				$wgOut->redirect( $this->getTitle( $story->story_title )->getFullURL() );
				return;
			}
		} else {
			// If a title is provided, query the story info.
			$story = $dbr->selectRow(
				'storyboard',
				array(
					'story_id',
					'story_author_id',
					'story_author_name',
					'story_author_location',
					'story_author_occupation',
					'story_author_contact',
					'story_author_image',
					'story_image_hidden',
					'story_title',
					'story_text',
					'story_created',
					'story_modified',
					'story_is_published',
					'story_is_hidden',
				),
				array( 'story_title' => $title )
			);
		}

		// If there is such a story, display it, or the edit form. 
		// If there isn't, display an error message.
		if ( $story ) {
			$isEdit = $wgRequest->getVal( 'action' ) == 'edit';
			
			if ( $isEdit && $wgUser->isAllowed( 'storyreview' ) ) {
				$this->showStoryForm( $story );
			} else {
				$wgOut->setPageTitle( $story->story_title );
				
				if ( $isEdit ) {
					$wgOut->addWikiMsg( 'storyboard-cantedit' );
				}
				
				if ( $story->story_is_published == 1 ) {
					$this->showStory( $story );
				}
				elseif ( !$isEdit ) {
					$wgOut->addWikiMsg( 'storyboard-unpublished' );
					
					if ( $wgUser->isAllowed( 'storyreview' ) ) {
						global $wgTitle;
						$wgOut->addHTML( // TODO: this still isn't working properly
							wfMsgHtml(
								'storyboard-canedit',
								$wgUser->getSkin()->link(
									$wgTitle,
									strtolower( wfMsg( 'edit' ) )
								)
							)
						);
					}
				}
			}
		}
		else {
			$wgOut->addWikiMsg( 'storyboard-nosuchstory' );
		}		
	}
	
	/**
	 * Ouputs the story in regular display mode.
	 * 
	 * @param $story
	 * 
	 * TODO: Improve layout, add social sharing stuff, add story meta data and show edit stuff for people with stroyreview permission.
	 */
	private function showStory( $story ) {
		global $wgOut, $wgLang, $wgUser, $egStoryboardScriptPath;
		
		$wgOut->addStyle( $egStoryboardScriptPath . '/storyboard.css' );		

		if ( $story->story_author_image != '' && $story->story_image_hidden != 1 ) {
			$story->story_author_image = htmlspecialchars( $story->story_author_image );
			$wgOut->addHTML( "<img src='$story->story_author_image' class='story-image'>" );
		}
		
		$wgOut->addWikiText( $story->story_text  );
		
		// If the user that submitted the story was logged in, create a link to his/her user page.
		if ( $story->story_author_id ) {
			$user = User::newFromId( $story->story_author_id );
			$userPage = $user->getUserPage();
			$story->story_author_name = '[[' . $userPage->getFullText() . '|' . $story->story_author_name . ']]';
		}
		
		$wgOut->addWikiText( 
			htmlspecialchars( wfMsgExt(
				'storyboard-submittedbyon',
				'parsemag',
				$story->story_author_name,
				$wgLang->timeanddate( $story->story_created )
			) )
		);
		
		// FIXME: this is a temporary solution untill the SkinTemplateNavigation on special pages issue is fixed.
		if ( $wgUser->isAllowed( 'storyreview' ) ) {
			$editMsg = htmlspecialchars( wfMsg( 'edit' ) );
			$editUrl = $this->getTitle( $story->story_title )->getLocalURL( 'action=edit' );
			$wgOut->addHtml(
				"<button type='button' onclick=\"window.location='$editUrl'\">$editMsg</button>"
			);
		}
		
	}
	
	/**
	 * Outputs a form to edit the story with. Code based on <storysubmission>.
	 * 
	 * @param $story
	 * 
	 * TODO: Add live validation for all fields, esp checking if a story title doesn't exist yet.
	 */	
	private function showStoryForm( $story ) {
		global $wgOut, $wgLang, $wgRequest, $wgUser, $wgJsMimeType, $egStoryboardScriptPath, $egStorysubmissionWidth, $egStoryboardMaxStoryLen, $egStoryboardMinStoryLen;
		
		$wgOut->setPageTitle( $story->story_title );
		
		$wgOut->addStyle( $egStoryboardScriptPath . '/storyboard.css' );
		$wgOut->includeJQuery();
		$wgOut->addScriptFile( $egStoryboardScriptPath . '/jquery/jquery.validate.js' );
		$wgOut->addScriptFile( $egStoryboardScriptPath . '/storyboard.js' );
		
		$fieldSize = 50;
		
		$width = $egStorysubmissionWidth;
		
		$maxLen = $wgRequest->getVal( 'maxlength' );
		if ( !is_int( $maxLen ) ) $maxLen = $egStoryboardMaxStoryLen;
		
		$minLen = $wgRequest->getVal( 'minlength' );
		if ( !is_int( $minLen ) ) $minLen = $egStoryboardMinStoryLen;
		
		$formBody = "<table width='$width'>";
			
		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-authorname' ) ) .
			'<td>' .
			Html::input(
				'name',
				$story->story_author_name,
				'text',
				array(
					'size' => $fieldSize,
					'class' => 'required',
					'minlength' => 2
				)
			) . '</td></tr>';
		
		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-authorlocation' ) ) .
			'<td>' . Html::input(
				'location',
				$story->story_author_location,
				'text',
				array(
					'size' => $fieldSize,
					'maxlength' => 255,
					'minlength' => 2
				)
			) . '</td></tr>';
		
		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-authoroccupation' ) ) .
			'<td>' . Html::input(
				'occupation',
				$story->story_author_occupation,
				'text',
				array(
					'size' => $fieldSize,
					'maxlength' => 255,
					'minlength' => 4
				)
			) . '</td></tr>';

		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-authorcontact' ) ) .
			'<td>' . Html::input(
				'contact',
				$story->story_author_contact,
				'text',
				array(
					'size' => $fieldSize,
					'maxlength' => 255,
					'minlength' => 7
				)
			) . '</td></tr>';
			
		$formBody .= '<tr>' . 
			'<td width="100%"><label for="storytitle">' . 
				htmlspecialchars( wfMsg( 'storyboard-storytitle' ) ) . 
			'</label></td><td>' . 
			Html::input(
				'storytitle',
				$story->story_title,
				'text',
				array(
					'size' => $fieldSize,
					'maxlength' => 255,
					'id' => 'storytitle',
					'class' => 'storytitle'
				)
			) . '</td></tr>';
		
		$formBody .= '<tr><td colspan="2">' .
			wfMsg( 'storyboard-thestory' ) .
			Html::element(
				'div',
				array( 'class' => 'storysubmission-charcount', 'id' => 'storysubmission-charlimitinfo' ),
				wfMsgExt( 'storyboard-charsneeded', 'parsemag', $minLen )
			) .
			'<br />' .
			Html::element(
				'textarea',
				array(
					'id' => 'storytext',
					'name' => 'storytext',
					'rows' => 7,
					'onkeyup' => "stbValidateStory( this, $minLen, $maxLen, 'storysubmission-charlimitinfo', 'storysubmission-button' )",
				),
				$story->story_text
			) .
			'</td></tr>';
		
		$checked = $story->story_is_published ? 'checked ' : '';
		$formBody .= '<tr><td colspan="2"><input type="checkbox" name="published" ' . $checked . '/>&nbsp;' .
			htmlspecialchars( wfMsg( 'storyboard-ispublished' ) ) .
			'</td></tr>';

		$checked = $story->story_is_hidden ? 'checked ' : '';
		$formBody .= '<tr><td colspan="2"><input type="checkbox" name="hidden" ' . $checked . '/>&nbsp;' .
			htmlspecialchars( wfMsg( 'storyboard-ishidden' ) ) .
			'</td></tr>';			
			
		$formBody .= '<tr><td colspan="2">' .
			Html::input( '', wfMsg( 'htmlform-submit' ), 'submit', array( 'id' => 'storysubmission-button' ) ) .
			'</td></tr>';
			
		$formBody .= '</table>';
		
		$formBody .= Html::hidden( 'wpEditToken', $wgUser->editToken() );
		$formBody .= Html::hidden( 'storyId', $story->story_id );
		
		$formBody = '<fieldset><legend>' . 
			htmlspecialchars( wfMsgExt(
				'storyboard-createdandmodified',
				'parsemag',
				$wgLang->timeanddate( $story->story_created ),
				$wgLang->timeanddate( $story->story_modified )
			) ) . 
		'</legend>' . $formBody . '</fieldset>';
		
		$query = "id=$story->story_id";
			
		$returnTo = $wgRequest->getVal( 'returnto' );
		if ( $returnTo ) $query .= '&returnto=' . $returnTo;
		
		$formBody = Html::rawElement(
			'form',
			array(
				'id' => 'storyform',
				'name' => 'storyform',
				'method' => 'post',
				'action' => $this->getTitle()->getLocalURL( $query ),
			),
			$formBody
		);
		
		$wgOut->addHTML( $formBody );
		
		$wgOut->addInlineScript( <<<EOT
addOnloadHook(
	function() {	
		stbValidateStory( document.getElementById('storytext'), $minLen, $maxLen, 'storysubmission-charlimitinfo', 'storysubmission-button' )
	}
);
jQuery( "#storyform" ).validate({
	rules: {
		storytitle: {
			required: true,
			minlength: 3,
			maxlength: 255,
			remote: wgScriptPath + '/api.php?action=storyexists&storyname=' + '' // TODO
		}
	},
	messages: {
		storytitle: "The sort title needs to be between 3 and 255 characters long and may not exist yet" // TODO: i18n	
	},
	success: function( label ) {
		label.addClass( "valid" ).text( "Valid story title!" )
	},
	submitHandler: function() {
		
	},		
	onkeyup: false
});

EOT
		);
	}
	
	/**
	 * Saves the story after a story edit form has been submitted.
	 */
	private function saveStory() {
		global $wgOut, $wgRequest, $wgUser;
		
		$dbw = wfGetDB( DB_MASTER );
		
		$dbw->update(
			'storyboard',
			array(
				'story_author_name' => $wgRequest->getText( 'name' ),
				'story_author_location' => $wgRequest->getText( 'location' ),
				'story_author_occupation' => $wgRequest->getText( 'occupation' ),
				'story_author_contact' => $wgRequest->getText( 'contact' ),
				'story_title' => $wgRequest->getText( 'storytitle' ),
				'story_text' => $wgRequest->getText( 'storytext' ),
				'story_modified' => $dbw->timestamp( time() ),
				'story_is_published' => $wgRequest->getCheck( 'published' ) ? 1 : 0,
				'story_is_hidden' => $wgRequest->getCheck( 'hidden' ) ? 1 : 0,
			),
			array(
				'story_id' => $wgRequest->getText( 'storyId' ),
			)
		);
	}
}