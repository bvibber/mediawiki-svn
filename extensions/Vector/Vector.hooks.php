<?php
/**
 * Hooks for Vector extension
 * 
 * @file
 * @ingroup Extensions
 */

class VectorHooks {
	
	/* Static Members */
	
	static $modules = array(
		'collapsiblenav' => array(
			'name' => 'vector.collapsibleNav',
			'resources' => array(
				'scripts' => 'extensions/Vector/modules/vector.collapsibleNav.js',
				'styles' => 'extensions/Vector/modules/vector.collapsibleNav.css',
				'messages' => array(
					'vector-collapsiblenav-more',
				),
				'dependencies' => array(
					'jquery.client',
					'jquery.cookie',
					'jquery.tabIndex',
				),
			),
			'preferences' => array(
				'key' => 'vector-collapsiblenav',
				'ui' => array(
					'type' => 'toggle',
					'label-message' => 'vector-collapsiblenav-preference',
					'section' => 'rendering/advancedrendering',
				),
			),
			'configurations' => array(
				'wgCollapsibleNavBucketTest',
				'wgCollapsibleNavForceNewVersion',
			),
		),
		'collapsibletabs' => array(
			'name' => 'vector.collapsibleTabs',
			'resources' => array(
				'scripts' => 'extensions/Vector/modules/vector.collapsibleTabs.js',
				'dependencies' => array(
					'jquery.collapsibleTabs',
					'jquery.delayedBind',
				),
			),
		),
		'editwarning' => array(
			'name' => 'vector.editWarning',
			'resources' => array(
				'scripts' => 'extensions/Vector/modules/vector.editWarning.js',
				'messages' => array(
					'vector-editwarning-warning',
				),
			),
			'preferences' => array(
				// Ideally this would be 'vector-editwarning'
				'key' => 'useeditwarning',
				'ui' => array(
					'type' => 'toggle',
					'label-message' => 'vector-editwarning-preference',
					'section' => 'editing/advancedediting',
				),
			),
		),
		'expandablesearch' => array(
			'name' => 'vector.expandableSearch',
			'resources' => array(
				'scripts' => 'extensions/Vector/modules/vector.expandableSearch.js',
				'styles' => 'extensions/Vector/modules/vector.expandableSearch.css',
				'dependencies' => array(
					'jquery.client',
					'jquery.expandableField',
					'jquery.delayedBind',
				),
			),
			'preferences' => array(
				'requirements' => array( 'vector-simplesearch' => true ),
			),
		),
		'footercleanup' => array(
			'name' => 'vector.footerCleanup',
			'resources' => array(
				'scripts' => 'extensions/Vector/modules/vector.footerCleanup.js',
				'styles' => 'extensions/Vector/modules/vector.footerCleanup.css',
			),
		),
		'simplesearch' => array(
			'name' => 'vector.simpleSearch',
			'resources' => array(
				'scripts' => 'extensions/Vector/modules/vector.simpleSearch.js',
				'messages' => array(
					'vector-simplesearch-search',
					'vector-simplesearch-containing',
				),
				'dependencies' => array(
					'jquery.client',
					'jquery.suggestions',
					'jquery.autoEllipsis',
				),
			),
			'preferences' => array(
				'requirements' => array( 'vector-simplesearch' => true, 'disablesuggest' => false ),
			),
		),
	);
	
	/* Protected Static Methods */
	
	protected static function isEnabled( $module ) {
		global $wgVectorModules, $wgUser;
		
		$enabled =
			$wgVectorModules[$module]['global'] ||
			(
				$wgVectorModules[$module]['user'] &&
				isset( self::$modules[$module]['preferences']['key'] ) &&
				$wgUser->getOption( self::$modules[$module]['preferences']['key'] )
			);
		if ( !$enabled ) {
			return false;
		}
		if ( isset( self::$modules[$module]['preferences']['requirements'] ) ) {
			foreach ( self::$modules[$module]['preferences']['requirements'] as $requirement => $value ) {
				// Important! We really do want fuzzy evaluation here
				if ( $wgUser->getOption( $requirement ) != $value ) {
					return false;
				}
			}
		}
		return true;
	}
	
	/* Static Methods */
	
	/**
	 * BeforePageDisplay hook
	 * 
	 * Adds the modules to the edit form
	 * 
	 * @param $out OutputPage output page
	 * @param $skin Skin current skin
	 */
	public static function beforePageDisplay( $out, $skin ) {
		global $wgVectorModules;
		
		// Don't load Vector modules for non-Vector skins
		if ( !( $skin instanceof SkinVector ) ) {
			return true;
		}
		
		// Add enabled modules
		foreach ( $wgVectorModules as $module => $enable ) {
			if ( self::isEnabled( $module ) ) {
				$out->addModules( self::$modules[$module]['name'] );
			}
		}
		return true;
	}
	
	/**
	 * GetPreferences hook
	 * 
	 * Adds Vector-releated items to the preferences
	 * 
	 * @param $out User current user
	 * @param $skin array list of default user preference controls
	 */
	public static function getPreferences( $user, &$defaultPreferences ) {
		global $wgVectorModules;
		
		foreach ( $wgVectorModules as $module => $enable ) {
			if ( $enable['user'] && isset( self::$modules['preferences'][$module]['ui'] ) ) {
				$defaultPreferences[self::$modules['preferences'][$module]['key']] =
					self::$modules['preferences'][$module]['ui'];
			}
		}
		return true;
	}
	
	/**
	 * MakeGlobalVariablesScript hook
	 * 
	 * Adds enabled/disabled switches for Vector modules
	 */
	public static function makeGlobalVariablesScript( &$vars ) {
		global $wgVectorModules;
		
		$configurations = array();
		foreach ( $wgVectorModules as $module => $enable ) {
			if (
				isset( self::$modules[$module]['configurations'] ) &&
				is_array( self::$modules[$module]['configurations'] )
			) {
				foreach ( self::$modules[$module]['configurations'] as $configuration ) {
					global $$configuration;
					$configurations[$configuration] = $$configuration;
				}
			}
		}
		if ( count( $configurations ) ) {
			$vars = array_merge( $vars, $configurations );
		}
		return true;
	}
	
	/*
	 * ResourceLoaderRegisterModules hook
	 * 
	 * Adds modules to ResourceLoader
	 */
	public static function resourceLoaderRegisterModules() {
		foreach ( self::$modules as $module ) {
			ResourceLoader::register( $module['name'], new ResourceLoaderFileModule( $module['resources'] ) );
		}
		return true;
	}
}