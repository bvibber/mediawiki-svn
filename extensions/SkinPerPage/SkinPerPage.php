<?php

$oldCredits = $wgExtensionCredits; // save extension credits

$wgExtensionFunctions[] = array( 'SkinPerPage', 'setup' );
$wgHooks['OutputPageParserOutput'][] = 'SkinPerPage::outputHook';

class SkinPerPage {
	static function setup() {
		global $wgParser;
		$wgParser->setHook( 'skin', array( __CLASS__, 'parserHook' ) );
	}

	static function parserHook( $text, $attribs, $parser ) {
		$parser->mOutput->spp_skin = trim( $text );
		return '';
	}
	
	static function outputHook( $out, $parserOutput ) {
		global $wgUser;
		if ( isset( $parserOutput->spp_skin ) ) {
			$wgUser->mSkin =& Skin::newFromKey( $parserOutput->spp_skin );
		}
		return true;
	}
}

// Restore extension credits
$wgExtensionCredits = $oldCredits; 
