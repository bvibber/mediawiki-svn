<?php
if ( isset( $GET_ ) ) {
	echo( "This file cannot be run from the web.\n" );
	die( 1 );
}

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

require_once( "$IP/maintenance/commandLine.inc" );

// requires PHPUnit 3.4
require_once 'PHPUnit/Framework.php';

error_reporting( E_ALL );

class DataTransclusionTest extends PHPUnit_Framework_TestCase {
	protected static $templates = array(
		'Test' => "{{{source-name}}}:'''{{{id}}}'''|{{{name}}}|{{{extra}}}|{{{info}}}|[{{{url}}} link]|[{{{evil}}} click me]",
	);

	static function getTemplate( $title, $parser ) {
		$name = $title->getText();

		if ( $title->getNamespace() == NS_TEMPLATE && isset( DataTransclusionTest::$templates[ $name ] ) ) {
			$text = DataTransclusionTest::$templates[ $name ];

			$entry = array(
				'text' => $text,
				'deps' => array( array( 'title' => $title, 'page_id' => 0, 'rev_id' => 0 ) ),
			);

			return $entry;
		} else {
			return Parser::statelessFetchTemplate( $title, $parser );
		}
	}

	function setUp() {
		global $wgTitle;

		$wgTitle = Title::newFromText( "Test" );
	}
	
	function runTest() {
		$this->testErrorMessage();
		$this->testSanitizeValue();
		$this->testNormalizeRecord();
		$this->testBuildAssociativeArguments();
		$this->testGetDataSource();
		$this->testCachedFetchRecord();
		$this->testRender();
		$this->testHandleRecordTransclusion();
		$this->testHandleRecordFunction();
		$this->testHandleRecordTag();
		$this->testDBDataTransclusionSource();
		$this->testWebDataTransclusionSource();
	}

	function testErrorMessage() {
		$m = DataTransclusionHandler::errorMessage( 'datatransclusion-test-wikitext', false );
		$this->assertEquals( $m, '<span class="error datatransclusion-test-wikitext">some <span class="test">html</span> and \'\'markup\'\'.</span>' );

		$m = DataTransclusionHandler::errorMessage( 'datatransclusion-test-evil-html', false );
		$this->assertEquals( $m, '<span class="error datatransclusion-test-evil-html">some <object>evil</object> html.</span>' );

		$m = DataTransclusionHandler::errorMessage( 'datatransclusion-test-nowiki', false );
		$this->assertEquals( $m, '<span class="error datatransclusion-test-nowiki">some <nowiki>{{nowiki}}</nowiki> code.</span>' );

		$m = DataTransclusionHandler::errorMessage( 'datatransclusion-test-wikitext', true );
		$this->assertEquals( $m, '<span class="error datatransclusion-test-wikitext">some <span class="test">html</span> and <i>markup</i>.</span>' );

		$m = DataTransclusionHandler::errorMessage( 'datatransclusion-test-evil-html', true );
		$this->assertEquals( $m, '<span class="error datatransclusion-test-evil-html">some &lt;object&gt;evil&lt;/object&gt; html.</span>' );

		$m = DataTransclusionHandler::errorMessage( 'datatransclusion-test-nowiki', true );
		$this->assertEquals( $m, '<span class="error datatransclusion-test-nowiki">some {{nowiki}} code.</span>' );
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
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( '==foo bar==' ), '&#61;=foo bar==' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo=bar' ), 'foo=bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( '---- foo bar' ), '&#45;--- foo bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( 'foo-bar' ), 'foo-bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( "foo\r\nbar" ), 'foo  bar' );
		$this->assertEquals( DataTransclusionHandler::sanitizeValue( '  foo bar' ), '&#32; foo bar' );
	}

	function testBuildAssociativeArguments() {
		$args = array( "foo bar", "x=y", " ah = \"be\" ", "blubber bla" );
		$assoc = DataTransclusionhandler::buildAssociativeArguments( $args );

		$this->asserttrue( !isset( $assoc[0] ) );
		$this->asserttrue( !isset( $assoc[3] ) );
		$this->asserttrue( !isset( $assoc['foo'] ) );
		$this->asserttrue( !isset( $assoc['foo bar'] ) );
		$this->assertEquals( $assoc[1], 'foo bar' );
		$this->assertEquals( $assoc[2], 'blubber bla' );
		$this->assertEquals( $assoc['x'], 'y' );
		$this->assertEquals( $assoc['ah'], 'be' );
	}

	function testGetDataSource() {
		global $wgDataTransclusionSources;

		$spec = array( 'name' => 'FOO', 'keyFields' => 'name,id', 'fieldNames' => 'id,name,info' );
		$data[] = array( "name" => "foo", "id" => 3, "info" => 'test 1' );
		$data[] = array( "name" => "bar", "id" => 5, "info" => 'test 2' );
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

		// /////////////////////////////////////////////////////////////////////////////
		$spec[ 'class' ] = 'FakeDataTransclusionSource';
		$spec[ 'data' ] = $data;

		$wgDataTransclusionSources[ 'BAR' ] = $spec;

		$src = DataTransclusionHandler::getDataSource( 'BAR' );
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

	function testHandleRecordTransclusion() {
		global $wgParser;
		global $wgDataTransclusionSources;

		$data[] = array( "name" => "foo", "id" => "3", "info" => 'test&X' );
		$spec = array(
			'class' => 'FakeDataTransclusionSource',
			'data' => $data,
			'keyFields' => 'name,id',
			'fieldNames' => 'id,name,info',
			'defaultKey' => 'id'
		);
		
		$wgDataTransclusionSources[ 'FOO' ] = $spec;

		# failure mode: no source given
		$s = DataTransclusionHandler::handleRecordTransclusion( "Dummy", array( 'foo' => 'bar', 'id' => 3 ), $wgParser, false );
		$this->assertTrue( preg_match( '/class="error datatransclusion-missing-source"/', $s ) === 1 );

		# failure mode: bad source given
		$s = DataTransclusionHandler::handleRecordTransclusion( "Dummy", array( 'source' => '*** nonsense ***', 'id' => 3 ), $wgParser, false );
		$this->assertTrue( preg_match( '/class="error datatransclusion-unknown-source"/', $s ) === 1 );

		# failure mode: bad source given (alternative)
		$s = DataTransclusionHandler::handleRecordTransclusion( "Dummy", array( 1 => '*** nonsense ***', 'id' => 3 ), $wgParser, false );
		$this->assertTrue( preg_match( '/class="error datatransclusion-unknown-source"/', $s ) === 1 );

		# failure mode: no key value specified
		$s = DataTransclusionHandler::handleRecordTransclusion( "Dummy", array( 'source' => 'FOO' ), $wgParser, false );
		$this->assertTrue( preg_match( '/class="error datatransclusion-missing-key"/', $s ) === 1 );

		# failure mode: no template specified
		$s = DataTransclusionHandler::handleRecordTransclusion( null, array( 'source' => 'FOO', 'id' => 3 ), $wgParser, false );
		$this->assertTrue( preg_match( '/class="error datatransclusion-missing-argument-template"/', $s ) === 1 );

		# failure mode: illegal template specified
		$s = DataTransclusionHandler::handleRecordTransclusion( "##", array( 'source' => 'FOO', 'id' => 3 ), $wgParser, false );
		$this->assertTrue( preg_match( '/class="error datatransclusion-bad-template-name"/', $s ) === 1 );

		# failure mode: record can't be found for that key
		$s = DataTransclusionHandler::handleRecordTransclusion( "Dummy", array( 'source' => 'FOO', 'id' => 'xxxxxxxxxx' ), $wgParser, false );
		$this->assertTrue( preg_match( '/class="error datatransclusion-record-not-found"/', $s ) === 1 );

		/*
		//TODO: re-enable this once DataTransclusionHandler::render() detects missing templates again.
		# failure mode: unknown template
		$s = DataTransclusionHandler::handleRecordTransclusion( "3", array( 'source' => 'FOO', 'template' => '---SomeTemplateThatHopefullyDoesNotExist---' ), $wgParser, false );
		$this->assertTrue( preg_match( '/class="error datatransclusion-unknown-template"/', $s ) === 1 );
		*/

		////////////////////////////////////////////////////////
		# success: render record
		$res = DataTransclusionHandler::handleRecordTransclusion( "Test", array( 'source' => 'FOO', 'id' => 3 ), $wgParser, false, "'''{{{id}}}'''|{{{name}}}|{{{info}}}" );
		$this->assertEquals( $res, '\'\'\'3\'\'\'|foo|test&X' );
		
		# success: render record (find by name)
		$res = DataTransclusionHandler::handleRecordTransclusion( "Test", array( 'source' => 'FOO', 'name' => 'foo'), $wgParser, false, "'''{{{id}}}'''|{{{name}}}|{{{info}}}" );
		$this->assertEquals( $res, '\'\'\'3\'\'\'|foo|test&X' );
		
		# success: render record (as HTML)
		$res = DataTransclusionHandler::handleRecordTransclusion( "Test", array( 'source' => 'FOO', 'id' => 3 ), $wgParser, true, "'''{{{id}}}'''|{{{name}}}|{{{info}}}" );
		$this->assertEquals( $res, '<b>3</b>|foo|test&X' ); // FIXME: & should have been escaped to &amp; here, no? why not?
	}

	function testHandleRecordFunction() {
		global $wgDataTransclusionSources;

		$data[] = array( "name" => "foo", "id" => "3", "info" => '<test>&[[X]]', "url" => 'http://test.org/', "evil" => 'javascript:alert("evil")' );
		$spec = array(
			'class' => 'FakeDataTransclusionSource',
			'data' => $data,
			'keyFields' => 'name,id',
			'fieldNames' => 'id,name,info,url,evil',
			'defaultKey' => 'id',
		);
		
		$wgDataTransclusionSources[ 'FOO' ] = $spec;

		global $wgParser;
		$title = Title::newFromText( "Dummy" );
		$options = new ParserOptions();
		$options->setTemplateCallback( 'DataTransclusionTest::getTemplate' );

		$text = 'xx {{#record:Test|FOO|id=3|extra=Hallo}} xx';
		$wgParser->parse( $text, $title, $options );

		$html = $wgParser->getOutput()->getText();   
		$this->assertEquals( $html, '<p>xx FOO:<b>3</b>|foo|Hallo|&lt;test&gt;&amp;&#91;&#91;X&#93;&#93;|<a href="http://test.org/" class="external text" rel="nofollow">link</a>|[javascript:alert("evil") click me] xx'."\n".'</p>' ); // XXX: should be more lenient wrt whitespace
		$templates = $wgParser->getOutput()->getTemplates();
		$this->assertTrue( isset( $templates[ NS_TEMPLATE ]['Test'] ) ); 
	}

	function testHandleRecordTag() {
		global $wgDataTransclusionSources;

		$data[] = array( "name" => "foo", "id" => "3", "info" => '<test>&[[X]]', "url" => 'http://test.org/', "evil" => 'javascript:alert("evil")' );
		$spec = array(
			'class' => 'FakeDataTransclusionSource',
			'data' => $data,
			'keyFields' => 'name,id',
			'fieldNames' => 'id,name,info,url,evil',
			'defaultKey' => 'id'
		);
		
		$wgDataTransclusionSources[ 'FOO' ] = $spec;

		global $wgParser;
		$title = Title::newFromText( "Dummy" );
		$options = new ParserOptions();
		$options->setTemplateCallback( 'DataTransclusionTest::getTemplate' );

		$text = 'xx <record source="FOO" id=3 extra="Hallo">Test</record> xx';
		$wgParser->parse( $text, $title, $options );

		$html = $wgParser->getOutput()->getText();      
		$this->assertEquals( $html, '<p>xx FOO:<b>3</b>|foo|Hallo|&lt;test&gt;&amp;&#91;&#91;X&#93;&#93;|<a href="http://test.org/" class="external text" rel="nofollow">link</a>|[javascript:alert("evil") click me] xx'."\n".'</p>' ); // XXX: should be more lenient wrt whitespace
		$templates = $wgParser->getOutput()->getTemplates();
		$this->assertTrue( isset( $templates[ NS_TEMPLATE ]['Test'] ) ); 
	}

	function testNormalizeRecord() {
		global $wgParser;

		$spec = array( 'name' => 'FOO',
			'keyFields' => 'name,id',
			'fieldNames' => 'id,name,info',
			'sourceInfo' => array( 'x' => 43, 'quux' => 'xyzzy' ),
		);
		$data[] = $rec = array( "name" => "foo", "id" => 3, "info" => '{{test}}=[[x]] 1&2 ', 'stuff' => 'bla bla bla' );

		$source = new FakeDataTransclusionSource( $spec, $data );

		$handler = new DataTransclusionHandler( $wgParser, $source, null );

		$args = array( "name" => "cruft", "more" => "stuff" );
		$rec = $handler->normalizeRecord( $rec, $args );

		$this->assertEquals( $rec['source-name'], 'FOO' );
		$this->assertEquals( $rec['quux'], 'xyzzy' );
		$this->assertEquals( $rec['x'], 43 );
		$this->assertEquals( $rec['more'], 'stuff' );
		$this->assertEquals( $rec['name'], 'foo' );
		$this->assertEquals( $rec['id'], '3' );
		$this->assertEquals( $rec['info'], '&#123;&#123;test&#125;&#125;=&#91;&#91;x&#93;&#93; 1&2 ' );
		$this->assertTrue( !isset( $rec['stuff'] ) );
		$this->assertTrue( !isset( $rec['name.keyFields'] ) );
	}

	function testRender() {
		global $wgParser;

		$source = null;
		$title = Title::newFromText( "Template:Thing" );
		$rec = array( "name" => "foo", "id" => 3, "info" => 'test X' );
		$template = "{{{id}}}|{{{name}}}|{{{info}}}";

		$handler = new DataTransclusionHandler( $wgParser, $source, $title, $template );
		$res = $handler->render( $rec );

		$this->assertEquals( $res, '3|foo|test X' );
	}

	function testCachedFetchRecord() {
		global $wgDataTransclusionSources;

		$data[] = array( "name" => "foo", "id" => 3, "info" => 'test 1' );
		$data[] = array( "name" => "bar", "id" => 5, "info" => 'test 2' );
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

		// get original version
		$rec = $src->fetchRecord( 'id', 3 );
		$this->assertEquals( $rec['id'], 3 );
		$this->assertEquals( $rec['name'], 'foo' );
		$this->assertEquals( $rec['info'], 'test 1' );

		// change record
		$rec = array( "name" => "foo", "id" => 3, "info" => 'test X' );
		$src->source->putRecord( $rec );

		// fetch record - should be the cached version
		$rec = $src->fetchRecord( 'id', 3 );
		$this->assertEquals( $rec['info'], 'test 1' );

		sleep( 3 );

		// fetch record - cached version should have expired
		$rec = $src->fetchRecord( 'id', 3 );
		$this->assertEquals( $rec['info'], 'test X' );
	}

	function testDBDataTransclusionSource() {
		$spec = array(
			'name' => 'FOO',
			'keyTypes' => array( 'id' => 'int', 'name' => 'string' ),
			'query' => 'SELECT * FROM foo ',
			'querySuffix' => ' GROUP BY id',
		);

		$source = new DBDataTransclusionSource( $spec );
		$sql = $source->getQuery( 'name', 'foo"' );
		
		$this->assertTrue( preg_match( '/^SELECT \* FROM foo/', $sql ) === 1 );
		$this->assertTrue( preg_match( '/GROUP BY id$/', $sql ) === 1 );
		$this->assertTrue( preg_match( "/WHERE \\( *name *= *'foo\\\\\"' *\\)/", $sql ) === 1 );

		$sql = $source->getQuery( 'id', '3' );
		$this->assertTrue( preg_match( '/WHERE \( *id *= *3 *\)/', $sql ) === 1 );

		// check blocking of evil field names
		$sql = $source->getQuery( 'name = 0; select * from x;', 'foo' );
		$this->assertEquals( $sql, false );
	}

	function testWebDataTransclusionSource() {
		$spec = array(
			'name' => 'FOO',
			'keyFields' => 'id,name',
			'optionNames' => 'x,y',
			'url' => 'http://acme.com/{name}',
			'dataFormat' => 'php',
			'dataPath' => 'response/content/@0',
			'fieldPathes' => array( 'id' => 'id/value', 'name' => 'name/value', 'info' => 'info/value',  ),
			'errorPath' => 'response/error',
		);

		$source = new WebDataTransclusionSource( $spec );

		$u = $source->getRecordURL( 'name', 'foo&bar' );
		$this->assertEquals( $u, 'http://acme.com/foo%26bar' );

		$u = $source->getRecordURL( 'id', '23' );
		$this->assertEquals( $u, 'http://acme.com/?id=23' );

		$u = $source->getRecordURL( 'name', 'foo&bar', array( 'x' => '42', 'y' => 57 ) );
		$this->assertEquals( $u, 'http://acme.com/foo%26bar?x=42&y=57' );

		$rec = array( 
			"name" => array( 'type' => 'string', 'value' => "foo" ), 
			"id" => 3,  
			"info" => array( 'type' => 'string', 'value' => "test X" ), 
		);

		$data = array( 'response' => array(
			'error' => 'test error',
			'content' => array(
				'foo' => $rec
			)
		) );

		$data = serialize( $data );
		$rec = $source->extractRecord( $source->decodeData( $data, 'php' ) );
		$err = $source->extractError( $source->decodeData( $data, 'php' ) );

		$this->assertEquals( $err, 'test error' );
		$this->assertEquals( $rec['id'], 3 );

		//TODO: test extractField, with fancy snytax!
		//TODO: test flattenRecord

		////////////////////////
		$spec['url'] = 'file://' . dirname( realpath( __FILE__ ) ) . '/test-data-name-{name}.pser';
		$spec['dataFormat'] = 'php';
		$source = new WebDataTransclusionSource( $spec );

		$rec = $source->fetchRecord( 'name', 'foo' );
		$this->assertEquals( $rec['id'], 3 );

		////////////////////////
		$spec['url'] = 'file://' . dirname( realpath( __FILE__ ) ) . '/test-data-name-{name}.json';
		$spec['dataFormat'] = 'json';
		$source = new WebDataTransclusionSource( $spec );

		$rec = $source->fetchRecord( 'name', 'foo' );
		$this->assertEquals( $rec['id'], 3 );

		////////////////////////
		if ( function_exists( 'wddx_unserialize' ) ) {
			$spec['url'] = 'file://' . dirname( realpath( __FILE__ ) ) . '/test-data-name-{name}.wddx';
			$spec['dataFormat'] = 'wddx';
			$source = new WebDataTransclusionSource( $spec );

			$rec = $source->fetchRecord( 'name', 'foo' );
			$this->assertEquals( $rec['id'], 3 );
		}
	}
}

$wgShowExceptionDetails = true;

$t = new DataTransclusionTest();
$t->setUp();
$t->runTest();

echo "OK.\n";
?>
