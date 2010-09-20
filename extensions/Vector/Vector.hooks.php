<?php
/**
 * Hooks for Vector extension
 * 
 * @file
 * @ingroup Extensions
 */

class VectorHooks {
	
	/* Protected Static Members */
	
	protected static $modules = array(
		'vector.collapsibleNav' => array(
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
		'vector.collapsibleTabs' => array(
			'scripts' => 'extensions/Vector/modules/vector.collapsibleTabs.js',
			'dependencies' => array(
				'jquery.collapsibleTabs',
				'jquery.delayedBind',
			),
		),
		'vector.editWarning' => array(
			'scripts' => 'extensions/Vector/modules/vector.editWarning.js',
			'messages' => array(
				'vector-editwarning-warning',
			),
		),
		'vector.expandableSearch' => array(
			'scripts' => 'extensions/Vector/modules/vector.expandableSearch.js',
			'styles' => 'extensions/Vector/modules/vector.expandableSearch.css',
			'dependencies' => array(
				'jquery.client',
				'jquery.expandableField',
				'jquery.delayedBind',
			),
		),
		'vector.footerCleanup' => array(
			'scripts' => 'extensions/Vector/modules/vector.footerCleanup.js',
			'styles' => 'extensions/Vector/modules/vector.footerCleanup.css',
		),
		'vector.simpleSearch' => array(
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
	);
	
	protected static $features = array(
		'collapsiblenav' => array(
			'preferences' => array(
				'vector-collapsiblenav' => array(
					'type' => 'toggle',
					'label-message' => 'vector-collapsiblenav-preference',
					'section' => 'rendering/advancedrendering',
				),
			),
			'configurations' => array(
				'wgCollapsibleNavBucketTest',
				'wgCollapsibleNavForceNewVersion',
			),
			'modules' => array( 'vector.collapsibleNav' ),
		),
		'collapsibletabs' => array(
			'modules' => array( 'vector.collapsibleTabs' ),
		),
		'editwarning' => array(
			'preferences' => array(
				// Ideally this would be 'vector-editwarning'
				'useeditwarning' => array(
					'type' => 'toggle',
					'label-message' => 'vector-editwarning-preference',
					'section' => 'editing/advancedediting',
				),
			),
			'modules' => array( 'vector.editWarning' ),
		),
		'expandablesearch' => array(
			'requirements' => array( 'vector-simplesearch' => true ),
			'modules' => array( 'vector.expandableSearch' ),
		),
		'footercleanup' => array(
			'modules' => array( 'vector.footerCleanup' ),
		),
		'simplesearch' => array(
			'requirements' => array( 'vector-simplesearch' => true, 'disablesuggest' => false ),
			'modules' => array( 'vector.simpleSearch' ),
		),
	);
	
	/* Protected Static Methods */
	
	protected static function isEnabled( $name ) {
		global $wgVectorFeatures, $wgUser;
		
		// Features with global set to true are always enabled
		if ( !isset( $wgVectorFeatures[$name] ) || $wgVectorFeatures[$name]['global'] ) {
			return true;
		}
		// Features with user preference control can have any number of preferences to be specific values to be enabled
		if ( $wgVectorFeatures[$name]['user'] ) {
			if ( isset( self::$features[$name]['requirements'] ) ) {
				foreach ( self::$features[$name]['requirements'] as $requirement => $value ) {
					// Important! We really do want fuzzy evaluation here
					if ( $wgUser->getOption( $requirement ) != $value ) {
						return false;
					}
				}
			}
			return true;
		}
		// Features controlled by $wgVectorFeatures with both global and user set to false are awlways disabled 
		return false;
	}
	
	/* Static Methods */
	
	/**
	 * BeforePageDisplay hook
	 * 
	 * Adds the modules to the page
	 * 
	 * @param $out OutputPage output page
	 * @param $skin Skin current skin
	 */
	public static function beforePageDisplay( $out, $skin ) {
		if ( $skin instanceof VectorSkin ) {
			// Add modules for enabled features
			foreach ( self::$features as $name => $feature ) {
				if ( isset( $feature['modules'] ) && self::isEnabled( $name ) ) {
					$out->addModules( $feature['modules'] );
				}
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
		global $wgVectorFeatures;
		
		foreach ( self::$features as $name => $feature ) {
			if (
				isset( $feature['preferences'] ) &&
				( !isset( $wgVectorFeatures[$name] ) || $wgVectorFeatures[$name]['user'] )
			) {
				foreach ( $feature['preferences'] as $key => $options )
				$defaultPreferences[$key] = $options;
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
		global $wgVectorFeatures;
		
		$configurations = array();
		foreach ( self::$features as $name => $feature ) {
			if (
				isset( $feature['configurations'] ) &&
				( !isset( $wgVectorFeatures[$name] ) || self::isEnabled( $name ) )
			) {
				foreach ( $feature['configurations'] as $configuration ) {
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
		foreach ( self::$modules as $name => $resources ) {
			ResourceLoader::register( $name, new ResourceLoaderFileModule( $resources ) );
		}
		return true;
	}
}