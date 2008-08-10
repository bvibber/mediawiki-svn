<?php
/**
 * Lua parser extensions for MediaWiki
 *
 * @author Fran Rogers
 * @package MediaWiki
 * @addtogroup Extensions
 * @license See 'COPYING'
 * @file
 */

$wgExtensionCredits['parserhook'][] = array(
	'name'           => 'Lua parser extensions',
	'author'         => 'Fran Rogers',
	'svn-date'       => '$LastChangedDate$',
	'svn-revision'   => '$LastChangedRevision$',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:Lua',
	'description'    => 'Extends the parser with support for embedded blocks ofLua code',
	'descriptionmsg' => 'lua_desc',
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['Lua'] = $dir . 'Lua.i18n.php';
// convert me to $wgAutoloadClasses
require_once( $dir . 'Lua_body.php');
$wgLuaWrapperFile = $dir . 'LuaWrapper.lua';

if (!isset($wgLuaExternalInterpreter))
	$wgLuaExternalInterpreter = FALSE;
if (!isset($wgLuaExternalInterpreter))
	$wgLuaExternalInterpreter = FALSE;
if (!isset($wgLuaMaxLines))
	$wgLuaMaxLines = 1000000;
if (!isset($wgLuaMaxCalls))
	$wgLuaMaxCalls = 2000;
if (!isset($wgLuaMaxTime))
	$wgLuaMaxTime = 5;

# Avoid unstubbing $wgParser on setHook() too early on modern (1.12+) MW versions, as per r35980
if (defined('MW_SUPPORTS_PARSERFIRSTCALLINIT')) {
	$wgHooks['ParserFirstCallInit'][] = 'efLua_ParserInit';
} else { // Otherwise do things the old fashioned way
	$wgExtensionFunctions[] = 'efLua_ParserInit';
}
# Define a setup function
$wgExtensionFunctions[] = 'efLua_FunctionSetup';
# Add a hook to initialise the magic word
$wgHooks['LanguageGetMagic'][] = 'efLua_Magic';
$wgHooks['ParserBeforeTidy'][] = 'efLua_BeforeTidy';
