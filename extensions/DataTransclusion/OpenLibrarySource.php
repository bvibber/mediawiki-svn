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
 * from the OpenLibrary RESTful API.
 *
 * This class provides all necessary $spec options per default. However, some
 * may be specifically useful to override:
 *
 *	 * $spec['url']: use an alternative URL to access the API. The default is
 *		http://openlibrary.org/api/books?bibkeys=ISBN:{isbn}&details=true
 *	 * $spec['httpOptions']: array of options to pass to Http::get. 
 *		For details, see Http::request.
 *	 * $spec['timeout']: seconds before the request times out. If not given,
 *		$spec['httpOptions']['timeout'] is used. If both are not givern, 
 *		5 seconds are assumed.
 *
 * For more information on options supported by DataTransclusionSource and 
 * WebDataTransclusionSource, see the class-level documentation there.
 */
class OpenLibrarySource extends WebDataTransclusionSource {

	function __construct( $spec ) {
		if ( !isset( $spec['url'] ) ) {
			$spec['url'] = 'http://openlibrary.org/api/books?bibkeys=ISBN:{isbn}&details=true';
		}

		if ( !isset( $spec['dataFormat'] ) ) {
			$spec['dataFormat'] = 'json';
		}

		if ( !isset( $spec['errorPath'] ) ) {
			$spec['errorPath'] = '?';
		}

		if ( !isset( $spec['keyFields'] ) ) {
			$spec['keyFields'] = 'isbn';
		}

		if ( !isset( $spec['fieldNames'] ) ) {
			$spec['fieldNames'] = array(
				'author',
				'date',
				'publisher',
				'title',
				'url',
				'city',
				'edition',
				'ISBN10',
				'ISBN13',
				'LCC',
				'LCCN',
				'DDC',
				'pages',
				'series',
				'subtitle',
				'language',
				'editor',
			);
		}

		if ( !isset( $spec['sourceInfo'] ) ) {
			$spec['sourceInfo'] = array();
		}

		if ( !isset( $spec['sourceInfo']['description'] ) ) {
			$spec['sourceInfo']['description'] = 'The Open Library Project';
		}

		if ( !isset( $spec['sourceInfo']['homepage'] ) ) {
			$spec['sourceInfo']['homepage'] = 'http://openlibrary.org';
		}

		if ( !isset( $spec['sourceInfo']['license'] ) ) {
			$spec['sourceInfo']['license'] = 'PD';
		}

		WebDataTransclusionSource::__construct( $spec );
	}

	public function flattenRecord( $rec ) {
		$r = array();

		$r['date'] = @$rec['details']['publish_date']; //TODO: split into year/month/day

		$r['title'] = $rec['details']['title'];
		if ( @$rec['details']['title_prefix'] ) {
			$r['title'] = trim( $rec['details']['title_prefix'] ) 
					. ' ' . trim( $r['title'] );
		}

		$r['url'] = $rec['info_url'];

		$r['pages'] = @$rec['details']['number_of_pages'];
		$r['edition'] = @$rec['details']['edition_name'];

		$r['publisher'] = "";
		if ( isset( $rec['details']['publishers'] ) ) {
			foreach ( $rec['details']['publishers'] as $publisher ) {
				if ( $r['publisher'] != "" ) $r['publisher'] .= '; ';
				$r['publisher'] .= $publisher;
			}
		}

		$r['author'] = "";
		if ( isset( $rec['details']['authors'] ) ) {
			foreach ( $rec['details']['authors'] as $author ) {
				if ( $r['author'] != "" ) $r['author'] .= ', ';

				if ( $author['key'] != "/authors/OL2693863A" ) { //"Journal" is not a real author.
					$r['author'] .= $author['name'];
				}
			}
		}

		$r['editor'] = "";
		if ( isset( $rec['details']['editors'] ) ) {
			foreach ( $rec['details']['editors'] as $editor ) {
				if ( $r['editor'] != "" ) $r['editor'] .= ', ';
				$r['editor'] .= $editor;
			}
		}

		if ( empty( $r['author'] ) && empty( $r['editor'] ) ) {
			if ( isset( $rec['details']['by_statement'] ) ) {
				$r['author'] = $rec['details']['by_statement']; //XXX ugly...
			}
		}

		$r['city'] = "";
		if ( isset( $rec['details']['publish_places'] ) ) {
			foreach ( $rec['details']['publish_places'] as $place ) {
				if ( $r['city'] != "" ) $r['city'] .= '/';
				$r['city'] .= $place;
			}
		}

		$r['LCC'] = "";
		if ( isset( $rec['details']['lc_classifications'] ) ) {
			foreach ( $rec['details']['lc_classifications'] as $place ) {
				if ( $r['LCC'] != "" ) $r['LCC'] .= ' / ';
				$r['LCC'] .= $place;
			}
		}

		$r['DDC'] = "";
		if ( isset( $rec['details']['dewey_decimal_class'] ) ) {
			$r['DDC'] .= $rec['details']['dewey_decimal_class'][ 0 ];
		}
	      
		$r['LCCN'] = "";
		if ( isset( $rec['details']['lccn'] ) ) {
			$r['LCCN'] .= $rec['details']['lccn'][ 0 ];
		}
	      
		$r['ISBN10'] = "";
		if ( isset( $rec['details']['isbn_10'] ) ) {
			$r['ISBN10'] .= $rec['details']['isbn_10'][ 0 ];
		}
	      
		$r['ISBN13'] = "";
		if ( isset( $rec['details']['isbn_13'] ) ) {
			$r['ISBN13'] .= $rec['details']['isbn_13'][ 0 ];
		}
	      
		$r['series'] = "";
		if ( isset( $rec['details']['series'] ) ) {
			$r['series'] .= $rec['details']['series'][ 0 ];
		}
	      
		$r['language'] = "";
		if ( isset( $rec['details']['languages'] ) ) {
			$r['language'] .= $rec['details']['languages'][ 0 ][ 'key' ];
			$r['language'] = preg_replace( '!^.*/!', '', $r['language'] );
		}
	      
		return $r;
	}

	public function extractRecord( $data ) {
		$data = array_values( $data );

		$rec = $data[0];
		$rec = $this->flattenRecord( $rec );

		return $rec;
	}
}
