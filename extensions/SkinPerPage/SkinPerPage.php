<?php
if (!defined('MEDIAWIKI')) die();

/**
 * @addtogroup Extensions
 */

$wgExtensionCredits['parserhook'][] = array(
	'name'           => 'Skin per page',
	'version'        => '1.0',
	'author'         => 'Tim Starling',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:SkinPerPage',
	'descriptionmsg' => 'skinperpage-desc',
);

$wgExtensionMessagesFiles['SkinPerPage'] = dirname( __FILE__ ) . "/SkinPerPage.i18n.php";

$wgParserOutputHooks['SkinPerPage'] = array( 'SkinPerPage', 'outputHook' );

if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
	$wgHooks['ParserFirstCallInit'][] = 'SkinPerPage::setup';
} else {
	$wgExtensionFunctions[] = array( 'SkinPerPage', 'setup' );
}

class SkinPerPage {
	static function setup() {
		global $wgParser;
		$wgParser->setHook( 'skin', array( __CLASS__, 'parserHook' ) );
		return true;
	}

	static function parserHook( $text, $attribs, $parser ) {
		$parser->mOutput->addOutputHook( 'SkinPerPage', trim( $text ) );
		return '';
	}

	static function outputHook( $out, $parserOutput, $skin ) {
		global $wgUser;
		$wgUser->mSkin =& Skin::newFromKey( $skin );
		return true;
	}
}
