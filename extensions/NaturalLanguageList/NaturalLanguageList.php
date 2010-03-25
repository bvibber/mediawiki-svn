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
	'name'        => 'Natural Language List',
	'author'      => array( 'Svip', 'Happy-melon', 'Conrad Irwin' ),
	'url'         => 'http://www.mediawiki.org/wiki/Extension:Natural_Language_List',
	'description' => 'Easy formatting of lists in natural languages.',
	'version'     => '2.2'
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

	public static function render( $parser, $frame, $args ) {
		if ( count( $args ) == 0 ) {
			return '';
		}
		$obj = new self( $parser, $frame, $args );
		$obj->readOptions( false );
		$obj->readArgs();

		return $obj->outputList();
	}

	public static function renderRaw ( $parser, $frame, $args ) {
		if ( count( $args ) == 0 ) {
			return '';
		}

		$obj = new self( $parser, $frame, $args );
		
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
		'fieldsperitem' => -1,
		'duplicates' => true,
		'blanks' => false,
		'itemoutput' => null,
		'outputseparator' => null,
		'lastseparator' => null,
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

		// Convert each item from an array into a string according to the format.
		$items = array_map( array( $this, 'formatOutputItem' ), $this->mParams );

		// If there's only one item, there are no separators
		if ( count( $items ) === 1 )
			return $items[0];

		// Otherwise remove the last from the list so that we can implode() the remainder
		$last = array_pop( $items );

		return implode( $this->mOptions['outputseparator'], $items ) . $this->mOptions['lastseparator'] . $last;
	}

	// Format the input pairs that make up each output item using the given format
	private function formatOutputItem( $pair ) {
		return wfMsgReplaceArgs( $this->mOptions['itemoutput'], $pair );
	}

	/**
	 * Create $this->mParams from $this->mReaditems using $this->mOptions.
	 */
	private function readArgs( $separator=null ) {
		$items = array(); # array of args to include

		$args = $this->mOptions['duplicates'] ? $this->mReaditems : array_unique( $this->mReaditems );

		foreach ( $args as $arg ) {
			if ( !$this->mOptions['blanks'] && $arg === '' )
				continue;
			self::parseArrayItem( $items, $arg, $separator );
		}
		
		// Remove the ignored elements from the array
		$items = array_diff( $items, $this->mIgnores );

		// Split the array into smaller arrays, one for each output item.
		$this->mParams = array_chunk( $items, $this->mOptions['fieldsperitem'] );

		// Disgard any leftovers, hrm...
		if ( count( end( $this->mParams ) ) != $this->mOptions['fieldsperitem'] ) {
			array_pop( $this->mParams );
		}

	}

	/**
	 * Create $this->mOptions and $this->mReaditems from $this->mArgs using $this->mFrame.
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
				preg_replace_callback( '/\$([1-9][0-9]*)/', array( $this, 'callbackMaxDollar' ), 
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

		} else if ( $this->mOptions['lastseparator'] === null ) {
			$this->mOptions['lastseparator'] = $this->mOptions['outputseparator'];
		}

		if ( $this->mOptions['itemoutput'] === null ) {
			$this->mOptions['itemoutput'] = wfMsgNoTrans( 'nll-itemoutput' );
		}
	}

	// Used to find the highest $n in a string
	private function callbackMaxDollar( $m ) {
		$this->maxDollar = max( $this->maxDollar, $m[1] );
		return $m[0];
	}

	/**
	 * This functions handles individual items found in the arguments,
	 * and decides whether it is an option or not.
	 * If it is, then it handles the option (and applies it).
	 * If it isn't, then it just returns the string it found. 
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
				$this->mOptions[$name] = self::parseBoolean( $value );
				break;
			case 'outputseparator':
			case 'lastseparator':
			case 'itemoutput':
				$this->mOptions[$name] = self::parseString( $value );
				break;
			case 'fieldsperitem':
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

	private static function parseOptionName( $value ) {

		static $magicWords = null;
		if ( $magicWords === null ) {
			$magicWords = new MagicWordArray( array(
				'nll_blanks', 'nll_duplicates', 
				'nll_fieldsperitem', 'nll_itemoutput',
				'nll_lastseparator', 'nll_outputseparator',
				'nll_ignore', 'nll_data'
			) );
		}

		if ( $name = $magicWords->matchStartToEnd( trim($value) ) ) {
			return str_replace( 'nll_', '', $name );
		}

		return false;
	}
	
	private static function parseArrayItem( &$array, $value, $separator=null ) {
		if ( $separator === null ) {
			$array[] = $value;
		} else {
			$tmp = explode ( $separator, $value );
			foreach ( $tmp as $v )
				$array[] = $v;
		}
	}

	private static function parseNumeral( $value, $default = 1 ) {
		if ( is_numeric( $value ) && $value > 0 ) {
			return floor( $value ); # only integers
		}
		return $default;
	}

	private static function parseString( $value, $default = null ) {
		if ( $value !== '' )
			return $value;
		return $default;
	}

	private static function parseBoolean( $value ) {
		return in_array( $value, array( 1, true, '1', 'true' ), true );
	}
}
