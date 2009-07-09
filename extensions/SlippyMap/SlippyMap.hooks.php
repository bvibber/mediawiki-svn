<?php
if ( ! defined( 'MEDIAWIKI' ) ) 
	die();

class SlippyMapHooks {

	var $mapId;
 
	/**
	 * Property: SlippyMapMarkerList
	 * Evil hack as documented at
	 * http://www.mediawiki.org/wiki/Manual:Tag_extensions#How_can_I_avoid_modification_of_my_extension.27s_HTML_output.3F
	 * This is here so that random <p> and <pre> tags aren't added to the inline JavaScript output
	 */
	var $SlippyMapMarkerList = array();
	
	/**
	 * Search string used to check for SlippyMap invocation
	 */
	protected static $WIKITEXT_PATTERN = "slippymap";

	public function __construct() {
		$this->mapId = 0;
	}

	/** 
	 * Sets up the slippy map and links JavaScript files
	 *
	 * Register the extension with the WikiText parser
	 * the first parameter is the name of the new tag.
	 * In this case it defines the tag <slippymap> ... </slippymap>
	 * the second parameter is the callback function for
	 * processing the text between the tags
	 *
	 * @return true
	 */

	public static function onParserFirstCallInit( ) {
		global $wgArticle, $wgOut, $wgLang, $wgParser, $wgScriptPath, $wgJsMimeType, $wgStyleVersion, $wgAutoLoadMaps;

		$smh = new SlippyMapHooks();

		/** 
		 * If not a special page ( $wgArticle exists ), 
		 * then check for slippymap tag and 
		 * add openlayers if page includes slippymap 
		 * otherwise, do not add slippymap and openlayers js
		 * where unnecessary and will slow page load time.
		 */
		if ( ( isset( $wgArticle ) && strstr($wgArticle->getRawText(), SlippyMapHooks::$WIKITEXT_PATTERN ) )
			 // Horrible hack. But it'll go away in time when this whole if () is replaced by delayed loading of OL
			 || class_exists("ParserTest") ) { 

			wfLoadExtensionMessages( 'SlippyMap' );

			if ( $wgAutoLoadMaps ) {
				$autoload = 'true';
			} else {
				$autoload = 'false';
			}

			$script = array(
				"<script type=\"$wgJsMimeType\">/*<![CDATA[*/",
				"var wgSlippyMapCode = " . Xml::encodeJsVar( wfMsg( 'slippymap_code' ) ) . ";",
				"var wgSlippyMapButtonCode = " . Xml::encodeJsVar( wfMsg( 'slippymap_button_code' ) ) . ";",
				"var wgSlippyMapResetview = " . Xml::encodeJsVar( wfMsg( 'slippymap_resetview' ) ) . ";",
				"var wgSlippyMapLanguageCode = " . Xml::encodeJsVar( $wgLang->getCode() ) . ";",
				"var autoInitMaps = {$autoload};",
				"/*]]>*/</script>",
			);

			$wgOut->addScript( implode( "\n\t\t", $script ) . "\n" );
			$wgOut->addScript( "<script type='$wgJsMimeType' src='" . $wgScriptPath . "/extensions/SlippyMap/OpenLayers/public/OpenLayers.js?{$wgStyleVersion}'></script>" );
			$wgOut->addScript( "<script type='$wgJsMimeType' src='" . $wgScriptPath . "/extensions/SlippyMap/SlippyMap.js?{$wgStyleVersion}'></script>" );
			$wgOut->addLink( array( 'rel'   => 'stylesheet','type'  => 'text/css','href'  => $wgScriptPath . '/extensions/SlippyMap/SlippyMap.css' ) );
			
		  	$wgParser->setHook( 'slippymap', array( $smh, 'wfParseMapAttributes' ) );
		}

  		return true;
	}
	
	public function wfParseMapAttributes( $input, $argv, $parser )
	{
		global $wgOut, $SlippyMapMarkerList, $wgMapModes;

		/**
		 * Support old style parameters from $input
		 * Parse the pipe separated name value pairs (e.g. 'aaa=bbb|ccc=ddd')
		 * With the new syntax we expect nothing in the $input, so this will result in '' values
		 */

		$oldStyleParamStrings = explode( '|', $input );
		foreach ( $oldStyleParamStrings as $oldStyleParamString ) {
			$oldStyleParamString = trim( $oldStyleParamString );
			$eqPos = strpos( $oldStyleParamString, "=" );
			if ( $eqPos === false ) {
				$oldStyleParams[$oldStyleParamString] = 'true';
			} else {
				$oldStyleParams[substr( $oldStyleParamString, 0, $eqPos )] = trim( htmlspecialchars( substr( $oldStyleParamString, $eqPos + 1 ) ) );
			}
		}
	
		foreach ( $argv as $key=>$val ) {
			// Receive new style args: <slippymap aaa=bbb ccc=ddd></slippymap>
			if ( isset( $val ) ) {
				$key = strtolower( $key );
				$mapParams[$key] = $val;
			}
		}

		/**
		 * If using old style params, turn them into a mapParams array
		 */
		if ( ( isset( $mapParams ) && ( count( $mapParams ) == 0 ) ) && ( isset( $oldStyleParams ) && ( count( $oldStyleParams ) > 0 ) ) ) {
			foreach ( $oldStyleParams as $key => $val ) {
				// Receive new style args: <slippymap aaa=bbb ccc=ddd></slippymap>
				if ( isset( $val ) ) {
					$key = strtolower( $key );
					$mapParams[$key] =& $val;
				}
			}
		}

		/** 
		 *  Give the map a unique id, so there can be multiple maps
		 */
		$mapParams['mapId'] = $this->mapId;
		$mode = isset( $mapParams['mode'] ) ? $mapParams['mode'] : null;
	
		if ( $mode && ! in_array( $mode, $wgMapModes ) ) {
			$errors = wfMsg( 'slippymap_invalidmode',  htmlspecialchars( $this->mode ) );
			$wgOut->addHTML( '<h3>' . $mode . ' is an invalid map mode</h3>' );	
		} else {	
			$output = '';
			switch ( $mode ) {
				case 'satellite':
					$map = new WorldWind( $mapParams );
					break;
				default:
					$map = new SlippyMap( $mapParams );				
			}

			$output .= $map->getMap( );
			$this->mapId++;
			$markercount = count( $SlippyMapMarkerList );
			$pMarker = "SlippyMap-marker".$markercount."-SlippyMap";
			$SlippyMapMarkerList[$markercount] = $output;
			return $pMarker;

		}

	}

	/**
	 * Evil hack
	 * @see http://www.mediawiki.org/wiki/Manual:Tag_extensions#How_can_I_avoid_modification_of_my_extension.27s_HTML_output.3F
	 */
	public static function wfSlippyMapParserAfterTidy( &$parser, &$text ) {
		global $SlippyMapMarkerList;
		$keys = array();
		$marker_count = count( $SlippyMapMarkerList );

		for ($i = 0; $i < $marker_count; $i++) {
			$keys[] = 'SlippyMap-marker' . $i . '-SlippyMap';
		}

		$text = str_replace( $keys, $SlippyMapMarkerList, $text );
	  	return true;
	}

	function error( $msg ) {
		$error = '<strong class="error">' . $msg . '</strong>';
		return $error;
	}
}
