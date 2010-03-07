<?php

/**
 * File holding the rendering function for the Storysubmission tag.
 *
 * @file Storysubmission_body.php
 * @ingroup Storyboard
 *
 * @author Jeroen De Dauw
 * 
 * Notice: This class is designed with the idea that only one storysubmission form is placed
 * on a single page, and might not work properly when multiple are placed on a page.
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class TagStorysubmission {
	
	// http://www.mediawiki.org/wiki/Manual:Forms
	// http://www.mediawiki.org/wiki/Manual:Hooks/UnknownAction
	public static function render( $input, $args, $parser, $frame ) {
		wfProfileIn( __METHOD__ );

		global $wgRequest;
		
		var_dump($wgRequest->wasPosted()); die();
		
		if ( $wgRequest->wasPosted() ) {
			$output = self::doSubmissionAndGetResult();
		} else {
			$output = self::getFrom( $parser, $args );
		}
		
		return $output;
		
		wfProfileOut( __METHOD__ );
	}
	
	private static function getFrom( $parser, $args ) {
		global $wgOut, $wgJsMimeType, $wgSc, $egStoryboardScriptPath, $egStorysubmissionWidth, $egStoryboardMaxStoryLen, $egStoryboardMinStoryLen;
		
		$wgOut->addStyle( $egStoryboardScriptPath . '/tags/Storysubmission/storysubmission.css' );
		$wgOut->addScriptFile( $egStoryboardScriptPath . '/tags/Storysubmission/storysubmission.js' );
		
		$fieldSize = 50;
		
		$width = StoryboardUtils::getDimension( $args, 'width', $egStorysubmissionWidth );
		$maxLen = array_key_exists('maxlength', $args) && is_numeric($args['maxlength']) ? $args['maxlength'] : $egStoryboardMaxStoryLen;
		$minLen = array_key_exists('minlength', $args) && is_numeric($args['minlength']) ? $args['minlength'] : $egStoryboardMinStoryLen;
		
		$submissionUrl = $parser->getTitle()->getLocalURL( '' ); // TODO: fix parameters
		
		$formBody = "<table width='$width'>";
		
		$formBody .= '<tr>' .
			Html::element( 'td', array('width' => '100%'), wfMsg( 'storyboard-yourname' ) ) .
			'<td>' . 
			Html::input('name' ,'', 'text', array( 'size' => $fieldSize )
			) . '</td></tr>';
		
		$formBody .= '<tr>' .
			Html::element( 'td', array('width' => '100%'), wfMsg( 'storyboard-location' ) ) .
			'<td>' . Html::input('location' ,'', 'text', array( 'size' => $fieldSize )
			) . '</td></tr>';
		
		$formBody .= '<tr>' .
			Html::element( 'td', array('width' => '100%'), wfMsg( 'storyboard-occupation' ) ) .
			'<td>' . Html::input('occupation' ,'', 'text', array( 'size' => $fieldSize )
			) . '</td></tr>';

		$formBody .= '<tr>' .
			Html::element( 'td', array('width' => '100%'), wfMsg( 'storyboard-contact' ) ) .
			'<td>' . Html::input('contact' ,'', 'text', array( 'size' => $fieldSize )
			) . '</td></tr>';
		
		$formBody .= '<tr><td colspan="2">' .
			wfMsg( 'storyboard-story' ) .
			Html::element(
				'div',
				array('class' => 'storysubmission-charcount', 'id' => 'storysubmission-charlimitinfo'),
				wfMsgExt( 'storyboard-charsneeded', 'parsemag', $minLen )
			) .
			'<br />' . 
			Html::element(
				'textarea',
				array(
					'id' => 'story',
					'rows' => 7,
					'onkeyup' => "stbValidateStory( this, $minLen, $maxLen, 'storysubmission-charlimitinfo', 'storysubmission-button' )",
					// TODO: make disabled when JS is enabled
				),
				null
			) .
			'</td></tr>';
		
		// TODO: add upload functionality
		
		$formBody .= '<tr><td colspan="2"><input type="checkbox" id="storyboard-agreement" />&nbsp;' .
			htmlspecialchars( wfMsg( 'storyboard-agreement' ) ) .
			'</td></tr>';
			
		$formBody .= '<tr><td colspan="2">' . 
			Html::input( '', wfMsg( 'htmlform-submit' ), 'submit', array('id' => 'storysubmission-button') ) .
			'</td></tr>';
			
		$formBody .= '</table>';
		
		$formHtml = Html::rawElement(
			'form',
			array(
				'id' => 'storyform',
				'name' => 'storyform',
				'method' => 'post',
				'action' => $submissionUrl,
				'onsubmit' => 'return stbValidateSubmission( "storyboard-agreement" );'
			),
			$formBody
		);
		
		// Disable the submission button when JS is enabled.
		$formJs = "<script type='$wgJsMimeType'>/*<![CDATA[*/ document.getElementById( 'storysubmission-button' ).disabled = true; /*]]>*/</script>";
		
		return $formHtml . $formJs;
	}
	
	private static function doSubmissionAndGetResult() {
		
	}
	
}