<?php
/**
 * QrCode.php
 * Written by David Raison
 * @license: LGPL (GNU Lesser General Public License) http://www.gnu.org/licenses/lgpl.html
 *
 * @file QrCode.php
 *
 * @author David Raison
 *
 * Uses the phpqrcode library written by Dominik Dzienia (C) 2010,
 * which is, in turn, based on C libqrencode library
 * Copyright (C) 2006-2010 by Kentaro Fukuchi
 * http://megaui.net/fukuchi/works/qrencode/index.en.html
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This is a MediaWiki extension, and must be run from within MediaWiki.' );
}

$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'QrCode',
	'version' => '0.02',
	'author' => array( 'David Raison' ), 
	'url' => 'http://www.mediawiki.org/wiki/Extension:QrCode',
	'descriptionmsg' => 'qrcode-desc'
);

$wgAutoloadClasses['QRcode'] = dirname(__FILE__) . '/phpqrcode/qrlib.php';
$wgExtensionMessagesFiles['QrCode'] = dirname(__FILE__) .'/QrCode.i18n.php';

$wgHooks['LanguageGetMagic'][] = 'wfQrCodeLanguageGetMagic';
$wgHooks['ParserFirstCallInit'][] = 'efQrcodeRegisterFunction';

function efQrcodeRegisterFunction( Parser &$parser ) {
	$parser->setFunctionHook( 'qrcode', array( new MWQrCode(), 'showCode' ) );
	return true;
}

function wfQrCodeLanguageGetMagic( &$magicWords, $langCode = 'en' ) {
	$magicWords['qrcode'] = array( 0, 'qrcode' );
	return true;
}

// Defaults (overwritten by LocalSettings.php and possibly also by the function's arguments)
$wgQrCodeECC = 'L';	// L,M,Q,H
$wgQrCodeSize = 4;	// pixel size of black squares
$wgQrCodeBoundary = 2;	// boundary around square
$wgQrCodeBot = 'QrCodeBot';
	
class MWQrCode {

	private $_dstFileName;	// what the file will be named?
	private $_label;		// What will the qrcode contain?
	private $_ecc;			// error correction
	private $_size;			// qrcode size
	private $_boundary;		// qrcode margin

	public function __construct(){
		global $wgQrCodeECC, $wgQrCodeSize, $wgQrCodeBoundary;
		$this->_ecc = $wgQrCodeECC;
		$this->_size = $wgQrCodeSize;
		$this->_boundary = $wgQrCodeBoundary;
	}

	/**
	 * If we don't have the code on file, generate, then publish it
	 * @return wikitext for image display
	 */
	public function showCode(){
		global $wgTitle;
		$this->_label = $wgTitle->getFullURL();

		$params = func_get_args();
		$parser = array_shift($params);

		foreach( $params as $pair ) {
			$rpms = explode( '=', $pair );
			if( $rpms[0] == 'ecc' ) $this->_ecc = $rpms[1];
			if( $rpms[0] == 'size' ) $this->_size = $rpms[1];
			if( $rpms[0] == 'boundary' ) $this->_boundary = $rpms[1];
			if( $rpms[0] == 'label' ) $this->_label = $rpms[1];
		}

		// Do we have a label?
		$append = ( $this->_label != $wgTitle->getFullURL() ) ? '-'.$this->_label : '';

		// Use this page's title as part of the filename
		$this->_dstFileName = 'QR-'.$wgTitle->getDBKey().$append.'.png';

		$file = wfFindFile( $this->_dstFileName );	// Shortcut for RepoGroup::singleton()->findFile() 
		if(  $file && $file->isVisible() ){
			$ft = $file->getTitle();
			return $this->_displayImage( $ft );
		} else {
			$this->_generate();
		}
	}
	
	/**
	 * This only creates the wikitext to display an image.
	 * @return wikitext for image display
	 */
	private function _displayImage( $fileTitle ){
		// a tag hook would use $parser->makeImage($ft,$options);
		return '[['.$fileTitle->getFullText().']]';
	}

	/**
	 * Generate the qrcode using the phpqrcode library
	 * @return output of the _publish method
	 */
	private function _generate(){
		global $wgTmpDirectory, $wgQrCodeECC, $wgQrCodeSize, $wgQrCodeBoundary;
		$tmpName = tempnam( $wgTmpDirectory, 'qrcode' );

		QRcode::png( $this->_label, $tmpName, $this->_ecc, $this->_size, $this->_boundary );
		wfDebug( "Generated qrcode file $tmpName with ecc $wgQrCodeECC, size $wgQrCodeSize and boundary $wgQrCodeBoundary.\n" );

		return $this->_publish( $tmpName );
	}
	
	/**
	 * Create or select a bot user to attribute the code generation to
	 * @return user object
	 * */
	private function _getBot(){
		global $wgQrCodeBot;
	 
		// there doesn't seem to be a decent method for checking if a user already exists...
		$bot = User::createNew( $wgQrCodeBot );
		if( $bot != null ){
			//$bot->setPassword( '' );        // doc says empty password disables, but it triggers an exception
		} else {
			$bot = User::newFromName( $wgQrCodeBot );
		}   
		if( !$bot->isBot() )
			$bot->addGroup( 'bot' );

		return $bot;
	 }

	/**
	 * Handle mediawiki file repositories
	 * @param $tmpName, the file's temporary name
	 * @return boolean value for success or failure of file "upload"
	 */
	private function _publish( $tmpName ){
		global $wgOut;
		
		$ft = Title::makeTitleSafe( NS_FILE, $this->_dstFileName );
		$localfile = wfLocalFile( $ft );		// Get an object referring to a locally registered file. 
		$saveName = $localfile->getName();
		$pageText = 'QrCode '.$saveName.', generated on '.date( "r" ).' by the QrCode Extension.';
		$status = $localfile->upload( $tmpName, $this->_label, $pageText, File::DELETE_SOURCE, false, false, $this->_getBot() );

		if( !$status->isGood() ){
			$wgOut->addWikiText( $status->getWikiText() );
			return false;
		} else {
			// now that we generated the file, let's display it
			$this->_displayImage( $ft );
			return true;
		}
	}
	
}