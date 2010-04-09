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
	
	/**
	 * Renders the storybsubmission tag.
	 * 
	 * @param $input
	 * @param array $args
	 * @param Parser $parser
	 * @param $frame
	 * 
	 * @return array
	 */	
	public static function render( $input, array $args, Parser $parser, $frame ) {
		wfProfileIn( __METHOD__ );

		global $wgRequest, $wgUser;
		
		$output = self::getFrom( $parser, $args );
		
		wfProfileOut( __METHOD__ );
		
		return array( $output, 'noparse' => true, 'isHTML' => true );
	}
	
	/**
	 * Returns the HTML for a storysubmission form.
	 * 
	 * @param Parser $parser
	 * @param array $args
	 * @return HTML
	 * 
	 * TODO: Fix the validation for the story title
	 * TODO: use HTMLForm
	 */
	private static function getFrom( Parser $parser, array $args ) {
		global $wgUser, $wgStyleVersion, $wgJsMimeType, $egStoryboardScriptPath, $egStorysubmissionWidth, $egStoryboardMaxStoryLen, $egStoryboardMinStoryLen;
		
		// Loading a seperate JS file would be overkill for just these 3 lines, and be bad for performance.
		$parser->getOutput()->addHeadItem(
			<<<EOT
			<link rel="stylesheet" href="$egStoryboardScriptPath/storyboard.css?$wgStyleVersion" />
			<script type="$wgJsMimeType" src="$egStoryboardScriptPath/storyboard.js?$wgStyleVersion"></script>
			<script type="$wgJsMimeType" src="$egStoryboardScriptPath/jquery/jquery.validate.js?$wgStyleVersion"></script>
			<script type="$wgJsMimeType"> /*<![CDATA[*/
			addOnloadHook( function() { 
				document.getElementById( 'storysubmission-button' ).disabled = true;
			} );
			/*]]>*/ </script>			
EOT
		);
		
		$fieldSize = 50;
		
		$width = StoryboardUtils::getDimension( $args, 'width', $egStorysubmissionWidth );
		$maxLen = array_key_exists( 'maxlength', $args ) && is_int( $args['maxlength'] ) ? $args['maxlength'] : $egStoryboardMaxStoryLen;
		$minLen = array_key_exists( 'minlength', $args ) && is_int( $args['minlength'] ) ? $args['minlength'] : $egStoryboardMinStoryLen;
		
		$formBody = "<table width='$width'>";
		
		$defaultName = '';
		$defaultEmail = '';
		
		if ( $wgUser->isLoggedIn() ) {
			$defaultName = $wgUser->getRealName() !== '' ? $wgUser->getRealName() : $wgUser->getName();
			$defaultEmail = $wgUser->getEmail();
		}
		
		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-yourname' ) ) .
			'<td>' .
			Html::input(
				'name', 
				$defaultName,
				'text',
				array(
					'size' => $fieldSize,
					'class' => 'required',
					'maxlength' => 255,
					'minlength' => 2
				)
			) . '</td></tr>';
		
		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-location' ) ) .
			'<td>' .
			Html::input(
				'location',
				'',
				'text',
				array(
					'size' => $fieldSize,
					'maxlength' => 255,
					'minlength' => 2				
				)
			) . '</td></tr>';
		
		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-occupation' ) ) .
			'<td>' . 
			Html::input(
				'occupation',
				'',
				'text',
				array(
					'size' => $fieldSize,
					'maxlength' => 255,
					'minlength' => 4				
				)
			) . '</td></tr>';

		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-email' ) ) .
			'<td>' .
			Html::input(
				'email',
				$defaultEmail,
				'text',
				array(
					'size' => $fieldSize,
					'class' => 'required email',
					'size' => $fieldSize,
					'maxlength' => 255				
				)
			) . '</td></tr>';
			
		$formBody .= '<tr>' .
			Html::element( 'td', array( 'width' => '100%' ), wfMsg( 'storyboard-storytitle' ) ) .
			'<td>' . 
			Html::input(
				'storytitle',
				'',
				'text',
				array(
					'size' => $fieldSize,
					'class' => 'required',
					'maxlength' => 255,
					'minlength' => 2
				)
			) . '</td></tr>';
		
		$formBody .= '<tr><td colspan="2">' .
			wfMsg( 'storyboard-story' ) .
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
					'class' => 'required',
					'onkeyup' => "stbValidateStory( this, $minLen, $maxLen, 'storysubmission-charlimitinfo', 'storysubmission-button' )",
				),
				null
			) .
			'</td></tr>';
		
		// TODO: add upload functionality

		$formBody .= '<tr><td colspan="2"><input type="checkbox" id="storyboard-agreement" />&nbsp;' .
			$parser->recursiveTagParse( htmlspecialchars( wfMsg( 'storyboard-agreement' ) ) ) .
			'</td></tr>';
			
		$formBody .= '<tr><td colspan="2">' .
			Html::input( '', wfMsg( 'htmlform-submit' ), 'submit', array( 'id' => 'storysubmission-button' ) ) .
			'</td></tr>';
			
		$formBody .= '</table>';
		
		$formBody .= Html::hidden( 'wpStoryEditToken', $wgUser->editToken() );
		
		if ( !array_key_exists( 'language', $args )
			|| !array_key_exists( $args['language'], Language::getLanguageNames() ) ) {
			$args['language'] = $wgContLanguageCode;
		}

		$formBody .= Html::hidden( 'lang', $args['language'] );
		
		$submissionUrl = SpecialPage::getTitleFor( 'StorySubmission' )->getFullURL();
		
		return Html::rawElement(
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
	}
	

	
}