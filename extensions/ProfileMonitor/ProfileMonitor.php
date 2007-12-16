<?php

/**
 * Special page to retrieve profiling information about a particular
 * profiling task; acts as a convenient access point for casual queries
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgAutoloadClasses['ProfileMonitor'] = dirname( __FILE__ ) . '/ProfileMonitor.class.php';
	$wgSpecialPages['Profiling'] = 'ProfileMonitor';
	
	$wgExtensionFunctions[] = 'efProfileMonitor';
	$wgExtensionCredits['specialpage'][] = array(
			'name' => 'ProfileMonitor',
			'author' => 'Rob Church',
			'description' => 'Special page to search and inspect profiling data',
	);
	
	function efProfileMonitor() {
		global $wgMessageCache, $wgHooks;
		require_once( dirname( __FILE__ ) . '/ProfileMonitor.i18n.php' );
		foreach( efProfileMonitorMessages() as $lang => $messages )
			$wgMessageCache->addMessages( $messages, $lang );
		$wgHooks['SkinTemplateSetupPageCss'][] = 'efProfileMonitorCss';
	}
	
	function efProfileMonitorCss( &$css ) {
		global $wgTitle;
		if( $wgTitle->isSpecial( 'Profiling' ) ) {
			$file = dirname( __FILE__ ) . '/ProfileMonitor.css';
			$css .= "/*<![CDATA[*/\n" . htmlspecialchars( file_get_contents( $file ) ) . "\n/*]]>*/";
		}
		return true;
	}
	
} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

