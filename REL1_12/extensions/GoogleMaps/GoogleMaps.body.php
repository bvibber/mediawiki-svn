<?php

// if we're not in the mediawiki framework just die
if( !defined( 'MEDIAWIKI' ) ) {
	die( );
}

/**
 * This is a class for adding Google maps to Mediawiki articles.  The class
 * takes care of all hook registration and output of both the map editing
 * interface and the map display within the article.
 *
 * To use this class, create an instance and add the 'install' function of
 * the instantiated object as an extension function:
 *
 * $wgGoogleMapExtension = new GoogleMaps( ... );
 * $wgExtensionFunctions[] = array( $wgGoogleMapExtension, 'install' );
 **/
class GoogleMaps {

	//----------------------------------------------
	// MEMBER FIELDS
	//----------------------------------------------

	const PARSE_INCLUDES = 0;
	const PARSE_ADD_MARKER = 1;
	const PARSE_POINTS = 2;
	// the Google API key (obtained from
	// http://www.google.com/apis/maps/signup.html)
	var $mApiKey = null;

	// if true, paths will be enabled on the maps
	var $mEnablePaths = false;

	// the map language message structure
	var $mMessages = null;

	// custom messages.  these override the values in $mMessages, if present
	var $mCustomMessages = null;

	// the MediaWiki language code (used to key messgaes)
	var $mLanguageCode = null;

	// a local count of how many maps	have been rendered on the current page
	var $mGoogleMapsOnThisPage = 0;

	// the mime type to use for javascript
	var $mJsMimeType = null;

	// the map default settings
	var $mMapDefaults = null;

	// the template variables to process
	var $mProcessTemplateVariables  = null;

	// an array of valid values for map settings. The keys are the setting names
	// and the values are arrays of valid values for that setting.
	var $mApprovedValues = null;

	// a dictionary of tokens	mapped to their	end	values for different settings.
	// the keys to the array are the setting names, the values are hashes	of
	// key/value pairs where the key is the token	and	the	value is the full
	// value.
	var $mOptionDictionary = null;

	// whether the current language is read right-to-left
	var $mLanguage = null;

	// a reference to the global parser object
	var $mGlobalParser = null;

	// a secret key used in parsing
	var $wgProxyKey = null;

	// the current page
	var $mTitle  = null;

	//----------------------------------------------
	// CONSTRUCTOR
	//----------------------------------------------

	/**
	 * Instantiates a new GoogleMaps object. The constructor simply captures all
	 * of the values passed as member fields. Since the constructor is called on
	 * every page load, we want to keep this as light as possible.  That's why
	 * any of the start up logic is moved to the 'install' function. Granted,
	 * the install function is called on almost every single MW page, but if MW
	 * changes how extensions are loaded to be more intelligent somehow, then this
	 * would benefit from it automatically.
	 *
	 * All of the values are passed by reference (even if not technically needed)
	 * and stored as references to keep from making copies of the various MW
	 * globals unnecessarily.
	 *
	 * @param $pApiKey string - the default Google API key
	 * @param $pUrlPath string - the URL path to the GoogleMaps extension
	 * @param $pEnablePaths boolean - whether or not to allow paths on maps
	 * @param $pMapDefaults array - an array of map setting defaults
	 * @param $pMessages array - the message data structure
	 * @param $pCustomMessages array - message overrides
	 * @param $pProcessTemplateVariables boolean - whether or not to process
	 *          template variables
	 * @param $pJsMimeType string - the Javascript mime type
	 * @param $pLanguageCode string - the language identifier ('en', 'fr', etc.)
	 **/
	function GoogleMaps (
		&$pApiKey,
		&$pUrlPath,
		&$pEnablePaths,
		&$pMapDefaults,
		&$pMessages,
		&$pCustomMessages,
		&$pProcessTemplateVariables,
		&$pJsMimeType,
		&$pLanguageCode,
		&$pContLang,
		&$pGlobalParser,
		&$pProxyKey,
		&$pTitle ) {

		$this->mApiKey	 =& $pApiKey;
		$this->mEnablePaths =& $pEnablePaths;
		$this->mMapDefaults =& $pMapDefaults;
		$this->mJsMimeType =& $pJsMimeType;
		$this->mLanguageCode			  =& $pLanguageCode;
		$this->mUrlPath	 =& $pUrlPath;
		$this->mMessages =& $pMessages;
		$this->mCustomMessages			  =& $pCustomMessages;
		$this->mProcessTemplateVariables  =& $pProcessTemplateVariables;
		$this->mLanguage =& $pContLang;
		$this->mGlobalParser =& $pGlobalParser;
		$this->mProxyKey =& $pProxyKey;
		$this->mTitle =& $pTitle;
	}


	//----------------------------------------------
	// PUBLIC METHODS
	//----------------------------------------------

	/**
	 * This function renders the control for opening the interactive map editor
	 * as well as some base javascript utilities needed by the map editing
	 * script (messages, settings, JS includes, etc.).  Most of the actual
	 * interactive editing code is in the EditorsMap.js file.
	 *
	 * @param $pForm EditPage - the EditPage object for the current article
	 *   being edited.
	 *
	 * @return boolean - true if successful
	 **/
	function editForm ( $pForm ) {

		// get the current map settings
		$o = GoogleMaps::getMapSettings( $this->mTitle, $this->mMapDefaults );

		$output = '';

		// output the necessary styles, script includes, and global variables
		$output .= '
<style type="text/css">
	@import "' . $this->mUrlPath . '/color_select.css";
	textarea.balloon_textarea {
		width: 220px;
		height: 52px;
	}
</style>
<!--[if IE]>
<style type="text/css">
	@import "' . $this->mUrlPath . '/color_select_ie.css";
</style><![endif]-->
<!--[if lt IE 7]>
<style type="text/css">
	@import "' . $this->mUrlPath . '/color_select_ie6.css";
</style><![endif]-->
<script src="http://maps.google.com/maps?file=api&amp;v=' . $o['api'] . '&amp;key=' . $this->mApiKey . '&amp;hl=' . $this->mLanguageCode . '" type="' . $this->mJsMimeType . '"></script>
<script src="http://www.google.com/uds/api?file=uds.js&amp;v=1.0&amp;key=' . $this->mApiKey . '" type="' . $this->mJsMimeType . '"></script>
<script type="' . $this->mJsMimeType . '">
//<![CDATA[
';

	$output .= <<<JAVASCRIPT
		var GME_SMALL_ICON;
		var editors_options = {
			'container':'toolbar', 'textbox':'wpTextbox1', 'toggle':'google_maps_toggle_link',
JAVASCRIPT;

	// add all of the map settings to the editors_options JS variable
	foreach( array_keys( $o ) as $key ) {
		if( is_numeric( $o[$key] ) ) {
			$output .= "'{$key}':{$o[$key]}, ";
		} elseif( $o[$key] ) {
			$output .= "'{$key}':'{$o[$key]}', ";
		}
	}

	// output the 'rtl' setting
	$isRTLString = $this->mLanguage->isRTL() ? 'true' : 'false';
	$output .= " 'rtl':{$isRTLString} };";

	// output the base utility JS (addLoadEvent function, etc.)
	$output .= $this->getEssentialJS( );

	// output the messages as the '_' variable
	$output .= $this->getMessageJS( );

	// output the paths supported setting
	$output .= "var GME_PATHS_SUPPORTED = " . ( $this->mEnablePaths ? "true" : "false" ) . "; ";

	// output the function to add the google map link to the editors toolbar
	$output .= <<<JAVASCRIPT

	function addScript(script) {
		var js = document.createElement("script");
		js.setAttribute('src', script);
		js.setAttribute('type', '{$this->mJsMimeType}');
		document.getElementsByTagName("head")[0].appendChild(js);
	}
	function loadEditorsMapJavascript(image) {
			addScript('{$this->mUrlPath}/color_select.js');
			addScript('{$this->mUrlPath}/EditorsMap.js');

			window.setTimeout(tryLoadingEditorsMap(image), 100);
	}

	function tryLoadingEditorsMap(image) {
		return(function() {
			if (typeof(EditorsMap) != "undefined") {
				GME_SMALL_ICON = new GIcon();
				GME_SMALL_ICON.image = "http://labs.google.com/ridefinder/images/mm_20_yellow.png";
				GME_SMALL_ICON.shadow	= "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
				GME_SMALL_ICON.iconSize =	new	GSize(12, 20);
				GME_SMALL_ICON.shadowSize	= new GSize(22,	20);
				GME_SMALL_ICON.iconAnchor	= new GPoint(6,	20);
				GME_SMALL_ICON.infoWindowAnchor =	new	GPoint(5, 1);

				emap = new EditorsMap(editors_options);
				image.initDone = true;
			} else {
				window.setTimeout(tryLoadingEditorsMap(image), 100);
			}
		});
	}

	function insertGoogleMapLinks() {
		var image = document.createElement("img");
		image.width = 23;
		image.height = 22;
		image.src = '{$this->mUrlPath}/button_map_open.gif';
		image.border = 0;
		image.alt = _['gm-make-map'];
		image.title = _['gm-make-map'];
		image.style.cursor = "pointer";
		image.onclick = function() {
			if( !this.initDone ) {
				loadEditorsMapJavascript(this);
			}
			else {
				emap.toggleGoogleMap();
			}
			if( this.buttonOn ) {
				this.src = this.src.replace(/_close/,"_open");
				this.alt = _['gm-make-map'];
				this.title = _['gm-make-map'];
				this.buttonOn = false;
			}
			else {
				this.src = this.src.replace(/_open/,"_close");
				this.alt = _['gm-hide-map'];
				this.title = _['gm-hide-map'];
				this.buttonOn = true;
			}
			return false;
		};
		document.getElementById('toolbar').appendChild(image);
	}
	window.unload = GUnload;

	 addLoadEvent(insertGoogleMapLinks);
	 //]]>
	 </script>
JAVASCRIPT;

	// add the output string as HTML to the wgOut object
	global $wgOut;
	$wgOut->addHTML( $output );

	// return true so other hooks can execute
	return true;
	}

	/**
	 * This function post processes the raw parser output.  It first prepends the output with some
	 * CSS and the main google JS include.  It then replaces the proxy key token with opening and
	 * closing <script> tags. The proxy key is used to prevent people from injecting javascript in
	 * a page by simply using 'BEGINJAVASCRIPT' and 'ENDJAVASCRIPT'.
	 *
	 * @param $pParser Parser - the MW Parser for the page
	 * @param $pValue string - the raw parsed page output
	 *
	 * @return boolean - true if successful
	 **/
	function commentJS ( &$pParser, &$pValue ) {
		// check to see if the proxy token appears in the page output (if not, we don't have a map so
		// no need to output our stuff)
		if( isset( $this->mGoogleMapsOnThisPage ) && strstr( $pValue, "%%BEGINJAVASCRIPT" . $this->mProxyKey . "%%" ) ) {
			$o = GoogleMaps::getMapSettings( $this->mTitle, $this->mMapDefaults );

		// output our standard css and script include
		$prefix = '
			<!--[if IE]>
			<style type="text/css">
			v\:* {
				behavior:url(#default#VML);
			}
			</style>
			<![endif]-->
			<script src="http://maps.google.com/maps?file=api&amp;v=' . $o['api'] . '&amp;key=' . $this->mApiKey . '&amp;hl=' . $this->mLanguageCode . '" type="' . $this->mJsMimeType . '"></script>
			%%BEGINJAVASCRIPT' . $this->mProxyKey . '%%
		';

		// concatenate the prefix and essential JS to the passed in value
		$pValue = $prefix . $this->getEssentialJS( ) . "%%ENDJAVASCRIPT" . $this->mProxyKey . "%%" . $pValue;

		// replace the proxy tokens with actual script tags
		$pValue =	str_replace( "%%BEGINJAVASCRIPT" . $this->mProxyKey	. "%%",	"<script type=\"" .	$this->mJsMimeType . "\">\n//<![CDATA[\n", $pValue );
		$pValue =	str_replace( "%%ENDJAVASCRIPT" . $this->mProxyKey .	"%%", "\n//]]>\n</script>\n", $pValue );
		}

		// return true so other	hooks can run
		return true;
	}

	//----------------------------------------------
	// PRIVATE METHODS
	//----------------------------------------------

	/**
	 * This function is for rendering a <googlemap> tag on MW 1.6+.
	 *
	 * @param $pContent string - the content of the <googlemap> tag
	 * @param $pArgv array - an array of attribute name/value pairs for the
	 *   tag
	 * @param $pParser Parser - the MW Parser object for the page being
	 *   rendered
	 *
	 * @return string - the HTML string to output for the <googlemap> tag
	 **/
	function render16 ( $pContent, $pArgv, &$pParser ) {
		global $wgGoogleMaps;
		// pass through to the main render function, creating a new parser
		// for parsing the local content
	return $wgGoogleMaps->render( $pContent, $pArgv, $pParser, new Parser( ) );
	}

	/**
	 * This function processes a single <googlemap> tag and produces the HTML and
	 * Javascript output for rendering the map represented by the tag.
	 *
	 * @param $pContent string - the content of the <googlemap> tag
	 * @param $pArgv array - the array of attribute name/value pairs for the tag
	 * @param $pParser Parser - the MW Parser object for the current page
	 * @param $pLocalParser Parser - the parser for parsing local content
	 *
	 * @return string - the html for rendering the map
	 **/
	function render ( $pContent, $pArgv, &$pParser, &$pLocalParser ) {

		// Keep a count of how many <googlemap> tags were used for unique ids
		if( !isset( $this->mGoogleMapsOnThisPage ) ) {
			$this->mGoogleMapsOnThisPage = 1;
		} else {
			$this->mGoogleMapsOnThisPage++;
		}

		if( $this->mProcessTemplateVariables ) { // experimental, see MW bug #2257
			foreach( array_keys( $pArgv ) as $key ) {
				$pArgv[$key] = $pParser->replaceTemplateVariables( $pArgv[$key] );
			}
			$pContent = $pParser->replaceTemplateVariables( $pContent );
		}

		// a dictionary for validating and interpreting some options.
		$o = GoogleMaps::getMapSettings( $this->mTitle, $this->mMapDefaults );

		// Override the defaults with what the user specified.
		foreach( array_keys( $o ) as $key ) {
			if( is_numeric( $o[$key] ) && isset( $pArgv[$key] ) && is_numeric( $pArgv[$key] ) ) {
				$o[$key] = $pArgv[$key];
			} elseif( isset($pArgv[$key] ) && GoogleMaps::isOptionLegal( $key, $pArgv[$key] ) ) {
				$o[$key] = $this->translateOption( $key, $pArgv[$key] );
			} else { // and translate
				$o[$key] = $this->translateOption( $key, $o[$key] );
			}
		}

		$o = array_merge($o, array('number_of_maps' => $this->mGoogleMapsOnThisPage,
			'incompatible_message' => $this->translateMessage( 'gm-incompatible-browser' )));
		$outputter = new GoogleMapsJsOutputter($this->mLanguage, $this->mProxyKey, $this->mEnablePaths);
		$outputter->addHeader($o);
		GoogleMaps::renderContent($pContent, &$pParser, &$pLocalParser,
			$pParser->mTitle, $pParser->mOptions, $outputter, $o);
		$outputter->addTrailer($o);
		return $outputter->render();
	}

	function renderKmlLink($pContent, $pArgv) {
		global $wgTitle;
		$article = isset($pArgv['article']) && $pArgv['article'] ? $pArgv['article'] : $wgTitle->getText();
		$title = Title::newFromText($article);
		$specialTitle = Title::makeTitle( NS_SPECIAL, 'GoogleMapsKML' );
		return '<a href="'.$specialTitle->escapeLocalUrl('article='.$title->getPartialURL()).'">'.$pContent.'</a>';
	}

	static function renderContent($pContent, &$pParser, &$pLocalParser, &$pParserTitle, $pParserOptions, &$outputter, $o) {
		// parse the content of the tag
		$lines        = preg_split( "/[\r\n]/", $pContent );
		$tabs         = array( ); // the tabs for the current marker
		$polyline     = array( ); // points in a polyline
		$icons        = array( ); // keeps track of which icons we've made in the JS
		$lineColor    = null;
		$lineOpacity  = null;
		$fillColor    = null;
		$fillOpacity  = null;
		$state        = GoogleMaps::PARSE_INCLUDES;

		$icon    = null;
		$lat     = null;
		$lon     = null;
		$caption = '';
		$title   = null;
		$stroke  = null;
		$syntax  = $o['version'];

	// The meat of the extension. Translate the content of the tag
	// into JS that produces a set of points, lines, and markers
	foreach( $lines as $line ) {
		// if the line is a hex code, it's the start of a path
		if( preg_match( "/^(\d+)?#([0-9a-fA-F]{2})?([0-9a-fA-F]{6})(?: \(#([0-9a-fA-F]{2})?([0-9a-fA-F]{6})\))?$/", $line, $matches ) ) {

			// if the color is already set, we were just rendering a path so finish it and start
			// a new one
			if( isset( $lineColor ) ) {
				$outputter->addPolyline( $polyline, $lineColor, $lineOpacity, $stroke, $fillColor, $fillOpacity );
				$polyline = array( );
			}

			$stroke      = isset($matches[1]) && $matches[1] ? $matches[1] : $o['stroke'];
			$lineOpacity = isset($matches[2]) && $matches[2] ? $matches[2] : "ff";
			$lineColor   = isset($matches[3]) && $matches[3] ? $matches[3] : null;
			$fillOpacity = isset($matches[4]) && $matches[4] ? $matches[4] : "ff";
			$fillColor   = isset($matches[5]) && $matches[5] ? $matches[5] : null;
		}

		// if the line matches the tab format, add the tabs
		else if( $syntax == "0" && preg_match( '/^\/([^\\\\]+)\\\\ *(.*)$/', $line, $matches ) ) {
			$parsed = $pLocalParser->parse( $matches[2], $pParserTitle, $pParserOptions, false );
			$tabs[] = array( 'title' => $matches[1], 'gm-caption' => $parsed->getText());
			$state = GoogleMaps::PARSE_ADD_MARKER;
		}
		else if ($syntax != "0" && preg_match( '/^\/([^\\\\]+)\\\\$/', $line, $matches ) ) {
			if (count($tabs)) {
				$parsed = $pLocalParser->parse( $caption, $pParserTitle, $pParserOptions, false );
				$tabs[count($tabs)-1]['gm-caption'] = $parsed->getText();
				$caption = '';
			}
			$tabs[] = array( 'title' => $matches[1] );
		}
		else if( $state == GoogleMaps::PARSE_INCLUDES && preg_match( "/^http:\/\//", $line ) ) {
			$outputter->addXmlSource($line);
		}
		// the line is a regular point
		else if( preg_match( "/^(?:\(([.a-zA-Z0-9_-]*?)\) *)?([^,]+), *([^ ,]+)(?:, ?(.+))?/", $line, $matches ) ) {
			// first create the previous marker, now that we have all the tab/caption info
			if( $state == GoogleMaps::PARSE_ADD_MARKER ) {
				$parsed = $pLocalParser->parse( $caption, $pParserTitle, $pParserOptions, false );
				if (count($tabs)) {
					$tabs[count($tabs)-1]['gm-caption'] = $parsed->getText();
					$outputter->addMarker( $lat, $lon, $icon, $title, $tabs, isset($lineColor) );
				} else {
					$outputter->addMarker( $lat, $lon, $icon, $title, $parsed->getText(), isset($lineColor));
				}
				// This parse function above lets us insert wiki markup into the map markers.

				$tabs    = array( );
				$caption = '';
				$title   = null;
			}

			$state = GoogleMaps::PARSE_POINTS;

			// extract the individual fields from the regex match
			$icon = isset( $matches[1] ) ? $matches[1] : null;
			$lat  = isset( $matches[2] ) ? $matches[2] : null;
			$lon  = isset( $matches[3] ) ? $matches[3] : null;
			if ($syntax == "0") {
				$caption = isset( $matches[4] ) ? $matches[4] : '';
			} else {
				$title = isset( $matches[4] ) ? $matches[4] : null;
			}

			// need to create this icon, since we haven't already
			if( $icon && !$icons[$icon] ) {
				$outputter->addIcon($icon, $o['icons']);
				$icons[$icon] = true;
			}

			// if we have numeric latitude and longitude, process the point
			if( is_numeric( $lat ) && is_numeric( $lon ) ) {

			// if it has an icon override, a caption, or is not in a path, add the marker
				if ( $icon || count($tabs) > 0 || $caption || $title || !isset( $lineColor ) ) {
					$state = GoogleMaps::PARSE_ADD_MARKER;
				}

				// If we're making a path, record the location and move on.
				if( isset( $lineColor ) ) {
					$polyline[] = array( 'lat' => $lat, 'lon' => $lon );
				}
			}
		}

		else if (($state == GoogleMaps::PARSE_POINTS || $state == GoogleMaps::PARSE_ADD_MARKER) && $syntax != "0") { // a caption line
			if ($line != '') {
				$caption .= $line . "\r\n";
				$state = GoogleMaps::PARSE_ADD_MARKER;
			}
		}
	}

	// if the last iteration was to add a marker, add it
	if( $state == GoogleMaps::PARSE_ADD_MARKER ) {
		$parsed = $pLocalParser->parse( $caption, $pParser->mTitle, $pParser->mOptions, false );
		if (count($tabs) > 0) {
			$tabs[count($tabs)-1]['gm-caption'] = $parsed->getText();
			$outputter->addMarker( $lat, $lon, $icon, $title, $tabs, isset($lineColor) );
		} else {
			$outputter->addMarker( $lat, $lon, $icon, $title, $parsed->getText(), isset($lineColor));
		}
	}

	// if the last iteration was to	add	a polyline,	add	it
	if(	isset( $lineColor )	) {
		$outputter->addPolyline( $polyline,	$lineColor,	$lineOpacity, $stroke, $fillColor, $fillOpacity	);
	}
	}

	//----------------------------------------------
	// UTILITIES
	//----------------------------------------------

	/**
	 * This function returns the array of	approved values	for	the	various	map
	 * settings.
	 *
	 * @return array - an	array whose	keys are setting names and whose values
	 *   are arrays containing the valid values for that setting
	 **/
	static function getApprovedValues	( )	{
		return array(
			'type' => array(
				'map',
				'normal',
				'hybrid',
				'terrain',
				'satellite'
			),
			'controls' => array(
				'small',
				'medium',
				'large',
				'none'
			),
			'units'  => array(
				'kilometers',
				'meters',
				'miles'
			),
			'scale' => array(
				'yes',
				'no'
			),
			'selector' => array(
				'yes',
				'no'
			),
			'zoomstyle' => array(
				'smooth',
				'fast'
			),
			'scrollwheel' => array(
				'zoom',
				'nothing'
			),
			'doubleclick' => array(
				'recenter',
				'zoom'
			),
			'version' => array(
				'0',
				'0.9'
			),
		);
	}

	/**
	 * This function returns the array of dictionary mapping for the various map
	 * settings. If the member field hasn't yet been initialized, it will be.
	 * The only reason this initialization lives here and not in the member
	 * field declaration itself is only to keep from initializing this array
	 * on every page request.  It's such a trivial amount of data but it still
	 * rubs me the wrong way to initialize data structures when they're only
	 * needed on a very small number of page requests.
	 *
	 * @return array - an array whose keys are setting names and whose values
	 *   are dictionaries containing the mappings for that setting
	 **/
	function &getOptionDictionary ( ) {

		if( empty( $this->mOptionDictionary ) ) {
			$this->mOptionDictionary = array(
				'type' => array(
					'map'       => 'G_NORMAL_MAP',
					'normal'    => 'G_NORMAL_MAP',
					'hybrid'    => 'G_HYBRID_MAP',
					'terrain'   => 'G_PHYSICAL_MAP',
					'satellite' => 'G_SATELLITE_MAP'
				),
				'controls' => array(
					'small'  => 'GSmallZoomControl',
					'medium' => 'GSmallMapControl',
					'large'  => 'GLargeMapControl',
					'none'   => 'none'
				),
			);
		}

		return $this->mOptionDictionary;
	}

	/**
	 * Gets the array of map settings by combining a set of defaults with the current set of
	 * configured options.
	 *
	 * @return array - a hash of setting name/value pairs
	 **/
	static function getMapSettings ( $pTitle = null, $pDefaults =	null) {

		// our defaults, in	case $wgGoogleMapsDefaults isn't specified.
		$o = array(
			'api'         => 2.94,
			'color'       => '#758bc5',
			'controls'    => 'medium',
			'doubleclick' => 'recenter',
			'geocoder'    => true,
			'height'      => 400,
			'icon'        => 'http://www.google.com/mapfiles/marker.png',
			'icons'       => 'http://maps.google.com/mapfiles/marker{label}.png',
			'lat'         => 42.711618,
			'localsearch' => true,
			'lon'         => -73.205112,
			'opacity'     => 0.7,
			'overview'    => 'no',
			'precision'   => 6,
			'scale'       => 'no',
			'scrollwheel' => 'nothing',
			'selector'    => 'yes',
			'stroke'      => 6,
			'type'        => 'hybrid',
			'units'       => 'kilometers',
			'version'     => 0,
			'width'       => 400,
			'zoom'        => 12,
			'zoomstyle'   => 'fast',
		);

		// if no map defaults are specified, just return the base set of defaults
		if( !is_array( $pDefaults ) ) {
			return $o;
		}

		$title = $pTitle->getText( );

		// Go through the options and set it to the value in $pDefaults if present and a
		// valid option
		foreach( array_keys( $o ) as $key ) {
			// use the same tests for all numeric options
			if( isset( $o[$key] ) && is_numeric( $o[$key] ) ) {
				if( isset( $pDefaults[$title] ) && is_array( $pDefaults[$title] ) &&
				isset( $pDefaults[$title][$key] ) && is_numeric( $pDefaults[$title][$key] ) ) {
					$o[$key] = $pDefaults[$title][$key];
				} elseif( isset( $pDefaults[$key] ) && is_numeric( $pDefaults[$key] ) ) {
					$o[$key] = $pDefaults[$key];
				}
			}
			else {
				if( isset( $pDefaults[$title] ) && is_array( $pDefaults[$title] ) &&
				  isset( $pDefaults[$title][$key] ) && GoogleMaps::isOptionLegal( $key, $pDefaults[$title][$key] ) ) {
					$o[$key] = $pDefaults[$title][$key];
				} elseif( isset( $pDefaults[$key] ) && GoogleMaps::isOptionLegal( $key, $pDefaults[$key] ) ) {
					$o[$key] = $pDefaults[$key];
				}
			}
		}
		return $o;
	}

	/**
	 * Check to see if the value of the specified setting is a valid value.
	 *
	 * @param $pKey string - the setting name
	 * @param $pValue mixed - the value to test
	 **/
	static function isOptionLegal ( $pKey, $pValue ) {

		// get the set of approved values to check
		$approvedValues = GoogleMaps::getApprovedValues( );

		// if it's in the approved list, explicitly check the value against the approved values
		if( isset( $approvedValues[$pKey] ) ) {
			foreach( $approvedValues[$pKey] as $value ) {
				// if we find the value in the approved list, return true
				if( $pValue == $value ) {
					return true;
				}
			}
			// if we didn't find the value in the approved list, return false
			return false;
		}

		// if this setting isn't listed in the approved value list, just return true for set values
		return isset( $pValue );
	}

	/**
	 * Translates a value token into the actual value
	 *
	 * @param $pKey string - the setting name
	 * @param $pValue string - the setting token
	 *
	 * @return mixed - the translated value
	 **/
	function translateOption ( $pKey, $pValue ) {

		// get the dictionary of setting options
		$optionDictionary = $this->getOptionDictionary( );

		// if the setting and token is in the dictionary, return its value
		if( isset( $optionDictionary[$pKey] ) ) {
			return isset( $optionDictionary[$pKey][$pValue] ) ? $optionDictionary[$pKey][$pValue] : null;
		}

		// if no translation was found, return the original value
		return $pValue;
	}

	/**
	 * Builds the string for javascript that should be included once per page with a map.
	 *
	 * @return string - the javascript string
	 **/
	function getEssentialJS ( ) {
		$js = <<<JAVASCRIPT
		var mapIcons = {};

		function addLoadEvent(func) {
			var oldonload = window.onload;
			if (typeof oldonload == 'function') {
				window.onload	= function() {
					oldonload();
					func();
				};
				} else {
					window.onload = func;
				}
			}
JAVASCRIPT;

		// replace multiple spaces with a single space and strip newlines and tabs (make sure no tabs
		// are used within a line of code!)
		return preg_replace( '/  +/', ' ', preg_replace( '/[\n\t]/', '', $js ) );
	}

	/**
	 * Puts the language file for the map extension into a javascript structure that can be used in
	 * the client side script.  The variable name is '_' for the javascript structure.  If
	 * translations for the messages are included in the language file, the translations are used.
	 * If not, the english message is used.  The '_' array is a 1 dimensional array whose keys are
	 * the message identifiers and whose values are the messages themselves.
	 *
	 * @return string - the javascript for initializing the '_' variable
	 **/
	function getMessageJS ( ) {
		$translation = "var _ = { ";
		foreach( array_keys( $this->mMessages["en"] ) as $key ) {
			$translation .= "'$key':'" . addslashes( $this->translateMessage( $key ) ) . "', ";
		}
		$translation = preg_replace( "/, $/", '', $translation );
		$translation .= " };";
		return $translation;
	}

	/**
	 * Looks up the message for a given key. If a translation of the message exists for the current
	 * language, that translation is returned.  Otherwise, the english value is returned.
	 *
	 * @param $pKey string - the message key
	 *
	 * @return string - the message
	 **/
	function translateMessage ( $pKey ) {

		// the current content language code
		$code = $this->mLanguageCode;

		// default to the english value
		$value = $this->mMessages['en'][$pKey];

		// if it's in the custom messages array, return that value
		if( is_array( $this->mCustomMessages ) && isset( $this->mCustomMessages[$pKey] ) ) {
			$value = $this->mCustomMessages[$pKey];
		}
		// if it's in the regular messages with the desired language, return that value
		else if( isset( $this->mMessages[$code] ) && is_array( $this->mMessages[$code] ) && isset( $this->mMessages[$code][$pKey] ) ) {
			$value = $this->mMessages[$code][$pKey];
		}

		return $value;
	}

	/**
	 * Tidy treats all input as a block, it will e.g. wrap most
	 * input in <p> if it isn't already, fix that and return the fixed text
	 *
	 * @static
	 *
	 * @param string $text The text to fix
	 * @return string The fixed text
	 */
	static function fixTidy( $text ) {
		global $wgUseTidy;

		if ( $wgUseTidy ) {
			$text = preg_replace( '~^<p>\s*~', '', $text );
			$text = preg_replace( '~\s*</p>\s*~', '', $text );
			$text = preg_replace( '~[\r\n]+~', '', $text );
			$text = preg_replace( '~<!-- Tidy found serious XHTML errors -->~', '', $text );
		}
		return $text;
	}

	static function hex2fraction ( $pHex ) {
		list($num) = sscanf( $pHex, "%2x" );
		return $num / 255;
	}

	static function fixBlockDirection( $text, $isRTL ) {
		if ($isRTL) {
			return '<div style="direction: rtl;">'.$text.'</div>';
		}
		return $text;
	}

	/**
	 * A string reversal that supports UTF-8 encoding but leaves numbers alone.
	 *
	 * @param $pString string - the string to reverse
	 *
	 * @return string - the reversed string
	 **/
	static function fixStringDirection ( $pString, $pIsRTL ) {
		if ($pIsRTL) {
			preg_match_all( '/(\d+)?./us', $pString, $ar );
			return join( '', array_reverse( $ar[0] ) );
		}
		return $pString;
	}
}
