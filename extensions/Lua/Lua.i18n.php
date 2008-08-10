<?php
/**
 * Lua parser extensions for MediaWiki - Internationalization
 *
 * @author Fran Rogers
 * @package MediaWiki
 * @addtogroup Extensions
 * @license See 'COPYING'
 * @file
 */

$messages = array();

$messages['en'] = array(
	'lua_desc'               => 'Extends the parser with support for embedded blocks of [http://www.lua.org/ Lua] code',
	'lua_error'              => 'Error on line $1',
	'lua_extension_notfound' => 'Lua extension not configured',
	'lua_interp_notfound'    => 'Lua interpreter not found',
	'lua_error_internal'     => 'Internal error',
	'lua_overflow_recursion' => 'Recursion limit reached',
	'lua_overflow_loc'       => 'Maximum lines of code limit reached',
	'lua_overflow_time'      => 'Maximum execution time reached',
);
