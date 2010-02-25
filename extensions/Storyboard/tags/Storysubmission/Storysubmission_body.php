<?php

/**
 * File holding the rendering function for the Storysubmission tag.
 *
 * @file Storysubmission_body.php
 * @ingroup Storyboard
 *
 * @author Jeroen De Dauw
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
		
		if ($wgRequest->wasPosted()) {
			$output = $this->doSubmissionAndGetResult();
		} else {
			$output = $this->getFrom();
		}
		
		return $output;
		
		wfProfileOut( __METHOD__ );
	}
	
	private function getFrom() {
		return <<<EOT
<form name="storysubmission" action="#" method="get">

</form>
EOT;
	}
	
	private function doSubmissionAndGetResult() {
		
	}
	
}