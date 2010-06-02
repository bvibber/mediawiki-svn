<?php
/**
 * DataTransclusion Source base class
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Daniel Kinzler for Wikimedia Deutschland
 * @copyright © 2010 Wikimedia Deutschland (Author: Daniel Kinzler)
 * @licence GNU General Public Licence 2.0 or later
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

class DataTransclusionSource {
  function __construct( $spec ) {
    $this->sourceInfo = $spec;

    $this->name = $spec[ 'name' ];

    $this->keyFields = $spec[ 'keyFields' ];
    if ( is_string( $this->keyFields ) ) $this->keyFields = preg_split( '!\s*[,;|]\s*!', $this->keyFields );

    $this->fieldNames = $spec[ 'fieldNames' ];
    if ( is_string( $this->fieldNames ) ) $this->fieldNames = preg_split( '!\s*[,;|]\s*!', $this->fieldNames );

    if ( !empty( $spec[ 'defaultKey' ] ) ) $this->defaultKey = $spec[ 'defaultKey' ];
    else $this->defaultKey = $this->keyFields[ 0 ];

    if ( !empty( $spec[ 'cacheDuration' ] ) ) $this->cacheDuration = (int)$spec[ 'cacheDuration' ];
    else $this->cacheDuration = null;
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

class CachingDataTransclusionSource extends DataTransclusionSource {
  function __construct( $source, $cache ) {
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
	if ( $rec ) $this->cache->set( $cacheKey, $rev, $this->getCacheDuration() ) ; //XXX: also cache negatives??
    }

    return $rec;
  }
}

class FakeDataTransclusionSource extends DataTransclusionSource {
  function __construct( $spec, $data ) {
    DataTransclusionSource::__construct( $spec );

    $this->lookup = array();

    $fields = $this->getKeyFields();
    foreach ( $fields as $f ) {
	$this->lookup[ $f ] = array();

	foreach ( $data as $rec ) {
	    $k = $rec[ $f ];
	    $this->lookup[ $f ][ $k ] = $rec;
	}
    }
  }

  public function fetchRecord( $key, $value ) {
    return @$this->lookup[ $key ][ $value ];
  }
}
