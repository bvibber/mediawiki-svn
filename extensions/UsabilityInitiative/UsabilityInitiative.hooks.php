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
				array( 'src' => 'css/suggestions.css', 'version' => 6 ),
				array( 'src' => 'css/vector.collapsibleNav.css', 'version' => 6 ),
				array( 'src' => 'css/vector.footerCleanup.css', 'version' => 1 ),
				array( 'src' => 'css/wikiEditor.css', 'version' => 8 ),
				array( 'src' => 'css/wikiEditor.dialogs.css', 'version' => 14 ),
				array( 'src' => 'css/wikiEditor.preview.css', 'version' => 1 ),
				array( 'src' => 'css/wikiEditor.toc.css', 'version' => 28 ),
				array( 'src' => 'css/wikiEditor.toolbar.css', 'version' => 10 ),
				array( 'src' => 'css/vector/jquery-ui-1.7.2.css', 'version' => '1.7.2y' ),
			),
			'combined' => array(
				array( 'src' => 'css/combined.css', 'version' => 56 ),
				array( 'src' => 'css/vector/jquery-ui-1.7.2.css', 'version' => '1.7.2y' ),
			),
			'minified' => array(
				array( 'src' => 'css/combined.min.css', 'version' => 56 ),
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
		// Core functionality of extension
		'base_sets' => array(
			'raw' => array(

				// These scripts can be pulled from core once the js2 is merged
				// NOTE:: a lot of the logic of hooks.php could be
				// simplified into loader.js
				array(
					'src' => 'js/js2stopgap/ui.core.js',
					'class' => 'j.ui',
					'version' => 1,
				),
				array(
					'src' => 'js/js2stopgap/ui.datepicker.js',
					'class' => 'j.fn.datePicker',
					'version' => 1
				),
				array(
					'src' => 'js/js2stopgap/ui.dialog.js',
					'class' => 'j.ui.dialog',
					'version' => 1
				),
				array(
					'src' => 'js/js2stopgap/ui.draggable.js',
					'class' => 'j.ui.draggable',
					'version' => 1
				),
				array(
					'src' => 'js/js2stopgap/ui.resizable.js',
					'class' => 'j.ui.resizable',
					'version' => 1
				),
				array(
					'src' => 'js/js2stopgap/ui.tabs.js',
					'class' => 'j.ui.tabs',
					'version' => 1
				),
				array(
					'src' => 'js/js2stopgap/jquery.cookie.js',
					'class' => 'j.cookie',
					'version' => 3
				),
				array(
					'src' => 'js/js2stopgap/jquery.textSelection.js',
					'class' => 'j.fn.textSelection',
					'version' => 25
				),

				// Core functionality of extension scripts
				array(
					'src' => 'js/plugins/jquery.async.js',
					'class' => 'j.whileAsync',
					'version' => 3
				),
				array(
					'src' => 'js/plugins/jquery.autoEllipsis.js',
					'class' => 'j.fn.autoEllipsis',
					'version' => 6
				),
				array(
					'src' => 'js/plugins/jquery.browser.js',
					'class' => 'j.browserTest',
					'version' => 3
				),
				array(
					'src' => 'js/plugins/jquery.collapsibleTabs.js',
					'class' => 'j.fn.collapsibleTabs',
					'version' => 5
				),
				array(
					'src' => 'js/plugins/jquery.delayedBind.js',
					'class' => 'j.fn.delayedBind',
					'version' => 1
				),
				array(
					'src' => 'js/plugins/jquery.namespaceSelect.js',
					'class' => 'j.fn.namespaceSelector',
					'version' => 1
				),
				array(
					'src' => 'js/plugins/jquery.suggestions.js',
					'class' => 'j.suggestions',
					'version' => 7
				),
				array(
					'src' => 'js/plugins/jquery.wikiEditor.js',
					'class' => 'j.wikiEditor',
					'version' => 67
				),
				array(
					'src' => 'js/plugins/jquery.wikiEditor.highlight.js',
					'class' => 'j.wikiEditor.modules.highlight',
					'version' => 22
				),
				array(
					'src' => 'js/plugins/jquery.wikiEditor.toolbar.js',
					'class' => 'j.wikiEditor.modules.toolbar',
					'version' => 44
				),
				array(
					'src' => 'js/plugins/jquery.wikiEditor.dialogs.js',
					'class' => 'j.wikiEditor.modules.dialogs',
					'version' => 10
				),
				array(
					'src' => 'js/plugins/jquery.wikiEditor.toc.js',
					'class' => 'j.wikiEditor.modules.toc',
					'version' => 72
				),
				array(
					'src' => 'js/plugins/jquery.wikiEditor.preview.js',
					'class' => 'j.wikiEditor.modules.preview',
					'version' => 9
				),
				array(
					'src' => 'js/plugins/jquery.wikiEditor.templateEditor.js',
					'class' => 'j.wikiEditor.modules.templateEditor',
					'version' => 16
				),
				array(
					'src' => 'js/plugins/jquery.wikiEditor.publish.js',
					'class' => 'j.wikiEditor.modules.publish',
					'version' => 1 ),
			),
			'combined' => array(
				array( 'src' => 'js/plugins.combined.js', 'version' => 178 ),
			),
			'minified' => array(
				array( 'src' => 'js/plugins.combined.min.js', 'version' => 178 ),
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
		global $wgExtensionAssetsPath, $wgJsMimeType;
		global $wgUsabilityInitiativeResourceMode;
		global $wgEditToolbarRunTests, $wgVersion;
		global $wgStyleVersion, $wgEnableScriptLoader;

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
		// Provide backward support for mediaWiki less than 1.17
		// by including "no_js2" js.
		if ( !version_compare( floatval( $wgVersion ), '1.17', '>=') ) {
			$out->includeJQuery();
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
			if ( !version_compare( floatval( $wgVersion ), '1.17', '>=') ) {
				// Add javascript to document
				if ( $script['src']{0} == '/' ) {
					// Path is relative to $wgScriptPath
					global $wgScriptPath;
					$src = "$wgScriptPath{$script['src']}";
				} else {
					// Path is relative to $wgExtensionAssetsPath
					$src = "$wgExtensionAssetsPath/UsabilityInitiative/{$script['src']}";
				}
				$version = isset( $script['version'] ) ? $script['version'] : $wgStyleVersion;
				$out->addScriptFile( $src, $version );
				continue ;
			}
			// else add by class:
			if( isset( $script['class'] ) ){
				$out->addScriptClass( $script['class'] );
			}
		}

		// Transforms messages into javascript object members
		// ( only not handled automatically )
		if ( version_compare( $wgVersion, '1.17', '<' ) ) {
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
		}
		// Loops over each style
		foreach ( self::$styles as $style ) {
			// Add css for various styles
			$out->addLink(
				array(
					'rel' => 'stylesheet',
					'type' => 'text/css',
					'href' => $wgExtensionAssetsPath .
							"/UsabilityInitiative/" .
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
