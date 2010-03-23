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
	'author'      => array( 'Svip', 'Happy-melon' ),
	'url'         => 'http://www.mediawiki.org/wiki/Extension:Natural_Language_List',
	'description' => 'Easy formatting of lists in natural languages.',
	'version'     => '2.0'
);

$dir = dirname(__FILE__);
$wgExtensionMessagesFiles['NaturalLanguageList'] = "$dir/NaturalLanguageList.i18n.php";

$wgHooks['ParserFirstCallInit'][] = 'NaturalLanguageList::onParserFirstCallInit';
$wgHooks['LanguageGetMagic'][] = 'NaturalLanguageList::onLanguageGetMagic';

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

	public static function onLanguageGetMagic( &$magicWords, $langCode ) {
		$magicWords['list'] = array( 0, 'list' );
		$magicWords['rawlist'] = array( 0, 'rawlist' );
		return true;
	}

	public static function render( $parser, $frame, $args ) {
		if ( count( $args ) == 0 ) {
			return '';
		}
		$obj = new self( $parser, $frame, $args );

		wfLoadExtensionMessages( 'NaturalLanguageList' );

		$obj->readOptions( false );
		$obj->readArgs();
		$obj->removedIgnored();

		return $obj->outputList();
	}

	public static function renderRaw ( $parser, $frame, $args ) {
		if ( count( $args ) == 0 ) {
			return '';
		}
		$obj = new self( $parser, $frame, $args );

		wfLoadExtensionMessages( 'NaturalLanguageList' );

		$obj->readOptions( true );
		$obj->readArgs();

		$separator = $obj->mArgs[0];
		$tmp = array();
		foreach ( $obj->mParams as $arg ) {
			$tmp = array_merge ( $tmp, explode( $separator, $arg ) );
		}
		$obj->mParams = $tmp;

		$obj->removedIgnored();
		return $obj->outputList();
	}

	private $mParser;
	private $mFrame;
	public $mArgs;
	private $mOptions = array(
		'fieldsperitem' => 1,
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

	private function outputList() {
		$length = count( $this->mParams );
		if ( $length == 1 ) {
			if ( $this->mOptions['fieldsperitem'] > 1 ) {
				return wfMsgReplaceArgs( $this->mOptions['itemcover'], $this->mParams[0] );
			} else {
				return $this->mOptions['itemoutput'] === null 
					? wfMsg ( 'nll-itemoutput' , $this->mParams[0] ) 
					: wfMsgReplaceArgs( $this->mOptions['itemoutput'], array( $this->mParams[0] ) );
			}
		}
		$str = '';
		foreach( $this->mParams as $i => $param ) {
			if ( $this->mOptions['fieldsperitem'] > 1 ) {
				$str .= wfMsgReplaceArgs( $this->mOptions['itemcover'], $param );
			} else {
				$str .= $this->mOptions['itemoutput'] === null 
					? wfMsg ( 'nll-itemoutput' , $param ) 
					: wfMsgReplaceArgs( $this->mOptions['itemoutput'], array( $param ) );
			}
			if ( $i == $length-1 ) {
 
			} elseif ( $i == $length-2 ) {
				$str .= ( $this->mOptions['lastseparator'] === null 
					? ( $this->mOptions['outputseparator'] === null 
						? wfMsg('nll-lastseparator') 
						: $this->mOptions['outputseparator'] ) 
					: $this->mOptions['lastseparator'] );
			} else {
				$str .= ( $this->mOptions['outputseparator'] === null 
					? wfMsg('nll-separator') 
					: $this->mOptions['outputseparator'] );
			}
		}
		return $str;
	}

	private function removedIgnored() {
		# if there are ignores, strip the params array of those.
		if ( count( $this->mIgnores ) > 0 ) {
			if ( $this->mOptions['fieldsperitem'] > 1 ) {
				$tmpparams = array();
				$tmp = array();
				foreach ( $this->mParams as $pair ) {
					foreach ( $pair as $param ) {
						if ( in_array ( $param, $this->mIgnores ) ){
							continue;
						}
						$tmp[] = $param;
						if ( count($tmp) == $this->mOptions['fieldsperitem'] ) {
							$tmpparams[] = $tmp;
							$tmp = array();
						}
					}
				}
				$this->mParams = $tmpparams;
			} else {
				$tmp = array();
				foreach ( $this->mParams as $param ) {
					if ( in_array( $param, $this->mIgnores ) ){
						continue;
					}
					$tmp[] = $param;
				}
				$this->mParams = $tmp;
			}
		}	
	}

	private function readArgs() {
		$ignoreblock = false;
		$i = 0; # increment for every param
		$j = 0; # pair check
		foreach( $this->mReaditems as $arg ){
			if ( !$this->mOptions['duplicates'] 
				&& in_array( $arg, $this->mParams ) )
			{
					continue;
			}
			if ( !$this->mOptions['blanks'] && $arg === '' ){
				continue;
			}
			if ( substr( $arg, 0, 6 ) === '#data:' ) {
				$ignoreblock = false;
				$arg = explode( ':', $arg, 2 );
				$arg = $arg[1];
			} elseif ( substr( $arg, 0, 8 ) === '#ignore:') {
				$ignoreblock = true;
				$arg = explode( ':', $arg, 2 );
				$arg = $arg[1];
			}
			if ( $this->mOptions['fieldsperitem'] > 1 ) {
				if ( $ignoreblock ) {
					$this->mIgnores[] = $arg;
				} else {
					if ( $j >= $this->mOptions['fieldsperitem'] ) {
						$this->mParams[] = $pair;
						$j = 0;
					}
					if ( $j == 0 ) {
						$pair = array();
					}
					$pair[] = $arg;
				}
			} else {
				if ( $ignoreblock ){
					$this->mIgnores[] = $arg;
				} else {
					$this->mParams[] = $arg;
				}
			}
			$i++; $j++;
		}
		if ( $this->mOptions['fieldsperitem'] > 1 
			&& ( count($pair) == $this->mOptions['fieldsperitem'] ) )
		{
			$this->mParams[] = $pair;
		}
	}

	private function readOptions ( $ignorefirst ) {
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
			$item = $this->handleInputItem( $arg );
			if ( $item !== false ) {
				$this->mReaditems[] = $item;
			}
		}
		# we need to check for a special case with fieldsperitem;
		# it cannot be set if itemoutput is not set
		if ( $this->mOptions['fieldsperitem'] > 1 
			&& $this->mOptions['itemoutput'] === null )
		{
			$this->mOptions['fieldsperitem'] = 1;
		}
	}

	/**
	 * This functions handles individual items found in the arguments,
	 * and decides whether it is an option or not.
	 * If it is, then it handles the option (and applies it).
	 * If it isn't, then it just returns the string it found. 
	 */
	private function handleInputItem( $arg ) {
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
			case 'itemcover':
				$this->mOptions[$name] = self::parseString( $value );
				break;
			case 'fieldsperitem':
				$this->mOptions[$name] = self::parseNumeral( $value );
				break;
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
				'nll_fieldsperitem', 'nll_itemcover',
				'nll_lastseparator', 'nll_outputseparator'
			) );
		}

		if ( $name = $magicWords->matchStartToEnd( trim($value) ) ) {
			return str_replace( 'nll_', '', $name );
		}

		return false;
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
