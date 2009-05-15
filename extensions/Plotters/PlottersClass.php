<?php

/**
 * Plotter class. Renders html and javascript for the Plotters extension.
 *
 * @addtogroup Extensions
 * @author Ryan Lane, rlane32+mwext@gmail.com
 * @copyright © 2009 Ryan Lane
 * @license GNU General Public Licence 2.0 or later
 */

class Plotter {

	var $parser;
	var $set;
	var $argumentArray, $dataArray;
	var $errors;

	function Plotter( $pParser, &$parser ) {
		$this->parser = $parser;
		$this->argumentArray = $pParser->getArguments();
		$this->dataArray = $pParser->getData();
	}

	function hasErrors() {
		if ( $this->errors == '' ) {
			return false;
		} else {
			return true;
		}
	}

	function getErrors() {
		return $this->errors;
	}

	function checkForErrors() {
		wfLoadExtensionMessages( 'Plotters' );

		$errors = '';

		// Check for a script
		if ( $this->argumentArray["script"] == "" ) {
			$errors .= wfMsg( "plotters-missing-script" ) . "<br />";
		} else if ( strlen( $this->argumentArray["script"] ) > 255 ) {
			// Check to ensure scriptname is < 255 characters
			$errors .= wfMsg( "plotters-excessively-long-scriptname" ) . "<br />";
		}
		// Check for data
		if ( count( $this->dataArray ) == 0 ) {
			$errors .= wfMsg( "plotters-no-data" ) . "<br />";
		}

		if ( $errors != '' ) {
			$this->errors = wfMsg( "plotters-errors" ) . " " . $errors;
		}
	}

	function toHTML() {
		// Add html
		$output = $this->renderPlot();

		// Add fallback
		$output .= $this->renderFallback();

		// Add javascript
		$output .= $this->renderJavascript();

		// Add tags to parser
		$this->parser->mOutput->mPlotterTag = true;

		foreach ( $this->argumentArray["preprocessors"] as $preprocessor ) {
			$preprocessor = "mplotter-" . $preprocessor;
			$this->parser->mOutput->$preprocessor = true;
		}

		$script = "mplotter-" . $this->argumentArray["script"];
		$this->parser->mOutput->$script = true;

		// output
		return $output;
	}

	function renderPlot() {
		// TODO: allow user defined graph id
		return '<div><canvas id="graph" height="' . $this->argumentArray["height"] . '" width="' . $this->argumentArray["width"] . '"></canvas></div>';
	}

	function renderFallback() {
		// Return an html table of the data
		return '';
	}

	function renderJavascript() {
		$output = '<script type="text/javascript">';
		// TODO: allow user defined graph id
		$output .= 'function drawGraph() {';
		$output .= 'var data = [];';

		// Prepare data
		for ( $i = 0; $i < count( $this->dataArray ); $i++ ) {
			$output .= "data[$i] = [];";
			$dataline = $this->dataArray[$i];
			for ( $j = 0; $j < count( $dataline ); $j++ ) {
				$output .= "data[$i][$j] = '" . $dataline[$j] . "';";
			}
		}

		// Prepare labels
		$output .= "var labels = [];";
		for ( $i = 0; $i < count( $this->argumentArray["labels"] ); $i++ ) {
			$output .= "labels[$i] = '" . $this->argumentArray["labels"][$i] . "';";
		}
		$output .= "fix_encoding( labels );";

		// Run preprocessors
		foreach ( $this->argumentArray["preprocessors"] as $preprocessor ) {
			$output .= 'plotter_' . $preprocessor . '_process( data, labels, ';
			foreach ( $this->argumentArray["preprocessorarguments"] as $argument ) {
				$output .= $argument . ', ';
			}
			// Strip the last ', '
			$output = substr( $output, 0, -2 );
			$output .= " );";
		}

		// Run script
		$output .= 'plotter_' . $this->argumentArray["script"] . '_draw( data, labels, ';
		foreach ( $this->argumentArray["scriptarguments"] as $argument ) {
			$output .= "'" . $argument . "'" . ", ";
		}
		$output = substr( $output, 0, -2 );
		$output .= " );";

		$output .= "}";

		// Add hook event
		// TODO: allow user defined graph id
		$output .= 'hookEvent("load", drawGraph);';
		$output .= "</script>";

		return $output;
	}

	static function setPlotterHeaders( &$outputPage ) {
		global $wgPlotterJavascriptPath;
		global $wgPlotterExtensionPath;

		$extensionpath = $wgPlotterJavascriptPath;

		// Add mochikit (required by PlotKit)
		$outputPage->addScript( '<script src="' . $extensionpath . '/mochikit/MochiKit.js" type="text/javascript"></script>' );

		// Add PlotKit javascript
		$outputPage->addScript( '<script src="' . $extensionpath . '/plotkit/Base.js" type="text/javascript"></script>' );
		$outputPage->addScript( '<script src="' . $extensionpath . '/plotkit/Layout.js" type="text/javascript"></script>' );
		$outputPage->addScript( '<script src="' . $extensionpath . '/plotkit/Canvas.js" type="text/javascript"></script>' );
		$outputPage->addScript( '<script src="' . $extensionpath . '/plotkit/SweetCanvas.js" type="text/javascript"></script>' );

		// Add javascript to fix encoding
		$outputPage->addScript( '<script src="' . $wgPlotterExtensionPath . '/libs/fixencoding.js" type="text/javascript"></script>' );

		return true;
	}

	static function debug( $debugText, $debugArr = null ) {
		global $wgPlotterDebug;

		if ( isset( $debugArr ) ) {
			if ( $wgPlotterDebug > 0 ) {
				$text = $debugText . " " . implode( "::", $debugArr );
				wfDebugLog( 'plot', $text, false );
			}
		} else {
			if ( $wgPlotterDebug > 0 ) {
				wfDebugLog( 'plot', $debugText, false );
			}
		}
	}
}
