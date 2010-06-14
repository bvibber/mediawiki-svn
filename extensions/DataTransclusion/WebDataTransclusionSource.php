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
 *	 * $spec['valuePath']: "path" to the actual field values inside the record associated
 *		with each field. Optional, should only be specified if field values are returned
 *		as complex records instead of simple values. For more complex processing, override
 *		the method sanitizeRecord().
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
		DataTransclusionSource::__construct( $spec );

		$this->url = $spec[ 'url' ];
		$this->dataFormat = @$spec[ 'dataFormat' ];
		$this->dataPath = DataTransclusionSource::splitList( @$spec[ 'dataPath' ] );
		$this->valuePath = DataTransclusionSource::splitList( @$spec[ 'valuePath' ] );
		$this->errorPath = DataTransclusionSource::splitList( @$spec[ 'errorPath' ] );
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
		return $this->extractField( $data, $this->errorPath );
	}

	public function extractRecord( $data ) {
		$rec = $this->extractField( $data, $this->dataPath );

		$rec = $this->sanitizeRecord( $rec );
		return $rec;
	}

	public function sanitizeRecord( $rec ) {
		if ( $this->valuePath !== null && $this->valuePath !== false ) {
			$r = array();

			foreach ( $rec as $k => $v ) {
				if ( is_array( $v ) || is_object( $v ) ) {
					$w = $this->extractField( $v, $this->valuePath );
					//XXX: how to hanlde $w === false failures here?
				} else {
					$w = $v; //XXX: ugly default. fail instead??
				}

				$r[ $k ] = $w; 
			}

			return $r;
		} else {
			return $rec;
		}
	}

	public function extractField( $data, $path ) {
		if ( $path == null ) {
			return $data;
		}

		if ( is_string( $path ) ) {
			return @$data[ $path ];
		}

		foreach ( $path as $p ) {
			if ( is_object( $data ) ) {
				$data = wfObjectToArray( $data );
			}

			// meta-key: index in the list of array-keys.
			// e.g. use @0 to grab the first value from an assoc array.
			if ( is_string( $p ) && preg_match( '/^@(\d+)$/', $p, $m ) ) {
				$i = (int)$m[1];
				$k = array_keys( $data );
				$p = $k[ $i ];
			}

			if ( !isset( $data[ $p ] ) ) {
				return false;
			}

			$data = $data[ $p ];
		}

		return $data;
	}
}
