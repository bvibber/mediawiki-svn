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
	private static $variables = array();
	private static $literalVariables = array();
	private static $styles = array();
	private static $styleFiles = array(
		'base_sets' => array(
			'raw' => array(
				array( 'src' => 'css/collapsibleLeftNav.css', 'version' => 3 ),
				array( 'src' => 'css/suggestions.css', 'version' => 6 ),
				array( 'src' => 'css/wikiEditor.css', 'version' => 7 ),
				array( 'src' => 'css/wikiEditor.toolbar.css', 'version' => 8 ),
				array( 'src' => 'css/wikiEditor.dialogs.css', 'version' => 10 ),
				array( 'src' => 'css/wikiEditor.toc.css', 'version' => 25 ),
				array( 'src' => 'css/wikiEditor.preview.css', 'version' => 1 ),
				array( 'src' => 'css/vector/jquery-ui-1.7.2.css', 'version' => '1.7.2y' ),
			),
			'combined' => array(
				array( 'src' => 'css/combined.css', 'version' => 44 ),
				array( 'src' => 'css/vector/jquery-ui-1.7.2.css', 'version' => '1.7.2y' ),
			),
			'minified' => array(
				array( 'src' => 'css/combined.min.css', 'version' => 44 ),
				array( 'src' => 'css/vector/jquery-ui-1.7.2.css', 'version' => '1.7.2y' ),
			),
		)
	);
	private static $scripts = array();
	private static $scriptFiles = array(
		'tests' => array(
			array( 'src' => 'js/tests/wikiEditor.toolbar.js', 'version' => 0 )
		),
		'modules' => array(
			'raw' => array(),
			'combined' => array(),
			'minified' => array()
		),
		// Code to include when js2 is not present
		'no_js2' => array(
			'raw' => array(
				array( 'src' => '../../js2/js2stopgap.js' )
			),
			'combined' => array(
				array( 'src' => '../../js2/js2stopgap.js' )
			),
			'minified' => array(
				array( 	'src' => '../../js2/js2stopgap.min.js' )
			),
		),
		// Core functionality of extension
		'base_sets' => array(
			'raw' => array(
				array( 'src' => 'js/plugins/jquery.async.js', 'version' => 3 ),
				array( 'src' => 'js/plugins/jquery.autoEllipse.js', 'version' => 4 ),
				array( 'src' => 'js/plugins/jquery.browser.js', 'version' => 3 ),
				array( 'src' => 'js/plugins/jquery.collapsibleTabs.js', 'version' => 5 ),
				array( 'src' => 'js/plugins/jquery.cookie.js', 'version' => 3 ),
				array( 'src' => 'js/plugins/jquery.delayedBind.js', 'version' => 1 ),
				array( 'src' => 'js/plugins/jquery.inherit.js', 'version' => 1 ),
				array( 'src' => 'js/plugins/jquery.namespaceSelect.js', 'version' => 1 ),
				array( 'src' => 'js/plugins/jquery.suggestions.js', 'version' => 6 ),
				array( 'src' => 'js/plugins/jquery.textSelection.js', 'version' => 21 ),
				array( 'src' => 'js/plugins/jquery.wikiEditor.js', 'version' => 40 ),
				array( 'src' => 'js/plugins/jquery.wikiEditor.highlight.js', 'version' => 9 ),
				array( 'src' => 'js/plugins/jquery.wikiEditor.toolbar.js', 'version' => 40 ),
				array( 'src' => 'js/plugins/jquery.wikiEditor.dialogs.js', 'version' => 10 ),
				array( 'src' => 'js/plugins/jquery.wikiEditor.toc.js', 'version' => 54 ),
				array( 'src' => 'js/plugins/jquery.wikiEditor.preview.js', 'version' => 8 ),
				array( 'src' => 'js/plugins/jquery.wikiEditor.templateEditor.js', 'version' => 11 ),
				array( 'src' => 'js/plugins/jquery.wikiEditor.publish.js', 'version' => 1 ),
			),
			'combined' => array(
				array( 'src' => 'js/plugins.combined.js', 'version' => 126 ),
			),
			'minified' => array(
				array( 'src' => 'js/plugins.combined.min.js', 'version' => 126 ),
			),
		),
	);
	
	/* Static Functions */
	
	public static function initialize() {
		self::$doOutput = true;
	}
	
	/**
	 * AjaxAddScript hook
	 * Adds scripts
	 */
	public static function addResources( $out ) {
		global $wgScriptPath, $wgJsMimeType;
		global $wgUsabilityInitiativeResourceMode;
		global $wgEnableJS2system, $wgEditToolbarRunTests;
		global $wgStyleVersion;
		
		wfRunHooks( 'UsabilityInitiativeLoadModules' );
		
		if ( !self::$doOutput )
			return true;
		
		// Default to raw
		$mode = $wgUsabilityInitiativeResourceMode; // Just an alias
		if ( !isset( self::$scriptFiles['base_sets'][$mode] ) ) {
			$mode = 'raw';
		}
		// Include base-set of scripts
		self::$scripts = array_merge(
			self::$scriptFiles['base_sets'][$mode],
			self::$scriptFiles['modules'][$mode],
			self::$scripts
		);
		// Provide enough support to make things work, even when js2 is not
		// in use (eventually it will be standard, but right now it's not)
		if ( !$wgEnableJS2system ) {
			self::$scripts = array_merge(
				self::$scriptFiles['no_js2'][$mode], self::$scripts
			);
		}
		// Include base-set of styles
		self::$styles = array_merge(
			self::$styleFiles['base_sets'][$mode], self::$styles
		);
		if ( $wgEditToolbarRunTests ) {
			// Include client side tests
			self::$scripts = array_merge(
				self::$scripts, self::$scriptFiles['tests']
			);
		}
		// Loops over each script
		foreach ( self::$scripts as $script ) {
			// Add javascript to document
			$src = "$wgScriptPath/extensions/UsabilityInitiative/{$script['src']}";
			$version = isset( $script['version'] ) ? $script['version'] : $wgStyleVersion;
			$out->addScript(
				Xml::element(
					'script',
					array(
						'type' => $wgJsMimeType,
						'src' => "$src?$version",
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
		// Add javascript to document
		if ( count( self::$messages ) > 0 ) {
			$out->addScript(
				Xml::tags(
					'script',
					array( 'type' => $wgJsMimeType ),
					'mw.addMessages({' . implode( ',', self::$messages ) . '});'
				)
			);
		}
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
	 * MakeGlobalVariablesScript hook
	 */
	public static function addJSVars( &$vars ) {
		$vars = array_merge( $vars, self::$variables );
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
	
	/**
	 * Adds variables that will be turned into global variables in JS
	 * @param $variables array of "name" => "value"
	 */
	public static function addVariables( $variables ) {
		self::$variables = array_merge( self::$variables, $variables );
	}
	
	/**
	 * Adds scripts for modules
	 * @param $scripts array with 'raw', 'combined' and 'minified' keys
	 */
	public static function addModuleScripts( $scripts ) {
		self::$scriptFiles['modules'] = array_merge(
			self::$scriptFiles['modules'], $scripts );
	}
}
