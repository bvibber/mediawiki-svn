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
 * Extension of WebDataTransclusionSource that allows to parse and process arbitrary XML.
 *
 * In addition to the options supported by the WebDataTransclusionSource class,
 * XmlDataTransclusionSource accepts some additional options, and changes the convention for others.
 *
 *	 * $spec['dataFormat']: must be "xml" or end with "+xml" if given. Defaults to "xml".
 *	 * $spec['dataPath']: xpath to the actual data in the structure returned from the
 *		HTTP request. This uses standard W3C XPath syntax. REQUIRED.
 *	 * $spec['fieldPathes']: an associative array giving a XPath for each fied which points
 *		to the actual field values inside the record, that is, the structure that 
 *		$spec['dataPath'] resolved to. Useful when field values are returned as complex
 *		records. For more complex processing, override the method flattenRecord().
 *		If given, $spec['fieldNames'] defaults to array_keys( $spec['fieldPathes'] ).
 *	 * $spec['errorPath']: xpath to error messages in the structure returned from the
 *		HTTP request. If an
 *		entry is found at the given position in the response structure, the request
 *		is assumed to have failed. For more complex detection of errors, override
 *		extractError(). REQUIRED.
 *
 * For more information on options supported by DataTransclusionSource and 
 * WebDataTransclusionSource, see the class-level documentation there.
 */
class XmlDataTransclusionSource extends WebDataTransclusionSource {

	function __construct( $spec ) {
		if ( !isset( $spec['dataFormat'] ) ) {
			$spec['dataFormat'] = 'xml';
		}

		if ( !preg_match( '/^(.*\+)?xml$/', $spec['dataFormat'] ) ) {
			throw new MWException( "not a known XML data format: {$spec['dataFormat']}" );
		}

		parent::__construct( $spec );
	}

	public function decodeData( $raw, $format = null ) {
		$dom = new DOMDocument();
		$dom->loadXML( $raw );
		return $dom->documentElement;
	}

	public function resolvePath( $dom, $xpath ) {
		$lookup = new DOMXPath( $dom->ownerDocument );
		$res = $lookup->query( $xpath, $dom );

		if ( $res instanceof DOMNodeList ) {
			if ( $res->length == 0 ) $res = null;
			else $res = $res->item( 0 );
		}

		return $res;
	}

	public function asString( $v ) {
		if ( is_object($v) ) {
			if ( $v instanceof DOMNodeList ) {
				if ( $v->length ) $v = $v->item( 0 ); 
				else $v = null;
			}

			if ( $v instanceof DOMNamedNodeMap ) {
				$v = $v->item( 0 ); 
			}

			if ( $v instanceof DOMNode ) {
				$v = $v->textContent; 
			}
		}

		return "$v";
	}

	public function flattenRecord( $rec ) {
		$rec = parent::flattenRecord( $rec );

		if ( !$rec ) return $rec;

		foreach ( $rec as $k => $v ) {
			if ( is_object($v) ) {
				if ( $v instanceof DOMNodeList ) {
					$v = $v->item( 0 ); 
				}

				if ( $v instanceof DOMNamedNodeMap ) {
					$v = $v->item( 0 ); 
				}

				if ( $v instanceof DOMNode ) {
					$rec[ $k ] = $v->textContent; 
				}
			}
		}

		return $rec;
	}

}
