<?php
/**
 * TemplateAdventures is for recreation of popular but demanding templates
 * in PHP.  Wikicode is powerful, but slow.  Templates such as cite core
 * suffers greatly from this.
 * 
 * 
 * Copyright (C) 2010 'Svip', 'MZMcBride' and others.
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
	'name'        => 'Template Adventures',
	'author'      => array( 'Svip' ),
	'url'         => 'http://www.mediawiki.org/wiki/Extension:TemplateAdventures',
	'descriptionmsg' => 'ta-desc',
	'version'     => '0.1'
);


$dir = dirname(__FILE__);
$wgExtensionMessagesFiles['TemplateAdventures'] = "$dir/TemplateAdventures.i18n.php";
$wgExtensionMessagesFiles['TemplateAdventuresMagic'] = "$dir/TemplateAdventures.i18n.magic.php";

$wgAutoloadClasses['Citation'] = $dir . '/Templates/Citation.php';

$wgHooks['ParserFirstCallInit'][] = 'TemplateAdventures::onParserFirstCallInit';

$wgParserTestFiles[] = dirname( __FILE__ ) . "/taParserTests.txt";

class TemplateAdventures {
	
	public static function onParserFirstCallInit( $parser ) {
		$parser->setFunctionHook( 
			'citation', 
			array( __CLASS__, 'citation' ), 
			SFH_OBJECT_ARGS 
		);
		return true;
	}

	/**
	 * Render {{#citation:}}
	 *
	 * @param $parser Parser
	 * @param $frame PPFrame_DOM
	 * @param $args Array
	 * @return wikicode parsed
	 */
	public static function citation( $parser, $frame, $args ) {
		if ( count( $args ) == 0 )
			return '';
		$obj = new Citation( $parser, $frame, $args );
		$obj->render();

		return $obj->output();
	}
}

class TemplateAdventureBasic {
	
	protected $mParser;
	protected $mFrame;
	public $mArgs;
	protected $mOutput;
	
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
	 * Outputter
	 */
	public function output() {
		return $this->mOutput;
	}

	/**
	 * Do stuff.
	 */
	public function render() {
		return;
	}

	/**
	 * Read options from $this->mArgs.  Let the children handle the options.
	 */
	protected function readOptions ( ) {
		
 		$args = $this->mArgs;
 
		# an array of items not options
		$this->mReaditems = array();

		# first input is a bit different than the rest,
		# so we'll treat that differently
		$primary = trim( $this->mFrame->expand( array_shift( $args ) ) );
		$primary = $this->handleInputItem( $primary );
		
		# check the rest for options
		foreach( $args as $arg ) {
			$item = $this->handleInputItem( $arg );
		}
	}

	/**
	 * This functions handles individual items found in the arguments,
	 * and decides whether it is an option or not.
	 * If it is, then it handles the option (and applies it).
	 * If it isn't, then it just returns the string it found. 
	 *
	 * @param $arg String Argument
	 * @return String if element, else return false
	 */
	protected function handleInputItem( $arg ) {
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
		return $this->optionParse( $var, $value );
	}

	/**
	 * Parse the option.
	 * This should be rewritten in classes inheriting this class.
	 *
	 * @param $var
	 * @param $value
	 * @return False if option else element
	 */
	protected function optionParse( $var, $value ) {
		return $arg instanceof PPNode_DOM
			? trim( $this->mFrame->expand( $arg ) )
			: $arg;
	}

	/**
	 * Using magic to store all known names for each option
	 *
	 * @param $input String
	 * @return The option found; otherwise false
	 */
	protected function parseOptionName( $value ) {
		return false;
	}
}
