<?php
/**
 * DataTransclusion Source base class
 *
 * @file
 * @ingroup Extensions
 * @author Daniel Kinzler for Wikimedia Deutschland
 * @copyright © 2010 Wikimedia Deutschland (Author: Daniel Kinzler)
 * @licence GNU General Public Licence 2.0 or later
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

/**
* Baseclass representing a source of data transclusion. All logic for addressing, fetching, decoding and filtering
* data is encapsulated by a subclass of DataTransclusionSource. Instances of DataTransclusionSource are instantiated
* by DataTransclusionHandler, and initialized by passing an associative array of options to the constructor. This array
* is taken from the $wgDataTransclusionSources configuration variable.
*
* Below is a list of options for the $spec array, as handled by the DataTransclusionSource
* base class (sublcasses may handle additional options):
*
*	* $spec['name']: the source's name, used to specify it in wiki text. 
*		Set automatically by DataTransclusionHandler. REQUIRED.
*	* $spec['keyFields']: list of fields that can be used as the key
*		for fetching a record. REQUIRED.
*	* $spec['fieldNames']: names of all fields present in each record.
*		Fields not listed here will not be available on the wiki,
*		even if they are returned by the data source. REQUIRED.
*	* $spec['defaultKey']: default key to select records. If not specified,
*		the first entry in $spec['keyFields'] is used.
*	* $spec['cacheDuration']: the number of seconds a result from this source
*		may be cached for. If not set, results are assumed to be cacheable
*		indefinitely. This setting determines the expiry time of the parser
*		cache entry for pages that show data from this source. If $spec['cache'],
*		i.e. if this DataTransclusionSource is wrapped by an instance of 
*		CachingDataTransclusionSource, $spec['cacheDuration'] also determines
*		the expiry time of ObjectCache entries for records from this source.
*	* $spec['sourceInfo']: associative array of information about the data source 
*		that should be made available on the wiki. This information will be 
*		present in the record arrays, with they keys prefixed by "source.". 
*		This is intended to allow information about source, license, etc to be
*		shown on the wiki. Note that DataTransclusionSource implementations may
*		provide extra information in the source info on their own: This base
*		class forces $spec['sourceInfo']['name'] = $spec['name'] and  
*		$spec['sourceInfo']['defaultKey'] = $spec['defaultKey'].
*
* Options used by DataTransclusionHandler but ignored by DataTransclusionSource:
*	* $spec['class']: see documentation if $wgDataTransclusionSources in DataTransclusion.
*	* $spec['cache']: see documentation if $wgDataTransclusionSources in DataTransclusion.
*
* Lists may be given as arrays or strings with items separated by [,;|].
*/
class DataTransclusionSource {
  static function splitList( $s ) {
    if ( $s === null || $s === false ) return $s;
    if ( !is_string( $s ) ) return $s;
    
    $list = preg_split( '!\s*[,;|/]\s*!', $s );
    return $list;
  }

  /**
  * Initializes the DataTransclusionSource from the given parameter array.
  * @param $spec associative array of options. See class-level documentation for details.
  */
  function __construct( $spec ) {
    $this->name = $spec[ 'name' ];

    $this->keyFields = self::splitList( $spec[ 'keyFields' ] );

    if ( isset( $spec[ 'fieldNames' ] ) )
	$this->fieldNames = self::splitList( $spec[ 'fieldNames' ] );
    else 
	$this->fieldNames = $this->keyFields;

    if ( !empty( $spec[ 'defaultKey' ] ) ) $this->defaultKey = $spec[ 'defaultKey' ];
    else $this->defaultKey = $this->keyFields[ 0 ];

    if ( !empty( $spec[ 'cacheDuration' ] ) ) $this->cacheDuration = (int)$spec[ 'cacheDuration' ];
    else $this->cacheDuration = null;

    $this->sourceInfo = array();

    if ( !empty( $spec[ 'sourceInfo' ] ) ) {
	foreach ( $spec[ 'sourceInfo' ] as $k => $v ) {
		$this->sourceInfo[ $k ] = $v;
	}
    }

    $this->sourceInfo[ 'name' ] = $this->name; //force this one
    $this->sourceInfo[ 'defaultKey' ] = $this->name; //force this one
  }

  public function getName() {
    return $this->name;
  }

  public function getDefaultKey() {
    return $this->defaultKey;
  }

  public function getSourceInfo() {
    return $this->sourceInfo;
  }

  public function getKeyFields() {
    return $this->keyFields;
  }

  public function getFieldNames() {
    return $this->fieldNames;
  }

  public function getCacheDuration() {
    return $this->cacheDuration;
  }

  public function fetchRecord( $key, $value ) {
    throw new MWException( "override fetchRecord()" );
  }
}

/**
* Implementation of DataTransclusionSource that wraps another DataTransclusionSource and applies caching in an
* ObjectCache. All methods delegate to the underlieing data source, fetchRecord adds logic for caching.
*/
class CachingDataTransclusionSource extends DataTransclusionSource {

  /**
  * Initializes the CachingDataTransclusionSource
  *
  * @param $source a DataTransclusionSource instance for fetching data records.
  * @param $cache an ObjectCache instance
  * @param $duration number of seconds for which records may be cached
  */
  function __construct( $source, $cache, $duration ) {
    $this->source = $source;
    $this->cache = $cache;
    $this->duration = $duration;
  }

  public function getName() {
    return $this->source->getName();
  }

  public function getDefaultTemplate() {
    return $this->source->getDefaultTemplate();
  }

  public function getSourceInfo() {
    return $this->source->getSourceInfo();
  }

  public function getKeyFields() {
    return $this->source->getKeyFields();
  }

  public function getFieldNames() {
    return $this->source->getFieldNames();
  }

  public function getCacheDuration() {
    return $this->source->getCacheDuration();
  }

  public function fetchRecord( $key, $value ) {
    global $wgDBname, $wgUser;

    $cacheKey = "$wgDBname:DataTransclusion(" . $this->getName() . ":$key=$value)";
    
    $rec = $this->cache->get( $cacheKey );

    if ( !$rec ) { 
	$rec = $this->source->fetchRecord( $key, $value );
	if ( $rec ) $this->cache->set( $cacheKey, $rec, $this->getCacheDuration() ) ; //XXX: also cache negatives??
    }

    return $rec;
  }
}

/**
* Implementations of DataTransclusionSource which simply fetches data from an array. This is
* intended mainly for testing and debugging.
*/
class FakeDataTransclusionSource extends DataTransclusionSource {

  /**
  * Initializes the CachingDataTransclusionSource
  *
  * @param $spec an associative array of options. See class-level 
  *		documentation of DataTransclusionSource for details.
  *
  * @param $data an array containing a list of records. Records from
  *		this list can be accessed via fetchRecord() using the key fields specified 
  *		by $spec['keyFields']. If $data is not given, $spec['data'] must contain the data array.
  */
  function __construct( $spec, $data = null ) {
	DataTransclusionSource::__construct( $spec );

	if ( $data === null ) $data = $spec[ 'data' ];

	$this->lookup = array();

	foreach ( $data as $rec ) {
		$this->putRecord( $rec );
	}
  }

  public function putRecord( $record ) {
	$fields = $this->getKeyFields();
	foreach ( $fields as $f ) {
		$k = $record[ $f ];
		
		if ( !isset( $this->lookup[ $f ] ) ) $this->lookup[ $f ] = array();
		$this->lookup[ $f ][ $k ] = $record;
	}
  }

  public function fetchRecord( $key, $value ) {
	return @$this->lookup[ $key ][ $value ];
  }
}
