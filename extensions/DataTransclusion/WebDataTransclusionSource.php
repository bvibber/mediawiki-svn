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
* Implementations of DataTransclusionSource, fetching data records via HTTP, 
* usually from an web API. 
*
* In addition to the options supported by the DataTransclusionSource base class, 
* WebDataTransclusionSource accepts some additional options
*
*	* $spec['url']: base URL for building urls for retrieving individual records.
*		The key/value pair is appended to the URL as a regular URL
*		parameter (preceeded by ? or &, as appropriate). For more 
*		complex rules for building the url, override getRecordURL(). REQUIRED.
*	* $spec['dataFormat']: Serialization format returned from the web service. 
*		Supported values are 'php' for PHP serialization format, 'json'
*		for JavaScript syntax, and 'wddx' for XML-based list/dicts.
*		To support more formats, override decodeData(). Default is 'php'.
*	* $spec['dataPath']: "path" to the actual data in the structure returned from the
*		HTTP request. The response data is assumed to consit of nested arrays. Each entry
*		In the path navigates one step in this structure. Each entry can be either a
*		string (for a lookup in an associative array), and int (an index in a list), or
*		a "meta-key" of the form @@N, where N is an integer. A meta-key refers to the 
*		Nth entry in an associative array: @1 would be "bar" in array( 'x' => "foo", 'y' => "bar" ).
*		For more complex retrieval of the record, override extractRecord(). REQUIRED.
*	* $spec['errorPath']: "path" to error messages in the structure returned from the
*		HTTP request. The path is evaluated as deswcribed for $spec['dataPath']. If an 
*		entry is found at the given position in the response structure, the request
*		is assumed to have failed. For more complex detection of errors, override 
*		extractError(). REQUIRED.
*	* $spec['httpOptions']: array of options to pass to Http::get. For details, see Http::request.
*	* $spec['timeout']: seconds before the request times out. If not given, 
*		$spec['httpOptions']['timeout'] is used. If both are not givern, 5 seconds are assumed.
*
* For more information on options supported by DataTransclusionSource, see the class-level
* documentation there.
*/
class WebDataTransclusionSource extends DataTransclusionSource {

  function __construct( $spec ) {
    DataTransclusionSource::__construct( $spec );

    $this->url = $spec[ 'url' ];
    $this->dataFormat = @$spec[ 'dataFormat' ];
    $this->dataPath = DataTransclusionSource::splitList( @$spec[ 'dataPath' ] );
    $this->errorPath = DataTransclusionSource::splitList( @$spec[ 'errorPath' ] );
    $this->httpOptions = @$spec[ 'httpOptions' ];
    $this->timeout = @$spec[ 'timeout' ];

    if ( !$this->dataFormat ) $this->dataFormat = 'php';
    if ( !$this->timeout ) $this->timeout = &$this->httpOptions[ 'timeout' ];
    if ( !$this->timeout ) $this->timeout = 5;
  }

  public function fetchRecord( $field, $value ) {
    $raw = $this->loadRecordData( $field, $value ); //TESTME
    if ( !$raw ) return false; //TODO: log error?

    $data = $this->decodeData( $raw, $this->dataFormat ); //TESTME
    if ( !$data ) return false; //TODO: log error?

    $err = $this->extractError( $data ); //TESTME
    if ( $err ) return false; //TODO: log error?
    
    $rec = $this->extractRecord( $data ); //TESTME
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

      $raw = Http::get( $u, $this->timeout, $this->httpOptions );
      return $raw;
  }

  protected function decodeData( $raw, $format = 'php' ) {
      if ( $format == 'json' ) return FormatJson::decode( $raw, true ); //TESTME
      if ( $format == 'wddx' ) return wddx_unserialize( $raw ); //TESTME
      if ( $format == 'php' ) return unserialize( $raw ); //TESTME

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
