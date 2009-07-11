<?php
/**
 * Classes for SlippyMap extension
 *
 * @file
 * @ingroup Extensions
 */

class SlippyMap {

	/* Fields */
	
	/**
	 * @var object
	 */
	protected $mParser;

	/**
	 *
	 */
	protected $mArgsRequired = array(
		'lat',
		'lon'
	);

	protected $mArgsList = array(
		'lat' => 'mLat',
		'lon' => 'mLon',
		'zoom' => 'mZoom',
		'width' => 'mWidth',
		'height' => 'mHeight',
		'mode' => 'mMode',
		'layer' => 'mLayer',
		'caption' => 'mCaption',
	);

	/**
	 * An array of error messages that apply to our arguments as
	 * extracted from extractOptions.
	 *
	 * Here because we want to announce all problems with the
     * extension usage at once to the user instead of playing the fix
     * one parameter error at a time game.
	 *
	 * @var array
	 */
	protected $mArgsError = array();

	/**
	 */
	protected $mArgs = array();

	/* Functions */

	/**
	 * Constructor
	 *
	 * @param object $parser Parser instance
	 */
	public function __construct( $parser ) {
		$this->mParser = $parser;
		$this->mMode = 'osm';
	}

	/**
	 * Extract and validate options from input and argv.
	 *
     * Returns a boolean indicating whether there were any errors
	 * during argument processing.
	 *
	 * @param string $input Parser hook input
	 * @param string $argv Parser hook arguments
	 * @return boolean
	 */
	public function extractOptions( $input, $args ) {
		wfProfileIn( __METHOD__ );

		/* <slippymap></slippymap> */
		if ( $input === '' ) {
			$this->mArgsError[] = wfMsg( 'slippymap_error_empty_element', wfMsg( 'slippymap_extname' ), wfMsg( 'slippymap_tagname' ) );
		}

		/* No arguments */
		if ( count( $args ) == 0 ) {
			$this->mArgsError[] = wfMsg( 'slippymap_error_missing_arguments', wfMSg( 'slippymap_tagname' ) );

		/* Some arguments */
		} else {

			/* Make sure we have lat/lon/zoom */
			foreach ($this->mArgsRequired as $requiredArg) {
				if ( ! isset( $args[$requiredArg] ) ) {
					$this->mArgsError[] = wfMsg( 'slippymap_error_missing_attribute_' . $requiredArg );
				}
			}

			/* Keys that the user made up, this is a fatal error since
			 * we want to protect our namespace
			 */
			foreach ( array_keys( $args ) as $user_key ) {
				if ( ! isset( $this->mArgsList[$user_key] ) )
					$this->mArgsError[] = wfMsg( 'slippymap_error_unknown_attribute', $user_key );
			}

			/**
			 *  Go through the list of options and add them to our
			 * fields if they validate.
			 */
			foreach ( $this->mArgsList as $key => $classVar ) {
				if ( isset( $args[$key] ) ) {
					$val = $args[$key];

					if ( $this->validateArgument($key, $val) ) {
						$this->$classVar = $args[$key];
					} else {
						/* Invalid value */
					}
				}
			}
		}

		if ( count( $this->mArgsError ) == 0 ) {
			$this->defaultOptions();
			wfProfileOut( __METHOD__ );
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Fill in defaults for those options for those options that
	 * weren't set during extractOptions()
	 */
	public function defaultOptions( ) {
		global $wgSlippyMapModes;

		if ( ! isset( $this->mMode ) ) {
			$modes = array_keys( $wgSlippyMapModes );
			$default_mode = $modes[0];

			$this->mMode = $default_mode;
		}

		if ( ! isset( $this->mLayer ) ) {
			$this->mLayer = $wgSlippyMapModes[$this->mMode]['layers'][0];
		}

		if ( ! isset( $this->mZoom ) ) {
			$this->mZoom = $wgSlippyMapModes[$this->mMode]['defaultZoomLevel'];
		}

		if ( ! isset( $this->mMarker ) ) {
			$this->mMarker = 0;
		}

		if ( ! isset( $this->mCaption ) ) {
			$this->mCaption = '';
		}

		if ( ! isset( $this->mWidth ) || ! isset( $this->mHeight ) ) {
			$thumbsize = self::getUserThumbSize();

			if ( ! isset( $this->mWidth ) )
				$this->mWidth = $thumbsize;
			if ( ! isset( $this->mHeight ) )
				$this->mHeight = $thumbsize * .72;

			// trim off the 'px' on the end of pixel measurement numbers (ignore if present)
			if ( substr( $this->mWidth, -2 ) == 'px' )
				$this->mWidth = (int) substr( $this->width, 0, -2 );

			if ( substr( $this->mHeight, - 2 ) == 'px' )
				$this->mHeight = (int) substr( $this->height, 0, -2 );
		}
	}

	private static function getUserThumbSize() {
		global $wgUser, $wgOut, $wgThumbLimits;

		return $wgThumbLimits[$wgUser->getOption( 'thumbsize' )];
	}

	/**
	 * Validate the values of a keys listed in $this->mArgsList.
	 *
	 * @param string $key A key we know to be good
	 * @param string $val A user supplied value to validate for the key
	 */
	private function validateArgument( $key, $val ) {
		global $wgSlippyMapSizeRestrictions;
		global $wgSlippyMapModes;
		global $wgLang;

		wfProfileIn( __METHOD__ );

		$ok = false;

		switch ( $key ) {
			case 'lat':
			case 'lon':
				if ( ! preg_match( '~^ -? [0-9]{1,3} (?: \\. [0-9]{1,20} )? $~x', $val ) ) {
					$this->mArgsError[] = wfMsg( 'slippymap_error_invalid_attribute_' . $key . '_value_nan', $val );
				} else {
					/* Note: I'm not calling $wgLang->formatNum( $val ) here on purpose */
					if ( $key === 'lat' && ( $val > 90 || $val < -90 ) ) {
						$this->mArgsError[] = wfMsg( 'slippymap_error_invalid_attribute_' . $key . '_value_out_of_range', $val );
					} else if ( $key === 'lon' && ( $val > 180 || $val < -180 ) ) {
						$this->mArgsError[] = wfMsg( 'slippymap_error_invalid_attribute_' . $key . '_value_out_of_range', $val );
					} else {
						$ok = true;
					}
				}
				break;

			case 'zoom':
				if ( ! preg_match( '~^ [0-9]{1,2} $~x', $val ) ) {
					$this->mArgsError[] = wfMsg( 'slippymap_error_invalid_attribute_' . $key . '_value_nan', $val );
				} else {
					/* TODO: Make configurable depending on layer settings */
					$min_zoom = 0;
					$max_zoom = 18;

					/* Note: I'm not calling $wgLang->formatNum( $val ) here on purpose */
					if ( ( $val > $max_zoom || $val < $min_zoom ) ) {
						$this->mArgsError[] = wfMsg( 'slippymap_error_invalid_attribute_' . $key . '_value_out_of_range', $val, $min_zoom, $max_zoom );
					} else {
						$ok = true;
					}
				}
				break;

			case 'width':
			case 'height':
				if ( ! preg_match( '~^ [0-9]{1,20} $~x', $val ) ) {
					$this->mArgsError[] = wfMsg( 'slippymap_error_invalid_attribute_' . $key . '_value_nan', $val );
				} else {
					list ($min_width, $max_width)   = $wgSlippyMapSizeRestrictions['width'];
					list ($min_height, $max_height) = $wgSlippyMapSizeRestrictions['height'];

					if ( $key == 'width' && ( $val > $max_width || $val < $min_width ) ) {
						$this->mArgsError[] = wfMsg(
							'slippymap_error_invalid_attribute_' . $key . '_value_out_of_range',
							$val,
							$min_width,
							$max_width
						);
					} else if ( $key == 'height' && ( $val > $max_height || $val < $min_height ) ) {
						$this->mArgsError[] = wfMsg(
							'slippymap_error_invalid_attribute_' . $key . '_value_out_of_range',
							$val,
							$min_width,
							$max_width
						);
					} else {
						$ok = true;
					}
				}
				break;

			case 'mode':
				$modes = array_keys( $wgSlippyMapModes );
				if ( ! in_array( $val, $modes ) ) {
					$this->mArgsError[] = wfMsg(
						'slippymap_error_invalid_attribute_' . $key . '_value_not_a_mode',
						$val,
						$wgLang->listToText( array_map( array( &$this, 'addHtmlTT' ), $modes ) )
					);
				} else {
					$ok = true;
				}
				break;
			case 'layer':
				/* TODO validate */
			case 'caption':
				/* Anything goes as far as the caption is concerned. It's the parser's problem if it's not OK */
				$ok = true;
				break;

			default:
				die("internal error: Unknown parameter");
		}

		wfProfileOut( __METHOD__ );
		return $ok;
	}

	/**
	 * Callback function for array_map to add <tt> to array elements.
	 */
	private static function addHtmlTT( $str ) {
		return "<tt>$str</tt>";
	}

	/**
	 * Return HTML output for the parser tag, hopefully a rendered map
	 * but if we've had any errors return an error message instead.
	 *
	 * @param int id
	 */
	public function render( $id ) {
		global $wgOut, $wgJsMimeType;
		global $wgSlippyMapModes;

		$mapcode = <<<EOT

			<script type="{$wgJsMimeType}">slippymaps.push(new slippymap_map($id, {
				mode: '{$this->mMode}',
				layer: '{$this->mLayer}',
				lat: {$this->mLat},
				lon: {$this->mLon},
				zoom: {$this->mZoom},
				width: {$this->mWidth},
				height: {$this->mHeight},
				marker: {$this->mMarker}
			}));</script>
			 
			<!-- mapframe -->
			<div class="mapframe" style="width:{$this->mWidth}px">
EOT;

		$static_rendering = $wgSlippyMapModes[$this->mMode]['static_rendering'];
		if ( isset( $static_rendering ) ) {
			$mapcode .= self::getStaticMap( $id, $static_rendering );
		} else {
			$mapcode .= self::getDynamicMap( $id );
		}

		if ( $this->mCaption ) {
			$mapcode .= "<div class='mapcaption'>" . $this->mParser->recursiveTagParse($this->mCaption) . "</div>";
		}

		$mapcode .= <<<EOT

		<!-- /mapframe -->
		</div>
EOT;

		return $mapcode;
	}


	/**
	 * This generates dynamic map code
	 *
	 * @return string: containing dynamic map html code
	 */
	protected function getDynamicMap( $id ) {
		global $wgJsMimeType;
		$mapcode = <<<EOT
				<!-- map div -->
				<div id="map{$id}" class="map" style="width:{$this->mWidth}px; height:{$this->mHeight}px;">
					<script type="{$wgJsMimeType}">slippymaps[{$id}].init();</script>
				<!-- /map div -->
				</div>
EOT;
		return $mapcode;
	}

	/**
	 * This generates static map code
	 *
	 * @return string: containing static map html code
	 */	
	protected function getStaticMap( $id, $static_rendering ) {
		$staticType				= $static_rendering['type'];
		$staticOptions			= $static_rendering['options'];

		$static = new $staticType($this->mLat, $this->mLon, $this->mZoom, $this->mWidth, $this->mHeight, $staticOptions);
		$rendering_url = $static->getUrl();

		$clickToActivate = wfMsgHtml('slippymap_clicktoactivate');
		$mapcode = <<<EOT

				<!-- map div -->
				<div id="map{$id}" class="map" style="width:{$this->mWidth}px; height:{$this->mHeight}px;">
					<!-- Static preview -->
					<img
						id="mapPreview{$id}"
						class="mapPreview"
						src="{$rendering_url}"
						onclick="slippymaps[{$id}].init();"
						width="{$this->mWidth}"
						height="{$this->mHeight}"
						alt="Slippy Map"
						title="{$clickToActivate}"/>
				<!-- /map div -->
				</div>
EOT;

		return $mapcode;
	}

	/* /AIDS */

	/**
	 * Reads $this->mArgsError and returns HTML explaining what the
	 * user did wrong.
	 */
	public function renderErrors() {
		return $this->mParser->recursiveTagParse( $this->errorHtml() );
	}

	protected function errorHtml() {
		if ( count( $this->mArgsError ) == 1 ) {
			return
				Xml::tags(
					'strong',
					array( 'class' => 'error' ),
					wfMsg( 'slippymap_error',
						   wfMsg( 'slippymap_extname' ),
						   $this->mArgsError[0]
					)
				);
		} else {
			$li = '';
			foreach ($this->mArgsError as $error) {
				$li .= Xml::tags(
					'li',
					array( 'class' => 'error' ),
					$error
				);
			}
			return
				Xml::tags(
					'strong',
					array( 'class' => 'error' ),
					wfMsgNoTrans( 'slippymap_errors', wfMsgNoTrans( 'slippymap_extname' ) )
					. 
					Xml::tags(
						'ul',
						null,
						$li
					)
				);
		}
	}
}
