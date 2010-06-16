<?php
/**
 * To get this working you must
 * - set a valid path to PEAR
 * - check upload size in php.ini: Multipage.tiff needs at least 3M
 * - Upload the image caspian.tif without PagedTiffHandler being active
 *   Caution: you need to allow tiff for upload:
 *   $wgFileExtensions[] = 'tiff';
 *   $wgFileExtensions[] = 'tif';
 * - Upload multipage.tiff when PagedTiffHandler is active
 */

if ( getenv( 'MW_INSTALL_PATH' ) ) {
	$IP = getenv( 'MW_INSTALL_PATH' );
} else {
	$IP = dirname( __FILE__ ) . '/../../..';
}
require_once( "$IP/maintenance/commandLine.inc" );

// requires PHPUnit 3.4
require_once 'PHPUnit/Framework.php';

error_reporting( E_ALL );

class PagedTiffHandlerTest extends PHPUnit_Framework_TestCase {

	private $handler;
	private $image;
	private $preCheckError;

	function setUp( $autoUpload = false ) {
		global $wgTitle;
		$wgTitle = Title::newFromText( 'PagedTiffHandler_UnitTest' );
		
		$this->handler = new PagedTiffHandler();
		if ( !file_exists( dirname( __FILE__ ) . '/testImages' ) ) {
			echo "testImages directory cannot be found.\n";
			$this->preCheckError = true;
		}
		if ( !file_exists( dirname( __FILE__ ) . '/testImages/caspian.tif' ) ) {
			echo "testImages/caspian.tif cannot be found.\n";
			$this->preCheckError = true;
		}
		if ( !file_exists( dirname( __FILE__ ) . '/testImages/multipage.tiff' ) ) {
			echo "testImages/Multipage.tif cannot be found.\n";
			$this->preCheckError = true;
		}
		$multipageTitle = Title::newFromText( 'Image:Multipage.tiff' );
		$this->image = wfFindFile( $multipageTitle );
		if ( !$this->image ) {
			if ( $autoUpload ) {
				echo "testImages/multipage.tiff seems not to be present in the wiki. Trying to upload.\n";
				$this->image = wfLocalFile( $multipageTitle );
				$archive = $this->image->publish( dirname(__FILE__) . '/testImages/multipage.tiff' );
				$this->image->recordUpload( $archive->value, 'Test file used for PagedTiffHandler unit test', 'No license' );
				if( WikiError::isError( $archive ) || !$archive->isGood() ) {
					echo "Something went wrong. Please manually upload testImages/multipage.tiff\n";
					$this->preCheckError = true;
				} else {
					echo "Upload was successful.\n";
				}
			} else {
				echo "Please upload the image testImages/multipage.tiff into the wiki\n";
				$this->preCheckError = true;
			}
			
		}

		$this->path = dirname(__FILE__) . '/testImages/multipage.tiff';
	}
	
	function runTest() {
		// do not execute test if preconditions check returned false;
		if ( $this->preCheckError ) {
			return false;
		}
		// ---- Parameter handling and lossy parameter
		// validateParam
		$this->assertTrue( $this->handler->validateParam( 'lossy', '0' ) );
		$this->assertTrue( $this->handler->validateParam( 'lossy', '1' ) );
		$this->assertTrue( $this->handler->validateParam( 'lossy', 'false' ) );
		$this->assertTrue( $this->handler->validateParam( 'lossy', 'true' ) );
		$this->assertTrue( $this->handler->validateParam( 'lossy', 'lossy' ) );
		$this->assertTrue( $this->handler->validateParam( 'lossy', 'lossless' ) );
		// normaliseParams
		// here, boxfit behavior is tested
		$params = array( 'width' => '100', 'height' => '100', 'page' => '4' );
		$this->handler->normaliseParams( $this->image, $params );
		$this->assertEquals( $params['height'], 75 );
		// makeParamString
		$this->assertEquals(
			$this->handler->makeParamString(
				array(
					'width' => '100',
					'page' => '4',
					'lossy' => 'lossless'
				)
			),
			'lossless-page4-100px'
		);
	
		// ---- File upload checks and Thumbnail transformation
		// check
		// TODO: check other images
		$this->assertTrue( $this->handler->check( 'multipage.tiff', $this->path, $error ) );
	
		$this->handler->check( 'Caspian.tif', dirname( __FILE__ ) . '/testImages/caspian.tif', $error );
		$this->assertEquals( $error, 'tiff_bad_file' );
		// doTransform
		$this->handler->doTransform( $this->image, dirname(__FILE__) . '/testImages/test.tif', 'test.tif', array( 'width' => 100, 'height' => 100 ) );
		$error = $this->handler->doTransform( wfFindFile( Title::newFromText( 'Image:Caspian.tif' ) ), dirname( __FILE__ ) . '/testImages/caspian.tif', 'Caspian.tif', array( 'width' => 100, 'height' => 100 ) );
		$this->assertEquals( $error->textMsg, wfMsg( 'thumbnail_error', wfMsg( 'tiff_bad_file' ) ) );
		// ---- Image information
		// getThumbExtension
		$this->assertEquals( $this->handler->getThumbExtension( $this->image, 2, 1 ), '.jpg' );
		// TODO: 0 is obviously the same as NULL
		$this->assertEquals( $this->handler->getThumbExtension( $this->image, 2, '0' ), '.png' );
		// getLongDesc
		$this->assertEquals( $this->handler->getLongDesc( $this->image ), wfMsg( 'tiff-file-info-size', '1.024', '768', '2,64 MB', 'image/tiff', '1' ) );
		// pageCount
		$this->assertEquals( $this->handler->pageCount( $this->image ), 7 );
		// getPageDimensions
		$this->assertEquals( $this->handler->getPageDimensions( $this->image, 0 ), array( 'width' => 1024, 'height' => 768 ) );
		$this->assertEquals( $this->handler->getPageDimensions( $this->image, 1 ), array( 'width' => 1024, 'height' => 768 ) );
		$this->assertEquals( $this->handler->getPageDimensions( $this->image, 2 ), array( 'width' => 640, 'height' => 564 ) );
		$this->assertEquals( $this->handler->getPageDimensions( $this->image, 3 ), array( 'width' => 1024, 'height' => 563 ) );
		$this->assertEquals( $this->handler->getPageDimensions( $this->image, 4 ), array( 'width' => 1024, 'height' => 768 ) );
		$this->assertEquals( $this->handler->getPageDimensions( $this->image, 5 ), array( 'width' => 1024, 'height' => 768 ) );
		$this->assertEquals( $this->handler->getPageDimensions( $this->image, 6 ), array( 'width' => 1024, 'height' => 768 ) );
		$this->assertEquals( $this->handler->getPageDimensions( $this->image, 7 ), array( 'width' => 768, 'height' => 1024 ) );
		// return dimensions of last page if page number is too high
		$this->assertEquals( $this->handler->getPageDimensions( $this->image, 8 ), array( 'width' => 768, 'height' => 1024 ) );
		// isMultiPage
		$this->assertTrue( $this->handler->isMultiPage( $this->image ) );
	
		// ---- Metadata handling
		// getMetadata
		$metadata =  $this->handler->getMetadata( false, $this->path );
		$this->assertTrue( strpos( $metadata, '"page_amount";i:7' ) !== false );
		// isMetadataValid
		$this->assertTrue( $this->handler->isMetadataValid( $this->image, $metadata ) );
		// getMetaArray
		$metaArray = $this->handler->getMetaArray( $this->image );

		$this->assertEquals( $metaArray['page_amount'], 7 );
		//this is also strtolower in PagedTiffHandler::getThumbExtension
		$this->assertEquals( strtolower( $metaArray['page_data'][1]['alpha'] ), 'false' );
		$this->assertEquals( strtolower( $metaArray['page_data'][2]['alpha'] ), 'true' );
		$this->assertEquals( $metaArray['exif']['Endianess'], 'MSB' );
		// formatMetadata
		$formattedMetadata = $this->handler->formatMetadata( $this->image );
		$this->assertEquals( $formattedMetadata['collapsed'][0]['value'], 'TIFF (Tagged Image File Format)' );
	}

}
$wgShowExceptionDetails = true;

$t = new PagedTiffHandlerTest();
$t->setUp( true );
$t->runTest();

echo "OK.\n";