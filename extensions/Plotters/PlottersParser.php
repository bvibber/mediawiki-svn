<?php

/**
 * Plotter parser. Parses arguments and data for the Plotters extension.
 *
 * @addtogroup Extensions
 * @author Ryan Lane, rlane32+mwext@gmail.com
 * @copyright © 2009 Ryan Lane
 * @license GNU General Public Licence 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
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
		// Parse arguments, set defaults, and do sanity checks
		$this->argumentArray = array ( "renderer" => "plotkit", "preprocessors" => array(), "preprocessorarguments" => array(),
			"script" => "", "scriptarguments" => array(), "datasep" => ",", "width" => "300", "height" => "300", "labels" => array() );
		if ( isset( $argv["renderer"] ) ) {
			//TODO: limit this to supported renderers
			$this->argumentArray["renderer"] = preg_replace( '/[^A-Z0-9]/i', '', $argv["renderer"] );
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
			$argv["preprocessorarguments"] = preg_replace( "/\\\:/", '§UNIQ§', $argv["preprocessorarguments"] );
			$argv["preprocessorarguments"] = preg_replace( "/\\\,/", '§UNIQ2§', $argv["preprocessorarguments"] );

			// Parse and sanitize arguments
			$arguments = explode( ':', $argv["preprocessorarguments"] );
			foreach ( $arguments as $argument ) {
				$subargumentarr = explode( ',', $argument );
				foreach ( $subargumentarr as &$singleargument ) {
					$singleargument = htmlentities( $singleargument, ENT_QUOTES );

					// Fix escaped separators
					$singleargument = preg_replace( "/§UNIQ§/", ":", $singleargument );
					$singleargument = preg_replace( "/§UNIQ2§/", ",", $singleargument );
				}
				$this->argumentArray["preprocessorarguments"][] = $subargumentarr;
			}

		}
		if ( isset( $argv["script"] ) ) {
			// Sanitize scripts - alphanumerics only
			$this->argumentArray["script"] = preg_replace( '/[^A-Z0-9]/i', '', $argv["script"] );
		}
		if ( isset( $argv["scriptarguments"] ) ) {
			// Replace escaped separators
			$argv["scriptarguments"] = preg_replace( "/\\\,/", '§UNIQ§', $argv["scriptarguments"] );

			// Parse and sanitize arguments
			$arguments = explode( ',', $argv["scriptarguments"] );
			foreach ( $arguments as $argument ) {
				$argument = htmlentities( $argument, ENT_QUOTES );

				// Fix escaped separators
				$argument = preg_replace( "/§UNIQ§/", ",", $argument );
				$this->argumentArray["scriptarguments"][] = $argument;
			}
			Plotter::debug( 'plot script argument values: ', $this->argumentArray["scriptarguments"] );
		}
		if ( isset( $argv["datasep"] ) ) {
			$this->argumentArray["datasep"] = htmlentities( $argv["datasep"], ENT_QUOTES );
		}
		if ( isset( $argv["width"] ) ) {
			$this->argumentArray["width"] = preg_replace( '/[^0-9]/', '', $argv["width"] );
		}
		if ( isset( $argv["height"] ) ) {
			$this->argumentArray["height"] = preg_replace( '/[^0-9]/', '', $argv["height"] );
		}
		if ( isset( $argv["labels"] ) ) {
			// Replace escaped separators
			$argv["labels"] = preg_replace( "/\\\,/", '§UNIQ§', $argv["labels"] );

			// Parse and sanitize arguments
			$labels = explode( ',', $argv["labels"] );
			foreach ( $labels as $label ) {
				$label = htmlentities( $label, ENT_QUOTES );

				// Fix escaped separators
				$label = preg_replace( "/§UNIQ§/", ",", $label );
				$this->argumentArray["labels"][] = $label;
			}
		}
	}

	function parseData( $input, $parser ) {
		// Replace escaped separators
		$sep = $this->argumentArray["datasep"];
		$input = preg_replace( "/\\\\$sep/", '§UNIQ§', $input );

		// Parse and sanitize data
		$lines = preg_split( "/\n/", $input, -1, PREG_SPLIT_NO_EMPTY );
		foreach ( $lines as $line ) {
			$values = explode( ',', $line );
			foreach ( $values as &$value ) {
				$value = htmlentities( $value, ENT_QUOTES );

				// Fix escaped separators
				$value = preg_replace( "/§UNIQ§/", "\\$sep", $value );
			}
			$this->dataArray[] = $values;
			Plotter::debug( 'plot data values: ', $values );
		}
	}
}
