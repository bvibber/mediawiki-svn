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
	'description'    => 'Extends the parser with support for embedded blocks of Lua code',
	'descriptionmsg' => 'lua_desc',
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['Lua'] = $dir . 'Lua.i18n.php';
// convert me to $wgAutoloadClasses
$wgAutoloadClasses['LuaHooks'] = $dir . 'Lua.hooks.php';
$wgAutoloadClasses['LuaError'] = $dir . 'Lua.wrapper.php';
$wgAutoloadClasses['LuaWrapper'] = $dir . 'Lua.wrapper.php';
$wgAutoloadClasses['LuaWrapperExternal'] = $dir . 'Lua.wrapper.php';
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
	$wgHooks['ParserFirstCallInit'][] = 'LuaHooks::parserInit';
} else { // Otherwise do things the old fashioned way
	$wgExtensionFunctions[] = 'LuaHooks::parserInit';
}
# Add a hook to initialise the magic word
$wgHooks['LanguageGetMagic'][] = 'LuaHooks::magic';
$wgHooks['ParserBeforeTidy'][] = 'LuaHooks::beforeTidy';
