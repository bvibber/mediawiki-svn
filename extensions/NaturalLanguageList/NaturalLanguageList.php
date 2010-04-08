<?php
/**
 * Natural Language List allows for creation of simple lists in 
 * natural languages (e.g. 1, 2, 3, ... n-1 and n), and several 
 * other sophisticated and useful list related functions.
 * 
 * 
 * Copyright (C) 2010 'Svip', 'Happy-melon', and others.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version; or the DWTFYWWI License version 1, 
 * as detailed below.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 * 
 * -----------------------------------------------------------------
 *                          DWTFYWWI LICENSE
 *                      Version 1, January 2006
 *
 * Copyright (C) 2006 Ævar Arnfjörð Bjarmason
 *
 *                        DWTFYWWI LICENSE
 *  TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
 * 0. The author grants everyone permission to do whatever the fuck they
 * want with the software, whatever the fuck that may be.
 * -----------------------------------------------------------------
 */

$wgExtensionCredits['parserhook'][] = array(
	'path'        => __FILE__,
	'name'        => 'Natural Language List',
	'author'      => array( 'Svip', 'Happy-melon', 'Conrad Irwin' ),
	'url'         => 'http://www.mediawiki.org/wiki/Extension:Natural_Language_List',
	'description' => 'Easy formatting of lists in natural languages.',
	'descriptionmsg' => 'nll-desc',
	'version'     => '2.4'
);

$dir = dirname(__FILE__);
$wgExtensionMessagesFiles['NaturalLanguageList'] = "$dir/NaturalLanguageList.i18n.php";
$wgExtensionMessagesFiles['NaturalLanguageListMagic'] = "$dir/NaturalLanguageList.i18n.magic.php";

$wgHooks['ParserFirstCallInit'][] = 'NaturalLanguageList::onParserFirstCallInit';

$wgParserTestFiles[] = dirname( __FILE__ ) . "/nllParserTests.txt";

class NaturalLanguageList {
	
	public static function onParserFirstCallInit( $parser ) {
		$parser->setFunctionHook( 
			'list', 
			array( __CLASS__, 'render' ), 
			SFH_OBJECT_ARGS 
		);
		$parser->setFunctionHook( 
			'rawlist', 
			array ( __CLASS__, 'renderRaw' ), 
			SFH_OBJECT_ARGS 
		);
		return true;
	}
	
	/**
	 * Render {{#list:}}
	 *
	 * @param $parser Parser
	 * @param $frame PPFrame_DOM
	 * @param $args Array
	 * @return wikicode parsed
	 */
	public static function render( $parser, $frame, $args ) {
		if ( count( $args ) == 0 ) {
			return '';
		}
		$obj = new self( $parser, $frame, $args );
		$obj->readOptions( false );
		$obj->readArgs();

		return $obj->outputList();
	}
	
	/**
	 * Render {{#rawlist:}}
	 *
	 * @param $parser Parser
	 * @param $frame PPFrame_DOM
	 * @param $args Array
	 * @return wikicode parsed
	 */
	public static function renderRaw ( $parser, $frame, $args ) {
		if ( count( $args ) == 0 ) {
			return '';
		}
		$obj = new self( $parser, $frame, $args );	
		# get separator between data
		$separator = $obj->mArgs[0];
		$obj->readOptions( true, $separator );
		$obj->readArgs( $separator );

		return $obj->outputList();
	}
	
	private $mParser;
	private $mFrame;
	public $mArgs;	
	private $mSeparator = null;
	private $mOptions = array(
		'fieldsperitem' => -1,     # size of pairs
		'duplicates' => true,      # allow same elements to appear
		'blanks' => false,         # allow blank elements to appear
		'intervals' => true,       # let 'num..num' be parsed as intervals
		'length' => -1,            # length, default no limit
		'itemoutput' => null,      # the format for each element
		'outputseparator' => null, # the separator between output elements
		'lastseparator' => null,   # the separator between the last two elements
	);
	private $mReaditems = array();
	public $mParams = array();
	private $mIgnores = array();

	/**
	 * Constructor
	 * @param $parser Parser
	 * @param $frame PPFrame_DOM
	 * @param $args Array
	 */
	public function __construct( &$parser, &$frame, &$args ){
		$this->mParser = $parser;
		$this->mFrame = $frame;
		$this->mArgs = $args;
	}

	/**
	 * Return $this->mParams formatted as a list according to $this->mOptions
	 */
	private function outputList() {

		# Convert each item from an array into a string according to the format.
		$items = array_map( array( $this, 'formatOutputItem' ), $this->mParams );

		# If there's only one item, there are no separators
		if ( count( $items ) === 1 )
			return $items[0];

		# Otherwise remove the last from the list so that we can implode() the remainder
		$last = array_pop( $items );

		return implode( $this->mOptions['outputseparator'], $items ) . $this->mOptions['lastseparator'] . $last;
	}

	/**
	 * Format the input pairs that make up each output item using the given format
	 *
	 * @param $pair array or string
	 * @return string formatted output
	 */
	private function formatOutputItem( $pair ) {
		return wfMsgReplaceArgs( $this->mOptions['itemoutput'], $pair );
	}

	/**
	 * Create $this->mParams from $this->mReaditems using $this->mOptions.
	 *
	 * @param $separator String [default:null] Input separator (e.g. ',')
	 */
	private function readArgs( $separator=null ) {
		$items = array(); # array of args to include

		# strip read items of duplicate elements if not permitted
		$args = $this->mOptions['duplicates'] 
			? $this->mReaditems 
			: array_unique( $this->mReaditems );

		foreach ( $args as $arg ) {
			if ( !$this->mOptions['blanks'] && $arg === '' )
				continue;
			self::parseArrayItem( $items, $arg, $separator, $this->mOptions['intervals'] );
		}
		
		# Remove the ignored elements from the array
		$items = array_diff( $items, $this->mIgnores );

		# Split the array into smaller arrays, one for each output item.
		$this->mParams = array_chunk( $items, $this->mOptions['fieldsperitem'] );

		# Disgard any leftovers, hrm...
		if ( count( end( $this->mParams ) ) != $this->mOptions['fieldsperitem'] ) {
			array_pop( $this->mParams );
		}
		
		# Remove anything over the set length, if set
		if ( $this->mOptions['length'] != -1 
			&& count( $this->mParams ) > $this->mOptions['length'] ) {
			while ( count( $this->mParams ) > $this->mOptions['length'] )
				array_pop ( $this->mParams );
		}
	}

	/**
	 * Create $this->mOptions and $this->mReaditems from $this->mArgs using $this->mFrame.
	 *
	 * @param $ignorefirst boolean Ignore first element in case of {{#rawlist:}}
	 * @param $separator String [default:null] Input separator
	 */
	private function readOptions ( $ignorefirst, $separator=null ) {
 		$args = $this->mArgs;
 
		# an array of items not options
		$this->mReaditems = array();

		# first input is a bit different than the rest,
		# so we'll treat that differently
		$primary = trim( $this->mFrame->expand( array_shift( $args ) ) );
		if ( !$ignorefirst ) {
			$primary = $this->handleInputItem( $primary );
			if ( $primary !== false ){
				$this->mReaditems[] = $primary;
			}
		}
		# check the rest for options
		foreach( $args as $arg ) {
			$item = $this->handleInputItem( $arg, $separator );
			if ( $item !== false ) {
				$this->mReaditems[] = $item;
			}
		}

		# if fieldsperitem is not set it should be 1, unless itemoutput contains
		# $2 or higher. Do we actually want to continue beyond 9? --conrad
		if ( $this->mOptions['fieldsperitem'] == -1 ) {
			$this->maxDollar = 1;
			if ( $this->mOptions['itemoutput'] !== null ) {
				# set $this->maxDollar to the maxmimum found
				preg_replace_callback( '/\$([1-9][0-9]*)/', 
					array( $this, 'callbackMaxDollar' ), 
				    $this->mOptions['itemoutput'] );
			}
			$this->mOptions['fieldsperitem'] = $this->maxDollar;
		}

		# get default values for lastseparator from outputseparator (if set) or message
		if ( $this->mOptions['outputseparator'] === null ) {

			$this->mOptions['outputseparator'] = wfMsgNoTrans( 'nll-separator' );
			
			if ( $this->mOptions['lastseparator'] === null ) {
				$this->mOptions['lastseparator'] = wfMsgNoTrans( 'nll-lastseparator' );
			}
		# set the last separator to the regular separator if the separator is
		# set and the last separator isn't set specifically
		} else if ( $this->mOptions['lastseparator'] === null ) {
			$this->mOptions['lastseparator'] = $this->mOptions['outputseparator'];
		}
		
		# use the default format if format not set
		if ( $this->mOptions['itemoutput'] === null ) {
			$this->mOptions['itemoutput'] = wfMsgNoTrans( 'nll-itemoutput' );
		}
	}

	/**
	 * Find the highest $n in a string
	 *
	 * @param $m Array (object, number)
	 * @return object
	 */
	private function callbackMaxDollar( $m ) {
		$this->maxDollar = max( $this->maxDollar, $m[1] );
		return $m[0];
	}

	/**
	 * This functions handles individual items found in the arguments,
	 * and decides whether it is an option or not.
	 * If it is, then it handles the option (and applies it).
	 * If it isn't, then it just returns the string it found. 
	 *
	 * @param $arg String Argument
	 * @param $separator String [default:null] Input separator
	 * @return String if element, else return false
	 */
	private function handleInputItem( $arg, $separator=null ) {
		if ( $arg instanceof PPNode_DOM ) {
			$bits = $arg->splitArg();
			$index = $bits['index'];
			if ( $index === '' ) { # Found
				$var = trim( $this->mFrame->expand( $bits['name'] ) );
				$value = trim( $this->mFrame->expand( $bits['value'] ) );
			} else { # Not found
				return trim( $this->mFrame->expand( $arg ) );
			}
		} else {
			$parts = array_map( 'trim', explode( '=', $arg, 2 ) );
			if ( count( $parts ) == 2 ) { # Found "="
				$var = $parts[0];
				$value = $parts[1];
			} else { # Not found
				return $arg;
			}
		}
		# Still here?  Then it must be an option
		switch ( $name = self::parseOptionName( $var ) ) {
			case 'duplicates':
			case 'blanks':
			case 'intervals':
				$this->mOptions[$name] = self::parseBoolean( $value );
				break;
			case 'outputseparator':
			case 'lastseparator':
			case 'itemoutput':
				$this->mOptions[$name] = self::parseString( $value );
				break;
			case 'fieldsperitem':
			case 'length':
				$this->mOptions[$name] = self::parseNumeral( $value );
				break;
			case 'ignore':
				self::parseArrayItem( $this->mIgnores, $value, $separator );
				break;
			case 'data':
				# just strip the parameter and make the $arg
				# the value, let the following case handle its
				# output.
				$arg = $value;
			default:
				# Wasn't an option after all
				return $arg instanceof PPNode_DOM
					? trim( $this->mFrame->expand( $arg ) )
					: $arg;
		}
		return false;
	}

	/**
	 * Using magic to store all known names for each option
	 *
	 * @param $input String
	 * @return The option found; otherwise false
	 */
	private static function parseOptionName( $value ) {

		static $magicWords = null;
		if ( $magicWords === null ) {
			$magicWords = new MagicWordArray( array(
				'nll_blanks', 'nll_duplicates', 
				'nll_fieldsperitem', 'nll_itemoutput',
				'nll_lastseparator', 'nll_outputseparator',
				'nll_ignore', 'nll_data', 'nll_length',
				'nll_intervals',
			) );
		}

		if ( $name = $magicWords->matchStartToEnd( trim($value) ) ) {
			return str_replace( 'nll_', '', $name );
		}
		
		# blimey, so not an option!?
		return false;
	}
	
	/**
	 * Check if a value is an interval (e.g. 0..10) and if it is allowed,
	 * if so, then insert them into the $array, otherwise bail.
	 *
	 * @param $array Array The array with values.
	 * @param $intervals Boolean Whether intervals are allowed.
	 * @param $value Mixed The element to be verified.
	 */
	private static function handle_interval ( &$array, $intervals, $value ) {
		if ( !$intervals )
			return false;
		if ( preg_match("@[0-9]+\.\.[1-9][0-9]*@is", $value ) ) {
			$tmp = explode ( "|", preg_replace("@([0-9]+)\.\.([1-9][0-9]*)@is", "$1|$2", $value) );
			if ( is_numeric($tmp[0])===false or is_numeric($tmp[1])===false or ($tmp[0] > $tmp[1]) )
				return false;
			for ( $i = $tmp[0]; $i <= $tmp[1]; $i++ )
				$array[] = $i;
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Insert a new element into an array.
	 *
	 * @param $array Array The array in question
	 * @param $value Mixed The element to be inserted
	 * @param $separator String [default:null] Input separator
	 * @param $intervals Boolean [default:false] Whether intervals are allowed
	 */
	private static function parseArrayItem( &$array, $value, $separator=null, $intervals = false ) {
		# if no separator, just assume the value can be appended,
		# simple as that
		if ( $separator === null ) {
			if ( ! self::handle_interval( $array, $intervals, $value ) ) 
				$array[] = $value;
		} else {
			# else, let's break the value up and append
			# each 'subvalue' to the array.
			$tmp = explode ( $separator, $value );
			foreach ( $tmp as $v )
				if ( ! self::handle_interval( $array, $intervals, $v ) ) 
					$array[] = $v;
		}
	}
	
	/**
	 * Parse numeral
	 *
	 * @param $value Integer
	 * @param $default Integer [default:1]
	 * @return The integer if integer and above 0, otherwise $default
	 */
	private static function parseNumeral( $value, $default = 1 ) {
		if ( is_numeric( $value ) && $value > 0 ) {
			return floor( $value ); # only integers
		}
		return $default;
	}

	/**
	 * Parse string
	 *
	 * @param $value String
	 * @param $default String [default:null]
	 * @return The string, if none found, return $default
	 */
	private static function parseString( $value, $default = null ) {
		if ( $value !== '' )
			return $value;
		return $default;
	}

	/** 
	 * Parse boolean
	 *
	 * @param $value String
	 * @return true if truth value found; otherwise false
	 */
	private static function parseBoolean( $value ) {
		return in_array( $value, array( 1, true, '1', 'true' ), true );
	}
}
