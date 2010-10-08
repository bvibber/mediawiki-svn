<?php
/**
 * Hooks for WikiEditor extension
 * 
 * @file
 * @ingroup Extensions
 */

class MwEmbedHooks {
	
	/*
	 * ResourceLoaderRegisterModules hook
	 * 
	 * Adds modules to ResourceLoader
	 */
	public static function resourceLoaderRegisterModules( &$resourceLoader ) {
		foreach ( self::$modules as $name => $resources ) {
			$resourceLoader->register(
				$name, new ResourceLoaderFileModule( $resources, 'extensions/WikiEditor/modules/' )
			);
		}
		return true;
	}
}