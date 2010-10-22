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
		global $IP, $wgExtensionMessagesFiles;
		
		$fullResourcePath = $IP .'/'. $mwEmbedResourcePath;
		
		// Get the module name from the end of the path: 
		$moduleName =  array_pop ( explode( '/', $mwEmbedResourcePath ) );
		if( !is_dir( $fullResourcePath ) ){
			throw new MWException( "MwEmbed registerModulePath not given readable path:  $mwEmbedResourcePath" );
		}
		
		if( substr( $mwEmbedResourcePath, -1 ) == '/' ){
			throw new MWException( "MwEmbed has trailing slash: " . htmlspecialchars( $mwEmbedResourcePath) );
		}
		
		// Add the messages to the extension messages set: 
		$wgExtensionMessagesFiles[ 'MwEmbed.' . $moduleName ] = $fullResourcePath . '/' . $moduleName . '.i18n.php';				
		
		// Get the mwEmbed module resource list
		$resourceList = include( $fullResourcePath . '/' . $moduleName . '.resources.php' );
		
		// Look for special 'messages' => 'moduleFile' key and load all modules file messages:
		foreach( $resourceList as $name => $resources ){
			if( isset( $resources['messages'] ) && $resources['messages'] == 'moduleFile' ){
				$resourceList[ $name ][ 'messages' ] = array();
				include( $fullResourcePath . '/' . $moduleName . '.i18n.php' );
				foreach( $messages['en'] as $msgKey => $na ){		
					 $resourceList[ $name ][ 'messages' ][] = $msgKey;
				}
			}			
		};
		
		// Add the moduleLoader to the resource list: 
		$resourceList[$moduleName. '.loader'] = array( 'loader' => $moduleName . '.loader.js' );
				
		// Add the resource list into the module set with its provided path 
		self::$moduleSet[ $mwEmbedResourcePath ] = $resourceList;		
	}
	
	/**
	 * ResourceLoaderRegisterModules hook
	 * 
	 * Adds any mwEmbedResources to the ResourceLoader
	 */
	public static function registerModules( &$resourceLoader ) {
			
		// Register all the resources with the resource loader
		foreach( self::$moduleSet as $path => $modules ) {
			foreach ( $modules as $name => $resources ) {							
				$resourceLoader->register(					
					// Resource loader expects trailing slash: 
					$name, new ResourceLoaderFileModule( $resources, $path . '/' )
				);
			}
		}		
		// Continue module processing
		return true;
	}
	
	// Add the mwEmbed module to the page: 
	public static function addMwEmbedModule(  &$out, &$sk ){		
		// Add the mwEmbed module to the output
		$out->addModules( 'mwEmbed' );
		return true;	
	}
}