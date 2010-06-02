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

class WebDataTransclusionSource extends DataTransclusionSource {

  function __construct( $spec ) {
    DataTransclusionSource::__construct( $spec );

    $this->url = $spec[ 'url' ];
    $this->dataFormat = @$spec[ 'dataFormat' ];
    $this->dataPath = @$spec[ 'dataPath' ];
    $this->errorPath = @$spec[ 'errorPath' ];
    $this->timeout = @$spec[ 'errorPath' ];

    if ( !$this->dataFormat ) $this->dataFormat = 'php';
    if ( !$this->timeout ) $this->timeout = 5;
  }

  public function fetchRecord( $field, $value ) {
    $raw = $this->loadRecordData( $field, $value );
    if ( !$raw ) return false; //TODO: log error?

    $data = $this->decodeData( $raw, $this->dataFormat );
    if ( !$data ) return false; //TODO: log error?

    $err = $this->extractError( $data );
    if ( $err ) return false; //TODO: log error?
    
    $rec = $this->extractRecord( $data );
    if ( !$rec ) return false; //TODO: log error?

    return $rec;
  }

  protected function getRecordURL( $field, $value ) {
      $u = $this->url;

      if ( strpos( $u, '?' ) === false ) $u .= '?';
      else $u .= '&';

      $u .= $field;
      $u .= '=';
      $u .= urlencode( $value );

      return $u;
  }

  protected function loadRecordData( $field, $value ) {
      $u = $this->getRecordURL( $field, $value );

      $raw = Http::get( $u, $this->timeout );
      return $raw;
  }

  protected function decodeData( $raw, $format = 'php' ) {
      if ( $format == 'json' ) return FormatJson::decode( $raw, true );
      if ( $format == 'wddx' ) return wddx_unserialize( $raw );
      if ( $format == 'php' ) return unserialize( $raw );

      return false;
  }

  protected function extractError( $data ) {
      return $this->extractField( $data, $this->errorPath );
  }

  protected function extractRecord( $data ) {
      return $this->extractField( $data, $this->dataPath );
  }

  protected function extractField( $data, $path ) {
      if ( $path == null ) return $data;
      if ( is_string( $path ) ) return @$data[ $path ];

      foreach ( $path as $p ) {
	  if ( is_object( $data ) ) $data = wfObjectToArray( $data );

	  // meta-key: index in the list of array-keys. 
	  // e.g. use @0 to grab the first value from an assoc array.
	  if ( is_string( $p ) && preg_match( '/^@(\d+)$/', $p, $m ) ) { 
	      $i = (int)$m[1];
	      $k = array_keys( $data );
	      $p = $k[ $i ];
	  }

	  if ( !isset( $data[ $p ] ) ) return false;
	  $data = $data[ $p ];
      }

      return $data;
  }
}
