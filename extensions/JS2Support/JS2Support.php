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

$wgAutoloadClasses = array_merge( $wgAutoloadClasses,
	 array(
		'JSMin' => $dir . 'js/mwEmbed/includes/library/JSMin.php',
		'Minify_CSS' => $dir . 'js/mwEmbed/includes/library/CSS.php',
		'Minify_CommentPreserver' => $dir . 'js/mwEmbed/includes/library/CommentPreserver.php',
		'Minify_CSS_Compressor' => $dir . 'js/mwEmbed/includes/library/CSS/Compressor.php',
		'Minify_CSS_UriRewriter' => $dir . 'js/mwEmbed/includes/library/CSS/UriRewriter.php',
		'JSMinException' => $dir . 'js/mwEmbed/includes/minify/JSMin.php',
		'jsScriptLoader' => $dir . 'js/mwEmbed/jsScriptLoader.php',
		'jsClassLoader' => $dir . 'js/mwEmbed/includes/jsClassLoader.php',
		'simpleFileCache' => $dir . 'js/mwEmbed/jsScriptLoader.php',
	)
);

// Autoloader for core mediaWiki JavaScript files (path is from the MediaWiki root folder)
// All other named paths should be merged with this global
$wgScriptLoaderNamedPaths = array(
	'ajax' => 'skins/common/ajax.js',
	'ajaxwatch' => 'skins/common/ajaxwatch.js',
	'allmessages' => 'skins/common/allmessages.js',
	'block' => 'skins/common/block.js',
	'changepassword' => 'skins/common/changepassword.js',
	'diff' => 'skins/common/diff.js',
	'edit' => 'skins/common/edit.js',
	'enhancedchanges.js' => 'skins/common/enhancedchanges.js',
	'history' => 'skins/common/history.js',
	'htmlform' => 'skins/common/htmlform.js',
	'IEFixes' => 'skins/common/IEFixes.js',
	'metadata' => 'skins/common/metadata.js',
	'mwsuggest' => 'skins/common/mwsuggest.js',
	'prefs' => 'skins/common/prefs.js',
	'preview' => 'skins/common/preview.js',
	'protect' => 'skins/common/protect.js',
	'rightclickedit' => 'skins/common/rightclickedit.js',
	'sticky' => 'skins/common/sticky.js',
	'upload' => 'skins/common/upload.js',
	'wikibits' => 'skins/common/wikibits.js',

	// Css bindings
	'mw.style.shared' => 'skins/common/shared.css',
	'mw.style.commonPrint' => 'skins/common/commonPrint.css',
	'mw.style.vectorMainLTR' => 'skins/vector/main-ltr.css',
	'mw.style.vectorMainRTR' => 'skins/vector/main-rtl.css',

	// Monobook css
	'mw.sytle.mbMain' => 'skins/monobook/main.css',
	'mw.style.mbIE5' => 'skins/monobook/IE50Fixes.css',
	'mw.style.mbIE55' => 'skins/monobook/IE55Fixes.css',
	'mw.style.mbIE60' => 'skins/skins/monobook/IE60Fixes.css',
	'mw.style.mbIE7' => 'skins/monobook/IE70Fixes.css',
);


/**
 * Remap output page
 */
$wgExtensionFunctions[] = 'wfReMapOutputPage';
function wfReMapOutputPage(){
	global $wgOut;
	$wgOut = new StubObject( 'wgOut', 'ScriptLoaderOutputPage' );
}

/**
 * MakeGlobalVariablesScript hook ( add the wgScriptLoaderPath var )
 */
$wgHooks['MakeGlobalVariablesScript'][] = 'js2SupportAddJSVars';
function js2SupportAddJSVars( &$vars ) {
	global $wgExtensionAssetsPath;
	$vars = array_merge( $vars,
		array(
			'wgScriptLoaderLocation' => $wgExtensionAssetsPath . 'JS2Support/mwScriptLoader.php'
		)
	);
	return true;
}

/***************************
* LocalSettings.php enabled js extensions
****************************/
require_once( $dir . 'AddMediaWizard/AddMediaWizard.php' );

/****************************
* DefaultSettings.php
*****************************/

/*
 * Simple global to tell extensions JS2 support is available
 */
$wgEnableJS2system = true;


/**
 * For defining the location of loader.js files of
 * for js-modules. ( ie modules hosted inside of extensions )
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
$wgMwEmbedDirectory = "extensions/JS2Support/js/mwEmbed";

/**
 * Enables javascript on debugging
 * forces fresh non-minified copies of javascript to be generated
 * on every request.
 */
$wgDebugJavaScript = false;
