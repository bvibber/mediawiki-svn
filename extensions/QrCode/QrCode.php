<?php
/**
 * QrCode.php
 * Written by David Raison
 * @license: LGPL (GNU Lesser General Public License) http://www.gnu.org/licenses/lgpl.html
 *
 * @file QrCode.php
 * @ingroup QrCode
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
	'version' => '0.08',
	'author' => array( 'David Raison' ), 
	'url' => 'http://www.mediawiki.org/wiki/Extension:QrCode',
	'descriptionmsg' => 'qrcode-desc'
);

$wgAutoloadClasses['QRcode'] = dirname(__FILE__) . '/phpqrcode/qrlib.php';
$wgExtensionMessagesFiles['QrCode'] = dirname(__FILE__) .'/QrCode.i18n.php';

$wgHooks['LanguageGetMagic'][] = 'wfQrCodeLanguageGetMagic';
$wgHooks['ParserFirstCallInit'][] = 'efQrcodeRegisterFunction';
$wgJobClasses['uploadQrCode'] = 'UploadQrCodeJob';

function efQrcodeRegisterFunction( Parser &$parser ) {
	$parser->setFunctionHook( 'qrcode', 'newQrCode' );
	return true;
}

function wfQrCodeLanguageGetMagic( &$magicWords, $langCode = 'en' ) {
	$magicWords['qrcode'] = array( 0, 'qrcode' );
	return true;
}

// Defaults (overwritten by LocalSettings.php and possibly also by the function call's arguments)
$wgQrCodeECC = 'L';	// L,M,Q,H
$wgQrCodeSize = 4;	// pixel size of black squares
$wgQrCodeBoundary = 2;	// margin around qrcode
$wgQrCodeBot = 'QrCodeBot'; // Name of the 'uploading' user/bot

/**
 * Create a new QrCode instance every time we need one,
 * in order to prevent data corruption and to adhere more strictly
 * to OOP patterns.
 */
function newQrCode() {

	$params = func_get_args();
	$parser = array_shift($params);	// we'll need the parser later

	// Handling "Undefined variable" notices
	$margin = $ecc = $size = $boundary = $label = false;

	foreach( $params as $pair ) {
		$rpms = explode( '=', $pair );
		if( $rpms[0] == 'ecc' ) $ecc = $rpms[1];
		if( $rpms[0] == 'size' ) $size = $rpms[1];
		if( $rpms[0] == 'boundary' ) $margin = $rpms[1];
		if( $rpms[0] == 'label' ) $label = $rpms[1];
	}

	$newQrCode = new MWQrCode( $parser, $ecc, $size, $margin );
	return $newQrCode->showCode( $label );
}
	
/**
 * Class that handles QrCode generation and MW file handling.
 *
 */
class MWQrCode {

	private $_parser;	// simply a link to the parser object
	private $_title;	// the current page's title object
	private $_label;	// contents of the qrcode
	private $_dstFileName;	// what the file will be named?
	private $_uploadComment;	// comment to be added to the upload
	private $_ecc;			// error correction
	private $_size;			// qrcode size
	private $_margin;		// qrcode margin

	/**
	 * Set qrcode properties
	 */
	public function __construct( $parser, $ecc = false, $size = false, $margin = false ) {
		global $wgQrCodeECC, $wgQrCodeSize, $wgQrCodeBoundary, $wgQrCodeBot;
		$this->_parser = $parser;
		$this->_title = $parser->getTitle();
		$this->_ecc = ( $ecc ) ? $ecc : $wgQrCodeECC;
		$this->_size = ( $size ) ? $size : $wgQrCodeSize;
		$this->_margin = ( $margin ) ? $margin : $wgQrCodeBoundary;
	}

	/**
	 * Look for the requested qrcode file. If we don't have the code on file,
	 * first generate then publish it.
	 */
	public function showCode( $label = false ){
		// Check for a provided label and use the page URL as default.
		// Also strip all non-alphanumeric characters
		if ( $label ) {
			$this->_label = preg_replace("/[^0-9a-zA-Z_]+/", "", $label);
			$this->_uploadComment = $label;	// should we sanitize this?
		} else {
			$this->_label = $this->_title->getFullURL();
			$this->_uploadComment = 'Encoded URL for '.$this->_title->getFullText();
		}

		// Use this page's title as part of the filename (Also regenerates qrcodes when the label changes).
		$this->_dstFileName = 'QR-'.md5($this->_label).'.png';
		$file = wfFindFile( $this->_dstFileName );	// Shortcut for RepoGroup::singleton()->findFile() 

		if( $file && $file->isVisible() ){
			return $this->_displayImage( $file );
		} else {
			return $this->_generate();
		}
	}
	
	/**
	 * This only creates the wikitext to display an image.
	 * @return wikitext for image display
	 */
	private function _displayImage( $file ){
		$ft = $file->getTitle();
		return '[['.$ft->getFullText().']]';
	}

	/**
	 * Generate the qrcode using the phpqrcode library
	 * Then queue the generation of the image in the jobqueue.
	 * @return boolean result of job insertion.
	 */
	private function _generate(){
		global $wgTmpDirectory;
		$tmpName = tempnam( $wgTmpDirectory, 'qrcode' );

		QRcode::png( $this->_label, $tmpName, $this->_ecc, $this->_size, $this->_margin );
		wfDebug( "QrCode::_generate: Generated qrcode file $tmpName with ecc ".$this->_ecc
			.", ".$this->_size." and boundary ".$this->_margin.".\n" );

		$jobParams = array( 'tmpName' => $tmpName, 'dstName' => $this->_dstFileName, 'comment' => $this->_uploadComment );
		$job = new UploadQrCodeJob( $this->_title, $jobParams );
		if( $job->insert() ) {
			return true;
		}	
	}
}

class UploadQrCodeJob extends Job {


	public function __construct( $title, $params, $id = 0 ) {
		$this->_dstFileName = $params['dstName'];
		$this->_tmpName = $params['tmpName'];
		$this->_uploadComment = $params['comment'];
		$this->_title = $title;
		parent::__construct( 'uploadQrCode', $title, $params, $id );
	}

	/**
	 * Handle the mediawiki file upload process
	 * @return boolean status of file "upload"
	 */
	public function run() {

		$mUpload = new UploadFromFile();
		$mUpload->initialize( $this->_dstFileName, $this->_tmpName, null );	// we don't know the filesize, how could we?

		$pageText = 'QrCode '.$this->_dstFileName.', generated on '.date( "r" )
                        .' by the QrCode Extension for page [['.$this->_title->getFullText().']].';

		wfDebug( 'UploadQrCodeJob::run: Uploading qrcode, c: '.$this->_uploadComment . ' t: ' . $pageText."\n" );
		$status = $mUpload->performUpload( $this->_uploadComment, $pageText, false, $this->_getBot() );
		
		if ( $status->isGood() ) {
			return true;
		} else {
			$wgOut->addWikiText( $status->getWikiText() );
			return false;
		}
	}

	/**
	 * Create or select a bot user to attribute the code generation to
	 * @return user object
	 * @note there doesn't seem to be a decent method for checking if a user already exists
	 * */
	private function _getBot(){
		global $wgQrCodeBot;

		$bot = User::createNew( $wgQrCodeBot );
		if( $bot != null ){
			wfDebug( 'UploadQrCode::_getBot: Created new user '.$wgQrCodeBot."\n" );
			//$bot->setPassword( '' );   // doc says empty password disables, but this triggers an exception
		} else {
			$bot = User::newFromName( $wgQrCodeBot );
		}   

		if( !$bot->isAllowed( 'bot' ) ) {	// User::isBot() has been deprecated
			$bot->addGroup( 'bot' );
			wfDebug( 'UploadQrCode::_getBot: Added user '.$wgQrCodeBot.' to the Bot group'."\n" );
		}

		return $bot;
	 }
	
}
