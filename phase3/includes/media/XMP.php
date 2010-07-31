<?php
/** Class for reading xmp data containing properties relevent to
* images, and spitting out an array that FormatExif accepts.
*
* It should be noted this is not done yet
*
* Note, this is not meant to recognize every possible thing you can
* encode in XMP. It should recognize all the properties we want.
* For example it doesn't have support for structures with multiple
* nesting levels, as none of the properties we're supporting use that
* feature. If it comes across properties it doesn't recognize, it should
* ignore them.
*
* The main methods one would call in this class are
* - parse( $content )
*	Reads in xmp content.
* - getResults
*	Outputs a results array.
*
*/
class XMPReader {

	private $curItem = array();
	private $ancestorStruct = false;
	private $charContent = false;
	private $mode = array();
	private $results = array();
	private $processingArray = false;

	private $xmlParser;

	protected $items; // Contains an array of all properties we try to extract.

	/*
	* These are various mode constants.
	* they are used to figure out what to do
	* with an element when its encoutered.
	*
	* For example, MODE_IGNORE is used when processing
	* a property we're not interested in. So if a new
	* element pops up when we're in that mode, we ignore it.
	*/
	const MODE_INITIAL = 0;
	const MODE_IGNORE  = 1;
	const MODE_LI      = 2;

	// The following MODE constants are also used in the
	// $items array to denote what type of property the item is.
	const MODE_SIMPLE = 3;
	const MODE_STRUCT = 4; // structure (associative array)
	const MODE_SEQ    = 5; // orderd list
	const MODE_BAG    = 6; // unordered list
	const MODE_LANG   = 7; // lang alt. TODO: implement
	const MODE_ALT    = 8; // non-language alt. Currently unused

	const NS_RDF = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';


	/** Constructor.
	*
	* Primary job is to intialize the items array
	* which is used to determine which props to extract.
	*/
	function __construct() {

		/*
		* $this->items keeps a list of all the items
		* we are interested to extract, as well as
		* information about the item like what type
		* it is.
		*
		* Format is an array of namespaces,
		* each containing an array of tags
		* each tag is an array of information about the
		* tag, including:
		* 	* map_group - what group (used for precedence during conflicts)
		*	* mode - What type of item (self::MODE_SIMPLE usually, see above for all values)
		*	* validate - method to validate input. Could also post-process the input. (TODO: implement this)
		*	* choices  - array of potential values (format of 'value' => true )
		*	* children - for MODE_STRUCT items, allowed children.
		*
		* currently this just has a bunch of exif values as this class is only half-done
		*/

		$this->items = array(
			'http://ns.adobe.com/exif/1.0/' => array(
				'ApertureValue' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'BrightnessValue' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'CompressedBitsPerPixel' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'DigitalZoomRatio' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'ExposureBiasValue' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'ExposureIndex' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'ExposureTime' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'FlashEnergy' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'FNumber' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'FocalLength' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'FocalPlaneXResolution' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'FocalPlaneYResolution' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				/* FIXME GPSAltitude */
				'GPSDestBearing' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'GPSDestDistance' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'GPSDOP' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'GPSImgDirection' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'GPSSpeed' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'GPSTrack' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'MaxApertureValue'  => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'ShutterSpeedValue' => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),
				'SubjectDistance'   => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SIMPLE,
					'validate'  => 'validateRational'
				),

				/* Flash */
				'Flash'             => array(
					'mode'      => self::MODE_STRUCT,
					'children'  => array( 
						'Fired'      => true,
						'Function'   => true,
						'Mode'       => true,
						'RedEyeMode' => true,
						'Return'     => true,
					),
				),
				'Fired'             => array(
					'map_group' => 'exif',
					'validate'  => 'validateBoolean',
					'mode'      => self::MODE_SIMPLE
				),
				'Function'          => array(
					'map_group' => 'exif',
					'validate'  => 'validateBoolean',
					'mode'      => self::MODE_SIMPLE,
				),
				'Mode'              => array(
					'map_group' => 'exif',
					'validate'  => 'validateClosed',
					'mode'      => self::MODE_SIMPLE,
					'choices'   => array( '0' => true, '1' => true,
							'2' => true, '3' => true ),
				),
				'Return'            => array(
					'map_group' => 'exif',
					'validate'  => 'validateClosed',
					'mode'      => self::MODE_SIMPLE,
					'choices'   => array( '0' => true,
							'2' => true, '3' => true ),
				),
				'RedEyeMode'        => array(
					'map_group' => 'exif',
					'validate'  => 'validateBoolean',
					'mode'      => self::MODE_SIMPLE,
				),
				/* End Flash */
				'ISOSpeedRatings'    => array(
					'map_group' => 'exif',
					'mode'      => self::MODE_SEQ,
				),
			),
		);

		if ( !function_exists('xml_parser_create_ns') ) {
			// this should already be checked by this point
			throw new MWException('XMP support requires XML Parser');
		}

		$this->xmlParser = xml_parser_create_ns( 'UTF-8', ' ' );
		xml_parser_set_option( $this->xmlParser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option( $this->xmlParser, XML_OPTION_SKIP_WHITE, 1 );

		xml_set_element_handler( $this->xmlParser,
			array( $this, 'startElement' ),
			array( $this, 'endElement' ) );

		xml_set_character_data_handler( $this->xmlParser, array( $this, 'char' ) );
	}

	/** Destroy the xml parser
	*
	* not sure if this is actualy needed.
	*/
	function __destruct() {
		// not sure if this is needed.
		xml_parser_free( $this->xmlParser );
	}

	/** Get the result array
	* @return Array array of results as an array of arrays suitable for
	*	FormatExif.
	*/
	public function getResults() {
		return $this->results;
	}

	/**
	* Main function to call to parse XMP. Use getResults to
	* get results.
	*
	* Also catches any errors during processing, writes them to
	* debug log, blanks result array and returns false.
	*
	* @param String: $content XMP data
	* @return Boolean success.
	* @todo charset detection (usually UTF-8, but UTF-16 or 32 is allowed).
	*/
	public function parse( $content ) {
		try {
			$ok = xml_parse( $this->xmlParser, $content, true );
			if (!$ok) {
				$error = xml_error_string( xml_get_error_code( $this->xmlParser ) );
				$where = 'line: ' . xml_get_current_line_number( $this->xmlParser )
					. ' column: ' . xml_get_current_column_number( $this->xmlParser )
					. ' byte offset: ' . xml_get_current_byte_index( $this->xmlParser );

				wfDebugLog( 'XMP', "XMPReader::parse : Error reading XMP content: $error ($where)");
				$this->results = array(); //blank if error.
				return false;
			}
		} catch (MWException $e) {
			wfDebugLog( 'XMP', 'XMP parse error: ' . $e );
			$this->results = array();
			return false;
		}
		return true;
	}

	/** Character data handler
	* Called whenever character data is found in the xmp document.
	*
	* does nothing if we're in MODE_IGNORE or if the data is whitespace
	* throws an error if we're not in MODE_SIMPLE (as we're not allowed to have character
	* data in the other modes).
	*
	* @param $parser XMLParser reference to the xml parser
	* @param $data String Character data
	* @throws MWException on invalid data
	*/
	function char( $parser, $data ) {

		$data = trim( $data ); 
		if ( trim($data) === "" ) {
			return;
		}

		if ( !isset( $this->mode[0] ) ) {
			throw new MWException('Unexpected character data before first rdf:Description element');
		}

		if ( $this->mode[0] === self::MODE_IGNORE ) return;

		if ( $this->mode[0] !== self::MODE_SIMPLE ) {
			throw new MWException('character data where not expected. (mode ' . $this->mode[0] . ')');
		}

		//to check, how does this handle w.s.
		if ( $this->charContent === false ) {
			$this->charContent = $data;
		} else {
			// I don't think this should happen,
			// but just in case.
			$this->charContent .= $data;
			//FIXME
			wfDebugLog( 'XMP', 'XMP: Consecuitive CDATA');
		}

	}
	/** When we hit a closing element in MODE_IGNORE
	* Check to see if this is the element we started to ignore,
	* in which case we get out of MODE_IGNORE
	*
	* @param $elm String Namespace of element followed by a space and then tag name of element.
	*/
	private function endElementModeIgnore ( $elm ) {
		if ( count( $this->curItem ) == 0 ) {
			// just to be paranoid.
			throw new MWException(' In ignore mode with no curItem');
		}
		if ( $this->curItem[0] === $elm ) {
			array_shift( $this->curItem );
			array_shift( $this->mode );
		}
		return;	

	}
	/** Hit a closing element when in MODE_SIMPLE.
	* This generally means that we finished processing a
	* property value, and now have to save the result to the
	* results array
	*
	* @param $elm String namespace, space, and tag name.
	*/
	private function endElementModeSimple ( $elm ) {
		if ( $this->charContent !== false ) {
			if ( $this->processingArray ) {
				// if we're processing an array, use the original element
				// name instead of rdf:li.
				list($ns, $tag) = explode(' ', $this->curItem[0], 2);
			} else {
				list($ns, $tag) = explode(' ', $elm, 2);
			}
			$this->saveValue( $ns, $tag, $this->charContent );

			$this->charContent = false; //reset
		}
		array_shift( $this->curItem );
		array_shift( $this->mode );

	}
	/** Hit a closing element in MODE_STRUCT, MODE_SEQ, MODE_BAG
	* generally means we've finished processing a nested structure.
	* resets some internal variables to indicate that.
	*
	* Note this means we hit the </closing element> not the </rdf:Seq>.
	*
	* @param $elm String namespace . space . tag name.
	*/
	private function endElementNested( $elm ) {
		if ( $this->curItem[0] !== $elm ) {
			throw new MWException("nesting mismatch. got a </$elm> but expected a </" . $this->curItem[0] . '>');
		}
		array_shift( $this->curItem );
		array_shift( $this->mode );
		$this->ancestorStruct = false;
		$this->processingArray = false;
	}
	/** Hit a closing element in MODE_LI (either rdf:Seq, or rdf:Bag )
	* Just resets some private variables
	*
	* note we still have to hit the outer </property>
	*
	* @param $elm String namespace . ' ' . element name
	*/
	private function endElementModeLi( $elm ) {
		if ( $elm === self::NS_RDF . ' Seq' ) {
			/* fixme, record _format*/
			array_shift( $this->mode );
		} elseif ( $elm === self::NS_RDF . ' Bag' ) {
			array_shift( $this->mode );
		} else {
			throw new MWException( __METHOD__ . " expected <rdf:seq> or <rdf:bag> but instead got $elm." );
		}
	}
	/** Handler for hitting a closing element.
	*
	* generally just calls a helper function depending on what mode we're in.
	* Ignores the outer wrapping elements that are optional in xmp and have no meaning.
	* @param $parser XMLParser
	* @param $elm String namespace . ' ' . element name
	*/
	function endElement( $parser, $elm ) {
		if ( $elm === (self::NS_RDF . ' RDF')
			|| $elm === 'adobe:ns:meta/ xmpmeta' )
		{
			//ignore these.
			return;
		}

		switch( $this->mode[0] ) {
			case self::MODE_IGNORE:
				$this->endElementModeIgnore( $elm );
				break;
			case self::MODE_SIMPLE:
				$this->endElementModeSimple( $elm );
				break;
			case self::MODE_STRUCT:
			case self::MODE_SEQ:
			case self::MODE_BAG:
				$this->endElementNested( $elm );
				break;
			case self::MODE_INITIAL:
				if ( $elm === self::NS_RDF . ' Description' ) {
					array_shift( $this->mode );
				} else {
					throw new MWException('Element ended unexpected while in MODE_INITIAL');
				}
				break;
			case self::MODE_LI:
				$this->endElementModeLi( $elm );
				break;
			default:
				wfDebugLog( 'XMP', __METHOD__ ." no mode (elm = $elm)");
				break;
		}
	}


	/** Hit an opening element while in MODE_IGNORE
	*
	* Mostly ignores, unless we encouter the element that we are ignoring.
	*
	* @param $elm String namespace . ' ' . tag name
	*/
	private function startElementModeIgnore( $elm ) {
		if ( $elm === $this->curItem[0] ) {
			array_unshift( $this->curItem, $elm );
			array_unshift( $this->mode, self::MODE_IGNORE );
		}
	}
	/* Start element in MODE_BAG
	* this should always be <rdf:Bag>
	*
	* @param $elm String namespace . ' ' . tag
	* @throws MWException if we have an element thats not <rdf:Bag>
	*/
	private function startElementModeBag( $elm ) {
		if ( $elm === self::NS_RDF . ' Bag' ) {
			array_unshift( $this->mode, self::MODE_LI );
		} else {
			throw new MWException("Expected <rdf:Bag> but got $elm.");
		}

	}
	/* Start element in MODE_SEQ
	* this should always be <rdf:Seq>
	*
	* @param $elm String namespace . ' ' . tag
	* @throws MWException if we have an element thats not <rdf:Seq>
	*/
	private function startElementModeSeq( $elm ) {
		if ( $elm === self::NS_RDF . ' Seq' ) {
			array_unshift( $this->mode, self::MODE_LI );
		} else {
			throw new MWException("Expected <rdf:Seq> but got $elm.");
		}

	}
	/** Handle an opening element when in MODE_SIMPLE
	* This should not happen often. This is for if a simple element
	* already opened has a child element. Could happen for a
	* qualified element, or if using overly verbose syntax.
	*
	* @param $elm String namespace and tag names seperated by space.
	*/
	private function startElementModeSimple( $elm ) {
		if ( $elm === self::NS_RDF . ' Description' 
			|| $elm === self::NS_RDF . ' value')
		{
			//fixme, better handling of value
			array_unshift( $this->mode, self::MODE_SIMPLE );
			array_unshift( $this->curItem, $this->curItem[0] );
		} else {
			//something else we don't recognize, like a qualifier maybe.
			array_unshift( $this->mode, self::MODE_IGNORE );
			array_unshift( $this->curItem, $elm );

		}

	}
	/** Starting an element when in MODE_INITIAL
	* This usually happens when we hit an element inside
	* the outer rdf:Description
	*
	* This is generally where most props start
	*
	* @param $ns String Namespace
	* @param $tag String tag name (without namespace prefix)
	* @param $attribs Array array of attributes
	*/
	private function startElementModeInitial( $ns, $tag, $attribs ) {
		if ($ns !== self::NS_RDF) {

			if ( isset( $this->items[$ns][$tag] ) ) {
				$mode = $this->items[$ns][$tag]['mode'];
				array_unshift( $this->mode, $mode );
				array_unshift( $this->curItem, $ns . ' ' . $tag );
				if ( $mode === self::MODE_STRUCT ) {
					$this->ancestorStruct = isset( $this->items[$ns][$tag]['map_name'] )
						? $this->items[$ns][$tag]['map_name'] : $tag;	
				}
				if ( $this->charContent !== false ) {
					// Something weird.
					// Should not happen in valid XMP.
					throw new MWException('tag nested in non-whitespace characters.');
				}
			} else {
				array_unshift( $this->mode, self::MODE_IGNORE );
				array_unshift( $this->curItem, $ns . ' ' . $tag );
				return;
			}

		}
		//process attributes
		$this->doAttribs( $attribs );
	}
	/** Hit an opening element when in a Struct (MODE_STRUCT)
	* This is generally for fields of a compound property
	*
	* @param $ns String namespace
	* @param $tag String tag name (no ns)
	* @param $attribs Array array of attribs w/ values.
	*/
	private function startElementModeStruct( $ns, $tag, $attribs ) {
		if ($ns !== self::NS_RDF) {

			if ( isset( $this->items[$ns][$tag] ) ) {
				if ( isset( $this->items[$ns][$this->ancestorStruct]['children'] )
					&& !isset($this->items[$ns][$this->ancestorStruct]['children'][$tag]) )
				{
					//This assumes that we don't have inter-namespace nesting
					//which we don't in all the properties we're interested in.
					throw new MWException(" <$tag> appeared nested in <" . $this->ancestorStruct
						. "> where it is not allowed.");
				}
				array_unshift( $this->mode, $this->items[$ns][$tag]['mode'] );
				array_unshift( $this->curItem, $ns . ' ' . $tag );
				if ( $this->charContent !== false ) {
					// Something weird.
					// Should not happen in valid XMP.
					throw new MWException("tag <$tag> nested in non-whitespace characters (" . $this->charContent . ").");
				}
			} else {
				array_unshift( $this->mode, self::MODE_IGNORE );
				array_unshift( $this->curItem, $elm );
				return;
			}

		}

		if ( $ns === self::NS_RDF && $tag === 'Description' ) {
			$this->doAttribs( $attribs );
		}
	}
	/** opening element in MODE_LI
	* process elements of array's
	*
	* @param $elm String namespace . ' ' . tag
	* @throws MWException if gets a tag other than <rdf:li>
	*/
	private function startElementModeLi( $elm ) {
		if ( $elm !== self::NS_RDF . ' li' ) {
			throw new MWException("<rdf:li> expected but got $elm.");
		}
		array_unshift( $this->mode, self::MODE_SIMPLE );
		//need to add curItem[0] on again since one is for the specific item
		// and one is for the entire group.
		array_unshift( $this->curItem, $this->curItem[0] );
		$this->processingArray = true;
	}

	/** Hits an opening element.
	* Generally just calls a helper based on what MODE we're in.
	* Also does some initial set up for the wrapper element
	*
	* @param $parser XMLParser
	* @param $elm String namespace <space> element
	* @param $attribs Array attribute name => value
	*/
	function startElement( $parser, $elm, $attribs ) {


		if ($elm === self::NS_RDF . ' RDF'
			|| $elm === 'adobe:ns:meta/ xmpmeta' )
		{
			/* ignore */
			return;	
		}

		if ( $elm === self::NS_RDF . ' Description' ) {
			if ( count( $this->mode ) === 0 ) {
				//outer rdf:desc
				array_unshift( $this->mode, self::MODE_INITIAL );
			} else {
				//inner rdf:desc
				// fixme this doesn't handle qualifiers right.
				$this->doAttribs( $attribs );
				return;
			}
		}

		list($ns, $tag) = explode( ' ', $elm, 2 );

		switch( $this->mode[0] ) {
			case self::MODE_IGNORE:
				$this->startElementModeIgnore( $elm );
				break;
			case self::MODE_SIMPLE:
				$this->startElementModeSimple( $elm );
				break;
			case self::MODE_INITIAL:
				$this->startElementModeInitial( $ns, $tag, $attribs );
				break;
			case self::MODE_STRUCT:
				$this->startElementModeStruct( $ns, $tag, $attribs );
				break;
			case self::MODE_BAG:
				$this->startElementModeBag( $elm );
				break;
			case self::MODE_SEQ:
				$this->startElementModeSeq( $elm );
				break;
			case self::MODE_LI:
				$this->startElementModeLi( $elm );
				break;
			default:
				throw new MWException('StartElement in unknown mode: ' . $this->mode[0] );
				break;
		}



	}
	/** process attributes.
	* Simple values can be stored as either a tag or attribute
	*
	* @param $attribs Array attribute=>value array.
	*/
	private function doAttribs( $attribs ) {
		foreach( $attribs as $name => $val ) {
			list($ns, $tag) = explode(' ', $name, 2);
			if ( $ns === self::NS_RDF ) {
				if ( $tag === 'value' || $tag === 'resource' ) {
					//resource is for url.
					// value attribute is a weird way of just putting the contents.
					$this->char( $val );
				}
			} elseif ( isset( $this->items[$ns][$tag] ) ) {
				if ( $this->mode[0] === self::MODE_SIMPLE ) {
					throw new MWException( __METHOD__ 
						. " $ns:$tag found as attribute where not allowed" );
				}
				$this->saveValue( $ns, $tag, $val );
			}
		}
	}
	/** Given a value, save it to results array
	*
	* note also uses $this->ancestorStruct and
	* $this->processingArray to determine what name to
	* save the value under. (in addition to $tag).
	*
	* @param $ns String namespace of tag this is for
	* @param $tag String tag name
	* @param $val String value to save
	*/
	private function saveValue( $ns, $tag, $val ) {

		$info =& $this->items[$ns][$tag];
		$finalName = isset( $info['map_name'] )
			? $info['map_name'] : $tag;
		if ( isset( $info['validate'] ) ) {
			//FIXME
		}

		if ( $this->ancestorStruct ) {
			$this->results['xmp-' . $info['map_group']][$this->ancestorStruct][$finalName] = $val;
		} elseif ( $this->processingArray ) {
			$this->results['xmp-' . $info['map_group']][$finalName][] = $val;	
		} else {
			$this->results['xmp-' . $info['map_group']][$finalName] = $val;
		}
	}


}
