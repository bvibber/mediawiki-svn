<?php
/**
 * Hybrid parser, and object, takes RSS or Atom feed as a string and returns a
 * simple object.
 * Handles RSS 0.9x, RSS 2.0, RSS 1.0, and Atom 0.3
 *
 * @file
 * @see RSSFetch.php for a simpler interface with integrated caching support
 */
class MagpieRSS {
	public $parser;

	public $current_item = array(); // item currently being parsed
	public $items = array(); // collection of parsed items
	public $channel = array(); // hash of channel fields
	public $textinput = array();
	public $image = array();
	public $feed_type;
	public $feed_version;
	public $encoding = ''; // output encoding of parsed rss

	public $_source_encoding = ''; // only set if we have to parse xml prolog

	public $ERROR = '';
	public $WARNING = '';

	// define some constants
	public $_CONTENT_CONSTRUCTS = array( 'content', 'summary', 'info', 'title', 'tagline', 'copyright' );
	public $_KNOWN_ENCODINGS = array( 'UTF-8', 'US-ASCII', 'ISO-8859-1' );

	// parser variables, useless if you're not a parser, treat as private
	public $stack = array(); // parser stack
	public $inchannel = false;
	public $initem = false;
	public $incontent = false; // if in Atom <content mode="xml"> field
	public $intextinput = false;
	public $inimage = false;
	public $current_namespace = false;

	/**
	 * Set up XML parser, parse source, and return populated RSS object..
	 *
	 * @param $source String: string containing the RSS to be parsed
	 *
	 * NOTE:  Probably a good idea to leave the encoding options alone unless
	 *		 you know what you're doing as PHP's character set support is
	 *		 a little weird.
	 *
	 * NOTE:  A lot of this is unnecessary but harmless with PHP5
	 *
	 *
	 * @param $output_encoding String: output the parsed RSS in this character
	 *								  set defaults to ISO-8859-1 as this is PHP's
	 *								  default.
	 *
	 *								  NOTE: might be changed to UTF-8 in future
	 *								  versions.
	 *
	 * @param $input_encoding String: the character set of the incoming RSS source.
	 *								  Leave blank and Magpie will try to figure it
	 *								  out.
	 *
	 * @param $detect_encoding Boolean: if false, Magpie won't attempt to
	 * 									detect source encoding. (caveat emptor)
	 */
	function __construct( $source, $output_encoding = 'ISO-8859-1',
						$input_encoding = null, $detect_encoding = true )
	{
		# if PHP xml isn't compiled in, die
		if ( !function_exists( 'xml_parser_create' ) ) {
			$this->error(
				"Failed to load PHP's XML Extension. " .
				'http://www.php.net/manual/en/ref.xml.php',
				E_USER_ERROR
			);
		}

		list( $parser, $source ) = $this->create_parser(
			$source,
			$output_encoding,
			$input_encoding,
			$detect_encoding
		);

		if ( !is_resource( $parser ) ) {
			$this->error(
				"Failed to create an instance of PHP's XML parser. " .
				'http://www.php.net/manual/en/ref.xml.php',
				E_USER_ERROR
			);
		}

		$this->parser = $parser;

		# pass in parser, and a reference to this object
		# setup handlers
		xml_set_object( $this->parser, $this );
		xml_set_element_handler(
			$this->parser,
			'feed_start_element',
			'feed_end_element'
		);

		xml_set_character_data_handler( $this->parser, 'feed_cdata' );

		$status = xml_parse( $this->parser, $source );

		if ( !$status ) {
			$errorcode = xml_get_error_code( $this->parser );
			if ( $errorcode != XML_ERROR_NONE ) {
				$xml_error = xml_error_string( $errorcode );
				$error_line = xml_get_current_line_number( $this->parser );
				$error_col = xml_get_current_column_number( $this->parser );
				$errormsg = "$xml_error at line $error_line, column $error_col";

				$this->error( $errormsg );
			}
		}

		xml_parser_free( $this->parser );

		$this->normalize();
	}

	function feed_start_element( $p, $element, &$attrs ) {
		$el = $element = strtolower( $element );
		$attrs = array_change_key_case( $attrs, CASE_LOWER );

		// check for a namespace, and split if found
		$ns = false;
		if ( strpos( $element, ':' ) ) {
			list( $ns, $el ) = explode( ':', $element, 2 );
		}
		if ( $ns && $ns != 'rdf' ) {
			$this->current_namespace = $ns;
		}

		// if feed type isn't set, then this is first element of feed
		// identify feed from root element
		if ( !isset( $this->feed_type ) ) {
			if ( $el == 'rdf' ) {
				$this->feed_type = 'RSS';
				$this->feed_version = '1.0';
			} elseif ( $el == 'rss' ) {
				$this->feed_type = 'RSS';
				$this->feed_version = $attrs['version'];
			} elseif ( $el == 'feed' ) {
				$this->feed_type = 'Atom';
				$this->feed_version = $attrs['version'];
				$this->inchannel = true;
			}
			return;
		}

		if ( $el == 'channel' ) {
			$this->inchannel = true;
		} elseif ( $el == 'item' || $el == 'entry' ) {
			$this->initem = true;
			if ( isset( $attrs['rdf:about'] ) ) {
				$this->current_item['about'] = $attrs['rdf:about'];
			}
		}

		// if we're in the default namespace of an RSS feed,
		// record textinput or image fields
		elseif (
			$this->feed_type == 'RSS' &&
			$this->current_namespace == '' &&
			$el == 'textinput' )
		{
			$this->intextinput = true;
		} elseif (
			$this->feed_type == 'RSS' &&
			$this->current_namespace == '' &&
			$el == 'image' )
		{
			$this->inimage = true;
		}

		// handle Atom content constructs
		elseif ( $this->feed_type == 'Atom' && in_array( $el, $this->_CONTENT_CONSTRUCTS ) ) {
			// avoid clashing w/ RSS mod_content
			if ( $el == 'content' ) {
				$el = 'atom_content';
			}

			$this->incontent = $el;
		}

		// if inside an Atom content construct (e.g. content or summary) field treat tags as text
		elseif ( $this->feed_type == 'Atom' && $this->incontent ) {
			// if tags are inlined, then flatten
			$attrs_str = join(
				' ',
				array_map(
					array( 'MagpieRSS', 'mapAttributes' ),
					array_keys( $attrs ),
					array_values( $attrs )
				)
			);

			$this->append_content( "<$element $attrs_str>" );

			array_unshift( $this->stack, $el );
		}

		// Atom support many links per containging element.
		// Magpie treats link elements of type rel='alternate'
		// as being equivalent to RSS's simple link element.
		elseif ( $this->feed_type == 'Atom' && $el == 'link' ) {
			if ( isset( $attrs['rel'] ) && $attrs['rel'] == 'alternate' ) {
				$link_el = 'link';
			} else {
				$link_el = 'link_' . $attrs['rel'];
			}

			$this->append( $link_el, $attrs['href'] );
		} else { // set stack[0] to current element
			array_unshift( $this->stack, $el );
		}
	}

	function feed_cdata( $p, $text ) {
		if ( $this->feed_type == 'Atom' && $this->incontent ) {
			$this->append_content( $text );
		} else {
			$current_el = join( '_', array_reverse( $this->stack ) );
			$this->append( $current_el, $text );
		}
	}

	function feed_end_element( $p, $el ) {
		$el = strtolower( $el );

		if ( $el == 'item' || $el == 'entry' ) {
			$this->items[] = $this->current_item;
			$this->current_item = array();
			$this->initem = false;
		} elseif ( $this->feed_type == 'RSS' && $this->current_namespace == '' && $el == 'textinput' ) {
			$this->intextinput = false;
		} elseif ( $this->feed_type == 'RSS' && $this->current_namespace == '' && $el == 'image' ) {
			$this->inimage = false;
		} elseif ( $this->feed_type == 'Atom' && in_array( $el, $this->_CONTENT_CONSTRUCTS ) ) {
			$this->incontent = false;
		} elseif ( $el == 'channel' || $el == 'feed' ) {
			$this->inchannel = false;
		} elseif ( $this->feed_type == 'Atom' && $this->incontent ) {
			// balance tags properly
			// note: I don't think this is actually neccessary
			if ( $this->stack[0] == $el ) {
				$this->append_content( "</$el>" );
			} else {
				$this->append_content( "<$el />" );
			}

			array_shift( $this->stack );
		} else {
			array_shift( $this->stack );
		}

		$this->current_namespace = false;
	}

	function concat( &$str1, $str2 = '' ) {
		if ( !isset( $str1 ) ) {
			$str1 = '';
		}
		$str1 .= $str2;
	}

	function append_content( $text ) {
		if ( $this->initem ) {
			$this->concat( $this->current_item[$this->incontent], $text );
		} elseif ( $this->inchannel ) {
			$this->concat( $this->channel[$this->incontent], $text );
		}
	}

	// smart append - field and namespace aware
	function append( $el, $text ) {
		if ( !$el ) {
			return;
		}
		if ( $this->current_namespace ) {
			if ( $this->initem ) {
				$this->concat(
					$this->current_item[$this->current_namespace][$el], $text
				);
			} elseif ( $this->inchannel ) {
				$this->concat(
					$this->channel[$this->current_namespace][$el], $text
				);
			} elseif ( $this->intextinput ) {
				$this->concat(
					$this->textinput[$this->current_namespace][$el], $text
				);
			} elseif ( $this->inimage ) {
				$this->concat(
					$this->image[$this->current_namespace][$el], $text
				);
			}
		} else {
			if ( $this->initem ) {
				$this->concat(
					$this->current_item[$el], $text
				);
			} elseif ( $this->intextinput ) {
				$this->concat(
					$this->textinput[$el], $text
				);
			} elseif ( $this->inimage ) {
				$this->concat(
					$this->image[$el], $text
				);
			} elseif ( $this->inchannel ) {
				$this->concat(
					$this->channel[$el], $text
				);
			}
		}
	}

	function normalize() {
		// if atom populate rss fields
		if ( $this->is_atom() ) {
			$this->channel['description'] = $this->channel['tagline'];
			for ( $i = 0; $i < count( $this->items ); $i++ ) {
				$item = $this->items[$i];
				if ( isset( $item['summary'] ) ) {
					$item['description'] = $item['summary'];
				}
				if ( isset( $item['atom_content'] ) ) {
					$item['content']['encoded'] = $item['atom_content'];
				}

				$atom_date = ( isset( $item['issued'] ) ) ? $item['issued'] : $item['modified'];
				if ( $atom_date ) {
					$epoch = @$this->parse_w3cdtf( $atom_date );
					if ( $epoch && $epoch > 0 ) {
						$item['date_timestamp'] = $epoch;
					}
				}

				$this->items[$i] = $item;
			}
		} elseif ( $this->is_rss() ) {
			$this->channel['tagline'] = $this->channel['description'];
			for ( $i = 0; $i < count( $this->items ); $i++ ) {
				$item = $this->items[$i];
				if ( isset( $item['description'] ) ) {
					$item['summary'] = $item['description'];
				}
				if ( isset( $item['content']['encoded'] ) ) {
					$item['atom_content'] = $item['content']['encoded'];
				}

				if ( $this->is_rss() == '1.0' && isset( $item['dc']['date'] ) ) {
					$epoch = @$this->parse_w3cdtf( $item['dc']['date'] );
					if ( $epoch && $epoch > 0 ) {
						$item['date_timestamp'] = $epoch;
					}
				} elseif ( isset( $item['pubdate'] ) ) {
					$epoch = @strtotime( $item['pubdate'] );
					if ( $epoch > 0 ) {
						$item['date_timestamp'] = $epoch;
					}
				}

				$this->items[$i] = $item;
			}
		}
	}

	function is_rss() {
		if ( $this->feed_type == 'RSS' ) {
			return $this->feed_version;
		} else {
			return false;
		}
	}

	function is_atom() {
		if ( $this->feed_type == 'Atom' ) {
			return $this->feed_version;
		} else {
			return false;
		}
	}

	/**
	 * Instantiate an XML parser.
	 * @return XML parser, and possibly re-encoded source
	 */
	function create_parser( $source, $out_enc, $in_enc, $detect ) {
		// by default PHP5 does a fine job of detecting input encodings
		if( !$detect && $in_enc ) {
			$parser = xml_parser_create( $in_enc );
		} else {
			$parser = xml_parser_create( '' );
		}
		if ( $out_enc ) {
			$this->encoding = $out_enc;
			xml_parser_set_option(
				$parser,
				XML_OPTION_TARGET_ENCODING,
				$out_enc
			);
		}

		return array( $parser, $source );
	}

	/**
	 * Checks if $enc is an encoding type supported by MagpieRSS.
	 * @param $enc String: encoding name
	 * @return String or false
	 */
	function known_encoding( $enc ) {
		$enc = strtoupper( $enc );
		if ( in_array( $enc, $this->_KNOWN_ENCODINGS ) ) {
			return $enc;
		} else {
			return false;
		}
	}

	function error( $errormsg, $lvl = E_USER_WARNING ) {
		// append PHP's error message if track_errors is enabled
		if ( isset( $php_errormsg ) ) {
			$errormsg .= " ($php_errormsg)";
		}

		$notices = E_USER_NOTICE|E_NOTICE;
		if ( $lvl&$notices ) {
			$this->WARNING = $errormsg;
		} else {
			$this->ERROR = $errormsg;
		}
	}

	/**
	 * Parse a W3CDTF date into unix epoch.
	 * This used to be in its own file.
	 * @note http://www.w3.org/TR/NOTE-datetime
	 * @param $date_str String: date string to parse
	 * @return Integer
	 */
	public static function parse_w3cdtf( $date_str ) {
		// regex to match wc3dtf
		$pat = "/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})(:(\d{2}))?(?:([-+])(\d{2}):?(\d{2})|(Z))?/";

		if ( preg_match( $pat, $date_str, $match ) ) {
			list( $year, $month, $day, $hours, $minutes, $seconds ) =
				array( $match[1], $match[2], $match[3], $match[4], $match[5], $match[6] );

			// calculate epoch for current date assuming GMT
			$epoch = gmmktime( $hours, $minutes, $seconds, $month, $day, $year );

			$offset = 0;
			if ( $match[10] == 'Z' ) {
				// zulu time, aka GMT
			} else {
				list( $tz_mod, $tz_hour, $tz_min ) =
					array( $match[8], $match[9], $match[10] );

				// zero out the variables
				if ( !$tz_hour ) {
					$tz_hour = 0;
				}
				if ( !$tz_min ) {
					$tz_min = 0;
				}

				$offset_secs = ( ( $tz_hour * 60 ) + $tz_min ) * 60;

				// is timezone ahead of GMT? then subtract offset
				if ( $tz_mod == '+' ) {
					$offset_secs = $offset_secs * -1;
				}

				$offset = $offset_secs;
			}
			$epoch = $epoch + $offset;
			return $epoch;
		} else {
			return -1;
		}
	}

	public static function mapAttributes( $k, $v ) {
		return "$k=\"$v\"";
	}

} // end class MagpieRSS