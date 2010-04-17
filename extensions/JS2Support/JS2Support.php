<?php
if ( !defined( 'MEDIAWIKI' ) ) die();
/**
 * @copyright Copyright © 2010 Michael Dale <michael.dale@kaltura.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'JS2Support',
	'url' => 'http://www.mediawiki.org/wiki/Extension:JS2Support',
	'author' => array( 'Michael Dale' ),
	'descriptionmsg' => 'js2support-desc',
);

$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles[ 'JS2Support' ] = $dir . 'JS2Support.i18n.php';
$wgAutoloadClasses[ 'ScriptLoaderOutputPage' ] = $dir . 'ScriptLoaderOutputPage.php';

$wgExtensionFunctions[] = 'wfReMapOutputPage';

function wfReMapOutputPage(){
	global $wgOut;
	$wgOut = new StubObject( 'wgOut', 'ScriptLoaderOutputPage' );
}
require_once( $dir . 'JS2AutoLoader.php' );


/****************************
* DefaultSettings.php 
*****************************/

/*
 * Array mapping JavaScript class to web path for use by the script loader.
 * This is populated in AutoLoader.php.
 */
$wgJSAutoloadClasses = array();

/**
 * For defining the location of loader.js files of
 * Extension mwEmbed modules. ( ie modules hosted inside of extensions )
 */
$wgExtensionJavascriptLoader = array();

/*
 * $wgEnableScriptLoader; If the script loader should be used to group all javascript requests.
 * more about the script loader: http://www.mediawiki.org/wiki/ScriptLoader
 *
 */
$wgEnableScriptLoader = false;

/**
 * $wgScriptModifiedCheck should run a file modified check on javascript files when
 * generating unique request ids for javascript include using the script-loader
 *
 * note this will only check core scripts that are directly included on the page.
 * (not scripts loaded after the initial page display since after initial page
 * display scripts inherit the unique request id)
 *
 * You can also update $wgStyleVersion
 */
$wgScriptModifiedFileCheck = true;

/**
 * The google closure compiler path
 */
$wgClosureCompilerPath = false;

/**
 * The path to java run time environment
 */
$wgJavaPath = false;

/**
 *  The level of optimization for the closure compiler
 *  NOTE: SIMPLE_OPTIMIZATIONS is recommended since it preserves
 *  functionality of syntactically valid JavaScript
 *
 *  for more info see: http://code.google.com/closure/compiler/docs/compilation_levels.html
 */
$wgClosureCompilerLevel = 'SIMPLE_OPTIMIZATIONS';

/*
 * $wgScriptModifiedMsgCheck Checks MediaWiki NS for latest
 * Revision for generating the request id.
 *
 */
$wgScriptModifiedMsgCheck = false;

/**
 * If the api iframe proxy should be enabled or not.
 */
$wgEnableIframeApiProxy = false;

/**
 * boolean; if we should enable javascript localization (it loads mw.addMessages json
 * call with mediaWiki msgs)
 */
$wgEnableScriptLocalization = true;

/**
 * Path for mwEmbed normally js/mwEmbed/
 */
$wgMwEmbedDirectory = "extensions/JS2Support/js/mwEmbed/";

/**
 * Enables javascript on debugging
 * forces fresh non-minified copies of javascript to be generated
 * on every request.
 */
$wgDebugJavaScript = false;


/* AddMedia Extension EntryPoints */
