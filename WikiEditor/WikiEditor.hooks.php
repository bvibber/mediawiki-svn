<?php
/**
 * Hooks for Usability Initiative WikiEditor extension
 *
 * @file
 * @ingroup Extensions
 */

class WikiEditorHooks {

	static $modules = array(
		'toolbar' => array(
			'preferences' => array(
				'usebetatoolbar' => array(
					'type' => 'toggle',
					'label-message' => 'wikieditor-toolbar-preference',
					'section' => 'editing/experimental',
				),
			),
			'scripts' => array(
				'raw' => array( 'src' => 'WikiEditor/Modules/Toolbar.js', 'version' => 1 ),
				'min' => array( 'src' => 'WikiEditor/Modules/Toolbar.min.js', 'version' => 1 ),
			)
		),
		'toc' => array(
			'preferences' => array(
				'usenavigabletoc' => array(
					'type' => 'toggle',
					'label-message' => 'wikieditor-toc-preference',
					'section' => 'editing/experimental',
				),
			),
			'scripts' => array(
				'raw' => array( 'src' => 'WikiEditor/Modules/Toc.js', 'version' => 1 ),
			)
		),
		'code' => array(
			'preferences' => array(
				'usebetacodeeditor' => array(
					'type' => 'toggle',
					'label-message' => 'wikieditor-code-preference',
					'section' => 'editing/experimental',
				),
			),
			'scripts' => array(
				'raw' => array( 'src' => 'WikiEditor/Modules/Code', 'version' => 1 ),
			)
		),
	);
	
	/* Static Functions */
	
	/**
	 * EditPage::showEditForm:initial hook
	 * Adds the modules to the edit form
	 */
	 public static function addModules( &$toolbar ) {
		global $wgUser, $wgWikiEditorStyleVersion, $wgWikiEditorEnable, $wgUsabilityInitiativeResourceMode;
		
		for ( $wgWikiEditorEnable as $module => $enable ) {
			if (
				$enable['global'] ||
				( $enable['user'] && $wgUser->getOption( self::$modules[$module]['preference']['key'] ) )
			) {
				UsabilityInitiativeHooks::initialize();
				$mode = $wgUsabilityInitiativeResourceMode;
				if ( !isset( self::$modules[$module]['scripts'][$mode] ) ) {
					$mode = 'raw';
				}
				UsabilityInitiativeHooks::addScript(
					self::$modules[$module]['scripts'][$mode]['src'],
					self::$modules[$module]['scripts'][$mode]['version']
				);
			}
		}
		return true;
	}
	
	/**
	 * GetPreferences hook
	 * Add module-releated items to the preferences
	 */
	public static function addPreferences( $user, &$defaultPreferences ) {
		global $wgWikiEditorEnable;
		
		foreach ( $wgWikiEditorEnable as $module => $enable ) {
			foreach ( self::$modules[$module]['preferences'] as $key => $preference ) {
				$defaultPreferences[$key] = $preference;
			}
		}
		return true;
	}
}
