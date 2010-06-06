<?php
if ( getenv( 'MW_INSTALL_PATH' ) ) {
	$IP = getenv( 'MW_INSTALL_PATH' );
} else {
	$dir = dirname( __FILE__ );

	if ( file_exists( "$dir/../../LocalSettings.php" ) ) $IP = "$dir/../..";
	else if ( file_exists( "$dir/../../../LocalSettings.php" ) ) $IP = "$dir/../../..";
	else if ( file_exists( "$dir/../../phase3/LocalSettings.php" ) ) $IP = "$dir/../../phase3";
	else if ( file_exists( "$dir/../../../phase3/LocalSettings.php" ) ) $IP = "$dir/../../../phase3";
	else $IP = $dir;
}

if( isset( $GET_ ) ) {
	echo( "This file cannot be run from the web.\n" );
	die( 1 );
}

require_once( "$IP/maintenance/commandLine.inc" );

// requires PHPUnit 3.4
require_once 'PHPUnit/Framework.php';

error_reporting(E_ALL);

class DataTransclusionTest extends PHPUnit_Framework_TestCase {

	function setUp()
	{
		global $wgTitle;

		$wgTitle = Title::newFromText( "Test" );
	}
	
	function runTest()
	{
		$this->testErrorMessage();
		$this->testSanitizeValue();
		$this->testNormalizeRecord();
		$this->testBuildAssociativeArguments();
		$this->testGetDataSource();
		$this->testCachedFetchRecord();
		$this->testRender();
		$this->testHandleRecordFunction();
		$this->testHandleRecordTag();
	}

	function testErrorMessage() {
		$m = DataTransclusionHandler::errorMessage('datatransclusion-test-wikitext', false);
		$this->assertEquals( $m, '<span class="error">some <span class="test">html</span> and \'\'markup\'\'.</span>' ); 

		$m = DataTransclusionHandler::errorMessage('datatransclusion-test-evil-html', false);
		$this->assertEquals( $m, '<span class="error">some <object>evil</object> html.</span>' ); 

		$m = DataTransclusionHandler::errorMessage('datatransclusion-test-nowiki', false);
		$this->assertEquals( $m, '<span class="error">some <nowiki>{{nowiki}}</nowiki> code.</span>' ); 

		$m = DataTransclusionHandler::errorMessage('datatransclusion-test-wikitext', true);
		$this->assertEquals( $m, '<span class="error">some <span class="test">html</span> and <i>markup</i>.</span>' ); 

		$m = DataTransclusionHandler::errorMessage('datatransclusion-test-evil-html', true);
		$this->assertEquals( $m, '<span class="error">some &lt;object&gt;evil&lt;/object&gt; html.</span>' ); 

		$m = DataTransclusionHandler::errorMessage('datatransclusion-test-nowiki', true);
		$this->assertEquals( $m, '<span class="error">some {{nowiki}} code.</span>' ); 
	}

	function testSanitizeValue() {
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo bar' ), 'foo bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo &bar;' ), 'foo &amp;bar;' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo&bar' ), 'foo&bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo <bar>' ), 'foo &lt;bar&gt;' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo [[bar]]' ), 'foo &#91;&#91;bar&#93;&#93;' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo {{bar}}' ), 'foo &#123;&#123;bar&#125;&#125;' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo \'bar\'' ), 'foo &apos;bar&apos;' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo|bar' ), 'foo&#124;bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( '* foo bar' ), '&#42; foo bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo*bar' ), 'foo*bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( '#foo bar' ), '&#35;foo bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo#bar' ), 'foo#bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( ':foo bar' ), '&#58;foo bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo:bar' ), 'foo:bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( ';foo bar' ), '&#59;foo bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo;bar' ), 'foo;bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( "foo\r\nbar" ), 'foo  bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( '  foo bar' ), '&#32; foo bar' );
	}

	function testNormalizeRecord() {
		//TODO...
	}

	function testBuildAssociativeArguments() {
		$args = array( "foo bar", "x=y", " ah = \"be\" ", "blubber bla" );
		$assoc = DataTransclusionhandler::buildAssociativeArguments( $args );

		$this->asserttrue( !isset($assoc[0]) );
		$this->asserttrue( !isset($assoc[3]) );
		$this->asserttrue( !isset($assoc['foo']) );
		$this->asserttrue( !isset($assoc['foo bar']) );
		$this->assertEquals( $assoc[1], 'foo bar' );
		$this->assertEquals( $assoc[2], 'blubber bla' );
		$this->assertEquals( $assoc['x'], 'y' );
		$this->assertEquals( $assoc['ah'], 'be' );
	}

	function testGetDataSource() {
		global $wgDataTransclusionSources;

		$spec = array( 'name' => 'FOO', 'keyFields' => 'name,id', 'fieldNames' => 'id,name,info' );
		$data[] = array( "name" => "foo", "id" => 3, "info" => 'test 1');
		$data[] = array( "name" => "bar", "id" => 5, "info" => 'test 2');
		$wgDataTransclusionSources[ 'FOO' ] = new FakeDataTransclusionSource( $spec, $data );

		$src = DataTransclusionHandler::getDataSource( 'FOO' );
		$this->assertTrue( $src instanceof FakeDataTransclusionSource );
		
		$rec = $src->fetchRecord( 'id', 3 );
		$this->assertEquals( $rec['id'], 3 );
		$this->assertEquals( $rec['name'], 'foo' );
		$this->assertEquals( $rec['info'], 'test 1' );
		
		$rec = $src->fetchRecord( 'name', 'bar' );
		$this->assertEquals( $rec['id'], 5 );
		$this->assertEquals( $rec['name'], 'bar' );
		$this->assertEquals( $rec['info'], 'test 2' );

		///////////////////////////////////////////////////////////////////////////////
		$spec[ 'class' ] = 'FakeDataTransclusionSource';
		$spec[ 'data' ] = $data;

		$wgDataTransclusionSources[ 'BAR' ] = $spec;

		$src = DataTransclusionHandler::getdataSource( 'BAR' );
		$this->assertTrue( $src instanceof FakeDataTransclusionSource );
		$this->assertEquals( $src->getName(), 'BAR' );
		
		$rec = $src->fetchRecord( 'id', 3 );
		$this->assertEquals( $rec['id'], 3 );
		$this->assertEquals( $rec['name'], 'foo' );
		$this->assertEquals( $rec['info'], 'test 1' );
		
		$rec = $src->fetchRecord( 'name', 'bar' );
		$this->assertEquals( $rec['id'], 5 );
		$this->assertEquals( $rec['name'], 'bar' );
		$this->assertEquals( $rec['info'], 'test 2' );

		$src = DataTransclusionHandler::getdataSource( 'XYZZY' );
		$this->assertTrue( $src === null || $src === false );
	}

	function testHandleRecordFunction() {
	}

	function testHandleRecordTag() {
	}

	function testRender() {
		global $wgParser;

		$source = null;
		$title = Title::newFromText( "Template:Thing" );
		$rec = array( "name" => "foo", "id" => 3, "info" => 'test X');
		$template = "{{{id}}}|{{{name}}}|{{{info}}}";

		$handler = new DataTransclusionHandler( $wgParser, $source, $title, $template );
		$res = $handler->render( $rec );

		$this->assertEquals( $res, '3|foo|test X' );
	}

	function testCachedFetchRecord() {
		global $wgDataTransclusionSources;

		$data[] = array( "name" => "foo", "id" => 3, "info" => 'test 1');
		$data[] = array( "name" => "bar", "id" => 5, "info" => 'test 2');
		$spec = array( 
			'class' => 'FakeDataTransclusionSource', 
			'data' => $data,
			'keyFields' => 'name,id', 
			'fieldNames' => 'id,name,info',
			'cacheDuration' => 2, 
			'cache' => new HashBagOStuff(), 
		);
		
		$wgDataTransclusionSources[ 'FOO' ] = $spec;

		$src = DataTransclusionHandler::getDataSource( 'FOO' );
		$this->assertTrue( $src instanceof CachingDataTransclusionSource );

		//get original version
		$rec = $src->fetchRecord( 'id', 3 );
		$this->assertEquals( $rec['id'], 3 );
		$this->assertEquals( $rec['name'], 'foo' );
		$this->assertEquals( $rec['info'], 'test 1' );

		//change record
		$rec = array( "name" => "foo", "id" => 3, "info" => 'test X');
		$src->source->putRecord( $rec );

		//fetch record - should be the cached version
		$rec = $src->fetchRecord( 'id', 3 );
		$this->assertEquals( $rec['info'], 'test 1' );

		sleep(3);

		//fetch record - cached version should have expired
		$rec = $src->fetchRecord( 'id', 3 );
		$this->assertEquals( $rec['info'], 'test X' );
	}

	/*
	function testWebFetchRecord() {
	    //TODO: decode, extract, etc
	}

	function testDBFetchRecord() {
	    //TODO: convert value, escape, build sql
	}
	*/
}

$wgShowExceptionDetails = true;

$t = new DataTransclusionTest();
$t->setUp();
$t->runTest();

echo "OK.\n";
?>