<?php

/*
 * Minify bundles the YUI CSS compressor by Julien Lecomte and Isaac Schlueter with
 * the JSMin Javascript compressor by Douglass Crockford and Ryan Grove.
 *
 * When installed it automatically catches calls to RawPage.php and pre-compresses the
 * CSS and Javascript output generated there.  This can significantly reduce the size of 
 * CSS and Javascript files that are dynamically returned by Mediawiki, such as
 * Mediawiki:Common.css and Mediawiki:Common.js.  However, it does not affect the static
 * files living in /skins/, etc.  
 */

$wgExtensionCredits['other'][] = array(
	'name'            => 'Minify',
	'version'         => '0.8.0', // June 22, 2009
	'description'     => 'Compresses CSS and JS from action=raw',
	'descriptionmsg'  => 'minify-desc',
	'author'          => 'Robert Rohde',
	'url'		  => 'http://www.mediawiki.org/wiki/Extension:Minify',
	'path'		  => __FILE__,
);

$wgAutoloadClasses['Minify'] = 
	dirname( __FILE__ ) . '/Minify_body.php';
$wgAutoloadClasses['JSMin'] = 
	dirname( __FILE__ ) . '/jsmin.php';

$wgHooks['RawPageViewBeforeOutput'][] = 'wfMinify';
$wgExtensionMessagesFiles['Minify'] = dirname( __FILE__ ) . '/Minify.i18n.php';

function wfMinify( &$rawPage, &$text ) {

	$minify = new Minify( $text );
	$text = $minify->run();

	return true;
}
