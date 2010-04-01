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
			$wgOut->setPageTitle( $title );
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
		
		if ( trim( $title ) != '' ) {
			$conds = array(
				'story_title' => $title
			);
		} else {
			$id = $wgRequest->getIntOrNull( 'id' );
			$conds = array(
				'story_id' => $id
			);		
		}
		
		$dbr = wfGetDB( DB_SLAVE );		
		
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
				'story_title',
				'story_text',
				'story_created',
				'story_modified',
				'story_is_published',
				'story_is_hidden',
			),
			$conds
		);

		if ( $story ) {
			$isEdit = $wgRequest->getVal( 'action' ) == 'edit';
			
			if ( $isEdit && $wgUser->isAllowed( 'storyreview' ) ) {
				$this->showStoryForm( $story );
			} else {
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
		global $wgOut, $wgLang, $egStoryboardScriptPath;
		
		$wgOut->addStyle( $egStoryboardScriptPath . '/storyboard.css' );		
		
		$imageSrc = 'http://upload.wikimedia.org/wikipedia/mediawiki/9/99/SemanticMaps.png'; // TODO: get cropped image here
		
		$title = htmlspecialchars( $story->story_title );
		$text = htmlspecialchars( $story->story_text );		
		
		$wgOut->addHTML( "<img src='$imageSrc' class='story-image'>" );
		$wgOut->addHTML( $text );
		
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
		
	}
	
	/**
	 * Outputs a form to edit the story with. Code based on <storysubmission>.
	 * 
	 * @param $story
	 * 
	 * TODO: add options to publish/unpublish, hide/unhide and delete the story
	 * TODO: confirm with erik that author info should be editable here
	 */	
	private function showStoryForm( $story ) {
		global $wgOut, $wgLang, $wgRequest, $wgUser, $wgJsMimeType, $egStoryboardScriptPath, $egStorysubmissionWidth, $egStoryboardMaxStoryLen, $egStoryboardMinStoryLen;
		
		$wgOut->addStyle( $egStoryboardScriptPath . '/storyboard.css' );
		$wgOut->addScriptFile( $egStoryboardScriptPath . '/storyboard.js' );
		
		$fieldSize = 50;
		
		$width = $egStorysubmissionWidth;
		
		$maxLen = $wgRequest->getVal( 'maxlength' );
		if ( !is_int( $maxLen ) ) $maxLen = $egStoryboardMaxStoryLen;
		
		$minLen = $wgRequest->getVal( 'minlength' );
		if ( !is_int( $minLen ) ) $minLen = $egStoryboardMinStoryLen;
		
		$formBody = "<table width='$width'>";
		
		$formBody .= '<tr><td colspan="2">' . 
			htmlspecialchars( wfMsgExt(
				'storyboard-createdandmodified',
				'parsemag',
				$wgLang->timeanddate( $story->story_created ),
				$wgLang->timeanddate( $story->story_modified )
			) ) . 
			'</td></tr>';		
		
		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-authorname' ) ) .
			'<td>' .
			Html::input( 'name', $story->story_author_name, 'text', array( 'size' => $fieldSize )
			) . '</td></tr>';
		
		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-authorlocation' ) ) .
			'<td>' . Html::input( 'location', $story->story_author_location, 'text', array( 'size' => $fieldSize )
			) . '</td></tr>';
		
		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-authoroccupation' ) ) .
			'<td>' . Html::input( 'occupation', $story->story_author_occupation, 'text', array( 'size' => $fieldSize )
			) . '</td></tr>';

		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-authorcontact' ) ) .
			'<td>' . Html::input( 'contact', $story->story_author_contact, 'text', array( 'size' => $fieldSize )
			) . '</td></tr>';
			
		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-storytitle' ) ) .
			'<td>' . Html::input( 'storytitle', $story->story_title, 'text', array( 'size' => $fieldSize )
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
		
		$returnToQuery = $wgRequest->getVal( 'returnto' );
		if ( $returnToQuery ) $returnToQuery = 'returnto=' . $returnToQuery;
		
		$formBody = Html::rawElement(
			'form',
			array(
				'id' => 'storyform',
				'name' => 'storyform',
				'method' => 'post',
				'action' => $this->getTitle( $story->story_title )->getLocalURL( $returnToQuery ),
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