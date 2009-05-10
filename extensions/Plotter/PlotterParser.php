<?php

/**
 * Plotter parser. Parses arguments and data for the Plotters extension.
 *
 * @addtogroup Extensions
 * @author Ryan Lane, rlane32+mwext@gmail.com
 * @copyright © 2009 Ryan Lane
 * @license GNU General Public Licence 2.0 or later
 */

if( !defined( 'MEDIAWIKI' ) ) {
        echo( "not a valid entry point.\n" );
        die( 1 );
}

class PlotterParser {

        var $argumentArray;
        var $dataArray;

	function PlotterParser( $input, $argv, &$parser ) {
                $this->parseArguments( $argv );
                $this->parseData( $input, $parser );
	}

	function getArguments() {
		return $this->argumentArray;
	}

	function getData() {
		return $this->dataArray;
	}

	function parseArguments( $argv ) {
                //Parse arguments, set defaults, and do sanity checks
		$this->argumentArray = array ( "renderer" => "plotkit", "preprocessors" => array(), "preprocessorarguments" => array(),
						"script" => "", "scriptarguments" => array(), "datasep" => "," );
                if ( isset( $argv["renderer"] ) ) {
                        $this->argumentArray["renderer"] = $argv["renderer"];
                }
		if ( isset( $argv["preprocessors"] ) ) {
			// Sanitize scripts - alphanumerics only
			$this->argumentArray["preprocessors"] = explode( ',', $argv["preprocessors"] );
			foreach ( $this->argumentArray["preprocessors"] as &$preprocessor ) {
				$preprocessor = preg_replace( '/[^A-Z0-9]/i', '', $preprocessor );
			}
		}
		if ( isset( $argv["preprocessorarguments"] ) ) {
			// Replace escaped separators
			$argv["preprocessorarguments"] = preg_replace( '/\\:/', '§UNIQ§', $argv["preprocessorarguments"] );
			$argv["preprocessorarguments"] = preg_replace( '/\\,/', '§UNIQ2§', $argv["preprocessorarguments"] );

			// Parse and sanitize arguments - escape single quotes and backslashes
			$arguments = explode( ':', $argv["preprocessorarguments"] );
			foreach ( $arguments as $argument ) {
				list($argumentkey, $argumentvalue) = explode( '=', $argument );
				$argumentkey = preg_replace( '/[^A-Z0-9]/i', '', $argumentkey );
				$argumentvalue = preg_replace( "/\\\\/", '\\\\', $argumentvalue );
				$argumentvalue = preg_replace( "/'/", "\\'", $argumentvalue );

				// Fix escaped separators
				$argumentvalue = preg_replace( "/§UNIQ§/", ":", $argumentvalue );
				$argumentvalue = preg_replace( "/§UNIQ2§/", ",", $argumentvalue );
				$this->argumentArray["preprocessorarguments"][$argumentkey] = $argumentvalue;
			}
				
		}
		if ( isset( $argv["script"] ) ) {
			// Sanitize scripts - alphanumerics only
			$this->argumentArray["script"] = preg_replace( '/[^A-Z0-9]/i', '', $argv["script"] );
		}
		if ( isset( $argv["scriptarguments"] ) ) {
			// Replace escaped separators
			$argv["scriptarguments"] = preg_replace( '/\\,/', '§UNIQ§', $argv["scriptarguments"] );

			// Parse and sanitize arguments - escape single quotes and backslashes
			$arguments = explode( ',', $argv["scriptarguments"] );
			foreach ( $arguments as &$argument ) {
				list($argumentkey, $argumentvalue) = explode( '=', $argument );
				$argumentkey = preg_replace( '/[^A-Z0-9]/i', '', $argumentkey );
				$argumentvalue = preg_replace( "/\\\\/", '\\\\', $argumentvalue );
				$argumentvalue = preg_replace( "/'/", "\\'", $argumentvalue );

				// Fix escaped separators
				$argumentvalue = preg_replace( "/§UNIQ§/", ",", $argumentvalue );
				$this->argumentArray["scriptarguments"][$argumentkey] = $argumentvalue;
			}
		}
		if ( isset( $argv["datasep"] ) ) {
			$this->argumentArray["datasep"] = $argv["datasep"];
		}
	}

	function parseData( $input, $parser ) {
		// Replace escaped separators
		$sep = $this->argumentArray["datasep"];
		$input = preg_replace( "/\\\\$sep/", '§UNIQ§', $input );

		// Parse and sanitize data - escape single quotes and backslashes
		$lines = preg_split( "/\n/", $input, -1, PREG_SPLIT_NO_EMPTY );
		foreach ( $lines as $line ) {
			$values = explode( ',', $line );
			foreach ( $values as &$value ) {
				$value = preg_replace( "/\\\\/", "\\\\", $value );
				$value = preg_replace( "/'/", "\\'", $value );

				// Fix escaped separators
				$value = preg_replace( "/§UNIQ§/", "\\$sep", $value );
			}
			$this->dataArray[] = $values;
			Plotter::debug( 'plot data values: ', $values );
		}
	}
}
