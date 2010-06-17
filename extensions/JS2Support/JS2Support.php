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

$js2Dir = dirname( __FILE__ ) . '/';

// Localizations
$wgExtensionMessagesFiles[ 'JS2Support' ] =
 	dirname( __FILE__ ) . '/JS2Support.i18n.php';

// Hooks
$wgAutoloadClasses['JS2SupportHooks'] =
	dirname( __FILE__ ) . "/JS2Support.hooks.php";

/**
 * Add Setup the js2 extension hook:
 */
$wgExtensionFunctions[] = 'JS2SupportHooks::setup';

/**
 * MakeGlobalVariablesScript hook ( add the wgScriptLoaderPath var )
 */
$wgHooks['MakeGlobalVariablesScript'][] = 'JS2SupportHooks::addJSVars';

/**
* Add a preference hook
*/
$wgHooks['GetPreferences'][] = 'JS2SupportHooks::addPreferences';

/****************************
* Configuration
* Could eventually go into DefaultSettings.php
*****************************/

/*
 * Simple global to tell extensions JS2 support is available
 */
$wgEnableJS2system = true;


/**
 * For naming javascript modules in extensions
 * for js-modules. ( ie modules hosted inside of extensions )
 */
$wgExtensionJavascriptModules = array();

/**
* If the mwEmbed loader.js javascript should be used to get a list of enabled modules
* This is for stand-alone usage and is set to false for mediaWiki.
*  ( all modules should be loaded from $wgExtensionJavascriptModules var )
*/
$wgUseMwEmbedLoaderModuleList = false;

/**
 * The set of script-loader Named Paths, populated via extensions and javascript module loaders
 */
$wgScriptLoaderNamedPaths = array();

/*
 * $wgEnableScriptLoader; If the script loader should be used to group all javascript requests.
 * more about the script loader: http://www.mediawiki.org/wiki/ScriptLoader
 *
 */
$wgEnableScriptLoader = false;


/**
 * wgEnableTestJavascriptModules if the test modules should be loaded and enabled
 * In production environments its recommend to disabled wgEnableTestJavascriptModules
 * since some tests can be very resource intensive.
 */

$wgEnableTestJavascriptModules = false;

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
 * $wgScriptModifiedMsgCheck Checks MediaWiki NS for latest messege
 * Revision for generating the request id.
 *
 */
$wgScriptModifiedMsgCheck = false;

/**
 * If the scriptloader should output relative css paths
 * Should be set to false if the script-loader is on a different domain
 * from your css
 */
$wgScriptLoaderRelativeCss = true;

/**
 * boolean; if scriptLoader should localize script text(it loads mw.addMessages json
 * call with mediaWiki msgs)
 */
$wgEnableScriptLocalization = true;

/**
 * Path for mwEmbed normally js/mwEmbed/
 */
$wgMwEmbedDirectory = "extensions/JS2Support/mwEmbed";

/**
 * Enables javascript on debugging
 * forces fresh non-minified copies of javascript to be generated
 * on every request.
 */
$wgDebugJavaScript = false;
