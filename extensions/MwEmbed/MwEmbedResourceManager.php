<?php
/**
 * Enables you to register mwEmbed resource sets / modules with a simple 
 * 
 * @file
 * @ingroup Extensions
 */

class MwEmbedResourceManager {
	
	protected static $moduleSet = array();
	
	/**
	 * Register mwEmbeed resource set 
	 * 
	 * Adds modules to ResourceLoader
	 */
	public static function registerModulePath( $mwEmbedResourcePath ) {
		
		// Get the module name from the end of the path: 
		$moduleName =  array_pop ( explode( '/', $mwEmbedResourcePath ) );
		if( !is_dir( $mwEmbedResourcePath ) ){
			throw new MWException( "registerModulePath not given readable path:  $mwEmbedResourcePath" );
		}
		
		// Get the resource list
		$resourceList = include( $mwEmbedResourcePath . '/ResourceList.php' );
		
		// Look for special 'messages' => 'moduleFile' key and load all modules file messages:
		foreach( $resourceList as $name => $resources ){
			if( isset( $resources['messages'] ) && $resources['messages'] == 'moduleFile' ){
				$resourceList[ $name ][ 'messages' ] = array();
				include( $mwEmbedResourcePath . '/' . $moduleName . '.i18n.php' );
				foreach( $messages['en'] as $msgKey => $na ){		
					 $resourceList[ $name ][ 'messages' ][] = $msgKey;
				}				
			}			
		} 
		
		// Add the resource list into the module set with its provided path 
		self::$moduleSet[ $mwEmbedResourcePath ] = $resourceList;		
	}
	
	/**
	 * ResourceLoaderRegisterModules hook
	 * 
	 * Adds any mwEmbedResources to the ResourceLoader
	 */
	public static function resourceLoaderRegisterModules( &$resourceLoader ) {
			
		// Register all the resources with the resource loader
		foreach( self::$moduleSet as $path => $modules ) {
			foreach ( $modules as $name => $resources ) {				
				$resourceLoader->register(
					$name, new ResourceLoaderFileModule( $resources, $path )
				);
			}
		}
		
		// Continue module processing
		return true;
	}
}