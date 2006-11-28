<?php

/**
 * Special page to retrieve profiling information about a particular
 * profiling task; acts as a convenient access point for casual queries
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <rob.church@mintrasystems.com>
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgAutoloadClasses['ProfileMonitor'] = dirname( __FILE__ ) . '/ProfileMonitor.class.php';
	$wgSpecialPages['Profiling'] = 'ProfileMonitor';
	
	$wgExtensionCredits['specialpage'][] = array( 'name' => 'ProfileMonitor', 'author' => 'Rob Church' );
	$wgExtensionFunctions[] = 'efProfileMonitor';
	
	function efProfileMonitor() {
		global $wgMessageCache, $wgHooks;
		require_once( dirname( __FILE__ ) . '/ProfileMonitor.i18n.php' );
		$wgMessageCache->addMessages( efProfileMonitorMessages() );
		$wgHooks['SkinTemplateSetupPageCss'][] = 'efProfileMonitorCss';
	}
	
	function efProfileMonitorCss( &$css ) {
		$file = dirname( __FILE__ ) . '/ProfileMonitor.css';
		$css = "/*<![CDATA[*/\n" . htmlspecialchars( file_get_contents( $file ) ) . "\n/*]]>*/";
		return false;
	}
	
} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

?>