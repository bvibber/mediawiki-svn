<?php
/**
 * DataTransclusion Source implementation
 *
 * @file
 * @ingroup Extensions
 * @author Daniel Kinzler for Wikimedia Deutschland
 * @copyright © 2010 Wikimedia Deutschland (Author: Daniel Kinzler)
 * @licence GNU General Public Licence 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
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
 *	 * $spec['url']: base URL for building urls for retrieving individual records.
 *		If the URL contains placeholders of the form {xxx}, these get replaced 
 *		by the respective key or option values.
 *		Otherwise, the key/value pair and options get appended to the URL as a 
 *		regular URL parameter (preceeded by ? or &, as appropriate). For more
 *		complex rules for building the url, override getRecordURL(). REQUIRED.
 *	 * $spec['dataFormat']: Serialization format returned from the web service.
 *		Supported values are 'php' for PHP serialization format, 'json'
 *		for JavaScript syntax, and 'wddx' for XML-based list/dicts.
 *		To support more formats, override decodeData(). Default is 'php'.
 *	 * $spec['dataPath']: "path" to the actual data in the structure returned from the
 *		HTTP request. The response data is assumed to consit of nested arrays. Each entry
 *		In the path navigates one step in this structure. Each entry can be either a
 *		string (for a lookup in an associative array), and int (an index in a list), or
 *		a "meta-key" of the form @@N, where N is an integer. A meta-key refers to the
 *		Nth entry in an associative array: @1 would be "bar" in array( 'x' => "foo", 'y' => "bar" ).
 *		For more complex retrieval of the record, override extractRecord(). REQUIRED.
 *	 * $spec['fieldPathes']: an associative array giving a "path" for each fied which points
 *		to the actual field values inside the record, that is, the structure that 
 *		$spec['dataPath'] resolved to. Useful when field values are returned as complex
 *		records. For more complex processing, override the method flattenRecord().
 *		If given, $spec['fieldNames'] defaults to array_keys( $spec['fieldPathes'] ).
 *	 * $spec['fieldNames']: names of all fields present in each record.
 *		Fields not listed here will not be available on the wiki,
 *		even if they are returned by the data source. Required if fieldPathes is not given.
 *	 * $spec['errorPath']: "path" to error messages in the structure returned from the
 *		HTTP request. The path is evaluated as deswcribed for $spec['dataPath']. If an
 *		entry is found at the given position in the response structure, the request
 *		is assumed to have failed. For more complex detection of errors, override
 *		extractError(). REQUIRED.
 *	 * $spec['httpOptions']: array of options to pass to Http::get. For details, see Http::request.
 *	 * $spec['timeout']: seconds before the request times out. If not given,
 *		$spec['httpOptions']['timeout'] is used. If both are not givern, 5 seconds are assumed.
 *
 * For more information on options supported by DataTransclusionSource, see the class-level
 * documentation there.
 */
class WebDataTransclusionSource extends DataTransclusionSource {

	function __construct( $spec ) {
		if ( !isset( $spec['fieldNames'] ) && isset( $spec['fieldPathes'] ) ) {
			$spec['fieldNames'] = array_keys( $spec['fieldPathes'] );
		}

		DataTransclusionSource::__construct( $spec );

		$this->url = $spec[ 'url' ];
		$this->dataPath = @$spec[ 'dataPath' ];
		$this->errorPath = @$spec[ 'errorPath' ];
		$this->dataFormat = @$spec[ 'dataFormat' ];
		$this->fieldPathes = @$spec[ 'fieldPathes' ];
		$this->httpOptions = @$spec[ 'httpOptions' ];
		$this->timeout = @$spec[ 'timeout' ];

		if ( !$this->dataFormat ) {
			$this->dataFormat = 'php';
		}

		if ( !$this->timeout ) {
			$this->timeout = &$this->httpOptions[ 'timeout' ];
		}

		if ( !$this->timeout ) {
			$this->timeout = 5;
		}
	}

	public function fetchRecord( $field, $value, $options = null ) {
		$raw = $this->loadRecordData( $field, $value, $options ); 
		if ( !$raw ) {
			wfDebugLog( 'DataTransclusion', "failed to fetch data for $field=$value\n" );
			return false; 
		}

		$data = $this->decodeData( $raw, $this->dataFormat ); 
		if ( !$data ) {
			wfDebugLog( 'DataTransclusion', "failed to decode data for $field=$value as {$this->dataFormat}\n" );
			return false; 
		}

		$err = $this->extractError( $data ); 
		if ( $err ) {
			wfDebugLog( 'DataTransclusion', "error message when fetching $field=$value: $err\n" );
			return false; 
		}

		$rec = $this->extractRecord( $data ); 
		if ( !$rec ) {
			wfDebugLog( 'DataTransclusion', "no record found in data for $field=$value\n" );
			return false; 
		}

		wfDebugLog( 'DataTransclusion', "loaded record for $field=$value from URL\n" );
		return $rec;
	}

	public function getRecordURL( $field, $value, $options = null ) {
		$u = $this->url;

		$args = array( $field => $value );

		if ( $options ) {
			$args = array_merge( $options, $args );
		} 

		foreach ( $args as $k => $v ) {
			$u = str_replace( '{'.$k.'}', urlencode( $v ), $u, $n );

			if ( $n ) { //was found and replaced
				unset( $args[ $k ] );
			}
		}

		$u = preg_replace( '/\{.*?\}/', '', $u ); //strip remaining placeholders

		foreach ( $args as $k => $v ) {
			if ( strpos( $u, '?' ) === false ) {
				$u .= '?';
			} else {
				$u .= '&';
			}

			$u .= urlencode( $k );
			$u .= '=';
			$u .= urlencode( $v );
		}

		return $u;
	}

	public function loadRecordData( $field, $value, $options ) {
		$u = $this->getRecordURL( $field, $value, $options );
		return $this->loadRecordDataFromURL( $u );
	}

	public function loadRecordDataFromURL( $u ) {
		if ( preg_match( '!^https?://!', $u ) ) {
			$raw = Http::get( $u, $this->timeout, $this->httpOptions );
		} else {
			$raw = file_get_contents( $u ); 
		}

		if ( $raw ) {
			wfDebugLog( 'DataTransclusion', "loaded " . strlen( $raw ) . " bytes of data from $u\n" );
		} else {
			wfDebugLog( 'DataTransclusion', "failed to load data from $u\n" );
		}

		return $raw;
	}

	public function decodeData( $raw, $format = null ) {
		if ( $format === null ) {
			$format = $this->dataFormat;
		}

		if ( $format == 'json' || $format == 'js' ) {
			$raw = preg_replace( '/^\s*(var\s)?\w([\w\d]*)\s+=\s*|\s*;\s*$/sim', '', $raw);
			return FormatJson::decode( $raw, true ); 
		}

		if ( $format == 'wddx' ) {
			return wddx_unserialize( $raw ); 
		}

		if ( $format == 'php' || $format == 'pser' ) {
			return unserialize( $raw ); 
		}

		return false;
	}

	public function extractError( $data ) {
		$err = $this->resolvePath( $data, $this->errorPath );

		$err = $this->asString( $err );
		return $err;
	}

	public function extractRecord( $data ) {
		$rec = $this->resolvePath( $data, $this->dataPath );

		$rec = $this->flattenRecord( $rec );
		return $rec;
	}

	public function asString( $value ) {
		return "$value"; //XXX: will often fail. we could just throw here for non-primitives?
	}

	public function flattenRecord( $rec ) {
		if ( !$rec ) return $rec;

		if ( $this->fieldPathes ) {
			$r = array();

			foreach ( $this->fieldNames as $k ) {
				if ( isset( $this->fieldPathes[$k] ) ) { 
					$path = $this->fieldPathes[$k];
					$v = $this->resolvePath( $rec, $path );
				} else {
					$v = $rec[ $k ];
				}

				$r[ $k ] = $v; 
			}

			return $r;
		} else {
			return $rec;
		}

		foreach ( $rec as $k => $v ) {
			if ( !is_null( $v ) && !is_string( $v ) && !is_int( $v ) ) {
				$rec[ $k ] = $this->asString( $v ); 
			}
		}
	}

	public function resolvePath( $data, $path, $split = true ) {
		if ( is_object( $data ) ) {
			$data = wfObjectToArray( $data );
		}

		if ( !is_array( $data ) || $path === '.' ) {
			return $data; 
		}

		if ( $split && is_string( $path ) ) {
			$path = DataTransclusionSource::splitList( $path, '/' );
		}

		if ( is_string( $path ) || is_int( $path ) ) {
			return @$data[ $path ];
		}

		if ( !$path ) {
			return $data; 
		}

		$p = array_shift( $path );

		if ( is_string( $p ) && preg_match( '/^(@)?(\d+)$/', $p, $m ) ) { //numberic index
			$i = (int)$m[2];

			if ( $m[1] ) { //meta-index
				$k = array_keys( $data );
				$p = $k[ $i ];
			}
		} 

		if ( !isset( $data[ $p ] ) ) {
			return false;
		}

		$next = $data[ $p ];

		if ( $next && $path ) {
			return $this->resolvePath( $next, $path );
		} else {
			return $next;
		}

		//TODO: named components. separator??
	}
}
