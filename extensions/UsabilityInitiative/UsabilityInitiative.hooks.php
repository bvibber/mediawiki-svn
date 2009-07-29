<?php
/**
 * Hooks for Usability Initiative extensions
 *
 * @file
 * @ingroup Extensions
 */

class UsabilityInitiativeHooks {

	/* Static Members */
	
	private static $doOutput = false;
	private static $messages = array();
	private static $styles = array();
	private static $scripts = array();
	private static $scriptFiles = array(
		'base_sets' => array(
			'combined' => array(
				array( 'src' => 'Resources/jquery.combined.js', 'version' => 2 ),
			),
			'combined-min' => array(
				array( 'src' => 'Resources/jquery.combined.min.js', 'version' => 2 ),
			),
			'raw' => array(
				array( 'src' => 'Resources/jquery.js', 'version' => 2 ),
				array( 'src' => 'Resources/jquery.async.js', 'version' => 2 ),
				array( 'src' => 'Resources/jquery.browser.js', 'version' => 2 ),
				array( 'src' => 'Resources/jquery.cookie.js', 'version' => 2 ),
				array( 'src' => 'Resources/jquery.textSelection.js', 'version' => 2 ),
				array( 'src' => 'Resources/jquery.wikiOutline.js', 'version' => 2 ),
			),
		),
		// Code to include when mv_embed is not present
		'no_mv_embed' => array(
			array( 'src' => 'Resources/messages.js', 'version' => 1 ),
		)
	);
	
	/* Static Functions */

	public static function initialize() {
		global $wgUsabilityInitiativeJsMode;
		global $wgUsabilityInitiativeCoesxistWithMvEmbed;
		
		// Only do this the first time!
		if ( !self::$doOutput ) {
			// Default to raw
			$mode = $wgUsabilityInitiativeJsMode; // Just an alias
			if ( !isset( self::$scriptFiles['base_sets'][$mode] ) ) {
				$mode = 'raw';
			}
			// Inlcude base-set of scripts
			self::$scripts = self::$scriptFiles['base_sets'][$mode];
			// Play nice with mv_embed
			if ( !$wgUsabilityInitiativeCoesxistWithMvEmbed ) {
				self::$scripts = array_merge(
					self::$scriptFiles['no_mv_embed'], self::$scripts
				);
			}
		}
		self::$doOutput = true;
	}
	
	/**
	 * AjaxAddScript hook
	 * Adds scripts
	 */
	public static function addResources( $out ) {
		global $wgScriptPath, $wgJsMimeType;
		
		if ( !self::$doOutput )
			return true;
		
		// Loops over each script
		foreach ( self::$scripts as $script ) {
			// Add javascript to document
			$out->addScript(
				Xml::element(
					'script',
					array(
						'type' => $wgJsMimeType,
						'src' => $wgScriptPath .
							"/extensions/UsabilityInitiative/" .
								"{$script['src']}?{$script['version']}",
					),
					'',
					false
				)
			);
		}
		// Transforms messages into javascript object members
		foreach ( self::$messages as $i => $message ) {
			$escapedMessageValue = Xml::escapeJsString( wfMsg( $message ) );
			$escapedMessageKey = Xml::escapeJsString( $message );
			self::$messages[$i] =
				"'{$escapedMessageKey}':'{$escapedMessageValue}'";
		}
		// Converts array of object members to a comma delimited list
		$messagesList = implode( ',', self::$messages );
		// Add javascript to document
		$out->addScript(
			Xml::element(
				'script',
				array( 'type' => $wgJsMimeType ),
				"loadGM({{$messagesList}});"
			)
		);
		
		// Loops over each style
		foreach ( self::$styles as $style ) {
			// Add css for various styles
			$out->addLink(
				array(
					'rel' => 'stylesheet',
					'type' => 'text/css',
					'href' => $wgScriptPath .
							"/extensions/UsabilityInitiative/" .
								"{$style['src']}?{$style['version']}",
				)
			);
		}
		return true;
	}

	/**
	 * Adds a reference to a javascript file to the head of the document
	 * @param string $src Path to the file relative to this extension's folder
	 * @param object $version Version number of the file
	 */
	public static function addScript( $src, $version = '' ) {
		self::$scripts[] = array( 'src' => $src, 'version' => $version );
	}

	/**
	 * Adds a reference to a css file to the head of the document
	 * @param string $src Path to the file relative to this extension's folder
	 * @param string $version Version number of the file
	 */
	public static function addStyle( $src, $version = '' ) {
		self::$styles[] = array( 'src' => $src, 'version' => $version );
	}

	/**
	 * Adds internationalized message definitions to the document for access
	 * via javascript using the gM() function
	 * @param array $messages Key names of messages to load
	 */
	public static function addMessages( $messages ) {
		self::$messages = array_merge( self::$messages, $messages );
	}
}
