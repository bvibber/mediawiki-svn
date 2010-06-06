<?php
/**
 * handler code for DataTransclusion extension.
 *
 * @file
 * @ingroup Extensions
 * @author Daniel Kinzler for Wikimedia Deutschland
 * @copyright © 2010 Wikimedia Deutschland (Author: Daniel Kinzler)
 * @licence GNU General Public Licence 2.0 or later
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "Not a valid entry point.\n" );
	die( 1 );
}

class DataTransclusionHandler {
    static function buildAssociativeArguments ( $params ) {
	// build associative arguments from flat parameter list
	$argv = array();
	$i = 1;
	foreach ( $params as $p ) {
		if ( preg_match( '/^\s*(\S.*?)\s*=\s*(.*?)\s*$/', $p, $m ) ) {
			$k = $m[1];
			$v = preg_replace( '/^"\s*(.*?)\s*"$/', '$1', $m[2] ); // strip any quotes enclosing the value
		}
		else {
			$v = trim( $p );
			$k = $i;
			$i += 1;
		}

		$argv[$k] = $v;
	}

	return $argv;
    }

    /**
    * Entry point for the {{#record}} parser function.
    * This is a wrapper around handleRecordTag
    */
    static function handleRecordFunction ( $parser ) {
	    $params = func_get_args();
	    array_shift( $params ); // first is &$parser, strip it

	    // first user-supplied parameter must be category name
	    if ( !$params ) {
		    return ''; // no category specified, return nothing
	    }

	    // build associative arguments from flat parameter list
	    $argv = DataTransclusionHandler::buildAssociativeArguments( $params );

	    //FIXME: error messages contining special blocks like <nowiki> don't get re-substitutet correctly.
	    $text = DataTransclusionHandler::handleRecordTag( null, $argv, $parser, false );
	    return array( $text, 'noparse' => false, 'isHTML' => false );
    }

    static function errorMessage( $key, $asHTML ) {
	    $params = func_get_args();
	    array_shift( $params ); // $key
	    array_shift( $params ); // $asHTML

	    if ( $asHTML ) $mode = 'parseinline'; //TESTME
	    else $mode = 'parsemag';

	    $m = wfMsgExt( $key, $mode, $params );
	    return "<span class=\"error\">$m</div>";
    }

    /**
    * Entry point for the <record> tag parser hook.
    */
    static function handleRecordTag( $key, $argv, $parser = null, $asHTML = true ) {
            //find out which data source to use...
	    if ( empty( $argv['source'] ) ) {
		if ( empty( $argv[1] ) ) return DataTransclusionHandler::errorMessage( 'datatransclusion-missing-source', $asHTML ); //TESTME
		else $sourceName = $argv[1];
	    } else {
		$sourceName = $argv['source'];
	    }

	    $source = DataTransclusionHandler::getDataSource( $sourceName );
	    if ( empty( $source ) ) return DataTransclusionHandler::errorMessage( 'datatransclusion-unknown-source', $asHTML, $sourceName ); //TESTME

            //find out how to find the desired record
	    if ( empty( $argv['by'] ) ) $by = $source->getDefaultKey();
	    else $by = $argv['by'];

	    $keyFields = $source->getKeyFields();
	    if ( ! in_array( $by, $keyFields ) ) return DataTransclusionHandler::errorMessage( 'datatransclusion-bad-argument-by', $asHTML, $sourceName, $by, join(', ', $keyFields) ); //TESTME

	    if ( !empty( $argv['key'] ) ) $key = $argv['key'];
	    else if ( $key === null || $key === false ) {
		if ( empty( $argv[2] ) ) return DataTransclusionHandler::errorMessage( 'datatransclusion-missing-argument-key', $asHTML ); //TESTME
		else $key = $argv[2];
	    }

            //find out how to render the record
	    if ( empty( $argv['template'] ) ) {
		if ( empty( $argv[3] ) ) return DataTransclusionHandler::errorMessage( 'datatransclusion-missing-argument-template', $asHTML ); //TESTME
		else $template = $argv[3];
	    } else {
		$template = $argv['template'];
	    }

            //load the record
	    $record = $source->fetchRecord( $by, $key );
	    if ( empty( $record ) ) return DataTransclusionHandler::errorMessage( 'datatransclusion-record-not-found', $asHTML, $sourceName, $by, $key ); //TESTME

	    //render the record into wiki text
	    $t = Title::newFromText( $template, NS_TEMPLATE );
	    if ( empty( $t ) ) return DataTransclusionHandler::errorMessage( 'datatransclusion-bad-template-name', $asHTML, $template ); //TESTME

	    //FIXME: log the template we used into the parser output, like regular template use 
	    //       (including templates used by the template, etc)

	    $handler = new DataTransclusionHandler( $parser, $source, $t );

	    $record = $handler->normalizeRecord( $record );
	    $text = $handler->render( $record );

	    if ( $text === false ) return DataTransclusionHandler::errorMessage( 'datatransclusion-unknown-template', $asHTML, $template ); //TESTME

	    //set parser output expiry
	    $expire = $source->getCacheDuration();
	    if ( $expire !== false && $expire !== null ) { 
		$parser->getOutput()->updateCacheExpiry( $expire ); //NOTE: this works only since r67185 //TESTME
	    } 

	    if ( $asHTML && $parser ) { //render into HTML if desired
		$html = $parser->recursiveTagParse( $text ); 
		return $html; //TESTME
	    } else {
		return $text; //TESTME
	    }
    }

    function __construct( $parser, $source, $template ) {
	    $this->template = $template;
	    $this->source = $source;
	    $this->parser = $parser;
    }

    function render( $record ) {
	    //XXX: use cached & preparsed template. $template doesn't have the right type, it seems
	    /*
	    list( $text, $this->template ) = $this->parser->getTemplateDom( $this->template );
	    $frame = $this->parser->getPreprocessor()->newCustomFrame( $record );
	    $text = $frame->expand( $template );
	    */

	    //XXX: trying another way. but $piece['parts'] needs to be a PPNode. how to do that?
	    /*
	    $frame = $this->parser->getPreprocessor()->newCustomFrame( $record );

	    $piece = array();

	    if ( $this->template->getNamespace() == NS_TEMPLATE ) $n = "";
	    else $n = $this->template->getNsText() . ":";

	    $piece ['title'] = $n . $this->template->getText();
	    $piece['parts'] = $record;
	    $piece['lineStart'] = false; //XXX: ugly. can't know here whether the brace was at the start of a line

	    $ret = $this->parser->braceSubstitution( $piece, $frame );
	    $text = $ret[ 'text' ];
	    */

	    //dumb and slow, but works
	    $p = new Article( $this->template );
	    if ( !$p->exists() ) return false; //TESTME

	    $text = $p->getContent(); 
	    $text = $this->parser->replaceVariables( $text, $record, true );

	    return $text;
    }

    function normalizeRecord( $record ) {
	    $rec = array();

	    //keep record fields, add missing values
	    $fields = $this->source->getFieldNames();
	    foreach ( $fields as $f ) {
		if ( isset( $record[ $f ] ) ) $v = $record[ $f ];
		else $v = '';

		$rec[ $f ] = $this->sanitizeValue( $v );
	    }

	    //add source meta info, so we can render links back to the source, 
	    //provide license info, etc
	    $info = $this->source->getSourceInfo();
	    foreach ( $info as $f => $v ) {
		if ( is_array( $v ) || is_object( $v ) || is_resource( $v ) ) continue;
		$rec[ "source.$f" ] = $this->sanitizeValue( $v );
	    }

	    return $rec;
    }

    protected static $sanitizerSubstitution = array(
	    # '!&!' => '&amp;',  #breaks URLs. not really needed when parsed as wiki-text...
	    '!<!' => '&lt;', 
	    '!>!' => '&gt;', 
	    '!\[!' => '&#91;', 
	    '!\]!' => '&#93;', 
	    '!\{!' => '&#123;', 
	    '!\}!' => '&#125;', 
	    '!\'!' => '&#apos;', 
	    '!\|!' => '&#124;', 
	    '!^\*!m' => '&#42;', 
	    '!^#!m' => '&#35;', 
	    '!^:!m' => '&#58;', 
	    '!^;!m' => '&#59;', 
	    '!^ !m' => '&#32;', 
    );

    function sanitizeValue( $v ) {
	    $find = array_keys( self::$sanitizerSubstitution );
	    $subst = array_values( self::$sanitizerSubstitution );

	    $v = preg_replace( $find, $subst, $v );
	    return $v;
    }

    static function getDataSource( $name ) {
	    global $wgDataTransclusionSources;
	    if ( empty( $wgDataTransclusionSources[ $name ] ) ) return false;

	    $source = $wgDataTransclusionSources[ $name ];

	    if ( is_array( $source ) ) { //if the source is an array, use it to instantiate the sourece object
		$spec = $source;
		$spec[ 'name' ] = $name;

		if ( !isset( $spec[ 'class' ] ) ) throw new MWException( "\$wgDataTransclusionSources['$name'] must specifying a class name in the 'class' field." );

		$c = $spec[ 'class' ];
		$obj = new $c( $spec ); //pass spec array as constructor argument
		if ( !$obj ) throw new MWException( "failed to instantiate \$wgDataTransclusionSources['$name'] as new $c." );

		$source = $obj;

		if ( isset( $spec[ 'cache' ] ) ) { //check if a cache should be used
		    $c = $spec[ 'cache' ];
		    if ( !is_object( $c ) ) { //cache may be specified as a string
			$c = wfGetCache( $c ); // $c should be one of the CACHE_* constants
		    }

		    $source = new CachingDataTransclusionSource( $obj, $c, @$spec['cache-duration'] ); //apply caching wrapper
		}

		$wgDataTransclusionSources[ $name ] = $source; //replace spec array by actual object, for later re-use
	    } 

	    if ( !is_object( $source ) ) {
		if ( !isset( $source[ 'class' ] ) ) throw new MWException( "\$wgDataTransclusionSources['$name'] must be an array or an object." );
	    } 
	    
	    return $source;
    }

}
